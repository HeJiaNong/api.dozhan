<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TestController extends Controller
{
    public function store(){
        dd(Auth::guard('api')->factory()->getTTL() * 60);
        return $this->response->array([
            'test' => 'hello',
        ]);
    }
}
