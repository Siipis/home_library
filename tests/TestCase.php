<?php

namespace Tests;

use App\Library;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, RefreshDatabase;

    protected $library;

    protected $ADMIN;
    protected $OWNER;
    protected $MEMBER;
    protected $USER;

    public function setUp(): void
    {
        parent::setUp();

        $this->library = factory(Library::class)->create();

        $this->ADMIN = factory(User::class)->create([
            'is_admin' => true
        ]);

        $this->OWNER = factory(User::class)->create();
        $this->library->members()->save($this->OWNER, [
            'role' => Library::OWNER_ROLE
        ]);

        $this->MEMBER = factory(User::class)->create();
        $this->library->members()->save($this->MEMBER, [
            'role' => Library::MEMBER_ROLE
        ]);

        $this->USER = factory(User::class)->create();
    }
}
