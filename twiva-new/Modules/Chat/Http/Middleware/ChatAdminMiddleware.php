<?php

namespace Modules\Chat\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\User\Entities\User;
use Symfony\Component\HttpFoundation\Response;

class ChatAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {

        $user = $request->user();

        if ($user->type != User::InfluencerType) {
            return response()->json([], Response::HTTP_FORBIDDEN);
        }

        if (!$user->is_admin_for_community) {
            return response()->json([], Response::HTTP_FORBIDDEN);
        }


        return $next($request);
    }
}
