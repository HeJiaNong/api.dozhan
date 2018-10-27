<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class JsontoArrExists implements Rule
{
    protected $table;
    protected $columns;
    protected $attribute;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($table,$columns = 'id')
    {
        $this->table = $table;
        $this->columns = $columns;
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
        $this->attribute = '标签';
        $arr = json_decode($value,true);

        //获取指定表所有指定字段
        $data = DB::table($this->table)->pluck($this->columns)->toArray();

        if (!empty($arr) && is_array($arr)){
            foreach ($arr as $v){
                if (!in_array($v,$data)){
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->attribute . ' 不存在';
    }
}
