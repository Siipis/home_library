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

        return $this->books()
            ->when($search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $this->querySearchables($query, $search);
                });
            })
            ->simplePaginate($this::$paginate);
    }

    /**
     * @param $query
     * @param $search
     */
    private function querySearchables($query, $search)
    {
        $booksTable = $this->books()->getRelated()->getTable();

        foreach (Book::$searchable as $index => $attribute) {
            $method = $index > 0 ? 'orWhere' : 'where';

            $query->$method("$booksTable.$attribute", "LIKE", "%$search%");
        }
    }
}
