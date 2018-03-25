<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Notifications\EmailVerification;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use League\OAuth2\Server\Exception\OAuthServerException;
use App\Notifications\ResetPassword as ResetPasswordNotification;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public static function boot()
    {
        parent::boot();

        static::created(function($user) {
            $user->sendEmailVerificationNotification();
        });

        static::creating(function($user) {
            $user->verification_token = str_random(40);
        });

        static::deleting(function($user) {
            // When deleting user also delete tokens belong to user
            $user->tokens->each(function($token) {
                DB::table('oauth_refresh_tokens')
                    ->where('access_token_id', $token->id)
                    ->delete();

                $token->delete();
            });
        });
    }

    /**
     * Send the reset password notification
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    /**
     *  Send the email verification notification
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new EmailVerification($this->verification_token));
    }

    /**
     * Is user's email verified
     */
    public function isVerified()
    {
        return !! $this->verified;
    }

    /**
     * Validate user's password for passport password grant
     */
    public function validateForPassportPasswordGrant($password)
    {
        if($this->isValidPassword($password)) {
            if(! $this->isVerified()) {
                throw new OAuthServerException (
                    'User email is not verified', 6, 'account_inactive', 401
                );
            }
            return true;
        } 
        return false;
    }

    private function isValidPassword($password)
    {
        return Hash::check($password, $this->password);
    }
}
