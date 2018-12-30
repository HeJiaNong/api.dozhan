<?php

namespace App\Models;

use App\Contracts\DoResource;
use Illuminate\Database\Eloquent\Model;

class Resource extends Model implements DoResource
{
    protected $keyType = 'string';

    public $incrementing = false;

    /*
     * 获取此资源对应用户
     */
    public function user(){
        return $this->belongsTo(User::class);
    }

    /*
     * 多态关联
     */
    public function resourceable(){
        return $this->morphTo();
    }

    /*
     * 展示链接
     */
    public function show(){
        //view,create,update,delete
        return $this->resourceable->show();
    }

    /*
     * 下载链接
     */
    public function download()
    {
        // TODO: Implement download() method.
    }
}
