<?php


namespace App\Http\Api\Providers\Covers;


class OpenLibraryCoverProvider extends CoverProvider
{
    protected function getIsbnUrl($isbn)
    {
        return "http://covers.openlibrary.org/b/isbn/" . $isbn . "-L.jpg";
    }
}
