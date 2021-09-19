<?php

namespace App\Http\Controllers\Api\Internal;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\NotificationToken;
use Illuminate\Support\Facades\Facade;

use App\Http\Requests\Internal\UserTokenRequest;

class UserController extends Controller
{
    //
    public function set_user_notif_token(UserTokenRequest $request)
    {
        /*
            this is a request from an internal service
            this request is validated through a middleware
            body {
                user_id
                token
            }
            create or update users token object
            resonse 200
        */
        $data = $request->validated();
        
        $notif_token = NotificationToken::where('user_id', $data['user_id']) -> first();
        if ($notif_token) {
            $notif_token->token = $data['token'];
            $notif_token->save();
        } else {
            $notif_token = NotificationToken::create([
                'user_id'=>$data['user_id'],
                'token'=>$data['token'],
            ]);
        }
        return response()->json([
            'data'=>$notif_token
        ]);

    }
}
