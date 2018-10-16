<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = ['name','description'];

    //模型关联
    public function av(){
        return $this->belongsToMany(Av::class);
    }
}
