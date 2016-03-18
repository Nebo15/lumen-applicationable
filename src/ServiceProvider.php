<?php
namespace Nebo15\LumenApplicationable;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider as LumenServiceProvider;
use Nebo15\LumenApplicationable\Router;

class ServiceProvider extends LumenServiceProvider
{
    public function boot()
    {
        Validator::extend('applicationable_alias', 'Nebo15\LumenApplicationable\Validators\AliasValidator@validate');
        $path = 'applicationable.php';
        $this->publishes([
            __DIR__.'/config/applicationable.php' => app()->basePath() . '/config' . ($path ? '/' . $path : $path),
        ]);
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/config/applicationable.php',
            'applicationable'
        );

        $this->app->routeMiddleware([
            'applicationable' => Middlewares\ApplicationableMiddleware::class,
            'applicationable.user_or_client' => Middlewares\UserOrClientMiddleware::class,
            'applicationable.acl' => Middlewares\ApplicationableCorrectScopeMiddleware::class,
        ]);

        $this->app->singleton('Applicationable.routes', function ($app) {
            return new Router($app);
        });
    }
}
