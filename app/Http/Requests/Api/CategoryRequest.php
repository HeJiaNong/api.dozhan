<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
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
        switch ($this->method()){
            case 'POST':
                return [
                    'name' => 'required|string|unique:categories',
                    'description' => 'required|string|max:255',
                ];
                break;
            case 'PATCH':
                return [
                    'name' => 'string|unique:categories,name,' . $this->category->id,
                    'description' => 'string|max:255',
                ];
                break;
        }

    }
}
