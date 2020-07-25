<?php


namespace App\Http\Api\Providers\Books;


use App\Book;
use App\Http\Api\Providers\ApiProvider;
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
     * @param array $options
     * @return Collection
     */
    public function books(array $options = [])
    {
        $records = $this->fetch($options);

        $books = collect();

        foreach ($records as $record) {
            $book = new Book();

            $book->title = $this->getTitle($record);
            $book->series = $this->getSeries($record);
            $book->authors = $this->getAuthors($record);
            $book->publisher = $this->getPublisher($record);
            $book->description = $this->getDescription($record);
            $book->year = $this->getYear($record);
            $book->isbn = $this->getIsbn($record);
            $book->language = $this->getLanguage($record);
            $book->keywords = $this->getKeywords($record);
            $book->original_data = $this->getOriginalData($record);

            $books->add($book);
        }

        return $books;
    }

    /**
     * @param $record
     * @return array
     */
    protected function getOriginalData($record)
    {
        return [
            'provider' => class_basename($this),
            'response' => (array) $record,
        ];
    }

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
     * @return string|null
     */
    public abstract function getDescription($record);

    /**
     * @param $record
     * @return string|null
     */
    public abstract function getLanguage($record);
}
