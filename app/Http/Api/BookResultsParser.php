<?php


namespace App\Http\Api;


use App\Book;
use App\Library;
use Illuminate\Support\Collection;

class BookResultsParser
{
    protected $similarityThreshold = 0.7;

    protected $library;

    protected $compared = [
        'year|title|authors|publisher',
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
    public function parseSearchResults(Collection $result)
    {
        $books = collect();
        $result = $this->spreadResults($result);

        while ($result->count() > 0) {
            $book = $result->shift();

            $existingIndex = $this->exists($books, $book);

            $book->existingIndex = $existingIndex;

            if ($existingIndex !== false) {
              $this->mergeRecords($books->get($existingIndex), $book);
              continue;
            }

           $books->push($book);
        }

        return array_values($books->take(config('api.books.limit'))->toArray());
    }

    /**
     * @param Collection $result
     * @return array
     */
    public function parseDetailResults(Collection $result)
    {
        $book = new Book();
        $result = $this->spreadResults($result);

        while ($result->isNotEmpty()) {
            $otherBook = $result->shift();

            $book = $this->mergeRecords($book, $otherBook);
        }

        return $book->toArray();
    }

    /**
     * @param Collection $results
     * @return Collection
     */
    private function spreadResults(Collection $results)
    {
        $books = collect();

        while ($results->isNotEmpty()) {
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
        if (empty($book1->getAttributes())) {
            return $book2;
        }

        if (empty($book2->getAttributes())) {
            return $book1;
        }

        $attributes = array_merge($book1->getAttributes(), $book2->getAttributes());

        foreach (array_keys($attributes) as $attribute) {
            $book1->setAttribute($attribute, $this->getCanonicalValue(
                $book1->getAttribute($attribute),
                $book2->getAttribute($attribute),
                $attribute
            ));
        }

        return $book1;
    }

    /**
     * @param $v1
     * @param $v2
     * @param string $attribute
     * @return mixed
     */
    private function getCanonicalValue($v1, $v2, string $attribute)
    {
        if (empty($v1)) {
            return $v2;
        }

        if (empty($v2)) {
            return $v1;
        }

        if (is_array($v1) || is_array($v2)) {
            if ($attribute === 'providers' || $attribute == 'images') {
                return array_merge(array_values($v1), array_values($v2));
            }

            return $this->mergeArrays((array)$v1, (array)$v2);
        }

        if (is_numeric($v1) || is_numeric($v2)) {
            return intval($v1) < intval($v2) ? intval($v2) : intval($v1);
        }

        if (is_string($v1)) {
            if ($this->compare($v1, $v2) >= $this->similarityThreshold) {
                return strlen($v1) > strlen($v2) ? $v1 : $v2;
            }
        }

        return $v1 . '; ' . $v2;
    }

    /**
     * @param array $array1
     * @param array $array2
     * @return array
     */
    private function mergeArrays(array $array1, array $array2)
    {
        $merged = array_merge($array1, $array2);

        if (empty($merged[0])) return [];

        return array_values(array_unique($merged));
    }

    /**
     * @param $s1
     * @param $s2
     * @return float|int
     */
    private function compare($s1, $s2)
    {
        if (empty($s1) && !empty($s2) || empty($s2) && !empty($s1)) return 1;

        if (is_numeric($s1) || is_numeric($s2)) {
            return intval($s1) === intval($s2) ? 1 : 0;
        }

        if (is_array($s1) && is_array($s2)) {
            return count(array_intersect($s1, $s2)) > 0;
        }

        if (is_array($s1)) {
            $s1 = implode(';', $s1);
        }

        if (is_array($s2)) {
            $s2 = implode(';', $s2);
        }

        $s1 = strtolower($s1);
        $s2 = strtolower($s2);

        $length1 = strlen($s1);
        $length2 = strlen($s2);

        // Avoid running algorithmic comparisons on large strings.
        if ($length1 > 40 || $length2 > 40) {
            return $length1 / $length2;
        }

        if (\Str::startsWith($s1, $s2) || \Str::startsWith($s2, $s1)) {
            return 1;
        }

        $score = levenshtein($s1, $s2, 1, 2, 1);

        if ($score === 0) return 1;

        return 1 - ($score / strlen($s1));
    }
}
