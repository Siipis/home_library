<?php

namespace Tests\Feature\Admin;

use App\Library;
use App\User;
use Tests\TestCase;

class LibraryControllerTest extends TestCase
{
    public function testIndexAsAdmin()
    {
        $response = $this->actingAs($this->ADMIN)
            ->get(route('admin.libraries.index', [
                'library' => $this->library
            ]));

        $response->assertStatus(200);
    }

    public function testIndexAsOwner()
    {
        $response = $this->actingAs($this->OWNER)
            ->get(route('admin.libraries.index', [
                'library' => $this->library
            ]));

        $response->assertStatus(403);
    }

    public function testCreateAsAdmin()
    {
        $response = $this->actingAs($this->ADMIN)
            ->get(route('admin.libraries.create', [
                'library' => $this->library
            ]));

        $response->assertStatus(200);
    }

    public function testCreateAsOwner()
    {
        $response = $this->actingAs($this->OWNER)
            ->get(route('admin.libraries.create', [
                'library' => $this->library
            ]));

        $response->assertStatus(403);
    }

    public function testStoreAsAdmin()
    {
        $response = $this->actingAs($this->ADMIN)
            ->post(route('admin.libraries.store', [
                'library' => $this->library
            ]), factory(Library::class)
                ->make()
                ->toArray()
            );

        $response->assertRedirect();
    }

    public function testStoreAsOwner()
    {
        $response = $this->actingAs($this->OWNER)
            ->post(route('admin.libraries.store', [
                'library' => $this->library
            ]), factory(Library::class)
                ->make()
                ->toArray()
            );

        $response->assertStatus(403);
    }

    public function testShowAsAdmin()
    {
        $response = $this->actingAs($this->ADMIN)
            ->get(route('admin.libraries.show', [
                'library' => $this->library
            ]));

        $response->assertStatus(200);
    }

    public function testShowAsOwner()
    {
        $response = $this->actingAs($this->OWNER)
            ->get(route('admin.libraries.show', [
                'library' => $this->library
            ]));

        $response->assertStatus(403);
    }

    public function testMembersAsAdmin()
    {
        $members = factory(User::class, 3)->create();
        $memberData = [];

        foreach ($members as $member) {
            array_push($memberData, [
                'id' => $member->id,
                'role' => Library::MEMBER_ROLE,
            ]);
        }

        $response = $this->actingAs($this->ADMIN)
            ->put(route('admin.library.members', [
                'library' => $this->library
            ]),[
                'data' => [
                    'members' => $memberData
                ]
            ]);

        $response->assertStatus(200);
    }

    public function testMembersAsOwner()
    {
        $members = factory(User::class, 3)->create();
        $memberData = [];

        foreach ($members as $member) {
            array_push($memberData, [
                'id' => $member->id,
                'role' => Library::MEMBER_ROLE,
            ]);
        }

        $response = $this->actingAs($this->OWNER)
            ->put(route('admin.library.members', [
                'library' => $this->library
            ]),[
                'data' => [
                    'members' => $memberData
                ]
            ]);

        $response->assertStatus(403);
    }

    public function testEditAsAdmin()
    {
        $response = $this->actingAs($this->ADMIN)
            ->get(route('admin.libraries.edit', [
                'library' => $this->library
            ]));

        $response->assertStatus(200);
    }

    public function testEditAsOwner()
    {
        $response = $this->actingAs($this->OWNER)
            ->get(route('admin.libraries.edit', [
                'library' => $this->library
            ]));

        $response->assertStatus(403);
    }

    public function testUpdateAsAdmin()
    {
        $response = $this->actingAs($this->ADMIN)
            ->put(route('admin.libraries.update', [
                'library' => $this->library
            ]), factory(Library::class)
                ->make()
                ->toArray()
            );

        $response->assertRedirect();
    }

    public function testUpdateAsOwner()
    {
        $response = $this->actingAs($this->OWNER)
            ->put(route('admin.libraries.update', [
                'library' => $this->library
            ]), factory(Library::class)
                ->make()
                ->toArray()
            );

        $response->assertStatus(403);
    }

    public function testDestroyAsAdmin()
    {
        $response = $this->actingAs($this->ADMIN)
            ->delete(route('admin.libraries.destroy', [
                'library' => $this->library
            ]));

        $response->assertRedirect();
    }

    public function testDestroyAsOwner()
    {
        $response = $this->actingAs($this->OWNER)
            ->delete(route('admin.libraries.destroy', [
                'library' => $this->library
            ]));

        $response->assertStatus(403);
    }
}
