<?php
namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use Spatie\Permission\Models\Permission;

class PermissionTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['roles'];

    public function transform(Permission $permission){
        return [
            'id' => $permission->id,
            'name' => $permission->name,
            'guard_name' => $permission->guard_name,
            'created_at' => $permission->created_at->toDateTimeString(),
            'updated_at' => $permission->updated_at->toDateTimeString(),
        ];
    }

    public function includeRoles(Permission $permission){
        return $this->collection($permission->roles,new RoleTransformer());
    }
}