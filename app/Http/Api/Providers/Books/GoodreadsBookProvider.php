<?php


namespace App\Http\Api\Providers\Books;


class GoodreadsBookProvider extends BookProvider
{
    /**
     * @inheritDoc
     */
    protected function parseResponse($response)
    {
        $results = $response['search']['results'];

        if (!isset($results)) {
            return [];
        }

        if (array_key_first($results) == 0) {
            return array_values($results['work']);
        }

        return $results;
    }

    /**
     * @inheritDoc
     */
    protected function getUrl(array $options = [])
    {
        return "https://www.goodreads.com/search/index.xml?" . implode("&", [
                "key=" . config('api.goodreads.key'),
                "q=" . urlencode($options['search']),
            ]);
    }

    /**
     * @inheritDoc
     */
    public function getTitle($record)
    {
        if (!isset($record['best_book'])) return null;

        return $record['best_book']['title'] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function getSeries($record)
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getAuthors($record)
    {
        if (!isset($record['best_book'])) return [];

        return [
            $record['best_book']['author']['name']
        ];
    }

    /**
     * @inheritDoc
     */
    public function getKeywords($record)
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getPublisher($record)
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getYear($record)
    {
        return $record['original_publication_year'][0] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function getIsbn($record)
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getDescription($record)
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getLanguage($record)
    {
        return null;
    }
}
