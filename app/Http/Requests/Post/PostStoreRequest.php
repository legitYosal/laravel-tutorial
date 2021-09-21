<?php

namespace App\Http\Requests\Post;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Request as FacadesRequest;

class PostStoreRequest extends FormRequest
{
   /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {   
        return true;
    }       

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => ['required', 'max:256'],
            'description' => ['required', 'max:2048'],
            'pictures' => ['required', 'array', 'min:1', 'max:3'],
            'pictures.*.file' => ['required', 'mimes:jpeg,png,jpg,gif,svg', 'image', 'max:2048'], 
        ];
    }

    public function messages()
    {
        return [
            'pictures.max' => __('lang.Pictures must not be more than 3'),
            'pictures.min' => __('lang.Pictures must not be less than 1'),
        ];
    }
}
