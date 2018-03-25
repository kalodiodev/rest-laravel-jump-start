<?php

namespace App;

use App\Events\UserDeleted;
use App\Observers\UserObserver;
use Laravel\Passport\HasApiTokens;
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

    /**
     * The event map for the model.
     * 
     * @var array
     */
    protected $dispatchesEvents = [
        'deleted' => UserDeleted::class,
    ];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        User::observe(UserObserver::class);
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
