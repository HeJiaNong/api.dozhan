<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = ['name','description'];

    //模型关联
    public function works(){
        return $this->belongsToMany(Work::class);
    }
}
