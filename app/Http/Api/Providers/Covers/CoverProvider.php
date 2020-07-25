<?php


namespace App\Http\Api\Providers\Covers;


use App\Book;
use App\Http\Api\Providers\ApiProvider;

abstract class CoverProvider extends ApiProvider
{
    protected $assoc = false;

    /**
     * @inheritDoc
     */
    protected function getTimeout()
    {
        return config('api.covers.timeout');
    }

    /**
     * @param Book $book
     * @return bool|mixed
     */
    public function cover(Book $book)
    {
        $result = $this->fetch([
            "title" => $book->title,
            "isbn" => $book->isbn,
            "original_data" => $book->original_data,
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
     * @param array $options
     * @return mixed
     */
    protected function fetch(array $options = [])
    {
        foreach (['isbn', 'original_data', 'title'] as $field) {
            if ($response = $this->attemptOption($field, $options)) {
                return $response;
            }
        }

        return false;
    }

    /**
     * @param string $option
     * @param array $options
     * @return bool
     */
    private function attemptOption(string $option, array $options)
    {
        if (is_null($options[$option])) return false;

        $url = $this->getUrl([
            $option => $options[$option]
        ]);

        if (!$url) return false;

        if ($this->isValidImage($this->request($url))) {
            return $url;
        }

        return false;
    }

    /**
     * @param string $response
     * @return bool
     */
    protected function isValidImage(string $response)
    {
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
            \Str::camel('get_' . array_key_first($options) . '_url')
        ], array_values($options)[0]);
    }

    /**
     * @param string $isbn
     * @return string|bool
     */
    protected abstract function getIsbnUrl(string $isbn);

    /**
     * @param array $response
     * @return string|bool
     */
    protected abstract function getOriginalDataUrl(array $response);

    /**
     * @param string $title
     * @return string|bool
     */
    protected abstract function getTitleUrl(string $title);
}
