<?php


namespace App\Http\Api;


use App\Book;
use App\Library;
use Illuminate\Support\Collection;

class BookResultsParser
{
    protected $similarityThreshold = 0.8;

    protected $library;

    protected $compared = [
        'isbn', 'year|authors|publisher',
    ];

    /**
     * BookResultsParser constructor.
     * @param Library $library
     */
    public function __construct(Library $library)
    {
        $this->library = $library;
    }

    /**
     * @param Collection $result
     * @return array
     */
    public function parse(Collection $result)
    {
        $books = collect();
        $result = $this->spreadResults($result);

        while ($result->count() > 0) {
            $book = $result->shift();

            $existingIndex = $this->exists($books, $book);

            if ($existingIndex !== false) {
                $this->mergeRecords($books->get($existingIndex), $book);
                continue;
            }

            $books->push($book);
        }

        return array_values($books->take(config('api.books.limit'))->toArray());
    }

    /**
     * @param Collection $results
     * @return Collection
     */
    private function spreadResults(Collection $results)
    {
        $books = collect();

        while ($results->count() > 0) {
            foreach ($results as $provider => $result) {
                if ($result->isEmpty()) {
                    $results->forget($provider);
                    continue;
                }

                $books->push($result->shift());
            }
        }

        return $books;
    }

    /**
     * @param Collection $books
     * @param Book $book
     * @return bool
     */
    private function exists(Collection $books, Book $book)
    {
        foreach ($books as $index => $otherBook) {

            foreach ($this->compared as $compare) {
                foreach (explode('|', $compare) as $key) {
                    $similarity = $this->compare($otherBook->getAttribute($key), $book->getAttribute($key));

                    if ($similarity < $this->similarityThreshold) {
                        return false;
                    }
                }
            }

            return $index;
        }

        return false;
    }

    /**
     * @param Book $book1
     * @param Book $book2
     * @return Book
     */
    private function mergeRecords(Book $book1, Book $book2)
    {
        foreach ($book1->getAttributes() as $attribute => $value) {
            $newValue = $value;
            $otherValue = $book2->getAttribute($attribute);

            if (empty($value)) {
                $newValue = $otherValue;
            } else if (is_array($value)) {
                $newValue = $this->mergeArrays($value, $otherValue);
            } else if (is_string($value)) {
                if ($this->compare($value, $newValue) >= $this->similarityThreshold) {
                    $newValue = strlen($value) > strlen($otherValue) ? $value : $otherValue;
                } else {
                    $newValue = $value . '; ' . $otherValue;
                }
            }

            $book1->setAttribute($attribute, $newValue);
        }

        return $book1;
    }

    /**
     * @param array $array1
     * @param array $array2
     * @return array
     */
    private function mergeArrays(array $array1, array $array2)
    {
        return array_values(array_unique(array_merge($array1, $array2)));
    }

    /**
     * @param $s1
     * @param $s2
     * @return float|int
     */
    private function compare($s1, $s2)
    {
        if (empty($s1) && !empty($s2) || empty($s2) && !empty($s1)) return 0;

        if (is_numeric($s1) && is_numeric($s2)) {
            return $s1 == $s2 ? 1 : 0;
        }

        if (is_array($s1)) {
            $s1 = implode(';', $s1);
        }

        if (is_array($s2)) {
            $s2 = implode(';', $s2);
        }

        $score = levenshtein($s1, $s2, 1, 2, 1);

        if ($score === 0) return 1;

        return 1 - ($score / strlen($s1));
    }
}
