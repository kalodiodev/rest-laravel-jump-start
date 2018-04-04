<?php

namespace App\Observers;

use App\User;
use Illuminate\Support\Facades\DB;

class UserObserver
{
    /**
     * Listen to the User creating event.
     * 
     * @param \App\User $user
     * @return void
     */
    public function creating(User $user)
    {
        $user->verification_token = str_random(40);
    }

    /**
     * Listen to the User created event.
     *
     * @param \App\User $user
     * @return void
     */
    public function created(User $user)
    {
        $user->sendEmailVerificationNotification();
    }

    /**
     * Listen to the User updated event.
     * 
     * @param \App\User $user
     * @return void
     */
    public function updating(User $user)
    {
        if($user->isDirty('email')) {
            $user->verified = false;
            $user->verification_token = User::generateVerificationCode();
        }
    }

    /**
     * Listen to the User updated event.
     * 
     * @param \App\User $user
     * @return void
     */
    public function updated(User $user)
    {
        if($user->isDirty('email')) {
            $user->sendEmailVerificationNotification();
        }   
    }
}