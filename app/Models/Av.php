<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Av extends Model
{
    protected $fillable = ['name','description','user_id','album_id','url_id','cover_id'];

    //模型关联
    public function tag(){
        return $this->belongsToMany(Tag::class);
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
        return $this->belongsTo(Video::class,'url_id');
    }

    //模型关联
    public function comment(){
        return $this->hasMany(Comment::class);
    }

    //模型关联
    public function image(){
        return $this->hasOne(Image::class,'cover_id');
    }
}