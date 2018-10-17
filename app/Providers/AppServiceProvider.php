<?php

namespace App\Providers;

use Dingo\Api\Facade\API;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {


        //注册模型监视器
        \App\Models\User::observe(\App\Observers\UserObserver::class);

        //由于Laravel 默认使用 utf8mb4 字符，如果线上服务器版本低于5.7.7，则需要设置默认字符串长度
//        Schema::defaultStringLength(191);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //针对dingoapi的所有都是500错误做出优化
        API::error(function (ModelNotFoundException $exception){
            abort(404);
        });

        API::error(function (AuthorizationException $exception){
            abort(403,$exception->getMessage());
        });
    }
}
