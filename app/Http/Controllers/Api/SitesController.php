<?php

namespace App\Http\Controllers\Api;

use Dingo\Api\Routing\UrlGenerator;
use App\Http\Requests\Api\FormRequest as Request;

class SitesController extends Controller
{
    /*
     * 获取站点首页banner
     */
    public function banners(){
        return $this->response->array(config('site.banners'));
    }

    /*
     * 获取所有友情链接
     */
    public function links(){
        return $this->response->array(config('site.links'));
    }

    /*
     * 获取站点所有路由
     */
    public function routes(){
        $routes = app()->routes->getRoutes();
        foreach ($routes as $k=>$value){
            $path[$k]['uri'] = $value->uri;
            $path[$k]['path'] = $value->methods[0];
            $path[$k]['name'] = $value->action['as']??null;
        }
        return $this->response->array($path);
    }

    /*
     * 通过路由名获取API路由
     */
    public function getApiRoute($routeName){
        return app(UrlGenerator::class)->version('v1')->route($routeName);
    }

    /*
     * 通过路由名获取WEB路由
     */
    public function getWebRoute($routeName){
        return route($routeName);
    }
}
