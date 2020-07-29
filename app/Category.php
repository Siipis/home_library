<?php

namespace App;

use App\Traits\Paginated;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Category extends Model
{
    use Paginated;

    /**
     * @return BelongsToMany
     */
    public function books()
    {
        return $this->belongsToMany(Book::class);
    }

    /**
     * @return BelongsTo
     */
    public function library()
    {
        return $this->belongsTo(Library::class);
    }
}
