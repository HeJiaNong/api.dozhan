<?php

namespace App\Http\Requests\Api;


class AlbumsRequest extends FormRequest
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
                    'name' => 'required|string|unique:albums',
                    'description' => 'required|string',
                    'category_id' => 'required|integer|exists:categories,id',
                ];
                break;
            case 'PATCH':
                return [
                    'name' => 'string|unique:albums,name,' . $this->album->id,
                    'description' => 'string',
                    'category_id' => 'integer|exists:categories,id',
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
