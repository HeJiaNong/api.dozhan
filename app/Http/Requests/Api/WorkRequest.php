<?php

namespace App\Http\Requests\Api;

use App\Models\Resource;
use App\Models\ResourceQiniu;
use App\Rules\JsontoArrExists;
use App\Rules\ResourceAuthor;
use App\Rules\ResourceMime;
use App\Rules\ResourceSingle;
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
                    'video_id' => [
                        'required',                         //字段必须
                        'uuid',                             //值必须是uuid格式
                        'different:cover_id',               //与cover_id字段值不同
                        new ResourceAuthor(),               //资源用户必须和当前用户一致
                        new ResourceMime('video/*'), //资源类型限制
                        new ResourceSingle(),               //该资源没有被其他模型所使用过
                    ],
                    'cover_id' => [
                        'required',                         //字段必须
                        'uuid',                             //值必须是uuid格式
                        'different:video_id',               //与cover_id字段值不同
                        new ResourceAuthor(),               //资源用户必须和当前用户一致
                        new ResourceMime('image/*'), //资源类型限制
                        new ResourceSingle(),               //该资源没有被其他模型所使用过
                    ],
                    'tags' => 'json',
                ];
                break;
            case 'PATCH':
                return [
                    'name' => 'string|unique:works,name,'.$this->work->id,
                    'description' => 'string|max:255',
                    'category_id' => 'integer|exists:categories,id',
                    'cover_id' => [
                        'uuid',                             //值必须是uuid格式
                        new ResourceAuthor(),               //资源用户必须和当前用户一致
                        new ResourceMime('image/*'), //资源类型限制
                        new ResourceSingle($this->work->id,$this->work->getTable()),    //该资源没有被其他模型所使用过
                    ],
                    'tags' => 'json',
                ];
                break;
        }
        return [];
    }

    public function attributes(){
        return [
            'video_id' => '视频资源',
            'cover_id' => '封面资源',
            'category_id' => '分类',
            'tags' => '标签',
        ];
    }

    public function messages(){
        return [
            'video_id.uuid' => 'uuid格式错误',
            'cover_id.uuid' => 'uuid格式错误',
        ];
    }

}
