<?php
return [
    'books' => [
        'timeout' => 60, // seconds
        'limit' => 10,

        'providers' => [
            \App\Http\Api\Providers\Books\OpenLibraryBookProvider::class,
            \App\Http\Api\Providers\Books\FinnaBookProvider::class,
            \App\Http\Api\Providers\Books\GoodreadsBookProvider::class,
        ],
    ],

    'covers' => [
        'timeout' => 60, // seconds

        'minimum' => [
            'width' => 10,
            'height' => 10,
        ],

        'providers' => [
            \App\Http\Api\Providers\Covers\OpenLibraryCoverProvider::class,
            \App\Http\Api\Providers\Covers\GoodreadsCoverProvider::class,
            \App\Http\Api\Providers\Covers\FinnaCoverProvider::class,
        ],
    ],

    'goodreads' => [
        'key' => env('GOODREADS_KEY'),
        'secret' => env('GOODREADS_SECRET'),
    ]
];
