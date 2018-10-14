<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

class TestController extends Controller
{
    public function store(){
        dd(app(\Dingo\Api\Routing\UrlGenerator::class)->version('v1')->route('api.user.emailRegister'));
        return $this->response->array([
            'test' => 'hello',
        ]);
    }
}
