<?php

namespace App;

use App\Traits\Paginated;
use App\Facades\Cover;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Routing\Exceptions\UrlGenerationException;
use Illuminate\Support\Facades\Storage;

class Book extends Model
{
    use Paginated, HasFactory;

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
            return self::updateAttributes($book);
        });

        static::saving(function (Book $book) {
            return self::updateAttributes($book);
        });

        static::updating(function (Book $book) {
            return self::updateAttributes($book);
        });

        static::deleted(function (Book $book) {
            Storage::disk('covers')->delete(Cover::getFilename($book));
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
     * @param Book $book
     * @return Book
     */
    private static function updateAttributes(Book $book)
    {
        if (empty($book->hash)) {
            $book->setAttribute('hash', uniqid());
        }
        $book->setAttribute('cover', Cover::make($book));
        unset($book->category_choices);

        return $book;
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
        $this->attributes['description'] = is_null($description) ? null :
            preg_replace("/<br[ ]?\/>/i", "\n",
                $this->stripTags($description)
            );
    }

    /**
     * @param $description
     */
    private function stripTags($description)
    {
        $tags = ['br', 'i', 'em', 'b', 'strong'];

        if (version_compare(PHP_VERSION, '7.4') >= 0) {
            return strip_tags($description, $tags);
        }

        $stripped = $description;

        foreach ($tags as $tag) {
            $stripped = strip_tags($stripped, $tag);
        }

        return $stripped;
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
        $this->attributes['other_isbn'] = array_values(
            array_filter(array_map(function ($isbn) {
                return $this->cleanIsbn($isbn);
            }, $isbns), 'is_string')
        );
    }

    /**
     * @param $isbn
     * @return string|null
     */
    public static function cleanIsbn($isbn)
    {
        if (empty($isbn)) return null;

        preg_match('/^([0-9-X]+)/', $isbn, $matches);

        if (empty($matches)) return null;

        return str_replace('-', '', $matches[1]);
    }
}
