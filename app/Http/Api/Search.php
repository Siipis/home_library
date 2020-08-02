<?php


namespace App\Http\Api;


use App\Book;
use App\Http\Api\Providers\Books\BookProvider;
use App\Http\Api\Providers\Covers\CoverProvider;
use App\Library;
use Exception;
use Illuminate\Support\Collection;

class Search
{
    /**
     * @param string $search
     * @return array
     * @throws Exception
     */
    public static function books(string $search)
    {
        $result = self::queryBooks(compact('search'));

        $parser = new BookResultsParser();
        return $parser->parseSearchResults($result);
    }

    /**
     * @param string $isbn
     * @return array
     * @throws Exception
     */
    public static function details(string $isbn)
    {
        $result = self::queryBooks(compact('isbn'));

        $parser = new BookResultsParser();
        return $parser->parseDetailResults($result);
    }

    /**
     * @param Library $library
     * @param array $options
     * @return Collection
     * @throws Exception
     */
    private static function queryBooks(array $options)
    {
        $providers = config('api.books.providers');

        $result = collect();

        foreach ($providers as $provider) {
            $provider = new $provider;

            if (!$provider instanceof BookProvider) {
                throw new Exception("Book provider must be an instance of " . BookProvider::class);
            }

            $result->put(class_basename($provider), $provider->books($options));
        }

        return $result;
    }

    /**
     * @param Book $book
     * @return array|string
     * @throws Exception
     */
    public static function cover(Book $book)
    {
        $providers = config('api.covers.providers');

        foreach ($providers as $provider) {
            $provider = new $provider;

            if (!$provider instanceof CoverProvider) {
                throw new Exception("Cover provider be an instance of " . CoverProvider::class);
            }

            if ($cover = $provider->cover($book)) {
                return $cover;
            }
        }

        return route('books.no_cover');
    }
}
