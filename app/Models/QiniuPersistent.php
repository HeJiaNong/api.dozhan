<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QiniuPersistent extends Model
{
    protected $fillable = [];

    public function qiniuResource(){
        return $this->belongsTo(QiniuResource::class,'id','persistent_id');
    }
}
