<?php

namespace Tests\Unit;

use App\Library;
use App\User;
use Tests\TestCase;

class LibraryTest extends TestCase
{
    public function testMemberRelation()
    {
        $user = factory(User::class)->create();
        $library = factory(Library::class)->create();

        $library->members()->save($user);

        $this->assertDatabaseHas('library_user', [
            'library_id' => $library->id,
            'user_id' => $user->id,
            'role' => Library::MEMBER_ROLE,
        ]);
    }

    public function testNonMembers()
    {
        $this->assertDatabaseCount('library_user', 2);
        $this->assertCount(2, $this->library->nonMembers());
    }
}
