<?php

namespace App\Providers;

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
        //
    }
}
