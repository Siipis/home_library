<?php
return [
    'books' => [
        'timeout' => 60, // seconds
        'limit' => 10,

        'providers' => [
            \App\Http\Api\Providers\Books\FinnaApiProvider::class,
            \App\Http\Api\Providers\Books\OpenLibraryApiProvider::class,
        ],
    ]
];
