<?php


namespace App\Http\Images;


use App\Http\Api\Providers\ApiProvider;
use App\Http\Images\Templates\LargeFilter;
use Cover;
use GuzzleHttp\Exception\RequestException;

class DownloadProvider extends ApiProvider
{
    public function download(string $url)
    {
        return $this->request($url);
    }

    protected function parseResponse($response)
    {
        return \Image::make($response)
            ->filter(new LargeFilter())
            ->encode(Cover::getExtension(), 100);
    }

    protected function handleException(RequestException $exception)
    {
        throw $exception;
    }

    protected function getTimeout()
    {
        return 60;
    }

    protected function getCacheConfig()
    {
        return false;
    }

    protected function getUrl(array $options = [])
    {
        return false;
    }
}
