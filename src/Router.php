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

        $applications_route = config('applicationable.routes.applications');
        $current_application_route = config('applicationable.routes.current_application');
        $consumers_route = config('applicationable.routes.consumers');
        $users_route = config('applicationable.routes.users');
        $set_admin_route = config('applicationable.routes.set_admin');

        if (!$middleware) {
            throw new MiddlewareException('You should set middleware key to applicationable config');
        }

        $this->app->group(
            [
                'prefix' => $prefix,
                'namespace' => '\Nebo15\LumenApplicationable\Controllers',
            ],
            function ($app) use (
                $applications_route,
                $consumers_route,
                $users_route,
                $current_application_route,
                $set_admin_route,
                $middleware
            ) {

                $app->post(
                    $applications_route,
                    ['uses' => 'ApplicationController@create', 'middleware' => $middleware]
                );
                $app->get(
                    $applications_route,
                    ['uses' => 'ApplicationController@projectsList', 'middleware' => $middleware]
                );

                $middleware = array_merge(['applicationable'], $middleware);

                $app->put(
                    $applications_route,
                    ['uses' => 'ApplicationController@updateApplication', 'middleware' => $middleware]
                );

                $app->get(
                    $current_application_route,
                    ['uses' => 'ApplicationController@index', 'middleware' => $middleware,]
                );

                $app->post(
                    $consumers_route,
                    ['uses' => 'ApplicationController@createConsumer', 'middleware' => $middleware,]
                );

                $app->put(
                    $consumers_route,
                    ['uses' => 'ApplicationController@updateConsumer', 'middleware' => $middleware,]
                );

                $app->delete(
                    $consumers_route,
                    ['uses' => 'ApplicationController@deleteConsumer', 'middleware' => $middleware,]
                );

                $app->get(
                    $users_route,
                    ['uses' => 'ApplicationController@getCurrentUser', 'middleware' => $middleware,]
                );

                $app->post(
                    $users_route,
                    ['uses' => 'ApplicationController@addUserToProject', 'middleware' => $middleware,]
                );

                $app->put($users_route, ['uses' => 'ApplicationController@updateUser', 'middleware' => $middleware,]);

                $app->delete(
                    $users_route,
                    ['uses' => 'ApplicationController@deleteUser', 'middleware' => $middleware,]
                );

                $app->post(
                    $set_admin_route,
                    ['uses' => 'ApplicationController@setProjectAdmin', 'middleware' => $middleware,]
                );

            }
        );
    }
}
