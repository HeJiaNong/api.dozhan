<?php

namespace App\Http\Requests\Api;


use App\Rules\ResourceAuthor;
use App\Rules\ResourceMime;
use App\Rules\ResourceSingle;

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
                    'icon_id' => [
                        'required',                         //字段必须
                        'uuid',                             //值必须是uuid格式
                        'unique:works,cover_id',
                        new ResourceAuthor(),               //资源用户必须和当前用户一致
                        new ResourceMime('image/*'), //资源类型限制
                    ],
                    'description' => 'required|string|max:255',
                ];
                break;
            case 'PATCH':
                return [
                    'name' => 'string|unique:categories,name,' . $this->category->id,
                    'icon_id' => [
                        'uuid',                             //值必须是uuid格式
                        'unique:works,cover_id,'.$this->category->id,
                        new ResourceAuthor(),               //资源用户必须和当前用户一致
                        new ResourceMime('image/*'), //资源类型限制
                    ],
                    'description' => 'string|max:255',
                ];
                break;
        }
    }

    public function messages()
    {
        return [
            'cover' => '图标'
        ];
    }
}
