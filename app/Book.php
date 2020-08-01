<?php

namespace App;

use App\Http\Api\Providers\Covers\ImageCast;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Book extends Model
{
    protected $visible = [
        'id', 'local_id', 'link', 'title', 'series',
        'authors', 'publisher', 'year', 'language',
        'isbn', 'other_isbn', 'cover', 'images',
        'providers',
    ];

    protected $appends = [
        'link',
    ];

    public static $searchable = [
        'title', 'series', 'authors', 'publisher',
        'description', 'keywords', 'isbn', 'local_id',
    ];

    // @link https://laravel.com/docs/7.x/eloquent#events
    protected static function booted()
    {
        static::saving(function (Book $book) {
            unset($book->category_choices);
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
        if ($this->cover === route('books.no_cover')) {
            \Storage::disk('covers')->delete("$this->id.png");
        } else {
            $provider = new ImageCast();

            $this->cover = $provider->cast($this, 'cover');
        }

        \Cache::forget($this->getCacheId());
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

    /**
     * @param $year
     * @return void
     */
    public function setYearAttribute($year)
    {
        if (is_numeric($year)) {
            $year = intval($year);
        }

        $this->attributes['year'] = $year;
    }

    /**
     * @param $description
     */
    public function setDescriptionAttribute($description)
    {
        $this->attributes['description'] = preg_replace("/<br[ ]?\/>/i", "\n", $description);
    }

    /**
     * @param $isbn
     */
    public function setIsbnAttribute($isbn)
    {
        $this->attributes['isbn'] = $this->cleanIsbn($isbn);
    }

    /**
     * @param array $isbns
     */
    public function setOtherIsbnAttribute(array $isbns)
    {
        $this->attributes['other_isbn'] = array_filter(array_map(function ($isbn) {
            return $this->cleanIsbn($isbn);
        }, $isbns), 'is_string');
    }

    /**
     * @param $isbn
     * @return string|null
     */
    private function cleanIsbn($isbn)
    {
        if (empty($isbn)) return null;

        preg_match('/^([0-9-X]+)/', $isbn, $matches);

        if (empty($matches)) return null;

        return $matches[1];
    }

    /**
     * @return string
     */
    public function getCacheId()
    {
        return "cover" . $this->id;
    }
}
