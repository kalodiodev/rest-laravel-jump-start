<?php

namespace App\Http\Controllers\Auth\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Auth\ForgotPasswordController as ForgotBaseController;

class ForgotPasswordController extends ForgotBaseController
{
    /**
     * Send reset link success response
     */
    protected function sendResetLinkResponse($response)
    {
        return response()->json([
            'message' => 'Reset password email sent.'
        ], 200);
    }

    /**
     * Send reset link fail response
     */
    protected function sendResetLinkFailedResponse(Request $request, $response)
    {
        return response()->json([
            'message' => 'Reset password email failed to be sent.'
        ], 400);
    }
}
