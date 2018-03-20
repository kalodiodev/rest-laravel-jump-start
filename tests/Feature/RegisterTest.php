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

    /** @test */
    public function a_registered_user_can_delete_the_account()
    {
        $user1 = factory(\App\User::class)->create();
        $user2 = factory(\App\User::class)->create();

        \Laravel\Passport\Passport::actingAs($user1);
                
        $response = $this->deleteJson('/api/delete')
            ->assertStatus(200)
            ->json();

        $this->assertDatabaseMissing('users', ['id' => $user1->id ]);
        $this->assertDatabaseHas('users', ['id' => $user2->id]);
        $this->assertEquals('User successfully deleted', $response['message']);
    }

    /** @test */
    public function a_guest_user_cannot_delete_an_account()
    {
        factory(\App\User::class, 3)->create();

        $response = $this->deleteJson('/api/delete')
            ->assertStatus(401);

        $this->assertCount(3, \App\User::all());
    }
}
