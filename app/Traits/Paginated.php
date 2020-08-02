<?php


namespace App\Traits;


use App\Book;
use Request;

trait Paginated
{
    /**
     * @var int
     */
    public static $paginate = 12;

    /**
     * @return \Illuminate\Contracts\Pagination\Paginator
     */
    public function paginate()
    {
        Request::validate([
            'search' => 'string|nullable'
        ]);

        $search = Request::query('search');

        if (method_exists($this, 'books')) {
            $books = $this->books();
        } else {
            $books = Book::query();
        }

        return $books->when($search, function ($query, $search) {
            $query->search($search);
        })->simplePaginate($this::$paginate);
    }
}
