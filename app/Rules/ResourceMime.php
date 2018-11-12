<?php

namespace App\Rules;

use App\Models\Resource;
use Illuminate\Contracts\Validation\Rule;

class ResourceMime implements Rule
{
    private $mime;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($mime)
    {
        $this->mime = $mime;
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
        return (bool)Resource::where([['id',$value],['mime','like',str_replace('*','%',$this->mime)]])
            ->count();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return '资源mime类型错误';
    }
}
