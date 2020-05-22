<?php

namespace App\Providers;

use App\Helpers\TestTreeBuilder;
use Illuminate\Support\ServiceProvider;

class TreeServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('App\Helpers\Contracts\TreeBuilder', function () {
            return new TestTreeBuilder();
        });
    }
}
