<?php

namespace App;

use App\Http\Api\Providers\Covers\ImageCast;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Book extends Model
{
    protected $hidden = [
        'providers', 'library_id',
    ];

    protected $appends = [
        'link',
    ];

    public static $coverPath = 'images/books/cover';

    // @link https://laravel.com/docs/7.x/eloquent#events
    protected static function booted()
    {
        static::saving(function (Book $book) {
            unset($book->category_id);
        });

        static::saved(function (Book $book) {
            $book->storeImage();
        });

        static::updating(function (Book $book) {
            $book->storeImage();
        });

        static::retrieved(function (Book $book) {
            $book->cover = route('library.books.cover', [$book->library, $book]);
        });
    }

    private function storeImage()
    {
        $provider = new ImageCast();

        $this->cover = $provider->cast($this, 'cover');
    }

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

    /**
     * @return string
     */
    public function getLinkAttribute()
    {
        if (empty($this->id)) return null;

        return route('library.books.show', [$this->library, $this]);
    }
}
