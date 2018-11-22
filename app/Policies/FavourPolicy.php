<?php

namespace App\Policies;

use App\Models\Favour;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FavourPolicy
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

    public function destroy(User $user,Favour $favour){
        return $user->isAuthOf($favour);
    }
}
