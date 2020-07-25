<?php


namespace App\Http\Api\Providers\Covers;


class GoodreadsCoverProvider extends CoverProvider
{
    /**
     * @inheritDoc
     */
    protected function getIsbnUrl(string $isbn)
    {
        $response = $this->request("https://www.goodreads.com/book/isbn/" . $isbn . "?key=" . config('api.goodreads.key'));

        return $response['book']['image_url'];
    }

    /**
     * @inheritDoc
     */
    protected function getOriginalDataUrl(array $response)
    {
        return $response['response']['best_book']['image_url'] ?? false;
    }

    /**
     * @inheritDoc
     */
    protected function getTitleUrl(string $title)
    {
        return false;
    }
}
