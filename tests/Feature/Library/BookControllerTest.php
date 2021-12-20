<?php

namespace Tests\Feature\Library;

use App\Book;
use Tests\TestCase;

class BookControllerTest extends TestCase
{
    public function testCreateAsOwner()
    {
        $response = $this->actingAs($this->OWNER)
            ->get(route('library.books.create', [
                'library' => $this->library
            ]));

        $response->assertStatus(200);
    }

    public function testCreateAsMember()
    {
        $response = $this->actingAs($this->MEMBER)
            ->get(route('library.books.create', [
                'library' => $this->library
            ]));

        $response->assertStatus(403);
    }

    public function testStoreAsOwner()
    {
        $response = $this->actingAs($this->OWNER)
            ->post(route('library.books.store', [
                'library' => $this->library
            ]), factory(Book::class)
                ->make()
                ->toArray()
            );

        $response->assertRedirect();
    }

    public function testStoreAsMember()
    {
        $response = $this->actingAs($this->MEMBER)
            ->post(route('library.categories.store', [
                'library' => $this->library
            ]), factory(Book::class)
                ->make()
                ->toArray()
            );

        $response->assertStatus(403);
    }

    public function testShowAsOwner()
    {
        $book = factory(Book::class)->create();
        $this->library->books()->save($book);

        $response = $this->actingAs($this->OWNER)
            ->get(route('library.books.show', [
                'library' => $this->library,
                'book' => $book,
            ]));

        $response->assertStatus(200);
    }

    public function testShowAsMember()
    {
        $book = factory(Book::class)->create();
        $this->library->books()->save($book);

        $response = $this->actingAs($this->MEMBER)
            ->get(route('library.books.show', [
                'library' => $this->library,
                'book' => $book,
            ]));

        $response->assertStatus(200);
    }

    public function testEditAsOwner()
    {
        $book = factory(Book::class)->create();
        $this->library->books()->save($book);

        $response = $this->actingAs($this->OWNER)
            ->get(route('library.books.edit', [
                'library' => $this->library,
                'book' => $book,
            ]));

        $response->assertStatus(200);
    }

    public function testEditAsMember()
    {
        $book = factory(Book::class)->create();
        $this->library->books()->save($book);

        $response = $this->actingAs($this->MEMBER)
            ->get(route('library.books.edit', [
                'library' => $this->library,
                'book' => $book,
            ]));

        $response->assertStatus(403);
    }

    public function testUpdateAsOwner()
    {
        $book = factory(Book::class)->create();
        $this->library->books()->save($book);

        $response = $this->actingAs($this->OWNER)
            ->put(route('library.books.update', [
                'library' => $this->library,
                'book' => $book,
            ]), $book->toArray());

        $response->assertRedirect();
    }

    public function testUpdateAsMember()
    {
        $book = factory(Book::class)->create();
        $this->library->books()->save($book);

        $response = $this->actingAs($this->MEMBER)
            ->put(route('library.books.update', [
                'library' => $this->library,
                'book' => $book,
            ]), $book->toArray());

        $response->assertStatus(403);
    }

    public function testDestroyAsOwner()
    {
        $book = factory(Book::class)->create();
        $this->library->books()->save($book);

        $response = $this->actingAs($this->OWNER)
            ->delete(route('library.books.destroy', [
                'library' => $this->library,
                'book' => $book,
            ]));

        $response->assertRedirect();
    }

    public function testDestroyAsMember()
    {
        $book = factory(Book::class)->create();
        $this->library->books()->save($book);

        $response = $this->actingAs($this->MEMBER)
            ->delete(route('library.books.destroy', [
                'library' => $this->library,
                'book' => $book,
            ]));

        $response->assertStatus(403);
    }
}
