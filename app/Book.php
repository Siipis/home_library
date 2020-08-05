<?php

namespace App;

use App\Http\Api\Providers\Covers\DownloadProvider;
use App\Traits\Paginated;
use Cover;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Routing\Exceptions\UrlGenerationException;

class Book extends Model
{
    use Paginated;

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

    /**
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'hash';
    }

    // @link https://laravel.com/docs/7.x/eloquent#events
    protected static function booted()
    {
        static::creating(function (Book $book) {
            $book->hash = uniqid();
        });

        static::saving(function (Book $book) {
            $book->cover = Cover::make($book);
            unset($book->category_choices);
        });

        static::updating(function (Book $book) {
            $book->cover = Cover::make($book);
        });

        static::retrieved(function (Book $book) {
            try {
                $book->cover = route('library.books.cover', [$book->library, $book]);
            } catch (UrlGenerationException $e) {
                // Do nothing
            }
        });
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
     * @param $query
     * @param string $search
     */
    public function scopeSearch($query, string $search)
    {
        $query->where(function ($query) use ($search) {
            $table = $this->getTable();

            foreach ($this::$searchable as $index => $attribute) {
                $method = $index > 0 ? 'orWhere' : 'where';

                $query->$method("$table.$attribute", "LIKE", "%$search%");
            }
        });
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
}
