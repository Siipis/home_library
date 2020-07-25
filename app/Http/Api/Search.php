<?php


namespace App\Http\Api;


use App\Http\Api\Providers\Books\BookApiProvider;
use App\Library;

class Search
{
    /**
     * @param Library $library
     * @param string $search
     * @return array
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

        return $result->toArray();
    }
}
