<?php


namespace App\Traits;


trait Paginated
{
    /**
     * @var int
     */
    public static $paginate = 3;

    /**
     * @return \Illuminate\Contracts\Pagination\Paginator
     */
    public function paginate()
    {
        return $this->books()->simplePaginate($this::$paginate);
    }
}
