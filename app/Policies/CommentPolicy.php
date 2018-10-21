<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CommentPolicy
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
        if ($user->hasPermissionTo('manage_comments')) {
            return true;
        }
    }

    public function update(User $user,Comment $comment){
        return $user->isAuthOf($comment);
    }

    public function destroy(User $user,Comment $comment){
        return $user->isAuthOf($comment);
    }
}
