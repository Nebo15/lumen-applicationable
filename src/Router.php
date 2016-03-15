<?php
namespace Nebo15\LumenApplicationable;

use Laravel\Lumen\Application;
use Nebo15\LumenApplicationable\Exceptions\MiddlewareException;
use OAuth2\Request;

class Router
{
    private $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function makeRoutes()
    {
        $middleware = config('applicationable.middleware');
        $prefix = config('applicationable.routes.prefix');
        $name = config('applicationable.routes.project_name');
        $consumer = config('applicationable.routes.consumer_name');

        if (!$middleware) {
            throw new MiddlewareException('You should set middleware key to applicationable config');
        }


        $this->app->post($prefix . $name, [
            'uses' => '\Nebo15\LumenApplicationable\Controllers\ApplicationController@create',
            'middleware' => $middleware,
        ]);

        $this->app->get($prefix . $name, [
            'uses' => '\Nebo15\LumenApplicationable\Controllers\ApplicationController@index',
            'middleware' => [$middleware, 'applicationable'],
        ]);

        $this->app->post($prefix . $consumer, [
            'uses' => '\Nebo15\LumenApplicationable\Controllers\ApplicationController@consumer',
            'middleware' => [$middleware, 'applicationable'],
        ]);
    }
}