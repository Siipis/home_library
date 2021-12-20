<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class LibraryControllerTest extends TestCase
{
    use WithoutMiddleware;

    public function testAdminHasAccess()
    {
        $this->assertTrue($this->ADMIN->can('view', $this->library));
    }

    public function testOwnerHasAccess()
    {
        $this->assertTrue($this->OWNER->can('view', $this->library));
    }

    public function testMemberHasAccess()
    {
        $this->assertTrue($this->MEMBER->can('view', $this->library));
    }

    public function testNonMemberHasNoAccess()
    {
        $this->assertFalse($this->USER->can('view', $this->library));
    }

    public function testIndexAsMember()
    {
        $response = $this->actingAs($this->MEMBER)
            ->get(route('library.index', [
                'library' => $this->library
            ]));

        $response->assertStatus(200);
    }

    public function testIndexAsUser()
    {
        $response = $this->actingAs($this->USER)
            ->get(route('library.index', [
                'library' => $this->library
            ]));

        $response->assertStatus(403);
    }

    public function testBooksAsMember()
    {
        $response = $this->actingAs($this->MEMBER)
            ->get(route('library.books', [
                'library' => $this->library
            ]));

        $response->assertStatus(200);
    }

    public function testBooksAsUser()
    {
        $response = $this->actingAs($this->USER)
            ->get(route('library.books', [
                'library' => $this->library
            ]));

        $response->assertStatus(403);
    }
}
