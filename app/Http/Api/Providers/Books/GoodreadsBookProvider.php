<?php


namespace App\Http\Api\Providers\Books;


class GoodreadsBookProvider extends BookProvider
{
    protected function parseResponse($response)
    {
        if (isset($response['book'])) {
            return array($response['book']);
        }

        if (isset($response['search']['results'])) {
            $results = $response['search']['results'];

            if (!isset($results) || empty($results)) {
                return [];
            }

            if (array_key_first($results) == 0) {
                return array_values($results['work']);
            }

            return $results;
        }

        return "Unknown response format.";
    }

    protected function getUrl(array $options = [])
    {
        if (isset($options['isbn'])) {
            return "https://www.goodreads.com/book/isbn/" . urlencode($options['isbn']) . "?" . implode("&", [
                    "key=" . config('api.goodreads.key'),
                ]);
        }

        return "https://www.goodreads.com/search/index.xml?" . implode("&", [
                "key=" . config('api.goodreads.key'),
                "q=" . urlencode($options['search']),
            ]);
    }

    public function getTitle($record)
    {
        if (isset($record['title'])) {
            return $record['title'];
        }

        if (isset($record['best_book'])) {
            return $record['best_book']['title'] ?? null;
        }

        return null;
    }

    public function getSeries($record)
    {
        return null;
    }

    public function getAuthors($record)
    {
        if (isset($record['authors'])) {
            $authors = [];

            foreach ($record['authors'] as $author) {
                if (isset($author['name'])) {
                    array_push($authors, $author['name']);
                    continue;
                }

                if (is_array($author)) {
                    foreach ($author as $coauthor) {
                        if (isset($coauthor['name'])) {
                            array_push($authors, $coauthor['name']);
                        }
                    }
                }
            }

            return array_values(array_unique($authors));
        }

        if (isset($record['best_book']['author']['name'])) {
            return array($record['best_book']['author']['name']);
        }

        return [];
    }

    public function getKeywords($record)
    {
        return [];
    }

    public function getPublisher($record)
    {
        if (isset($record['publisher'])) {
            return $record['publisher'];
        }

        return null;
    }

    public function getYear($record)
    {
        if (isset($record['publication_year'])) {
            return $record['publication_year'];
        }

        if (isset($record['original_publication_year'])) {
            return $record['original_publication_year'];
        }

        return null;
    }

    public function getIsbn($record)
    {
        if (isset($record['isbn13'])) {
            return $record['isbn13'];
        }

        if (isset($record['isbn'])) {
            return $record['isbn'];
        }

        return null;
    }

    public function getOtherIsbn($record)
    {
        $otherIsbn = [];

        if (isset($record['isbn13'])) {
            array_push($otherIsbn, $record['isbn13']);
        }

        if (isset($record['isbn'])) {
            array_push($otherIsbn, $record['isbn']);
        }

        return $otherIsbn;
    }

    public function getDescription($record)
    {
        if (isset($record['description'])) {
            return $record['description'];
        }

        return null;
    }

    public function getLanguage($record)
    {
        return null;
    }

    public function getProviderId($record)
    {
        if (!is_array($record['id'])) {
            return $record['id'];
        }

        return $record['id'][0];
    }

    public function getImages($record)
    {
        if (isset($record['image_url'])) {
            return array($record['image_url']);
        }

        if (isset($record['best_book']['image_url'])) {
            return array($record['best_book']['image_url']);
        }

        return [];
    }

    public function getProviderPage($record)
    {
        if (isset($record['url'])) {
            return $record['url'];
        }

        if (isset($record['link'])) {
            return $record['link'];
        }

        return null;
    }
}
