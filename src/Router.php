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
        $applications = config('applicationable.routes.applications');
        $current_application = config('applicationable.routes.current_application');
        $consumers = config('applicationable.routes.consumers');
        $users = config('applicationable.routes.users');

        if (!$middleware) {
            throw new MiddlewareException('You should set middleware key to applicationable config');
        }

        $this->app->group(
            [
                'prefix' => $prefix,
                'namespace' => '\Nebo15\LumenApplicationable\Controllers',
            ],
            function ($app) use ($applications, $consumers, $users, $current_application, $middleware) {

                $app->post($applications, ['uses' => 'ApplicationController@create', 'middleware' => $middleware]);
                $app->get($applications, ['uses' => 'ApplicationController@projectsList', 'middleware' => $middleware]);

                $middleware = array_merge(['applicationable'], $middleware);

                $app->get($current_application,
                    ['uses' => 'ApplicationController@index', 'middleware' => $middleware,]);

                $app->post($consumers, ['uses' => 'ApplicationController@consumer', 'middleware' => $middleware,]);

                $app->delete($consumers,
                    ['uses' => 'ApplicationController@deleteConsumer', 'middleware' => $middleware,]);

                $app->get($users, ['uses' => 'ApplicationController@getCurrentUser', 'middleware' => $middleware,]);

                $app->post($users, ['uses' => 'ApplicationController@user', 'middleware' => $middleware,]);

                $app->delete($users, ['uses' => 'ApplicationController@deleteUser', 'middleware' => $middleware,]);
            }
        );
    }
}
