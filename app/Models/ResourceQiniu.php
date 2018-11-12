<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResourceQiniu extends Model
{
    //声明表名
    protected $table = 'resources_qiniu';

    protected $fillable = [
        'params',
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
     * 获取此资源的展示链接
     */
    public function showUrl(ResourceQiniu $resourceQiniu){

        //默认为原文件url
        $url = $resourceQiniu->key;

        //如果该资源有持久化处理资源
        if ($persistent = $resourceQiniu->persistent);{
            foreach ($persistent->items as $item){
                if ($item['code'] == 0){
                    //对每个item进行筛选
                    //todo 这里暂时没有pick
                    $url = $item['key'];
                }
            }
        }

        return compact('url');
    }




    /*
     * 访问器，拼接 key 为链接地址
     */
    public function getKeyAttribute($value){
        return config('services.qiniu.domain').'/'.$value;
    }

    /*
     * 将参数转为数组
     */
    public function getParamsAttribute($value){
        return json_decode($value,true);
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
     * 获取七牛资源的持久化处理资源
     */
    public function persistent(){
        return $this->belongsTo(ResourceQiniuPersistent::class,'persistentId');
    }

    /*
     * 中继器
     */
    public function resource(){
        return $this->morphOne(Resource::class,'resourceable');
    }

}
