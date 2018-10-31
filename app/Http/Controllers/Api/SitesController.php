<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

class SitesController extends Controller
{
    public function banners(){
        return $this->response->array(config('site.banners'));
    }
}
