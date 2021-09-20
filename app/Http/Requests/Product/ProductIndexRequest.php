<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Request as FacadesRequest;
use Illuminate\Support\Facades\Validator;

class ProductIndexRequest extends FormRequest
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
        if ($this->has('user_id')) {
            $filtering_params = $filtering_params + [
                'user_id' => $this->user_id
            ];
        }
        if ($this->has('sort_by_price'))
            $filtering_params = $filtering_params + [
                'sort_by_price' => $this->sort_by_price
            ];
        if ($this->has('low_limit_price'))
            $filtering_params = $filtering_params + [
                'low_limit_price' => $this->low_limit_price
            ];
        if ($this->has('high_limit_price'))
            $filtering_params = $filtering_params + [
                'high_limit_price' => $this->high_limit_price
            ];
        if ($this->has('search'))
            $filtering_params = $filtering_params + [
                'search' => $this->search
            ];

        Validator::make($filtering_params, [
            'user_id' => ['sometimes', 'exists:users,id'],
            'sort_by_price' => ['sometimes', 'in:desc,asc'],
            'low_limit_price' => ['sometimes', 'integer'],
            'high_limit_price' => ['sometimes', 'integer'],
            'search' => ['sometimes', 'string', ]
        ])->validate();

        return [];
    }
}
