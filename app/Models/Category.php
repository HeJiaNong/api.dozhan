<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name','description'];

    //模型关联
    public function album(){
        return $this->hasMany(Album::class);
    }

    //模型关联
    public function av(){
        return $this->hasMany(Av::class);
    }
}
