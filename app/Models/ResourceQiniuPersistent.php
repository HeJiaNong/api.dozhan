<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResourceQiniuPersistent extends Model
{
    protected $table = 'resources_qiniu_persistent';

    protected $fillable = ['id', 'pipeline', 'code', 'desc', 'reqid', 'inputBucket', 'inputKey', 'items',];

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    public $incrementing = false;

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
            //添加前端格式区分

            $format = 'other';

            if (strpos($item['cmd'],'avthumb/mp4') !== false){
                $format = 'mp4';
            }elseif (strpos($item['cmd'],'avthumb/m3u8') !== false){
                $format = 'm3u8';
            }elseif (strpos($item['cmd'],'format/webp') !== false){
                $format = 'webp';
            }

            $item['format'] = $format;
        }

        return $value;
    }

    /*
     * 获取此持久化处理资源所属的原资源
     */
    public function resource(){
        return $this->belongsTo(ResourceQiniu::class,'id','persistent_id');
    }
}
