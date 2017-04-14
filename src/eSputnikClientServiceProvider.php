<?php namespace Vis\eSputnikClient;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class eSputnikClientServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        require __DIR__ . '/../vendor/autoload.php';

        $this->publishes([
            __DIR__ . '/config' => config_path('esputnik-client/')
        ], 'esputnik-client-config');

    }

    /**
     * Define the routes for the application.
     *
     * @param  \Illuminate\Routing\Router $router
     *
     * @return void
     */
    public function setupRoutes(Router $router)
    {

    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
    }

    public function provides()
    {
    }
}
