<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

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
    public function when_user_deletes_account_tokens_should_also_be_deleted()
    {
        factory(\Laravel\Passport\Client::class, 2)->create();
        $user = factory(\App\User::class)->create(['password' => Hash::make('password')]);
        
        $params = [
            'username' => $user->email,
            'password' => 'password'
        ];

        // Creating two access tokens for user
        $this->postJson('/api/access', $params);
        $token = $this->postJson('/api/access', $params)->json('access_token');

        $headers = ['Authorization' => 'Bearer ' . $token];
        $this->deleteJson('/api/delete',[], $headers)
            ->assertStatus(200);

        $this->assertEquals(0 , DB::table('oauth_access_tokens')->count());
        $this->assertEquals(0, DB::table('oauth_refresh_tokens')->count());
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
