<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = ['content'];

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

    public function target(){
        return $this->belongsTo(User::class,'target_id');
    }
}


