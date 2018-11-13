<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name','icon_id','description'];

    /*
     * 获取此分类下的所有作品
     */
    public function works(){
        return $this->hasMany(Work::class);
    }

    /*
     * 获取此分类的图标资源
     */
    public function icon(){
        return $this->belongsTo(Resource::class,'icon_id');
    }
}
