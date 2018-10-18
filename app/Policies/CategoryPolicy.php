<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CategoryPolicy
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
        if ($user->hasPermissionTo('manage_categories')) {
            return true;
        }
    }

    //新增权限
    public function create(){
        return true;
    }

    //更新权限
    public function update(){
        return true;
    }

    //删除权限
    public function destroy(){
        return true;
    }
}
