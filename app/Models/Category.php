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

    /*
     * testing获取此模型的资源文件
     */
    public function resources(){
        return $this->morphMany(Resource::class,'modelable');
    }

    /*
     * 获取此分类的图标资源
     */
    public function cover(){
        return $this->belongsTo(Resource::class,'cover_id');
    }
}
