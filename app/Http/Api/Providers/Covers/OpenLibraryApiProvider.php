<?php


namespace App\Http\Api\Providers\Covers;


class OpenLibraryApiProvider extends CoverApiProvider
{
    protected function getIsbnUrl($isbn)
    {
        return "http://covers.openlibrary.org/b/isbn/" . $isbn . "-L.jpg";
    }

    protected function getOriginalDataUrl($response)
    {
        if (isset($response['cover_i'])) {
            return "http://covers.openlibrary.org/b/id/" . $response['cover_i'] . "-L.jpg";
        }

        return false;
    }

    protected function getTitleUrl(string $title)
    {
        return false;
    }
}
