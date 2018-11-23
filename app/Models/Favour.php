<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Favour extends Model
{
    protected $fillable = ['user_id'];

    protected static function boot()
    {
        parent::boot();

        //添加全局作用域 最新点赞排序
        static::addGlobalScope('latest', function (Builder $builder) {
            $builder->latest();
        });
    }

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
