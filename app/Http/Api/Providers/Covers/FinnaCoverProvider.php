<?php


namespace App\Http\Api\Providers\Covers;


class FinnaCoverProvider extends CoverProvider
{
    protected function getIsbnUrl(string $isbn)
    {
        return "https://finna.fi/Cover/Show?isbn=" . $isbn;
    }

    protected function getOriginalDataUrl(array $response)
    {
        return false;
    }

    protected function getTitleUrl(string $title)
    {
        return false;
    }
}
