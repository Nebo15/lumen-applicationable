<?php
namespace Nebo15\LumenApplicationable\Middlewares;

use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Factory as Auth;
use Nebo15\LumenApplicationable\Models\Application;

class UserOrClientMiddleware
{
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    public function handle(Request $request, Closure $next)
    {
        if ($this->auth->guard(null)->guest()) {
            $consumer = app()->make('Nebo15\LumenApplicationable\Models\Application')->getConsumer($request->getUser());
            if (!$consumer || $consumer->client_secret != $request->getPassword()) {
                throw new AuthorizationException;
            } else {
                app()->offsetSet('applicationable.consumer', $consumer);
            }
        }
        return $next($request);
    }
}
