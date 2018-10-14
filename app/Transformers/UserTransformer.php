<?php
namespace App\Transformers;

use App\Models\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    public function transform(User $user){
        return [
            'id' => $user->id,
            'do_id' => $user->do_id,
            'name' => $user->name,
            'phone_number' => $user->phone_number ? true : false,
            'qq_number' => $user->qq_number ? true : false,
            'created_at' => $user->created_at->toDateTimeString(),
            'updated_at' => $user->updated_at->toDateTimeString(),
        ];
    }
}