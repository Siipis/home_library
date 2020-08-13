<?php


namespace App\Http\Api\Providers\Books;


class FinnaBookProvider extends BookProvider
{
    protected function getUrl(array $options = [])
    {
        $lookFor = [];

        if (isset($options['isbn'])) {
            $lookFor = [
                "lookfor=" . urlencode("\"$options[isbn]\""),
                "type=isbn",
                "join=AND",
            ];
        } else if (isset($options['search'])) {
            $lookFor = [
                "lookfor=" . urlencode("\"$options[search]\""),
                "join=AND",
            ];
        }

        return "https://api.finna.fi/api/v1/search?" . implode("&", array_merge([
                "limit=" . config('api.books.limit'),
                "sort=relevance",
            ], $lookFor, [
                "filter[]=format%3A%221%2FBook%2FBook%2F%22",
                "field[]=id",
                "field[]=title",
                "field[]=series",
                "field[]=authors",
                "field[]=primaryAuthors",
                "field[]=genres",
                "field[]=subjects",
                "field[]=publishers",
                "field[]=year",
                "field[]=cleanIsbn",
                "field[]=isbns",
                "field[]=languages",
                "field[]=summary",
                "field[]=images",
                "field[]=recordPage",
                "field[]=urls",
            ]));
    }

    protected function parseResponse($response)
    {
        return isset($response['records']) ? $response['records'] : [];
    }

    public function getProviderId($record)
    {
        return $record['id'] ?? null;
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
            ->map(function ($author) {
                $author = preg_replace('/[, ]+(kirjoittaja|kääntäjä)/i', '', $author);
                $author = preg_replace('/^([\w. ]+),[ ]?([\w. ]+).*$/i', '$2 $1', $author);
                $author = preg_replace('/[ ]{2,}/', ' ', $author);

                return $author;
            })
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
        if (!isset($record['year'])) return null;

        return $record['year'];
    }

    public function getIsbn($record)
    {
        return $record['cleanIsbn'] ?? null;
    }

    public function getOtherIsbn($record)
    {
        return $record['isbns'] ?? [];
    }

    public function getDescription($record)
    {
        if (!empty($record['summary'])) {
            $record['summary'];
        }

        return null;
    }

    public function getLanguage($record)
    {
        return $record['languages'][0] ?? null;
    }

    public function getImages($record)
    {
        return array_map(function ($image) {
            return 'https://finna.fi' . $image;
        }, $record['images']);
    }

    public function getProviderPage($record)
    {
        return 'https://finna.fi' . $record['recordPage'];
    }
}
