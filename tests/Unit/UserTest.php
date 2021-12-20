<?php

namespace Tests\Unit;

use App\Library;
use App\User;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function testIsNotMemberOf()
    {
        $user = factory(User::class)->create();
        $library = factory(Library::class)->create();

        $this->assertFalse($user->isMemberOf($library));
    }

    public function testIsMemberOf()
    {
        $user = factory(User::class)->create();
        $library = factory(Library::class)->create();

        $library->members()->save($user, [
            'role' => Library::MEMBER_ROLE
        ]);

        $this->assertTrue($user->isMemberOf($library));
    }

    public function testIsNotOwnerOf()
    {
        $user = factory(User::class)->create();
        $library = factory(Library::class)->create();

        $library->members()->save($user, [
            'role' => Library::MEMBER_ROLE
        ]);

        $this->assertFalse($user->isOwnerOf($library));
    }

    public function testIsOwnerOf()
    {
        $user = factory(User::class)->create();
        $library = factory(Library::class)->create();

        $library->members()->save($user, [
            'role' => Library::OWNER_ROLE
        ]);

        $this->assertTrue($user->isOwnerOf($library));
    }

    public function testAdminIsOwnerOf()
    {
        $user = factory(User::class)->create([
            'is_admin' => true
        ]);
        $library = factory(Library::class)->create();

        $this->assertTrue($user->isOwnerOf($library));
    }
}
