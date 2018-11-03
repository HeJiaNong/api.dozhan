<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QiniuPersistent extends Model
{
    protected $fillable = ['id', 'pipeline', 'code', 'desc', 'reqid', 'inputBucket', 'inputKey', 'items',];

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    /*
     * 将值json化
     */
    public function setItemsAttribute($value)
    {
        $this->attributes['items'] = json_encode($value);
    }

    /*
     * 将json解析为数组
     */
    public function getItemsAttribute($value)
    {
        $value = json_decode($value,true);

        foreach ($value as &$item){
            //限制展示字段
            $item = array_intersect_key($item,array_flip(['cmd','key']));
            //将key拼接成链接
            $item['key'] = config('services.qiniu.domain').'/'.$item['key'];
        }

        return $value;
    }

    /*
     * 获取此持久化处理资源所属的原资源
     */
    public function qiniuResource(){
        return $this->belongsTo(QiniuResource::class,'id','persistent_id');
    }
}
