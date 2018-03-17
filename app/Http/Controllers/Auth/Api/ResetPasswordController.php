<?php

namespace App\Http\Controllers\Auth\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Auth\ResetPasswordController as ResetBaseController;

class ResetPasswordController extends ResetBaseController
{
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
