<?php


namespace App\Http\Api\Providers\Covers;


class FinnaCoverProvider extends CoverProvider
{
    protected function getIsbnUrl(string $isbn)
    {
        $response = $this->request($this->getDetailUrl($isbn));

        if ($url = $this->parseDetailResponse($response)) {
            return $url;
        }

        return false;
    }

    private function getDetailUrl(string $isbn)
    {
        return "https://api.finna.fi/api/v1/search?" . implode("&", [
                "limit=" . config('api.books.limit'),
                "sort=relevance",
                "lookfor=" . urlencode("\"$isbn\""),
                "type=isbn",
                "join=AND",
                "filter[]=format%3A%221%2FBook%2FBook%2F%22",
                "field[]=title",
                "field[]=cleanIsbn",
                "field[]=images",
            ]);
    }

    private function parseDetailResponse($response)
    {
        if (isset($response['records'])) {
            foreach ($response['records'] as $record) {
                if (!empty($record['images'])) {
                    return 'https://finna.fi' . $record['images'][0];
                }
            }
        }

        return false;
    }
}
