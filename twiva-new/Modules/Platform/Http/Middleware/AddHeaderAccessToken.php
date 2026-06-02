<?php

namespace Modules\Platform\Http\Middleware;


use Closure;
use Illuminate\Http\Request;

class AddHeaderAccessToken
{

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->has('access_token')) {
            $request->headers->set('Authorization', 'Bearer ' . $request->get('access_token'));
        }
        return $next($request);
    }

}
