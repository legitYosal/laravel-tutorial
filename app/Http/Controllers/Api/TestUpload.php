<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class TestUpload extends Controller
{
    //
    public function upload(Request $request) 
    {
        $validator = Validator::make($request->all(), [
            'file'=> ['required', 'mimes:jpeg,png,jpg,gif,svg', 'image', 'max:2048'], 
        ]);
        $validator->validate();
        $file = $validator->validated()['file'];

        $path = $file->store('public/files');
        $name = $file->getClientOriginalName();

        return response()->json([
            'path' => $path,
            'name' => $name,
        ]);
        
    }
}
