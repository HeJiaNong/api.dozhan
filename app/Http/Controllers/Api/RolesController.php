<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Transformers\RoleTransformer;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesController extends Controller
{
    //获取所有角色
    public function index(){
        $this->authorize('isAdmin',Permission::class);

        return $this->response->collection(Role::all(),new RoleTransformer());
    }

    //获取当前用户的角色
    public function me(){
        $this->authorize('isAdmin',Permission::class);

        return $this->response->array($this->user()->getRoleNames()->toArray());
    }

}
