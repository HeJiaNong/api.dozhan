<?php
namespace App\Transformers;

use App\Models\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    //同模型关联
    protected $availableIncludes = ['resources','comments','works','favours'];

    public function transform(User $user){
        return [
            'id' => $user->id,
            'do_id' => $user->do_id,
            'name' => $user->name,
            'introduction' => $user->introduction,
            'avatar' => $user->avatar,
            'email' => $user->email,
            'phone' => $user->phone ? substr_replace($user->phone,'****',3,4) : false,
            'qq' => $user->qq ??false,
            'notification_count' => $user->notification_count,
            'created_at' => $user->created_at->toDateTimeString(),
            'updated_at' => $user->updated_at->toDateTimeString(),
        ];
    }

    public function includeResources(User $user){
        return $this->collection($user->resources,new ResourceTransformer());
    }

    public function includeComments(User $user){
        return $this->collection($user->comments,new CommentTransformer());
    }

    public function includeWorks(User $user){
        return $this->collection($user->works,new WorkTransformer());
    }

    public function includeFavours(User $user){
        return $this->collection($user->favours,new FavourTransformer());
    }

}