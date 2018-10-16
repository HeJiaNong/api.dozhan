<?php

namespace App\Models;

use App\Handlers\QiniuCloudHandler;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $fillable = ['user_id','mime','key','bucket'];

    //模型关联
    public function user(){
        $this->belongsTo(User::class);
    }

    //模型关联
    public function av(){
        $this->hasOne(Av::class);
    }

    //访问器
    public function getKeyAttribute($value)
    {
        $qiniu = new QiniuCloudHandler();
        //拼接url
        return $qiniu->qiniuDomain . '/' . $value;
    }
}
