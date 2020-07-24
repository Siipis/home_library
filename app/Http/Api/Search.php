<?php


namespace App\Http\Api;


use App\Http\Providers\Books\BookApiProvider;
use App\Library;
use Illuminate\Support\Collection;

class Search
{
    /**
     * @param Library $library
     * @param string $search
     * @return Collection
     */
    public static function make(Library $library, string $search)
    {
        $providers = config('api.books.providers');

        $result = collect();

        foreach ($providers as $provider) {
            $provider = new $provider;

            if ($provider instanceof BookApiProvider) {
                $result = $result->merge($provider->books([
                    'search' => $search
                ]));
            }
        }

        return $result->sortBy(function ($book) {
            return $book->title;
        });
    }
}
