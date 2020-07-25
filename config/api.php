<?php
return [
    'books' => [
        'timeout' => 60, // seconds
        'limit' => 10,

        'providers' => [
            \App\Http\Api\Providers\Books\OpenLibraryApiProvider::class,
            \App\Http\Api\Providers\Books\FinnaApiProvider::class,
        ],
    ],

    'covers' => [
        'timeout' => 60, // seconds

        'minimum' => [
            'width' => 10,
            'height' => 10,
        ],

        'providers' => [
            \App\Http\Api\Providers\Covers\OpenLibraryApiProvider::class,
            \App\Http\Api\Providers\Covers\FinnaApiProvider::class,
        ],
    ],
];
