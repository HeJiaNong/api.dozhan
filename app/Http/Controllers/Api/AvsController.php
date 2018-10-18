<?php

namespace App\Http\Controllers\Api;

use App\Models\Av;
use App\Transformers\AvTransformer;
use Illuminate\Http\Request;

class AvsController extends Controller
{
    //获取视频列表
    public function index(){
        return $this->response->paginator(Av::paginate(20),new AvTransformer());
    }
}
