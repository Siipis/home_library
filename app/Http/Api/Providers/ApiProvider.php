<?php


namespace App\Http\Api\Providers;


use Cache;
use Str;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

abstract class ApiProvider
{
    protected $client;

    /**
     * Setting this to false disables all caching.
     * @var bool
     */
    protected $cache = false;

    /**
     * Setting this to true ignores all responses from the provider.
     * @var bool
     */
    protected $disabled = false;

    /**
     * ApiProvider constructor.
     */
    public function __construct()
    {
        $this->client = new Client([
            'timeout' => $this->getTimeout(),
        ]);

        $this->cache = \Env::production();
    }

    /**
     * @param array $options
     * @return mixed
     * @throws RequestException
     * @link http://docs.guzzlephp.org/en/stable/quickstart.html
     */
    protected function fetch(array $options = [])
    {
        if (!$this->getUrl($options) || $this->disabled) return false;

        return $this->request($this->getUrl($options));
    }

    /**
     * @param string $url
     * @return mixed
     * @link http://docs.guzzlephp.org/en/stable/quickstart.html
     */
    protected function request(string $url)
    {
        if ($this->disabled) return false;

        if ($this->hasCache() && Cache::has($url)) {
            return Cache::get($url);
        }

        try {
            $response = $this->client->get($url);

            $handled = $this->handleResponse($response);

            if ($this->hasCache()) {
                Cache::put($url, $handled, $this->getCacheExpiry());
            }

            return $handled;
        } catch (RequestException $exception) {
            return $this->handleException($exception);
        }
    }

    /**
     * @param $response
     * @return mixed
     */
    private function handleResponse($response)
    {
        $contentType = $response->getHeader('Content-Type');
        $contents = $response->getBody()->getContents();

        if (!empty($contentType)) {
            $contentType = is_array($contentType) ? $contentType[0] : $contentType;

            if (Str::contains($contentType, 'application/json')) {
                return $this->parseResponse(json_decode($contents, true));
            }

            if (Str::contains($contentType, 'application/xml')) {
                return $this->parseResponse($this->parseXml($contents));
            }
        }

        return $this->parseResponse($contents);
    }

    /**
     * @param $xml
     * @return array
     */
    private function parseXml($xml)
    {
        $xml = simplexml_load_string($xml);

        return $this->xmlToArray($xml);
    }

    /**
     * @param $xml
     * @param array $array
     * @return array
     */
    private function xmlToArray($xml, array $array = [])
    {
        foreach ((array) $xml as $key => $value) {
            if (is_iterable($value)) {
                if ($value instanceof \SimpleXMLElement) {
                    if (count($value) === 0) {
                        $array[$key] = trim((string) $value);
                        continue;
                    }
                }

                $array[$key] = $this->xmlToArray($value);
                continue;
            }


            $array[$key] = $value;
        }

        return $array;
    }

    /**
     * @return bool
     */
    private function hasCache()
    {
        if (!$this->cache) return false;

        return !empty($this->getCacheConfig());
    }

    /**
     * @return mixed
     */
    private function getCacheExpiry()
    {
        $config = $this->getCacheConfig();

        if (empty($config)) return null;

        $time = now();

        if (isset($config['days'])) {
            return $time->addDays($config['days']);
        }

        if (isset($config['hours'])) {
            return $time->addHours($config['hours']);
        }

        if (isset($config['minutes'])) {
            return $time->addMinutes($config['minutes']);
        }

        if (isset($config['seconds'])) {
            return $time->addSeconds($config['seconds']);
        }

        return $time;
    }

    /**
     * @param $response
     * @return mixed
     */
    protected abstract function parseResponse($response);

    /**
     * @param RequestException $exception
     * @return mixed
     */
    protected abstract function handleException(RequestException $exception);

    /**
     * @return int
     */
    protected abstract function getTimeout();

    /**
     * @return array
     */
    protected abstract function getCacheConfig();

    /**
     * @param array $options
     * @return string
     */
    protected abstract function getUrl(array $options = []);
}
