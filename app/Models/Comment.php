<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = ['comment'];

    //模型关联
    public function user(){
        return $this->belongsTo(User::class);
    }

    //模型关联
    public function av(){
        return $this->belongsTo(Av::class);
    }
}
