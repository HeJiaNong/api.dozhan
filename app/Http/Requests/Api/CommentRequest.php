<?php

namespace App\Http\Requests\Api;

class CommentRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        switch ($this->method()){
            case 'POST' :
                return [
                    'comment' => 'required|string|max:255',
                    'av_id' => 'required|integer|exists:avs,id',
                    'parent_id' => 'integer|exists:comments,id',
                    'target_id' => 'integer|exists:users,id',
                ];
                break;
            case 'PATCH':
                return [
                    'comment' => 'string|max:255',
                    'target_id' => 'integer|exists:users,id',
                ];
                break;
        }

    }

    public function attributes(){
        return [
            'comment' => '评论',
            'av_id' => '视频',
            'parent_id' => '父级评论',
            'target_id' => '目标用户',
        ];
    }
}
