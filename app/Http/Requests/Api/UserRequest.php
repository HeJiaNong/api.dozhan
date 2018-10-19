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
                    'code' => 'required|string',
                    'key' => 'required|string',
                ];
                break;
            case 'PATCH':
                $userId = Auth::guard('api')->id();
                return [
                    'name' => 'between:3,25|unique:users,name,' .$userId,    //unique:table,column,except,idColumn
                    'avatar' => 'string',   //图片链接地址
                    'phone_number' => 'string|unique:users,phone_number,' .$userId,    //强迫 Unique 规则忽略指定 ID
                    'qq_number' => 'string|unique:users,qq_number,' .$userId,    //强迫 Unique 规则忽略指定 ID
                ];
                break;
            default :
                return [];
                break;
        }


    }

    public function messages()
    {
        return [
            'name.unique.unique' => '名称不能重复',
            'phone_number.unique' => '手机号码不能重复',
            'qq_number.unique' => 'QQ号码不能重复',
        ];
    }
}