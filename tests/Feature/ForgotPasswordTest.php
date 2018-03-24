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

        $response = $this->postJson(route('api.password.forgot'), [
            'email' => $this->user->email
        ])->assertStatus(200)->json();

        Notification::assertSentTo(
            $this->user, \App\Notifications\ResetPassword::class
        );

        $this->assertEquals('Reset password email sent.', $response['message']);
    }

    /** @test */
    public function a_user_can_reset_password()
    {
        Notification::fake();

        $this->postJson(route('api.password.forgot'), [
                'email' => $this->user->email
            ]
        );
        $resetToken = '';

        Notification::assertSentTo(
            $this->user,
            \App\Notifications\ResetPassword::class,
            function ($notification, $channels) use (&$resetToken) {
                $resetToken = $notification->token;
                return true;
            });
        
        $response = $this->postJson(
            route('api.password.reset', ['token' => $resetToken]), [
                'email' => $this->user->email,
                'password' => '123456',
                'password_confirmation' => '123456'
            ]
        )->assertStatus(200)->json();
    }

    /** @test */
    public function a_user_cannot_reset_password_with_invalid_data()
    {
        $response = $this->postJson(
            route('api.password.reset', ['token' => 'anytoken']), 
            []
        )->assertStatus(422)->json();

        $this->assertEquals(
            "The given data was invalid.", 
            $response['message']);

        $this->assertEquals(
            "The email field is required.", 
            $response['errors']['email'][0]);
            
        $this->assertEquals(
            "The password field is required.", 
            $response['errors']['password'][0]);
    }
}
