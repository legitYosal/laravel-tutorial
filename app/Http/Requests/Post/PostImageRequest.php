<?php

namespace App\Http\Requests\Post;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Request as FacadesRequest;

class PostImageRequest extends FormRequest
{
  /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {   
        $is_owner = $this->route('post')->user_id == auth()->user()->id;
        if (FacadesRequest::isMethod('post')) 
            return $is_owner;
        else if (FacadesRequest::isMethod('delete'))
            return $is_owner && 
                $this->route('post')->id == $this->route('picture')->post_id;
    }       

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        if (FacadesRequest::isMethod('post')) {
            $old_images = $this->route('post')->images;
            if (sizeof($old_images) >= 3) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'pictures' => [__('lang.Pictures must not be more than 3')],
                ]);          
            }
            return [
                'file' => ['required', 'mimes:jpeg,png,jpg,gif,svg', 'image', 'max:2048'], 
            ];
        }
        else if (FacadesRequest::isMethod('delete'))
            return [];
    }
}
