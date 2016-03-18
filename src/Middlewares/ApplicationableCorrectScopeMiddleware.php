<?php
namespace Nebo15\LumenApplicationable\Middlewares;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Factory as Auth;
use Nebo15\LumenApplicationable\Exceptions\AccessDeniedException;

class ApplicationableCorrectScopeMiddleware
{
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    public function handle(Request $request, Closure $next)
    {
        $routes = config('applicationable.acl.' . strtolower($request->getMethod()), []);

        foreach ($routes as $route => $scopes) {
            if (preg_match($route, $request->getPathInfo())) {

                $scopesMethods = array_map(function ($value) {
                    return 'can' . ucfirst($value);
                }, $scopes);

                if (!$this->auth->guard()->guest()) {
                    $user = $this->auth->guard()->user()->getApplicationUser();

                    foreach ($scopesMethods as $scopeMethod) {
                        if (!$user->$scopeMethod()) {
                            throw new AccessDeniedException;
                        }
                    }
                } else {
                    $consumer = app()->offsetGet('applicationable.consumer');
                    if (!$consumer) {
                        return $next($request);
                    }
                    foreach ($scopesMethods as $scopeMethod) {
                        if (!$consumer->$scopeMethod()) {
                            throw new AccessDeniedException;
                        }
                    }
                }
                return $next($request);
            }
        }

        return $next($request);
    }
}
