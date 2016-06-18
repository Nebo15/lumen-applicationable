<?php
namespace Nebo15\LumenApplicationable\Middlewares;

use Closure;
use Illuminate\Http\Request;
use Nebo15\LumenApplicationable\Exceptions\MiddlewareException;
use Nebo15\LumenApplicationable\Exceptions\XApplicationException;
use Nebo15\LumenApplicationable\Models\Application;
use Illuminate\Contracts\Auth\Factory as Auth;

class ApplicationableMiddleware
{
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    public function handle(Request $request, Closure $next)
    {
        $project_id = $request->header('X-Application');
        $project = Application::find($project_id);

        if (!$project_id || !$project) {
            throw new XApplicationException("You should set correct 'X-Application' header");
        }

        app()->bind('Nebo15\LumenApplicationable\Models\Application', function () use ($project) {
            return $project;
        });

        if ($this->auth->guard()->user()) {
            $this->auth->guard()->user()->setCurrentApplication($project)->getAndSetApplicationUser();
        }

        return $next($request);
    }
}
