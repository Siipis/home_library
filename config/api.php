<?php
return [
    'books' => [
        'timeout' => 60, // seconds
        'limit' => 10,

        'providers' => [
            \App\Http\Api\Providers\Books\FinnaBookProvider::class,
            \App\Http\Api\Providers\Books\OpenLibraryBookProvider::class,
            \App\Http\Api\Providers\Books\GoodreadsBookProvider::class,
        ],

        /**
         * Determine how long the API response is being cached.
         * Supported keys are "days", "hours", "minutes" and "seconds".
         */
        'cache' => [
            'hours' => 24,
        ],
    ],

    'covers' => [
        'timeout' => 60, // seconds

        'providers' => [
            \App\Http\Api\Providers\Covers\OpenLibraryCoverProvider::class,
            \App\Http\Api\Providers\Covers\GoodreadsCoverProvider::class,
            \App\Http\Api\Providers\Covers\FinnaCoverProvider::class,
        ],

        /**
         * Determine how long the API response is being cached.
         * Supported keys are "days", "hours", "minutes" and "seconds".
         */
        'cache' => [
            'hours' => 24,
        ],

        /**
         * Set the minimum accepted image size in pixels.
         */
        'minimum' => [
            'width' => 10,
            'height' => 10,
        ],
    ],

    'goodreads' => [
        'key' => env('GOODREADS_KEY'),
        'secret' => env('GOODREADS_SECRET'),
    ],
];
