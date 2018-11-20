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
        switch ($this->method()){

            case 'POST' :
                return [
                    'content' => 'required|string|max:255',
                    'parent_id' => 'required_with:target_id|integer|exists:comments,id',
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
            'parent_id' => '父级评论',
            'target_id' => '目标用户',
        ];
    }
}
