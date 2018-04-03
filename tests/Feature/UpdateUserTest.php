<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateUserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_with_token_can_perform_a_profile_update()
    {
        $user = factory(User::class)->create();

        \Laravel\Passport\Passport::actingAs($user);

        $response = $this->patchJson(route('api.update'), [
            'name' => 'New name',
            'email' => 'newmail@example.com',
            'password' => 'New password',
            'password_confirmation' => 'New password',
        ])->assertStatus(200);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'New name',
            'email' => 'newmail@example.com'
        ]);
    }
}
