<?php

namespace App\Http\Requests\Api;


class CategoryRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        switch ($this->method()){
            case 'POST':
                return [
                    'name' => 'required|string|unique:categories',
                    'cover' => 'required|string',
                    'description' => 'required|string|max:255',
                ];
                break;
            case 'PATCH':
                return [
                    'name' => 'string|unique:categories,name,' . $this->category->id,
                    'cover' => 'string',
                    'description' => 'string|max:255',
                ];
                break;
        }

    }
}
