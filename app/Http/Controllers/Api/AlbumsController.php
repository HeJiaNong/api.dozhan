<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Transformers\AlbumTransformer;
use Illuminate\Http\Request;

class AlbumsController extends Controller
{
    public function userIndex(User $user,Request $request){
        $albums = $user->album();
        $albums = $albums->paginate(2);
        return $this->response->paginator($albums,new AlbumTransformer());
    }
}
