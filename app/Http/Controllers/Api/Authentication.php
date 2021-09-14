<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

use App\Models\User;

class Authentication extends Controller
{
    use \App\Traits\GetAuthObject;

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email'],
            'password' => ['required', 'min:3'],
        ]);
        $validator->validate();
        $data = $validator->validated();
        $auth = $this->get_auth();
        $token = $auth->attempt(['email' => $data['email'], 'password' => $data['password']]);
        if ($token) {
            return response()->json([
                'message'=> 'Successfull login',
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => $auth->factory()->getTTL() * 60
            ]);
        } else {
            return response()->json([
                'message'=> 'Email or password was wrong',
            ], 403);
        }
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required'],
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required', 'min:3'],
        ]);
        $validator->validate();
        $data = $validator->validated();

        $user = User::create([
            'name'=> $data['name'],
            'email'=> $data['email'],
            'password'=> Hash::make($data['password']),
        ]);

        return response()->json([
            'user' => $user,
            'message' => 'User created successfully',
        ], 200);
    }
}
