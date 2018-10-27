<?php

namespace App\Http\Requests\Api;

use Illuminate\Database\Eloquent\Relations\Relation;

class CommentRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        if (!$commentable_type = Relation::getMorphedModel($this->commentable_type)){
            abort(422,'模型类型 错误');
        }
//        dd(2);
        switch ($this->method()){

            case 'POST' :
                return [
                    'content' => 'required|string|max:255',
                    'commentable_id' => 'required|integer|exists:'.$this->commentable_type.',id',
                    'commentable_type' => 'required|string',
                    'parent_id' => 'integer|exists:comments,id',
                    'target_id' => 'integer|exists:users,id',
                ];
                break;
            case 'PATCH':
                return [
                    'content' => 'string|max:255',
                ];
                break;
        }

    }

    public function attributes(){
        return [
            'content' => '评论内容',
            'commentable_id' => '模型id',
            'parent_id' => '父级评论',
            'target_id' => '目标用户',
        ];
    }
}
