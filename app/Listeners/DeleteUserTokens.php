<?php

namespace App\Listeners;

use App\Events\UserDeleted;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class DeleteUserTokens
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(UserDeleted $event)
    {
        $event->user->tokens->each(function($token) {
            DB::table('oauth_refresh_tokens')
                ->where('access_token_id', $token->id)
                ->delete();

            $token->delete();
        });
    }
}
