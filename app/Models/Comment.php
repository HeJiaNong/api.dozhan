<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = ['comment','user_id','av_id','parent_id','target_id'];

    //模型关联
    public function user(){
        return $this->belongsTo(User::class);
    }

    //模型关联
    public function av(){
        return $this->belongsTo(Av::class);
    }

    //二级评论回复列表
    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    //目标用户
    public function target(){
        return $this->belongsTo(User::class,'target_id');
    }

}
