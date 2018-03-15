<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

class ForgotPasswordTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    public function setUp()
    {
        parent::setUp();

        $this->user = factory(\App\User::class)
            ->create(['email' => 'test@example.com']);
    }

    /** @test */
    public function a_user_can_request_password_reset()
    {
        Notification::fake();

        $response = $this->postJson('/api/email', [
            'email' => $this->user->email
        ])->assertStatus(200)->json();

        Notification::assertSentTo(
            $this->user, \App\Notifications\ResetPassword::class
        );

        $this->assertEquals('Reset password email sent.', $response['message']);
    }
}
