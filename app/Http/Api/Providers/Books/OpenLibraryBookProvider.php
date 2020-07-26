<?php


namespace App\Http\Api\Providers\Books;


class OpenLibraryBookProvider extends BookProvider
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
            ->values()
            ->toArray();
    }

    public function getPublisher($record)
    {
        return $record['publisher'][0] ?? null;
    }

    public function getYear($record)
    {
        if (!isset($record['first_publish_year'])) return null;

        return intval($record['first_publish_year']);
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

    public function getProviderId($record)
    {
        return $record['key'];
    }

    public function getImages($record)
    {
        if (!isset($response['cover_i'])) return [];

        return [
            "https://covers.openlibrary.org/b/id/" . $response['cover_i'] . "-L.jpg"
        ];
    }

    public function getProviderPage($record)
    {
        return "https://openlibrary.org" . $record['key'];
    }
}
