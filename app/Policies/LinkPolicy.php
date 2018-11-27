<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LinkPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //dw
    }

    public function before($user, $ability){
        if ($user->hasPermissionTo('manage_links')) {
            return true;
        }
    }

    public function create(){
        return false;
    }

    public function update(){
        return false;
    }

    public function destroy(){
        return false;
    }
}
