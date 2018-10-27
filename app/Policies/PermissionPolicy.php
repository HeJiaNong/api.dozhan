<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PermissionPolicy
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
    public function before($user,$ability)
    {
        if ($user->hasPermissionTo('manage_permissions')) {
            return true;
        }
    }

    public function isAdmin(){
        return false;
    }

    public function update(User $user){
        return $user->hasRole(['Founder']);
    }
}
