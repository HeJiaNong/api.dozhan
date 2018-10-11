<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class TestApiController extends Controller
{
    public function store(){
//        $res = Cache::put('ss','qqqqqqqqqqqq',30);
        $res = Cache::get('ss');

        dd($res) ;
    }
}
