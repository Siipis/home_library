<?php

namespace Tests\Unit;

use App\Book;
use App\Listing;
use Tests\TestCase;

class ListingTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testSaveFromListing()
    {
        $book = factory(Book::class)->create();
        $listing = new Listing();
        $listing->add($this->USER, $book);
        $listing->save();

        $this->assertDatabaseHas('listings', [
            'user_id' => $this->USER->id,
            'book_id' => $book->id,
        ]);
    }

    public function testSaveTypedFromListing()
    {
        $book = factory(Book::class)->create();
        $listing = new Listing();
        $listing->add($this->USER, $book, Listing::WISHLIST);
        $listing->save();

        $this->assertDatabaseHas('listings', [
            'user_id' => $this->USER->id,
            'book_id' => $book->id,
            'type' => Listing::WISHLIST,
        ]);
    }

    public function testAbstractList()
    {
        $book = factory(Book::class)->create();

        Listing::list($this->USER, $book, Listing::WISHLIST);

        $this->assertDatabaseHas('listings', [
            'user_id' => $this->USER->id,
            'book_id' => $book->id,
            'type' => Listing::WISHLIST,
        ]);
    }

    public function testAbstractUnlist()
    {
        $book = factory(Book::class)->create();

        Listing::unlist($this->USER, $book, Listing::WISHLIST);

        $this->assertDatabaseMissing('listings', [
            'user_id' => $this->USER->id,
            'book_id' => $book->id,
            'type' => Listing::WISHLIST,
        ]);
    }

    /**
     * @depends testAbstractList
     */
    public function testAbstractExists()
    {
        $book = factory(Book::class)->create();

        Listing::list($this->USER, $book, Listing::WISHLIST);

        $this->assertTrue(Listing::exists($this->USER, $book, Listing::WISHLIST));
    }

    /**
     * @depends testAbstractList
     */
    public function testAbstractMissing()
    {
        $book = factory(Book::class)->create();

        Listing::list($this->USER, $book, Listing::WISHLIST);

        $this->assertTrue(Listing::missing($this->USER, $book, Listing::TBR));
    }

    /**
     * @depends testAbstractCreate
     */
    public function testUnlistDoesNotBleed()
    {
        $book = factory(Book::class)->create();
        Listing::unlist($this->USER, $book, Listing::WISHLIST);

        Listing::list($this->USER, factory(Book::class)->create());
        Listing::list($this->USER, factory(Book::class)->create(), Listing::WISHLIST);
        Listing::list($this->USER, factory(Book::class)->create(), Listing::FAVORITE);

        Listing::unlist($this->USER, $book, Listing::WISHLIST);

        $this->assertDatabaseMissing('listings', [
            'user_id' => $this->USER->id,
            'book_id' => $book->id,
            'type' => Listing::WISHLIST,
        ]);

        $this->assertDatabaseCount('listings', 3);
    }

    /**
     * @depends testSaveFromListing
     */
    public function testUserListedRelation()
    {
        $book = factory(Book::class)->create();
        $listing = new Listing();
        $listing->add($this->USER, $book);
        $listing->save();

        $this->assertTrue($this->USER->listed->contains($book));
    }

    /**
     * @depends testUserListedRelation
     */
    public function testUserTBRRelation()
    {
        $book = factory(Book::class)->create();
        $listing = new Listing();
        $listing->add($this->USER, $book, Listing::TBR);
        $listing->save();

        $this->assertTrue($this->USER->to_be_read->contains($book));
    }

    /**
     * @depends testUserListedRelation
     */
    public function testUserWishlistRelation()
    {
        $book = factory(Book::class)->create();
        $listing = new Listing();
        $listing->add($this->USER, $book, Listing::WISHLIST);
        $listing->save();

        $this->assertTrue($this->USER->wishlist->contains($book));
    }

    /**
     * @depends testUserListedRelation
     */
    public function testUserFavoritesRelation()
    {
        $book = factory(Book::class)->create();
        $listing = new Listing();
        $listing->add($this->USER, $book, Listing::FAVORITE);
        $listing->save();

        $this->assertTrue($this->USER->favorites->contains($book));
    }

    /**
     * @depends testUserListedRelation
     */
    public function testNoCrossListing()
    {
        $book = factory(Book::class)->create();
        $listing = new Listing();
        $listing->add($this->USER, $book, Listing::FAVORITE);
        $listing->save();

        $this->assertFalse($this->USER->wishlist->contains($book));
    }

    public function testSaveToTBR()
    {
        $book = factory(Book::class)->create();
        $this->USER->to_be_read()->save($book);

        $this->assertDatabaseHas('listings', [
            'user_id' => $this->USER->id,
            'book_id' => $book->id,
            'type' => Listing::TBR,
        ]);
    }

    public function testSaveToWishlist()
    {
        $book = factory(Book::class)->create();
        $this->USER->wishlist()->save($book);

        $this->assertDatabaseHas('listings', [
            'user_id' => $this->USER->id,
            'book_id' => $book->id,
            'type' => Listing::WISHLIST,
        ]);
    }

    public function testSaveToFavorites()
    {
        $book = factory(Book::class)->create();
        $this->USER->favorites()->save($book);

        $this->assertDatabaseHas('listings', [
            'user_id' => $this->USER->id,
            'book_id' => $book->id,
            'type' => Listing::FAVORITE,
        ]);
    }
}
