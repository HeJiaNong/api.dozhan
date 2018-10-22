<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Av extends Model
{
    protected $fillable = ['name','description','user_id','album_id','video_id','image_id','category_id','comment_count'];

    //模型关联
    public function tag(){
        return $this->belongsToMany(Tag::class);
    }

    //模型关联
    public function category(){
        return $this->morphToMany(Category::class,'categorygable');
    }

    //模型关联
    public function album(){
        return $this->belongsTo(Album::class);
    }

    //模型关联
    public function user(){
        return $this->belongsTo(User::class);
    }

    //模型关联
    public function video(){
        return $this->belongsTo(Video::class);
    }

    //模型关联
    public function comment(){
        //这里只查询1级评论
        return $this->hasMany(Comment::class)->where('parent_id',null);
    }

    //模型关联
    public function image(){
        return $this->belongsTo(Image::class);
    }
}
