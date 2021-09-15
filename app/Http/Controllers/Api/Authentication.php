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
            'mobile' => ['required', 'max:11', 'min:11', 'regex:/(09)[0-9]{9}/'],
            'password' => ['required', 'min:3'],
        ]);
        $validator->validate();
        $data = $validator->validated();
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
            ], 403);
        }
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required'],
            'mobile' => ['required', 'max:11', 'min:11', 'regex:/(09)[0-9]{9}/', 'unique:users'],
            'password' => ['required', 'min:3'],
        ]);
        $validator->validate();
        $data = $validator->validated();

        $user = User::create([
            'name'=> $data['name'],
            'mobile'=> $data['mobile'],
            'password'=> Hash::make($data['password']),
        ]);

        return response()->json([
            'data' => $user,
            'message' => 'User created successfully',
        ], 200);
    }
}
