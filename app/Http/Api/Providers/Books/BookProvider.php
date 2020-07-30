<?php


namespace App\Http\Api\Providers\Books;


use App\Book;
use App\Http\Api\Providers\ApiProvider;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Collection;

abstract class BookProvider extends ApiProvider
{
    /**
     * @return int
     */
    protected function getTimeout()
    {
        return config('api.books.timeout');
    }

    /**
     * @return array
     */
    protected function getCacheConfig()
    {
        return config('api.books.cache');
    }

    /**
     * @param array $options
     * @return Collection
     */
    public function books(array $options = [])
    {
        $books = collect();

        if ($records = $this->fetch($options)) {
            foreach ($records as $record) {
                $book = new Book();

                if (is_null($this->getTitle($record))) {
                    continue;
                }

                $book->title = $this->getTitle($record);
                $book->series = $this->getSeries($record);
                $book->authors = $this->getAuthors($record);
                $book->publisher = $this->getPublisher($record);
                $book->description = $this->getDescription($record);
                $book->year = $this->getYear($record);
                $book->language = $this->getLanguage($record);
                $book->keywords = $this->getKeywords($record);
                $book->isbn = isset($options['isbn']) ? $options['isbn'] : $this->getIsbn($record);
                $book->other_isbn = array_filter(
                    array_merge(array($this->getIsbn($record)), $this->getOtherIsbn($record)),
                    'is_string');
                $book->images = $this->getImages($record);
                $book->providers = array([
                    'class' => class_basename($this),
                    'id' => $this->getProviderId($record),
                    'page' => $this->getProviderPage($record),
                ]);

                $books->add($book);
            }
        }

        return $books;
    }

    protected function handleException(RequestException $exception)
    {
        return [
            "error" => [
                "message" => "Request returned with response code " . $exception->getResponse()->getStatusCode(),
                "errors" => $exception->getMessage(),
            ]
        ];
    }

    /**
     * @param $record
     * @return mixed
     */
    public abstract function getProviderId($record);

    /**
     * @param $record
     * @return string
     */
    public abstract function getTitle($record);

    /**
     * @param $record
     * @return string|null
     */
    public abstract function getSeries($record);

    /**
     * @param $record
     * @return array
     */
    public abstract function getAuthors($record);

    /**
     * @param $record
     * @return array
     */
    public abstract function getKeywords($record);

    /**
     * @param $record
     * @return string|null
     */
    public abstract function getPublisher($record);

    /**
     * @param $record
     * @return int|null
     */
    public abstract function getYear($record);

    /**
     * @param $record
     * @return string|null
     */
    public abstract function getIsbn($record);

    /**
     * @param $record
     * @return array
     */
    public abstract function getOtherIsbn($record);

    /**
     * @param $record
     * @return string|null
     */
    public abstract function getDescription($record);

    /**
     * @param $record
     * @return string|null
     */
    public abstract function getLanguage($record);

    /**
     * @param $record
     * @return string|null
     */
    public abstract function getImages($record);

    /**
     * @param $record
     * @return string|null
     */
    public abstract function getProviderPage($record);
}
