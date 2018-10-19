<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name','description'];

    //模型关联
    public function albums(){
        return $this->morphedByMany(Album::class, 'categorygable');
    }

    //模型关联
    public function avs(){
        return $this->morphedByMany(Av::class, 'categorygable');
    }

}
