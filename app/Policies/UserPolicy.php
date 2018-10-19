<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
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

    public function before(User $user){
        if ($user->hasPermissionTo('manage_users')) {
            return true;
        }
    }

    public function destroy(User $user){
        return false;
    }

    public function update(User $user,$manageUser){
        //如果操作的用户和当前登陆用户一直才允许更新
        return $manageUser->id == $user->id;
    }
}
