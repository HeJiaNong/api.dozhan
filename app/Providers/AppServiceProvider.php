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
        \App\Models\Category::observe(\App\Observers\CategoryObserver::class);
        \App\Models\Album::observe(\App\Observers\AlbumObserver::class);

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
