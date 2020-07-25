<?php


namespace App\Http\Api\Providers;


use Str;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

abstract class ApiProvider
{
    protected $client;

    /**
     * ApiProvider constructor.
     */
    public function __construct()
    {
        $this->client = new Client([
            'timeout' => $this->getTimeout(),
        ]);
    }

    /**
     * @param array $options
     * @return mixed
     * @throws RequestException
     * @link http://docs.guzzlephp.org/en/stable/quickstart.html
     */
    protected function fetch(array $options = [])
    {
        if (!$this->getUrl($options)) return false;

        return $this->request($this->getUrl($options));
    }

    /**
     * @param string $url
     * @return mixed
     */
    protected function request(string $url)
    {
        $response = $this->client->get($url);

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
            $array[$key] = (is_object ($value) || is_array ($value)) ? $this->xmlToArray($value) : $value;
        }

        return $array;
    }

    /**
     * @param $response
     * @return mixed
     */
    protected abstract function parseResponse($response);

    /**
     * @return int
     */
    protected abstract function getTimeout();

    /**
     * @param array $options
     * @return string
     */
    protected abstract function getUrl(array $options = []);
}
