<?php

namespace App\Http\Controllers\Auth\Api;

use Illuminate\Http\Request;
use Laravel\Passport\Client;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class AccessTokenController extends Controller
{
    private $client;

    public function __construct()
    {
        $this->client = Client::find(2);
    }

    public function access(Request $request)
    {
        $this->validate($request, [
            'username' => 'required|email',
            'password' => 'required|min:6'
        ]);

        $params = [
            'grant_type' => 'password',
            'client_id' => $this->client->id,
            'client_secret' => $this->client->secret,
            'username' => $request->username,
            'password' => $request->password,
            'scope' => '*'
        ];

        $request = Request::create('oauth/token', 'POST', $params);

        return app()->handle($request);
    }

    public function revoke(Request $request)
    {
        $token = auth()->user()->token();

        DB::table('oauth_refresh_tokens')
            ->where('access_token_id', $token->id)
            ->update(['revoked' => true]);

        $token->revoke();

        return response([], 204);
    }

    public function refresh(Request $request)
    {
        $params = [
            'grant_type' => 'refresh_token',
            'refresh_token' => $request->refresh_token,
            'client_id' => $this->client->id,
            'client_secret' => $this->client->secret,
            'scope' => '*',
        ];

        $request = Request::create('oauth/token', 'POST', $params);
        $response = app()->handle($request);

        return $response;
    }
}
