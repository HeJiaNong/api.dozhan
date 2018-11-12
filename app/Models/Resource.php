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

    //todo 获取使用此资源的模型
    //todo 方案1 这个可以同时使用多态关联和1对多反向关联，从而达到区分资源的所属模型和一个模型多个字段多个资源
    public function modelable(){
        return $this->morphTo();
    }
}
