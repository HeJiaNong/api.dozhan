<?php

namespace App\Rules;

use App\Models\Resource;
use Illuminate\Contracts\Validation\Rule;

class ResourceSingle implements Rule
{
    private $exceptModelId;
    private $exceptModelType;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($exceptModelId = null,$exceptModelType = null)
    {
        $this->exceptModelId = $exceptModelId;
        $this->exceptModelType = $exceptModelType;
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
        if ($this->exceptModelId && $this->exceptModelType){
            return Resource::where([['id',$value],['modelable_id',null],['modelable_type',null]])
                ->orWhere([['id',$value],['modelable_id',$this->exceptModelId],['modelable_type',$this->exceptModelType]])
                ->count();
        }

        return Resource::where([['id',$value],['modelable_id',$this->exceptModelId],['modelable_type',$this->exceptModelType]])
            ->count();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return '资源已被使用';
    }
}
