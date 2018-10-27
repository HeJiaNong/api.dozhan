<?php

namespace App\Models;

use App\Handlers\QiniuCloudHandler;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $fillable = [];

    /*
     * 将属性 key 拼接为url
     */
    public function getKeyAttribute($value)
    {
        $qiniu = new QiniuCloudHandler();
        //拼接url
        return $qiniu->qiniuDomain . '/' . $value;
    }

    /*
     * 获取资源所属用户
     */
    public function user(){
        $this->belongsTo(User::class);
    }
}
