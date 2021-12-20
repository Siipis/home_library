<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;

class HomeControllerTest extends TestCase
{
    public function testAdminHasAccess()
    {
        $this->assertTrue($this->ADMIN->can('access-backend', $this->library));
    }

    public function testOwnerHasNoAccess()
    {
        $this->assertFalse($this->OWNER->can('access-backend', $this->library));
    }

    public function testMemberHasNoAccess()
    {
        $this->assertFalse($this->MEMBER->can('access-backend', $this->library));
    }

    public function testNonMemberHasNoAccess()
    {
        $this->assertFalse($this->USER->can('access-backend', $this->library));
    }

    public function testIndexAsAdmin()
    {
        $response = $this->actingAs($this->ADMIN)
            ->get(route('admin.index'));

        $response->assertStatus(200);
    }

    public function testIndexAsOwner()
    {
        $response = $this->actingAs($this->OWNER)
            ->get(route('admin.index'));

        $response->assertStatus(403);
    }
}
