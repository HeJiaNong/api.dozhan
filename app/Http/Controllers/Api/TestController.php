<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

class TestController extends Controller
{
    public function store(){
        return $this->response->array([
            'test' => 'hello',
        ]);
    }
}
