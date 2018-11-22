<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

class SitesController extends Controller
{
    /*
     * 获取站点首页banner
     */
    public function banners(){
        return $this->response->array(config('site.banners'));
    }

    public function links(){
        return $this->response->array(config('site.links'));
    }
}
