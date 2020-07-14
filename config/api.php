<?php
return [
    'books' => [
        'timeout' => 60, // seconds
        'limit' => 1,

        'sources' => [
            'finna' => [
                'url' => 'https://api.finna.fi/api/v1/search?type=AllFields&sort=relevance%2Cid%20asc&page=1&prettyPrint=false&lng=fi',
                'query' => 'lookfor',
                'limit' => 'limit',
                'fields' => [],
            ],

            'open_library' => [
                'url' => 'http://openlibrary.org/search.json',
                'query' => 'q',
                'limit' => 'limit',
                'fields' => [],
            ],
        ]
    ]
];
