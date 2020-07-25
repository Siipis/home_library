<?php


namespace App\Http\Api\Providers;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

abstract class ApiProvider
{
    private $client;

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
        $response = $this->client->get($this->getUrl($options));

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
