<?php
namespace App\Transformers;

use App\Models\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['album','image','comment','video','av'];

    public function transform(User $user){
        return [
            'id' => $user->id,
            'do_id' => $user->do_id,
            'name' => $user->name,
            'avatar' => $user->avatar,
            'phone_number' => $user->phone_number ? true : false,
            'qq_number' => $user->qq_number ? true : false,
            'created_at' => $user->created_at->toDateTimeString(),
            'updated_at' => $user->updated_at->toDateTimeString(),
        ];
    }

    public function includeAlbum(User $user){
        return $this->collection($user->album,new AlbumTransformer());
    }

    public function includeImage(User $user){
        return $this->collection($user->image,new ImageTransformer());
    }

    public function includeComment(User $user){
        return $this->collection($user->comment,new CommentTransformer());
    }

    public function includeVideo(User $user){
        return $this->collection($user->video,new VideoTransformer());
    }

    public function includeAv(User $user){
        return $this->collection($user->av,new AvTransformer());
    }
}