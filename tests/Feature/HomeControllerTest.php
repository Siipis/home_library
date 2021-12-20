<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class HomeControllerTest extends TestCase
{
    use WithoutMiddleware;

    public function testIndex()
    {
        $response = $this->actingAs($this->USER)
            ->get(route('index'));

        $response->assertStatus(200);
    }

    public function testSettings()
    {
        $response = $this->actingAs($this->USER)
            ->get(route('settings'));

        $response->assertStatus(200);
    }

    public function testUpdateAccount()
    {
        $response = $this->actingAs($this->USER)
            ->post(route('settings.account'),
                $this->USER->all()->toArray()
            );

        $response->assertRedirect();
    }
}
