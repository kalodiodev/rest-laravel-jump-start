<?php

namespace App\Http\Controllers\Auth\Api;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EmailVerificationController extends Controller
{
    public function verify($token)
    {
        $user = User::where('verification_token', $token)->first();

        if(isset($user)) {
            $user->verified = true;
            $user->verification_token = '';
            $user->save();
                    
            return response()->json(['message' => 'Email successfully verified.'], 200);
        }

        return response()->json([
            'error' => 'invalid_token',
            'message' => 'Email cannot be identified.'
        ], 404);
    }
}
