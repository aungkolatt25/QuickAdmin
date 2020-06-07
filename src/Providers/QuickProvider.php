<?php

namespace Quick\Providers;

use Illuminate\Support\ServiceProvider;

class QuickProvider extends ServiceProvider
{
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
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $controller = "Default";
        if(request()->segment("2")){
            $quickdata = \Quick\Quick\QuickData::get(request()->segment("2"));
            $controller = $quickdata->getBindedController();
        }

        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        $this->loadTranslationsFrom(__DIR__.'/../translations', 'quick');

        $this->loadViewsFrom(__DIR__.'/../resources/views/', 'quick');

        $this->mergeConfigFrom(
            __DIR__.'/../config/quick.php', 'quick'
        );

        $this->publishes([
            __DIR__.'/../config/quick.php' => config_path('quick.php'),
            __DIR__.'/../translations' => resource_path('lang/vendor/'),
            __DIR__.'/../resources/views' => resource_path('views/vendor/quick')
        ]);
    }
}
