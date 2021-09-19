<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Request as FacadesRequest;

class ProductImageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $is_owner = $this->route('product')->user_id == auth()->user()->id;
        if (FacadesRequest::isMethod('post')) 
            return $is_owner;
        else if (FacadesRequest::isMethod('delete'))
            return $is_owner && 
                $this->route('product')->id == $this->route('picture')->product_id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        if (FacadesRequest::isMethod('post'))
            return [
                'file' => ['required', 'mimes:jpeg,png,jpg,gif,svg', 'image', 'max:2048'], 
            ];
        else if (FacadesRequest::isMethod('delete'))
            return [];
    }
}
