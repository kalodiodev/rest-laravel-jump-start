<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
    * Send password change success response
    */
    protected function sendResetResponse($response)
    {
        return response()->json([
            'message' => 'Password changed!'
        ], 200);
    }

    /**
     * Send password change fail response
     */
    protected function sendResetFailedResponse(Request $request, $response)
    {
        return response()->json([
            'message' => 'Failed to change password'
        ], 400);
    }
}
