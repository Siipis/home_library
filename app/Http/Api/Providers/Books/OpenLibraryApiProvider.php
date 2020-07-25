<?php


namespace App\Http\Api\Providers\Books;


class OpenLibraryApiProvider extends BookApiProvider
{
    /**
     * @param array $options
     * @return string
     */
    protected function getUrl(array $options = [])
    {
        return "http://openlibrary.org/search.json?" . implode("&", [
                "q=" . urlencode($options['search']),
                "type" => "work",
                "limit=" . config('api.books.limit'),
            ]);
    }

    protected function parseResponse($response)
    {
        return isset($response['docs']) ? $response['docs'] : [];
    }

    public function getTitle($record)
    {
        return $record['title'] ?? null;
    }

    public function getSeries($record)
    {
        return null;
    }

    public function getAuthors($record)
    {
        return $record['author_name'] ?? [];
    }

    public function getKeywords($record)
    {
        return collect()
            ->merge($record['subject'] ?? [])
            ->merge($record['place'] ?? [])
            ->merge($record['person'] ?? [])
            ->merge($record['time'] ?? [])
            ->unique()
            ->toArray();
    }

    public function getPublisher($record)
    {
        return $record['publisher'][0] ?? null;
    }

    public function getYear($record)
    {
        return $record['first_publish_year'] ?? null;
    }

    public function getIsbn($record)
    {
        return $record['isbn'][0] ?? null;
    }

    public function getDescription($record)
    {
        return null;
    }

    public function getLanguage($record)
    {
        return null;
    }
}
