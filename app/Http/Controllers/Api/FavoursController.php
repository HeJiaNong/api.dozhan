<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\FormRequest as Request;
use App\Transformers\FavourTransformer;

class FavoursController extends Controller
{
    public function __construct()
    {
        $this->middleware('api.auth')->except(['']);;
    }

    public function meIndex(){
        return $this->response->collection($this->user()->favours,new FavourTransformer());
    }
}
