<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QiniuPersistent extends Model
{
    protected $fillable = [
        'id',
        'pipeline',
        'code',
        'desc',
        'reqid',
        'inputBucket',
        'inputKey',
        'items',
    ];

//    protected $primaryKey = 'id';

    protected $keyType = 'string';

    /*
     * 访问器
     */
    public function setItemsAttribute($value)
    {
        $this->attributes['items'] = json_encode($value);
    }

    public function getItemsAttribute($value)
    {
        return json_decode($value,true);
    }

    /*
     * 获取此持久化处理资源所属的原资源
     */
    public function qiniuResource(){
        return $this->belongsTo(QiniuResource::class,'id','persistent_id');
    }
}
