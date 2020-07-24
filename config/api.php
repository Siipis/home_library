<?php
return [
    'books' => [
        'timeout' => 60, // seconds
        'limit' => 10,

        'providers' => [
            \App\Http\Providers\Books\FinnaApiProvider::class,
            \App\Http\Providers\Books\OpenLibraryApiProvider::class,
        ],
    ]
];
