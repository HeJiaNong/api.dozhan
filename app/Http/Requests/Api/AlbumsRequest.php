<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class AlbumsRequest extends FormRequest
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
                    'name' => 'required|string|unique:albums',
                    'description' => 'required|string',
                    'category_id' => 'required|string|exists:categories,id',
                ];
                break;
            case 'PATCH':
                return [
                    'name' => 'string|unique:albums,' . $this->user()->id,
                    'description' => 'string',
                    'category_id' => 'string|exists:categories,id',
                ];
                break;
        }

    }

    public function attributes(){
        return [
            'category_id' => '分类',
        ];
    }
}
