<?php

namespace App\Http\Requests\Api;

use App\Models\ResourceQiniu;
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
                    //必须，格式为uuid，字段video_id唯一，存在于资源表
                    'video_id' => 'required|uuid|unique:works,video_id|exists:qiniu_resources,id',
                    'cover_id' => 'required|uuid|unique:works,cover_id|exists:qiniu_resources,id',
                    'tags' => 'json',
                ];
                break;
            case 'PATCH':
                return [
                    'name' => 'string|unique:works,name,'.$this->work->id,
                    'description' => 'string|max:255',
                    'category_id' => 'integer|exists:categories,id',
                    'cover_id' => 'uuid|exists:qiniu_resources,id|unique:works,cover_id,'.$this->work->id,
                    'tags' => 'json',
                ];
                break;
        }
    }

    /**
     *  配置验证器实例。
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        switch ($this->method()){
            case 'POST':
                if ($validator->errors()->count() == 0) {
                    //验证后钩子
                    $validator->after(function ($validator) {
                        //视频资源和封面资源不相同
                        if ($validator->attributes()['video_id'] == $validator->attributes()['cover_id']) {
                            $validator->errors()->add('resource', '资源重复');
                        }
                        $video = ResourceQiniu::find($validator->attributes()['video_id']);
                        $cover = ResourceQiniu::find($validator->attributes()['cover_id']);

                        //资源的所属用户必须是自己
                        if ((int)$video->endUser !== $this->user()->id) {
                            $validator->errors()->add('video', '这是别人的资源');
                        }
                        //资源的所属用户必须是自己
                        if ((int)$cover->endUser !== $this->user()->id) {
                            $validator->errors()->add('cover', '这是别人的资源');
                        }
                        //todo 资源必须没有被其他作品/分类图片/头像/等等..使用

                        //video_id必须是视频资源
                        if (strpos($video->mimeType, 'video/') === false) {
                            $validator->errors()->add('video', '资源格式错误');
                        }
                        //cover_id必须是图片资源
                        if (strpos($cover->mimeType, 'image/') === false) {
                            $validator->errors()->add('cover', '资源格式错误');
                        }
                    });
                }
                break;
            case 'PATCH':
                if ($validator->errors()->count() == 0) {
                    //验证后钩子
                    $validator->after(function ($validator) {
                        $cover = ResourceQiniu::find($validator->attributes()['cover_id']);
                        //资源的所属用户必须是自己
                        if ((int)$cover->endUser !== $this->user()->id) {
                            $validator->errors()->add('cover', '这是别人的资源');
                        }
                        //todo 资源必须没有被其他作品/分类图片/头像/等等..使用

                        //cover_id必须是图片资源
                        if (strpos($cover->mimeType, 'image/') === false) {
                            $validator->errors()->add('cover', '资源格式错误');
                        }
                    });
                }
                break;
        }

    }

    public function attributes(){
        return [
            'video_id' => '视频资源',
            'cover_id' => '封面资源',
            'category_id' => '分类',
            'tags' => '标签',
        ];
    }

    public function messages()
    {
        return [
            'video_id.uuid' => 'uuid格式错误',
            'cover_id.uuid' => 'uuid格式错误',
        ];
    }
}
