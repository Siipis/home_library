<?php


namespace App\Http\Api\Providers\Covers;


use App\Book;
use App\Http\Api\Providers\ApiProvider;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Str;

abstract class CoverProvider extends ApiProvider
{
    /**
     * @inheritDoc
     */
    protected function getTimeout()
    {
        return config('api.covers.timeout');
    }

    /**
     * @inheritDoc
     */
    protected function getCacheConfig()
    {
        return config('api.covers.cache');
    }

    /**
     * @param Book $book
     * @return bool|mixed
     */
    public function cover(Book $book)
    {
        $result = $this->fetch([
            "isbn" => $book->isbn,
        ]);

        return $result ?? false;
    }

    /**
     * @param $response
     * @return mixed
     */
    protected function parseResponse($response)
    {
        return $response;
    }

    /**
     * @param RequestException $exception
     * @return bool
     */
    protected function handleException(RequestException $exception)
    {
        return false;
    }

    /**
     * @param array $options
     * @return mixed
     */
    protected function fetch(array $options = [])
    {
        foreach ($options as $field => $value) {
            if (empty($value)) return false;

            if ($response = $this->attemptOption($field, $value)) {
                return $response;
            }
        }

        return false;
    }

    /**
     * @param string $option
     * @param string $value
     * @return bool
     */
    private function attemptOption(string $option, string $value)
    {
        if (is_null($value)) return false;

        $url = $this->getUrl([
            $option => $value
        ]);

        if (!$url) return false;

        if ($this->isValidImage($this->request($url))) {
            return $url;
        }

        return false;
    }

    /**
     * @param mixed $response
     * @return bool
     */
    protected function isValidImage($response)
    {
        if (empty($response)) return false;

        $info = getimagesizefromstring($response);

        if ($info[0] > config('api.covers.minimum.width') && $info[1] > config('api.covers.minimum.height')) {
            return true;
        }

        return false;
    }

    /**
     * @param array $options
     * @return mixed|string
     */
    protected function getUrl(array $options = [])
    {
        return call_user_func([
            $this,
            Str::camel('get_' . array_key_first($options) . '_url')
        ], array_values($options)[0]);
    }

    /**
     * @param string $isbn
     * @return string|bool
     */
    protected abstract function getIsbnUrl(string $isbn);
}
