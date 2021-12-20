<?php

namespace Tests\Feature\Library;

use App\Category;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    public function testIndexAsMember()
    {
        $response = $this->actingAs($this->MEMBER)
            ->get(route('library.categories.index', [
                'library' => $this->library
            ]));

        $response->assertStatus(200);
    }

    public function testIndexAsUser()
    {
        $response = $this->actingAs($this->USER)
            ->get(route('library.categories.index', [
                'library' => $this->library
            ]));

        $response->assertStatus(403);
    }

    public function testCreateAsOwner()
    {
        $response = $this->actingAs($this->OWNER)
            ->get(route('library.categories.create', [
                'library' => $this->library
            ]));

        $response->assertStatus(200);
    }

    public function testCreateAsMember()
    {
        $response = $this->actingAs($this->MEMBER)
            ->get(route('library.categories.create', [
                'library' => $this->library
            ]));

        $response->assertStatus(403);
    }

    public function testStoreAsOwner()
    {
        $response = $this->actingAs($this->OWNER)
            ->post(route('library.categories.store', [
                'library' => $this->library
            ]), factory(Category::class)
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
            ]), factory(Category::class)
                ->make()
                ->toArray()
            );

        $response->assertStatus(403);
    }

    public function testShowAsOwner()
    {
        $category = factory(Category::class)->make();
        $this->library->categories()->save($category);

        $response = $this->actingAs($this->OWNER)
            ->get(route('library.categories.show', [
                'library' => $this->library,
                'category' => $category,
            ]));

        $response->assertStatus(200);
    }

    public function testShowAsMember()
    {
        $category = factory(Category::class)->make();
        $this->library->categories()->save($category);

        $response = $this->actingAs($this->MEMBER)
            ->get(route('library.categories.show', [
                'library' => $this->library,
                'category' => $category,
            ]));

        $response->assertStatus(200);
    }

    public function testEditAsOwner()
    {
        $category = factory(Category::class)->make();
        $this->library->categories()->save($category);

        $response = $this->actingAs($this->OWNER)
            ->get(route('library.categories.edit', [
                'library' => $this->library,
                'category' => $category,
            ]));

        $response->assertStatus(200);
    }

    public function testEditAsMember()
    {
        $category = factory(Category::class)->make();
        $this->library->categories()->save($category);

        $response = $this->actingAs($this->MEMBER)
            ->get(route('library.categories.edit', [
                'library' => $this->library,
                'category' => $category,
            ]));

        $response->assertStatus(403);
    }

    public function testUpdateAsOwner()
    {
        $category = factory(Category::class)->make();
        $this->library->categories()->save($category);

        $response = $this->actingAs($this->OWNER)
            ->put(route('library.categories.update', [
                'library' => $this->library,
                'category' => $category,
            ]), $category->toArray());

        $response->assertRedirect();
    }

    public function testUpdateAsMember()
    {
        $category = factory(Category::class)->make();
        $this->library->categories()->save($category);

        $response = $this->actingAs($this->MEMBER)
            ->put(route('library.categories.update', [
                'library' => $this->library,
                'category' => $category,
            ]), $category->toArray());

        $response->assertStatus(403);
    }

    public function testDestroyAsOwner()
    {
        $category = factory(Category::class)->make();
        $this->library->categories()->save($category);

        $response = $this->actingAs($this->OWNER)
            ->delete(route('library.categories.destroy', [
                'library' => $this->library,
                'category' => $category,
            ]));

        $response->assertRedirect();
    }

    public function testDestroyAsMember()
    {
        $category = factory(Category::class)->make();
        $this->library->categories()->save($category);

        $response = $this->actingAs($this->MEMBER)
            ->delete(route('library.categories.destroy', [
                'library' => $this->library,
                'category' => $category,
            ]));

        $response->assertStatus(403);
    }
}
