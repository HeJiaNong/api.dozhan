<?php
namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use Spatie\Permission\Models\Role;

class RoleTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['permissions'];

    public function transform(Role $role){
        return [
            'id' => $role->id,
            'name' => $role->name,
            'guard_name' => $role->guard_name,
            'created_at' => $role->created_at->toDateTimeString(),
            'updated_at' => $role->updated_at->toDateTimeString(),
        ];
    }

    public function includePermissions(Role $role){
        return $this->collection($role->permissions,new PermissionTransformer());
    }
}