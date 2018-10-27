<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Favour extends Model
{
    protected $fillable = [];

    /*
     * 获取拥有此点赞的模型
     */
    public function favourable(){
        return $this->morphTo();
    }

    /*
     * 获取点赞的用户
     */
    public function user(){
        return $this->belongsTo(User::class);
    }
}
