<?php


namespace App\Http\Api\Providers\Books;


class OpenLibraryBookProvider extends BookProvider
{
    protected function getUrl(array $options = [])
    {
        if (isset($options['isbn'])) {
            return "https://openlibrary.org/api/books?bibkeys=ISBN:" . urlencode($options['isbn']) . "&format=json&jscmd=data";
        }

        if (isset($options['search'])) {
            return "http://openlibrary.org/search.json?" . implode("&", [
                    "q=" . urlencode($options['search']),
                    "type" => "work",
                    "limit=" . config('api.books.limit'),
                ]);
        }

        return false;
    }

    protected function parseResponse($response)
    {
        if (\Str::startsWith(array_key_first($response), 'ISBN')) {
            return $response;
        }

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
        if (isset($record['authors'])) {
            return array_map(function ($author) {
                return $author['name'];
            }, $record['authors']);
        }

        return $record['author_name'] ?? [];
    }

    public function getKeywords($record)
    {
        if (isset($record['subjects']) || isset($record['subject_places']) || isset($record['subject_people']) || isset($record['subject_times'])) {
            return collect()
                ->merge($record['subjects'] ?? [])
                ->merge($record['subject_places'] ?? [])
                ->merge($record['subject_people'] ?? [])
                ->merge($record['subject_times'] ?? [])
                ->pluck('name')
                ->unique()
                ->values()
                ->toArray();
        }

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
        if (isset($record['publishers'])) {
            return implode('; ', array_map(function ($publisher) {
                return $publisher['name'];
            }, $record['publishers']));
        }

        return null;
    }

    public function getYear($record)
    {
        if (isset($record['first_publish_year'])) {
            return intval($record['first_publish_year']);
        }

        if (isset($record['publish_date'])) {
            return $record['publish_date'];
        }

        return null;
    }

    public function getIsbn($record)
    {
        return $record['isbn'][0] ?? null;
    }

    public function getOtherIsbn($record)
    {
        return $record['isbn'] ?? [];
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
        if (isset($record['cover'])) {
            return array($record['cover']['large']);
        }

        if (!isset($response['cover_i'])) return [];

        return array("https://covers.openlibrary.org/b/id/" . $response['cover_i'] . "-L.jpg");
    }

    public function getProviderPage($record)
    {
        if (isset($record['url'])) {
            return $record['url'];
        }

        return "https://openlibrary.org" . $record['key'];
    }
}
