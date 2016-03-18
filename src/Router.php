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

        $this->app->group(
            [
                'prefix' => $prefix,
                'namespace' => '\Nebo15\LumenApplicationable\Controllers',
            ],
            function ($app) use ($name, $consumer, $middleware) {
                $app->post($name, ['uses' => 'ApplicationController@create', 'middleware' => $middleware]);

                $app->get(
                    $name,
                    [
                        'uses' => 'ApplicationController@index',
                        'middleware' => array_merge(['applicationable'], $middleware),
                    ]
                );

                $app->post(
                    $consumer,
                    [
                        'uses' => 'ApplicationController@consumer',
                        'middleware' => array_merge(['applicationable'], $middleware),
                    ]
                );

                $app->delete(
                    $consumer,
                    [
                        'uses' => 'ApplicationController@deleteConsumer',
                        'middleware' => array_merge(['applicationable'], $middleware),
                    ]
                );

                $app->post(
                    $name . '/users',
                    [
                        'uses' => 'ApplicationController@user',
                        'middleware' => array_merge(['applicationable'], $middleware),
                    ]
                );

                $app->delete(
                    $name . '/users',
                    [
                        'uses' => 'ApplicationController@deleteUser',
                        'middleware' => array_merge(['applicationable'], $middleware),
                    ]
                );
            }
        );
    }
}
