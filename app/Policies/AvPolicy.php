<?php

namespace App\Policies;

use App\Models\Av;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AvPolicy
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
        if ($user->hasPermissionTo('manage_avs')) {
            return true;
        }
    }

    //更新权限
    public function update(User $user,Av $av){
        return $user->isAuthOf($av);
    }

    //删除权限
    public function destroy(User $user,Av $av){
        return $user->isAuthOf($av);
    }
}
