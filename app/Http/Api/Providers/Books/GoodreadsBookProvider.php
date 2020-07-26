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

        if (array_key_first($results) == 0 && !empty($results)) {
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
        return [];
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
        if (!isset($record['original_publication_year'][0])) return null;

        return intval($record['original_publication_year'][0]);
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

    /**
     * @inheritDoc
     */
    public function getProviderId($record)
    {
        return $record['id'][0];
    }

    /**
     * @inheritDoc
     */
    public function getImages($record)
    {
        if (!isset($record['best_book']['image_url'])) return [];

        return [
            $record['best_book']['image_url']
        ];
    }

    /**
     * @inheritDoc
     */
    public function getProviderPage($record)
    {
        return null;
    }
}
