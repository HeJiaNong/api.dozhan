<?php

namespace App\Rules;

use App\Models\Resource;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class ResourceAuthor implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        //当前登陆用户id
        $userId = Auth::guard('api')->user()->id;
        return (bool)Resource::where([['id',$value],['user_id',$userId]])
            ->count();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return '资源用户不一致';
    }

}
