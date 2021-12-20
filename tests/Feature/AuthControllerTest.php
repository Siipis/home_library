<?php

namespace Tests\Feature;

use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    public function testLogin()
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }
}
