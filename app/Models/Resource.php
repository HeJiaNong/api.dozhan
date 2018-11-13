<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    protected $keyType = 'string';

    public $incrementing = false;

    /*
     * 获取此资源对应用户
     */
    public function user(){
        return $this->belongsTo(User::class);
    }

    /*
     * 多态关联
     */
    public function resourceable(){
        return $this->morphTo();
    }
}
