<?php
return [
    'books' => [
        'timeout' => 60, // seconds
        'limit' => 10,

        'providers' => [
            \App\Http\Api\Providers\Books\OpenLibraryBookProvider::class,
            \App\Http\Api\Providers\Books\FinnaBookProvider::class,
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
            \App\Http\Api\Providers\Covers\FinnaCoverProvider::class,
        ],
    ],
];
