<?php


namespace App\Http\Api;


use App\Book;
use App\Http\Api\Providers\Books\BookProvider;
use App\Http\Api\Providers\Covers\CoverProvider;
use App\Library;

class Search
{
    /**
     * @param Library $library
     * @param string $search
     * @return array
     */
    public static function books(Library $library, string $search)
    {
        $providers = config('api.books.providers');

        $result = collect();

        foreach ($providers as $provider) {
            $provider = new $provider;

            $result->put(class_basename($provider), $provider->books([
                'search' => $search,
            ]));
        }

        $parser = new BookResultsParser($library);
        return $parser->parse($result);
    }

    /**
     * @param Book $book
     * @return string|bool
     */
    public static function cover(Book $book)
    {
        $providers = config('api.covers.providers');

        foreach ($providers as $provider) {
            $provider = new $provider;

            if ($provider instanceof CoverProvider) {
                if ($cover = $provider->cover($book)) {
                    return $cover;
                }
            }
        }

        return "https://via.placeholder.com/323x500.jpg?text=" . (is_null($book->title) ? '?' : urlencode($book->title));
    }
}
