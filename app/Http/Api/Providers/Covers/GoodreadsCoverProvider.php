<?php


namespace App\Http\Api\Providers\Covers;


use Illuminate\Support\Str;

class GoodreadsCoverProvider extends CoverProvider
{
    protected function getIsbnUrl(string $isbn)
    {
        if ($response = $this->request("https://www.goodreads.com/book/isbn/" . $isbn . "?key=" . config('api.goodreads.key'))) {
            $imageUrl = $response['book']['image_url'];

            if (Str::contains($imageUrl, 'nophoto')) return false;

            return $imageUrl;
        }

        return false;
    }
}
