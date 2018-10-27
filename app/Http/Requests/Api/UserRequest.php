<?php

namespace App\Http\Requests\Api;

use Illuminate\Support\Facades\Auth;

class UserRequest extends FormRequest
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
                    'email' => 'required|email|max:255|unique:users',    //unique:table,column,except,idColumn
                    'password' => 'required|string|min:6',
                    'code' => 'required|integer',
                    'key' => 'required|string',
                ];
                break;
            case 'PATCH':
                $userId = $this->user()->id;
                if ($this->user){
                    //管理员操作
                    $userId = $this->user->id;
                }
                return [
                    'name' => 'between:3,25|unique:users,name,' .$userId,    //unique:table,column,except,idColumn
                    'avatar' => 'string',   //图片链接地址
                    'phone' => 'integer|unique:users,phone,' .$userId,    //强迫 Unique 规则忽略指定 ID
                    'qq' => 'integer|unique:users,qq,' .$userId,    //强迫 Unique 规则忽略指定 ID
                ];
                break;
        }


    }

    public function messages()
    {
        return [
            'name.unique.unique' => '名称不能重复',
            'phone.unique' => '手机号码不能重复',
            'qq.unique' => 'QQ号码不能重复',
        ];
    }
}