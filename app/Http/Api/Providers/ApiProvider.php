<?php


namespace App\Http\Api\Providers;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

abstract class ApiProvider
{
    protected $client;

    protected $assoc = true;

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

        if (!$this->assoc) {
            return $this->parseResponse($response->getBody()->getContents());
        }

        return $this->parseResponse(json_decode($response->getBody()->getContents(), true));
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
