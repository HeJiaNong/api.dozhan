<?php

namespace App\Models;

use App\Notifications\Follow;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
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

        //未读消息总数++
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
     * 获取手机号属性
     */
    public function getPhoneAttribute($value){
        return substr_replace($value,'****',3,4);
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

    /*
     * 获取此用户的头像
     */
    public function avatar(){
        return $this->belongsTo(Resource::class,'avatar_id');
    }

    /*
     * 获取此用户的所有粉丝
     */
    public function followers(){
        return $this->belongsToMany(User::class,'followers','user_id','follower_id');
    }

    /*
     * 获取此用户关注了那些人
     */
    public function followings(){
        return $this->belongsToMany(User::class,'followers','follower_id','user_id');
    }

    /*
     * 批量订阅
     */
    public function follow(array $user_ids)
    {
        foreach ($user_ids as $k => $id){
            //不能订阅自己
            $this->id == $id ? eval('unset($user_ids[$k]);') : $id;
        }

        $users = User::find($this->followings()->sync($user_ids, false)['attached']);

        //被订阅用户粉丝数+1
        $users->each(function ($model,$index){
            $model->increment('followers_count');
        });

        //订阅消息通知
        if ($users->isNotEmpty()){
            \Notification::send($users,new Follow($this));
        }

    }

    /*
     * 批量取消订阅
     */
    public function unfollow(array $user_ids)
    {
        $followings = $this->followings()->pluck('id')->all();

        if (!empty(array_diff($user_ids,$followings))){
            return abort('422','参数错误');
        }

        $this->followings()->detach($user_ids);

        //被取消订阅用户粉丝数-1
        User::find($user_ids)->each(function ($model,$index){
            $model->decrement('followers_count');
        });

        //TODO 取消订阅消息通知
    }

}
