<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Client;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    public function setUp()
    {
        parent::setUp();

        factory(Client::class, 2)->create();

        $this->user = factory(\App\User::class)->create([
            "password" => bcrypt("password")
        ]);
    }

    /** @test */
    public function a_registered_user_can_get_access_token()
    {
        $response = $this->postJson('/api/login', [
            "username" => $this->user->email,
            "password" => "password"
        ])->assertStatus(200)->json();

        $this->assertArrayHasKey('access_token', $response);
        $this->assertArrayHasKey('refresh_token', $response);
    }

     /** @test */
     public function a_non_registered_user_cannot_get_access_token()
     {
         $response = $this->postJson('/api/login', [
             "username" => 'guest@example.com',
             "password" => "123456"
         ])->assertStatus(401)->json();
 
         $this->assertArrayHasKey('error', $response);
     }

     /** @test */
     public function a_user_cannot_login_with_invalid_credentials()
     {
         $response = $this->postJson('/api/login', [
             "username" => "",
             "password" => "test"
         ])->assertStatus(422)->json('errors');

         $this->assertArrayHasKey('username', $response);
         $this->assertArrayHasKey('password', $response);
     }
}
