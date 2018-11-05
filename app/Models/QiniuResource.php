<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QiniuResource extends Model
{
    //设置主键
    protected $primaryKey = 'id';

    protected $keyType = 'string';

    //因为id是uuid，所以这个自增要设置为false，不然id查询出来是0
    public $incrementing = false;

    protected $fillable = [
        'params',
        'id',
        'endUser',
        'persistentId',
        'bucket',
        'key',
        'etag',
        'fsize',
        'mimeType',
        'imageAve',
        'ext',
        'exif',
        'imageInfo',
        'avinfo',
    ];

    /*
     * 访问器，拼接 key 为链接地址
     */
    public function getKeyAttribute($value){
        return config('services.qiniu.domain').'/'.$value;
    }

    /*
     * 修改器,将值转换为json格式
     */
    public function setExifAttribute($value)
    {
        $this->attributes['exif'] = json_encode(($value));
    }

    /*
     * 修改器,将值转换为json格式
     */
    public function setImageAveAttribute($value)
    {
        $this->attributes['imageAve'] = json_encode(($value));
    }

    /*
     * 修改器,将值转换为json格式
     */
    public function setImageInfoAttribute($value)
    {
        $this->attributes['imageInfo'] = json_encode(($value));
    }

    /*
     * 修改器,将值转换为json格式
     */
    public function setAvinfoAttribute($value)
    {
        $this->attributes['avinfo'] = json_encode(($value));
    }

    /*
     * 获取此资源对应用户
     */
    public function user(){
        return $this->belongsTo(User::class,'endUser');
    }

    /*
     * 获取此资源的持久化处理资源
     */
    public function persistent(){
        return $this->belongsTo(QiniuPersistent::class,'persistentId');
    }

}
