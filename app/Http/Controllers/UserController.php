<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function update(Request $request)
    {
        $user = auth()->user();

        $this->validate($request, [
            'name' => 'max:190',
            'email' => 'email|max:190|unique:users,email,' . $user->id,
            'password' => 'min:6|max:190|confirmed'
        ]);

        $data = [];

        if($request->has('password'))
        {
            $data['password'] = Hash::make($request->password);
        }

        $data = array_merge($data, $request->only(['name', 'email']));

        $user->update($data);
        
        return response()->json([
            'user' => $user
        ], 200);
    }
}
