<?php
namespace App\Observers;

// creating, created, updating, updated, saving,
// saved,  deleting, deleted, restoring, restored
use App\Models\User;

class UserObserver
{
    public function creating(User $user){
        $do_id = uniqid('do_');

        //为新用户生成一个 do_id
        $user->do_id = $do_id;
        $user->name = $do_id;

//        dd($user);
    }
}