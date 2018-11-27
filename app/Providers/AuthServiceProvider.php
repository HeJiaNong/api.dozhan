<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Horizon\Horizon;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        \App\Models\Work::class => \App\Policies\WorkPolicy::class,
        \App\Models\Category::class => \App\Policies\CategoryPolicy::class,
        \App\Models\Tag::class => \App\Policies\TagPolicy::class,
        \App\Models\User::class => \App\Policies\UserPolicy::class,
        \App\Models\Comment::class => \App\Policies\CommentPolicy::class,
        \Illuminate\Notifications\DatabaseNotification::class => \App\Policies\NotificationPolicy::class,
        \Spatie\Permission\Models\Permission::class => \App\Policies\PermissionPolicy::class,
        \App\Models\Favour::class => \App\Policies\FavourPolicy::class,
        \App\Models\Banner::class => \App\Policies\BannerPolicy::class,
        \App\Models\Link::class => \App\Policies\LinkPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //Horizon授权访问
        Horizon::auth(function ($request){
            return true;
        });
    }
}
