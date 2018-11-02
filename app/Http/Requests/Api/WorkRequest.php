<?php

namespace App\Http\Requests\Api;

use App\Rules\JsontoArrExists;
use Illuminate\Validation\Rule;

class WorkRequest extends FormRequest
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
                    'name' => 'required|string|unique:works',
                    'description' => 'required|string|max:255',
                    'category_id' => 'required|integer|exists:categories,id',
                    'resource_url' => 'required|string',
                    'cover_url' => 'required|string',
                    'tags' => 'json',
                ];
                break;
            case 'PATCH':
                return [
                    'name' => 'string|unique:works,name,'.$this->work->id,
                    'description' => 'string|max:255',
                    'category_id' => 'integer|exists:categories,id',
                    'cover_url' => 'string',
                    'tags' => 'json',
                ];
                break;
        }
    }

    public function attributes(){
        return [
            'resource_url' => '视频',
            'cover_url' => '封面',
            'category_id' => '分类',
            'tags' => '标签',
        ];
    }
}
