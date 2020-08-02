<?php

namespace App;

use App\Traits\Paginated;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Library extends Model
{
    use Paginated;

    protected $hidden = [
        'id', 'created_at', 'updated_at',
    ];

    /**
     * @return BelongsToMany|User
     */
    public function members()
    {
        return $this->belongsToMany(User::class)->withPivot(['role']);
    }

    /**
     * @return mixed|User
     */
    public function nonMembers()
    {
        return User::whereDoesntHave('libraries', function (Builder $query) {
            $query->where('library_id', '=', $this->id);
        })->get();
    }

    /**
     * @return HasMany|Book
     */
    public function books()
    {
        return $this->hasMany(Book::class);
    }

    /**
     * @return HasMany|Category
     */
    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    /**
     * @param string $slug
     * @return Library|null
     */
    public static function findBySlug(string $slug)
    {
        return self::whereSlug($slug)->first();
    }

    /**
     * @param string $slug
     * @return Library
     */
    public static function findBySlugOrFail(string $slug)
    {
        return self::whereSlug($slug)->firstOrFail();
    }
}
