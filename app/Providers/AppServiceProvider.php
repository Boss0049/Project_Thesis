<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use App\Resolvers\SocialUserResolver;
use Coderello\SocialGrant\Resolvers\SocialUserResolverInterface;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
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

    /**
     * All of the container bindings that should be registered.
     *
     * @var array
     */
    public $bindings = [
        SocialUserResolverInterface::class => SocialUserResolver::class,
    ];
}
