<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $fillable = ['user_id','type','mime','url','bucket'];

    public function user(){
        $this->belongsTo(User::class);
    }
}
