<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Album extends Model
{
    protected $fillable = ['name','description','user_id','category_id'];

    //模型关联
    public function av(){
        return $this->hasMany(Av::class);
    }

    //模型关联
    public function user(){
        return $this->belongsTo(User::class);
    }

    //模型关联
    public function category(){
        return $this->belongsTo(Category::class);
    }
}
