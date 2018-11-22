<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Comment;
use App\Models\ResourceQiniu;
use App\Models\Video;
use App\Models\Work;
use Carbon\Carbon;
use Dingo\Api\Facade\API;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\Relation;
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
        //设置 Carbon 默认时间语言
        Carbon::setLocale('zh');

        //注册模型监视器
        \App\Models\User::observe(\App\Observers\UserObserver::class);
        \App\Models\Category::observe(\App\Observers\CategoryObserver::class);
        \App\Models\Work::observe(\App\Observers\WorkObserver::class);
        \App\Models\Tag::observe(\App\Observers\TagObserver::class);
        \App\Models\Comment::observe(\App\Observers\CommentObserver::class);
        \App\Models\Favour::observe(\App\Observers\FavourObserver::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

        /*
         * 针对dingoapi的所有都是500错误做出优化
         */
        API::error(function (ModelNotFoundException $exception){
            abort(404);
        });

        /*
         * 针对dingoapi的所有都是500错误做出优化
         */
        API::error(function (AuthorizationException $exception){
            abort(403,$exception->getMessage());
        });

        /*
         * 注册「多态映射表」
         */
        Relation::morphMap([
            //将表名作为映射
            (new Work())->getTable() => Work::class,
            (new Comment())->getTable() => Comment::class,
            (new ResourceQiniu())->getTable() => ResourceQiniu::class,
            (new Category())->getTable() => Category::class,
        ]);
    }
}
