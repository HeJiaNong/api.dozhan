<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UserRequest extends FormRequest
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
                    'name' => 'required|string|max:255',
                    'password' => 'required|string|min:6',
                ];
                break;
            case 'PATCH':
                $userId = Auth::guard('api')->id();
                return [
                    'name' => 'between:3,25|unique:users,name,' .$userId,    //unique:table,column,except,idColumn
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