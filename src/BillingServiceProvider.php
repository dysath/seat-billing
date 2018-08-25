<?php

namespace Denngarr\Seat\Billing;

use Illuminate\Support\ServiceProvider;
use Denngarr\Seat\Billing\Commands\BillingUpdate;

class BillingServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->add_routes();
        $this->add_views();
        $this->add_publications();
        $this->add_translations();
        $this->add_commands();
    }

    /**
     * Include the routes.
     */
    public function add_routes()
    {
        if (! $this->app->routesAreCached()) {
            include __DIR__ . '/Http/routes.php';
        }
    }

    public function add_translations()
    {
        $this->loadTranslationsFrom(__DIR__ . '/lang', 'billing');
    }

    /**
     * Set the path and namespace for the views.
     */
    public function add_views()
    {
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'billing');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/Config/billing.config.php',
            'billing.config'
        );

        $this->mergeConfigFrom(
            __DIR__ . '/Config/billing.sidebar.php',
            'package.sidebar'
        );

        $this->mergeConfigFrom(
            __DIR__ . '/Config/billing.permissions.php',
            'web.permissions'
        );
    }

    public function add_publications()
    {
        $this->publishes([
            __DIR__ . '/database/migrations/' => database_path('migrations'),
        ]);
    }

    private function add_commands()
    {
        $this->commands([
            BillingUpdate::class,
        ]);
    }
}
