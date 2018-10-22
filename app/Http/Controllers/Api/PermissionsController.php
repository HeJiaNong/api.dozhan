<?php

namespace App\Http\Controllers\Api;

use App\Transformers\PermissionTransformer;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionsController extends Controller
{
    //获取权限列表
    public function index(){
        $this->authorize('isAdmin',Permission::class);
        return $this->response->collection(Permission::all(),new PermissionTransformer());
    }

    //获取当前用户的权限
    public function me(){
        return $this->response->collection($this->user()->getAllPermissions(),new PermissionTransformer());
    }
}
