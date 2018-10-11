<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    //可写入字段
    protected $fillable = [
        'do_id','name','email','phone_number','qq_number','password',
    ];

    //隐藏字段
    protected $hidden = [
        'password', 'remember_token',
    ];
}
