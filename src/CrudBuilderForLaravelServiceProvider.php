<?php

namespace CrudBuilderForLaravelCoreUI;

use Illuminate\Support\ServiceProvider;

class CrudBuilderForLaravelServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'crud-builder-for-laravel-core-ui');

        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/crud-builder-for-laravel-core-ui'),
        ], 'views');
    }

    public function register()
    {
        // Registra os helpers como singletons
        $this->app->singleton('CrudBuilderForLaravelCoreUI\FormHelper', function () {
            return new FormHelper();
        });

        $this->app->singleton('CrudBuilderForLaravelCoreUI\ListHelper', function () {
            return new ListHelper();
        });
    }
}
