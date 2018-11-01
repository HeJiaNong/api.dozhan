<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QiniuResource extends Model
{
//    protected $table = 'qiniu_resources';

    protected $fillable = [];

    public function user(){
        $this->belongsTo(User::class,'endUser');
    }
}
