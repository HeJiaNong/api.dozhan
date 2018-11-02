<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QiniuResource extends Model
{
    //设置主键
    protected $primaryKey = 'uuid';

    protected $keyType = 'string';

    protected $fillable = [
        'params',
        'uuid',
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
     * 修改器
     */
    public function setExifAttribute($value)
    {
        $this->attributes['exif'] = json_encode(($value));
    }

    /*
     * 修改器
     */
    public function setImageAveAttribute($value)
    {
        $this->attributes['imageAve'] = json_encode(($value));
    }

    /*
     * 修改器
     */
    public function setImageInfoAttribute($value)
    {
        $this->attributes['imageInfo'] = json_encode(($value));
    }

    /*
     * 修改器
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
