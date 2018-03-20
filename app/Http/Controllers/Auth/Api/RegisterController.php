<?php

namespace App\Http\Controllers\Auth\Api;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|max:192',
            'email' => 'required|string|email|max:192|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
    }
    
    /**
     * Handle a registration request
     */
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        return response()->json([
            'message' => 'Successfully created user!'
        ], 201);
    }

    public function destroy()
    {
        auth()->user()->delete();

        return response()->json([
            'message' => 'User successfully deleted'
        ], 200);
    }
}
