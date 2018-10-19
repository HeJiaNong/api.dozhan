<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable implements JWTSubject
{
    use HasRoles;   //用户权限管理包
    use Notifiable;
    use SoftDeletes;    //启用软删除

//    protected $guard_name = 'api';    //或者你想要使用的任何警卫

    /**
     * 需要被转换成日期的属性。
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    //可写入字段
    protected $fillable = [
        'do_id','name','avatar','email','phone_number','qq_number','password',
    ];

    //隐藏字段
    protected $hidden = [
        'password', 'remember_token',
    ];

    //返回了 User 的 id
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    //需要额外再 JWT 载荷中增加的自定义内容
    public function getJWTCustomClaims()
    {
        return [];
    }

    //模型关联
    public function album(){
        return $this->hasMany(Album::class);
    }

    //模型关联
    public function av(){
        return $this->hasMany(Av::class);
    }

    //模型关联
    public function image(){
        return $this->hasMany(Image::class);
    }

    //模型关联
    public function video(){
        return $this->hasMany(Video::class);
    }

    //模型关联
    public function comment(){
        return $this->hasMany(Comment::class);
    }

    //验证模型的对应用户是否与当前登陆用户一致
    public function isAuthOf($model)
    {
        return $model->user_id == $this->id;
    }

}
