<?php

namespace App\Policies;

use App\Models\Album;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AlbumPolicy
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

    //更新权限
    public function update(User $user,Album $album){
        return $user->isAuthOf($album);
    }

    //删除权限
    public function destroy(User $user,Album $album){
        return $user->isAuthOf($album);
    }
}
