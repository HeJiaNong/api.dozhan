<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Comment extends Model
{
    protected $fillable = ['content'];

    protected static function boot()
    {
        parent::boot();

        //添加全局作用域 最热评论排序
        static::addGlobalScope('hottest', function (Builder $builder) {
            /*
             * TODO 热评排序算法
             * 1.时间倒序排序
             * 2.
             */
            $builder->orderByDesc('favour_count')->orderByDesc('created_at');
        });
    }

    /*
     * 获取拥有此评论的模型
     */
    public function commentable(){
        return $this->morphTo();
    }

    /*
     * 获取此评论对应的用户
     */
    public function user(){
        return $this->belongsTo(User::class);
    }

    /*
     * 获取此评论的所有点赞
     */
    public function favours(){
        return $this->morphMany(Favour::class,'favourable');
    }

    /*
     * 获取此评论的回复
     */
    public function replies(){
        return $this->hasMany(Comment::class,'parent_id');
    }

    /*
     * 获取此评论的目标用户
     */
    public function target(){
        return $this->belongsTo(User::class,'target_id');
    }
}


