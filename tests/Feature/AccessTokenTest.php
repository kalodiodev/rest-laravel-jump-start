<?php

namespace Tests\Feature;

use Tests\TestCase;
use Laravel\Passport\Client;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AccessTokenTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    public function setUp()
    {
        parent::setUp();

        factory(Client::class, 2)->create();

        $this->user = factory(\App\User::class)->create([
            'password' => bcrypt('password')
        ]);
    }

    /** @test */
    public function a_registered_user_can_get_access_token()
    {
        $response = $this->postJson(route('api.access'), [
            'username' => $this->user->email,
            'password' => 'password'
        ])->assertStatus(200)->json();

        $this->assertArrayHasKey('access_token', $response);
        $this->assertArrayHasKey('refresh_token', $response);
    }

     /** @test */
     public function a_non_registered_user_cannot_get_access_token()
     {
         $response = $this->postJson(route('api.access'), [
             'username' => 'guest@example.com',
             'password' => '123456'
         ])->assertStatus(401)->json();
 
         $this->assertArrayHasKey('error', $response);
     }

     /** @test */
     public function a_user_cannot_get_access_token_with_invalid_credentials()
     {
         $response = $this->postJson(route('api.access'), [
             'username' => '',
             'password' => 'test'
         ])->assertStatus(422)->json('errors');

         $this->assertArrayHasKey('username', $response);
         $this->assertArrayHasKey('password', $response);
     }

     /** @test */
    public function a_user_with_access_token_can_revoke_token()
    {        
        $token = $this->postJson(route('api.access'), [
            'username' => $this->user->email,
            'password' => 'password'
        ])->json();

        $response = $this->postJson(route('api.token.revoke'), [], [
            'Authorization' => $token['token_type'] . ' ' . $token['access_token']
        ])->assertStatus(204);
    }

    /** @test */
    public function a_user_without_access_token_cannot_revoke_token()
    {
        $response = $this->postJson(route('api.token.revoke'))
            ->assertStatus(401)
            ->json();
        
        $this->assertEquals("Unauthenticated.", $response['message']);
    }

    /** @test */
    public function a_user_with_refresh_token_can_refresh_access_token()
    {
        $token = $this->postJson(route('api.access'), [
            'username' => $this->user->email,
            'password' => 'password'
        ])->json();

        $response = $this->postJson(route('api.token.refresh'), [
            'refresh_token' => $token['refresh_token']
        ])->assertStatus(200)->json();

        $this->assertArrayHasKey('access_token', $response);
        $this->assertArrayHasKey('refresh_token', $response);
        $this->assertNotEquals($token, $response['access_token']);
    }
}
