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
        return $user->hasPermissionTo('manage_users');
    }

    public function destroy(User $user){
        return false;
    }

    public function update(User $user){
        return $user->isAuthOf($user);
    }
}
