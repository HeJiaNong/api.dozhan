<?php
namespace App\Observers;

// creating, created, updating, updated, saving,
// saved,  deleting, deleted, restoring, restored
use App\Models\User;

class UserObserver
{
    public function creating(User $user){
        $do_id = uniqid('do_');

        //如果未设置 do_id 生成一个
        if (!isset($user->do_id)){
            $user->do_id = $do_id;
        }

        //如果未设置 name 则赋值为 do_id
        if (!isset($user->name)){
            $user->name = $do_id;
        }

    }

    public function saving(User $user){
        //todo 用户默认头像
        if (!$user->avatar_id){
            //TODO 默认头像方案待优化,这里由于所有资源都是唯一id形式，默认头像却是可以重复的
            //为未设置头像的用户添加默认头像
            $user->avatar_id = \App\Models\ResourceQiniu::where('key','like','seeder/avatar/%')->limit(1)->first()->resource->id;
        }
    }

}