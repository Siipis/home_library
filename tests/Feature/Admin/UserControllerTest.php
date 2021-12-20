<?php

namespace Tests\Feature\Admin;

use App\User;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    public function testIndexAsAdmin()
    {
        $response = $this->actingAs($this->ADMIN)
            ->get(route('admin.users.index'));

        $response->assertStatus(200);
    }

    public function testIndexAsOwner()
    {
        $response = $this->actingAs($this->OWNER)
            ->get(route('admin.users.index'));

        $response->assertStatus(403);
    }

    public function testCreateAsAdmin()
    {
        $response = $this->actingAs($this->ADMIN)
            ->get(route('admin.users.create'));

        $response->assertStatus(200);
    }

    public function testCreateAsOwner()
    {
        $response = $this->actingAs($this->OWNER)
            ->get(route('admin.users.create'));

        $response->assertStatus(403);
    }

    public function testStoreAsAdmin()
    {
        $response = $this->actingAs($this->ADMIN)
            ->post(route('admin.users.store'),
                factory(User::class)
                ->make()
                ->toArray()
            );

        $response->assertRedirect();
    }

    public function testStoreAsOwner()
    {
        $response = $this->actingAs($this->OWNER)
            ->post(route('admin.users.store'),
                factory(User::class)
                ->make()
                ->toArray()
            );

        $response->assertStatus(403);
    }

    public function testShowAsAdmin()
    {
        $response = $this->actingAs($this->ADMIN)
            ->get(route('admin.users.show', [
                'user' => $this->MEMBER
            ]));

        $response->assertStatus(200);
    }

    public function testShowAsOwner()
    {
        $response = $this->actingAs($this->OWNER)
            ->get(route('admin.users.show', [
                'user' => $this->MEMBER
            ]));

        $response->assertStatus(403);
    }

    public function testEditAsAdmin()
    {
        $this->assertTrue(true);
    }

    public function testEditAsOwner()
    {
        $this->assertTrue(true);
    }

    public function testUpdateAsAdmin()
    {
        $this->assertTrue(true);
    }

    public function testUpdateAsOwner()
    {
        $this->assertTrue(true);
    }

    public function testPromoteAsAdmin()
    {
        $response = $this->actingAs($this->ADMIN)
            ->put(route('admin.users.promote', [
                'user' => $this->MEMBER
            ]), [
                'data' => [
                    'user' => [
                        'is_admin' => true
                    ]
                ]
            ]);

        $response->assertRedirect();
    }

    public function testPromoteAsOwner()
    {
        $response = $this->actingAs($this->OWNER)
            ->put(route('admin.users.promote', [
                'user' => $this->MEMBER
            ]), [
                'data' => [
                    'user' => [
                        'is_admin' => true
                    ]
                ]
            ]);

        $response->assertStatus(403);
    }

    public function testDestroyAsAdmin()
    {
        $response = $this->actingAs($this->ADMIN)
            ->delete(route('admin.users.destroy', [
                'user' => $this->MEMBER
            ]));

        $response->assertRedirect();
    }

    public function testDestroyAsOwner()
    {
        $response = $this->actingAs($this->OWNER)
            ->delete(route('admin.users.destroy', [
                'user' => $this->MEMBER
            ]));

        $response->assertStatus(403);
    }
}
