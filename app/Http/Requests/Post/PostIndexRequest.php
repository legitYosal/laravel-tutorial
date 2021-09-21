<?php

namespace App\Http\Requests\Post;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Request as FacadesRequest;
use Illuminate\Support\Facades\Validator;

class PostIndexRequest extends FormRequest
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

        $filtering_params = [];
        if ($this->has('user_id'))
            $filtering_params = $filtering_params + [
                'user_id' => $this->user_id,
            ];
        
        Validator::make($filtering_params, [
            'user_id' => ['sometimes', 'exists:users,id'],
        ])->validate();
        return [];
    }

}
