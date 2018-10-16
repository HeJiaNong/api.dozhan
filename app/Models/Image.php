<?php

namespace App\Models;

use App\Handlers\QiniuCloudHandler;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $fillable = ['user_id','scene','mime','key','bucket'];

    //访问器
    public function getKeyAttribute($value)
    {
        $qiniu = new QiniuCloudHandler();
        //拼接url
        return $qiniu->qiniuDomain . '/' . $value;
    }

    //模型关联
    public function user(){
        $this->belongsTo(User::class);
    }
}
