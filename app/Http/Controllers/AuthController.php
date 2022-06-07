<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)    
    {
        //validate fields
        $credentials = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password'=> 'required|min:8|confirmed'
        ]);

        //create user
        $user = User::create([
            'name' => $credentials['name'],
            'email'=> $credentials['email'],
            'password' => bcrypt($credentials['password']),
        ]);

        $token = $user->createToken('secret')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token,
        ];

        return response($response, 201);
    }

    //login user
    public function Login(Request $request)
    {
        $fields = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        // Check Number
        $user = User::where('email', $fields['email'])->first();

        // Check Password
        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return response([
                'message' => 'Bad Credentials'
            ], 401);
        }

        $token = $user->createToken('secret')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token,
        ];

        return response($response, 200);
    }

    public function user() 
    {
        $response = [
            'user' => auth()->user(),
        ];

        return response($response, 200);
    }

    public function Logout(Request $request)
    {
        // Revoke the token that was used to authenticate the current request...
        $request->user()->currentAccessToken()->delete();

        $response = [
            'message' => 'Logged out',
        ];

        return response($response, 200);
    }
}
