<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Book extends Model
{
    /**
     * @return BelongsTo
     */
    public function library()
    {
        return $this->belongsTo(Library::class);
    }

    /**
     * @return BelongsToMany
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    /**
     * @return BelongsTo
     */
    public function owner()
    {
        return $this->belongsTo(Library::class, 'user_id');
    }
}
