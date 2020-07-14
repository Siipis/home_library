<?php

namespace App\Http\Controllers;

use GuzzleHttp\Psr7;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    /**
     * @param string $query
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @link http://docs.guzzlephp.org/en/stable/quickstart.html
     */
    public function books(Request $request)
    {
        $request->validate([
            'query' => 'string|required',
        ]);

        $query = $request->input('query');

        $sources = config('api.books.sources');
        $limit = config('api.books.limit');
        $timeout = config('api.books.timeout');

        $requests = [];
        $results = [];

        try {
            foreach ($sources as $source) {
                if (empty($source['url']) || empty($source['query'])) continue;

                $base_url = $source['url'];

                // Append the search query
                $variables = [
                    $source['query'] => $query
                ];

                // Append any optional parameters
                if (!empty($source['limit'])) {
                    $variables[$source['limit']] = $limit;
                }

                // Record the request URL
                $url = $base_url . (str_contains($base_url, '&') ? "&" : "?") . http_build_query($variables);
                array_push($requests, $url);

                // Make the API call
                $client = new Client([
                    'timeout' => $timeout
                ]);

                $response = $client->get($url);

                // Record the response
                $data = json_decode($response->getBody()->getContents());
                array_push($results, $data);
            }
        } catch (RequestException $e) {
            return [
                'error' => [
                    'requests' => $requests,
                    'response' => $e->hasResponse() ? Psr7\str($e->getResponse()) : 0,
                ]
            ];
        }

        return [
            'requests' => $requests,
            'results' => $results,
        ];
    }
}
