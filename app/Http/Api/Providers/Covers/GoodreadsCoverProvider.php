<?php


namespace App\Http\Api\Providers\Covers;


class GoodreadsCoverProvider extends CoverProvider
{
    protected function getIsbnUrl(string $isbn)
    {
        if ($response = $this->request("https://www.goodreads.com/book/isbn/" . $isbn . "?key=" . config('api.goodreads.key'))) {
            return $response['book']['image_url'];
        }

        return false;
    }
}
