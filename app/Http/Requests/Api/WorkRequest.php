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
                    'url' => 'required|string',
                    'cover' => 'required|string',
                    'tag_ids' => ['json',new JsontoArrExists('tags')],
                ];
                break;
            case 'PATCH':
                return [
                    'name' => 'string|unique:works,name,'.$this->work->id,
                    'description' => 'string|max:255',
                    'category_id' => 'integer|exists:categories,id',
                    'cover' => 'string',
                    'tag_ids' => ['json',new JsontoArrExists('tags')],  //自己写的验证规则，反正json转数组后是否存在与某个表
                ];
                break;
        }
    }

    public function attributes(){
        return [
            'url' => '视频链接',
            'cover' => '封面链接',
            'category_id' => '分类',
        ];
    }
}
