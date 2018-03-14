<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_register()
    {
        $response = $this->postJson('/api/register', [
            "name" => "John Doe",
            "email" => "test@example.com",
            "password" => "password",
            "password_confirmation" => "password"
        ])->assertStatus(201)->json();

        $this->assertDatabaseHas("users", [
            "name" => "John Doe",
            "email" => "test@example.com"
        ]);
    }

    /** @test */
    public function a_user_cannot_register_with_invalid_data()
    {
        $response = $this->postJson('/api/register', [
            "name" => "",
            "email" => "",
            "password" => "",
            "password_confirmation" => ""
        ])->assertStatus(422)->json('errors');

        $this->assertArrayHasKey('name', $response);
        $this->assertArrayHasKey('email', $response);
        $this->assertArrayHasKey('password', $response);
    }
}
