<?php

namespace App\Policies;

use App\Models\Work;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class WorkPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    //过滤器
    public function before($user, $ability)
    {
        if ($user->hasPermissionTo('manage_works')) {
            return true;
        }
    }

    //更新权限
    public function update(User $user,Work $work){
        return $user->isAuthOf($work);
    }

    //删除权限
    public function destroy(User $user,Work $work){
        return $user->isAuthOf($work);
    }
}
