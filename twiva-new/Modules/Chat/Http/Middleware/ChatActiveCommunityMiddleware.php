<?php

namespace Modules\Chat\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\User\Entities\User;
use Symfony\Component\HttpFoundation\Response;

class ChatActiveCommunityMiddleware
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

        if ($user->banned_at) {
            return response()->json(['please connect with admin!'], Response::HTTP_FORBIDDEN);
        }

        if ($user->de_active_community_at) {
            return response()->json(['please check your community setting section and active community.'], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
