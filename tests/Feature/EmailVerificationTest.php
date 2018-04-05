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

    /** @test */
    public function user_with_verification_token_can_be_verified()
    {
        Notification::fake();

        $user = factory(\App\User::class)->create([
            'verified' => false, 'verification_token' => str_random(40)
        ]);

        $verificationToken = '';

        Notification::assertSentTo(
            $user, 
            EmailVerification::class, 
            function ($notification, $channels) use (&$verificationToken) {
                $verificationToken = $notification->token;
                return true;
            });

        $response = $this->getJson(route('api.email.verify', ['token' => $verificationToken]))
            ->assertStatus(200)
            ->json();
        
        $this->assertEquals('Email successfully verified.', $response['message']);
        $this->assertTrue($user->fresh()->isVerified());
       // $this->assertEquals("", $user->verification_token);
    }

    /** @test */
    public function a_user_cannot_be_verified_with_invalid_token()
    {
        Notification::fake();

        $user = factory(\App\User::class)->create([
            'verified' => false, 'verification_token' => str_random(40)
        ]);

        $verificationToken = '';

        Notification::assertSentTo(
            $user, 
            EmailVerification::class, 
            function ($notification, $channels) use (&$verificationToken) {
                $verificationToken = $notification->token;
                return true;
            });

        $response = $this->getJson(route('api.email.verify', ['token' => 'any token']))
            ->assertStatus(404)
            ->json();
        
        $this->assertEquals('invalid_token', $response['error']);
        $this->assertEquals('Email cannot be identified.', $response['message']);
    }

    /** @test */
    public function a_user_can_request_to_resend_verification_token()
    {
        $user = factory(\App\User::class)->create([
            'verified' => false, 'verification_token' => str_random(40)
        ]);

        Notification::fake();

        $response = $this->postJson(route('api.verification.resend', [
            'email' => $user->email
        ]))->assertStatus(200)->json();

        Notification::assertSentTo($user, EmailVerification::class);
    }

    /** @test */
    public function a_verified_user_cannot_request_a_verification_token_resend()
    {
        $user = factory(\App\User::class)->create([
            'verified' => true, 'verification_token' => null
        ]);

        Notification::fake();

        $response = $this->postJson(route('api.verification.resend', [
            'email' => $user->email
        ]))->assertStatus(409)->json();

        Notification::assertNotSentTo($user, EmailVerification::class);
    }
}
