<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Laravel\Passport\Passport;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateUserTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    private $data = [
        'name' => 'New name',
        'email' => 'newmail@example.com',
        'password' => 'New password',
        'password_confirmation' => 'New password',
    ];

    public function setUp()
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
    }

    /** @test */
    public function a_user_with_token_can_perform_a_profile_update()
    {
        Passport::actingAs($this->user);

        $response = $this->patchJson(route('api.update'), $this->data)
            ->assertStatus(200);

        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'name' => $this->data['name'],
            'email' => $this->data['email']
        ]);
    }

    /** @test */
    public function a_user_cannot_be_updated_with_invalid_data()
    {
        Passport::actingAs($this->user);

        $response = $this->patchJson(route('api.update'), [
            'email' => 'test',
            'password' => '123',
            'password_confirmation' => '123'
        ])->assertStatus(422)->json('errors');

        $this->assertArrayHasKey('email', $response);
        $this->assertArrayHasKey('password', $response);
    }

    /** @test */
    public function an_unauthenticated_user_cannot_be_updated()
    {
        $response = $this->patchJson(route('api.update'), $this->data)
            ->assertStatus(401);
    }

    /** @test */
    public function when_user_updates_email_a_new_verification_is_required()
    {
        Passport::actingAs($this->user);

        $response = $this->patchJson(route('api.update'), [
            'email' => $this->data['email']
        ])->assertStatus(200);

        $this->assertFalse($this->user->isVerified());
        $this->assertNotNull($this->user->verification_token);

        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'verified' => 0
        ]);
    }
}
