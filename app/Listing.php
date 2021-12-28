<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Listing extends Model
{
    public const TBR = 'tbr';
    public const WISHLIST = 'wishlist';
    public const FAVORITE = 'favorite';

    /**
     * @param User $user
     * @param Book $book
     * @param string|null $type
     */
    public static function list(User $user, Book $book, string $type = null)
    {
        $listing = new self;
        $listing->add($user, $book, $type);
        $listing->save();
    }

    /**
     * @param User $user
     * @param Book $book
     * @param string $type
     */
    public static function unlist(User $user, Book $book, string $type = self::TBR)
    {
        self::where('user_id', $user->id)
            ->where('book_id', $book->id)
            ->where('type', $type)
            ->delete();
    }

    /**
     * @param User $user
     * @param Book $book
     * @param string $type
     * @return bool
     */
    public static function exists(User $user, Book $book, string $type = self::TBR)
    {
        return self::where('user_id', $user->id)
            ->where('book_id', $book->id)
            ->where('type', $type)
            ->count() > 0;
    }

    /**
     * @param User $user
     * @param Book $book
     * @param string $type
     * @return bool
     */
    public static function missing(User $user, Book $book, string $type = self::TBR)
    {
        return !self::exists($user, $book, $type);
    }

    /**
     * @param User $user
     * @param Book $book
     * @param string|null $type
     */
    public function add(User $user, Book $book, string $type = null)
    {
        if (!is_null($type)) {
            $this->type = $type;
        }
        $this->user()->associate($user);
        $this->book()->associate($book);
    }

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo
     */
    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
