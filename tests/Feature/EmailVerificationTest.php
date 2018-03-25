<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use App\Notifications\EmailVerification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    private $user_register_data = [
        'name' => 'Test name',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password'
    ];

    /** @test */
    public function when_user_registers_a_notification_with_verification_link_is_sent()
    {
        Notification::fake();

        $this->postJson(route('api.register'), $this->user_register_data)
            ->assertStatus(201);

        $user = User::first();
        $this->assertFalse($user->isVerified());

        $verificationToken = '';

        Notification::assertSentTo(
            $user,
            EmailVerification::class,
            function ($notification, $channels) use (&$verificationToken) {
                $verificationToken = $notification->token;
                return true;
            });
        
        $this->assertEquals($user->verification_token, $verificationToken);
    }
}
