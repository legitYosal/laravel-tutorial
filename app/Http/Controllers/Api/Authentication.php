<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\Http\Requests\Authentication\LoginRequest;
use App\Http\Requests\Authentication\RegisterRequest;
use Symfony\Component\HttpFoundation\Response;

class Authentication extends Controller
{
    use \App\Traits\GetAuthObject;

    public function login(LoginRequest $request)
    {
        $data = $request->validated();
        $auth = $this->get_auth();
        $token = $auth->attempt(['mobile' => $data['mobile'], 'password' => $data['password']]);
        if ($token) {
            return response()->json([
                'data' => [
                    'access_token' => $token,
                    'token_type' => 'bearer',
                    'expires_in' => $auth->factory()->getTTL() * 60
                ],
                'message'=> 'Successfull login',
            ]);
        } else {
            return response()->json([
                'message'=> 'Mobile or password was wrong',
            ], Response::HTTP_FORBIDDEN);
        }
    }

    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        $user = User::create([
            'name'=> $data['name'],
            'mobile'=> $data['mobile'],
            'password'=> Hash::make($data['password']),
        ]);

        return response()->json([
            'data' => $user,
            'message' => 'User created successfully',
        ], Response::HTTP_CREATED);
    }
}
