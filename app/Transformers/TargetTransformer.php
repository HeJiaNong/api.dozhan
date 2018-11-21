<?php
namespace App\Transformers;

use App\Models\User;
use League\Fractal\TransformerAbstract;

class TargetTransformer extends TransformerAbstract
{
    public function transform(User $user){
        return [
            'id' => $user->id,
            'name' => htmlspecialchars($user->name),
            'introduction' => htmlspecialchars($user->introduction),
            'avatar' => $user->avatar,
        ];
    }
}