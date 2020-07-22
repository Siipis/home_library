<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Library extends Model
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function members()
    {
        return $this->belongsToMany(User::class)->withPivot(['role']);
    }

    public function nonMembers()
    {
        return User::whereDoesntHave('libraries', function (Builder $query) {
            $query->where('library_id', '=', $this->id);
        })->get();
    }
}
