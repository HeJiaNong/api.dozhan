<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable implements JWTSubject
{
    use HasRoles;
    use Notifiable{
        notify as protected laravelNotify;
    }
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = ['name','avatar_id','introduction','phone','qq'];

    protected $hidden = ['password','auth_token',];

    protected $guard_name = 'api';

    /*
     * 对laravel消息通知的notify方法的优化
     */
    public function notify($instance){
        //如果要通知的人是自己，就不用通知了
        if ($this->id == Auth::guard('api')->user()->id){
            return;
        }

        $this->increment('notification_count');
        $this->laravelNotify($instance);
    }

    /*
     * 返回了 User 的 id
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /*
     * 需要额外再 JWT 载荷中增加的自定义内容
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /*
     * 验证模型的对应用户是否与当前登陆用户一致
     */
    public function isAuthOf($model)
    {
        return $model->user_id == $this->id;
    }

    /*
     * 获取此用户的所有资源文件
     */
    public function resources(){
        return $this->hasMany(Resource::class);
    }

    /*
     * 获取此用户下的所有评论
     */
    public function comments(){
        return $this->hasMany(Comment::class);
    }

    /*
     * 获取此用户下的所有作品
     */
    public function works(){
        return $this->hasMany(Work::class);
    }

    /*
     * 获取此用户点过的赞
     */
    public function favours(){
        return $this->hasMany(Favour::class);
    }

    public function avatar(){
        return $this->belongsTo(Resource::class,'avatar_id');
    }

}
