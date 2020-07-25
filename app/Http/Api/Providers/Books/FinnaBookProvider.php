<?php


namespace App\Http\Api\Providers\Books;


class FinnaBookProvider extends BookProvider
{
    protected function getUrl(array $options = [])
    {
        return "https://api.finna.fi/api/v1/search?" . implode("&", [
                "limit=" . config('api.books.limit'),
                "sort=relevance",
                "lookfor=" . urlencode("\"$options[search]\""),
                "join=AND",
                "filter[]=format%3A%221%2FBook%2FBook%2F%22",
                "field[]=title",
                "field[]=series",
                "field[]=primaryAuthors",
                "field[]=genres",
                "field[]=subjects",
                "field[]=publishers",
                "field[]=year",
                "field[]=cleanIsbn",
                "field[]=languages",
                "field[]=summary",
            ]);
    }

    protected function parseResponse($response)
    {
        return isset($response['records']) ? $response['records'] : [];
    }

    public function getTitle($record)
    {
        return $record['title'] ?? null;
    }

    public function getSeries($record)
    {
        return collect()
            ->merge($record['series'])
            ->flatten()
            ->unique()
            ->join(' ');
    }

    public function getAuthors($record)
    {
        return collect()
            ->merge($record['primaryAuthors'])
            ->flatten()
            ->unique()
            ->toArray();
    }

    public function getKeywords($record)
    {
        return collect()
            ->merge($record['subjects'])
            ->merge($record['genres'])
            ->flatten()
            ->unique()
            ->values()
            ->toArray();
    }

    public function getPublisher($record)
    {
        return $record['publishers'][0] ?? null;
    }

    public function getYear($record)
    {
        return $record['year'] ?? null;
    }

    public function getIsbn($record)
    {
        return $record['cleanIsbn'] ?? null;
    }

    public function getDescription($record)
    {
        return $record['summary'] ?? null;
    }

    public function getLanguage($record)
    {
        return $record['languages'][0] ?? null;
    }
}
