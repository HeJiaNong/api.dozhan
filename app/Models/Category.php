<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name','cover','description'];

    /*
     * 获取此分类下的所有作品
     */
    public function works(){
        return $this->hasMany(Work::class);
    }
}
