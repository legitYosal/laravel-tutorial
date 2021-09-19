<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Request as FacadesRequest;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (FacadesRequest::isMethod('get') 
            || FacadesRequest::isMethod('post'))
                return true;

        else if (FacadesRequest::isMethod('put') 
            || FacadesRequest::isMethod('delete'))
                return $this->route('product')->user_id == auth()->user()->id;

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        if (FacadesRequest::isMethod('get'))
            return [];
        else if (FacadesRequest::isMethod('post'))
            return [
                'title' => ['required', 'max:256'],
                'description' => ['required', 'max:2048'],
                'pictures' => ['required'],
                'pictures.*.file' => ['required', 
                        'mimes:jpeg,png,jpg,gif,svg', 
                        'image', 'max:2048'], 
                // 'price' => ['required'],
                'price.bought_price' => ['required', 'integer'],
                'price.selling_price' => ['required', 'integer'],
            ];
        else if (FacadesRequest::isMethod('put'))
            return [
                'title' => ['sometimes', 'max:256'],
                'description' => ['sometimes', 'max:2048'],
            ];
        else if (FacadesRequest::isMethod('delete'))
            return [];
    }
}
