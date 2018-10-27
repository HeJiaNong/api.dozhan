<?php
namespace App\Transformers;

use App\Models\User;
use League\Fractal\TransformerAbstract;

class TargetTransformer extends TransformerAbstract
{
    public function transform(User $user){
        return [
            'id' => $user->id,
            'name' => $user->name,
            'introduction' => $user->introduction,
            'avatar' => $user->avatar,
        ];
    }
}