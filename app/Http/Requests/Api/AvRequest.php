<?php

namespace App\Http\Requests\Api;

use App\Rules\JsontoArrExists;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AvRequest extends FormRequest
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
                    'name' => 'required|string|unique:avs',
                    'description' => 'required|string|max:255',
                    'album_id' => 'string|exists:albums,id',
                    'video_id' => 'required|integer|exists:videos,id',
                    'image_id' => 'required|integer|exists:images,id',
                    'category_id' => 'required|integer|exists:categories,id',
                    'tag_ids' => 'json',
                ];
                break;
            case 'PATCH':
                return [
                    'name' => 'string|unique:avs,name,'.$this->av->id,
                    'description' => 'string|max:255',
                    'album_id' => 'exists:albums,id',
                    'image_id' => 'integer|exists:images,id',
                    'category_id' => 'integer|exists:categories,id',
                    'tag_ids' => ['json',new JsontoArrExists('tags')],  //自己写的验证规则，反正json转数组后是否存在与某个表
                ];
                break;
        }
    }

    public function attributes(){
        return [
            'album_id' => '专辑',
            'video_id' => '视频资源',
            'image_id' => '封面',
            'category_id' => '分类',
            'tag_ids' => '标签',
        ];
    }
}
