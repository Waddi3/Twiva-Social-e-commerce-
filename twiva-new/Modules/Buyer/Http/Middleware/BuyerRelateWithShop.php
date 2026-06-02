<?php

namespace Modules\Buyer\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\User\Entities\User;
use Symfony\Component\HttpFoundation\Response;

class BuyerRelateWithShop
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (\Illuminate\Http\Response|RedirectResponse) $next
     * @return \Illuminate\Http\Response|RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = User::fromShop(request('account'));
        $buyer = Auth::guard('buyer-api')->user();

        if ($buyer->created_by == $user->id) {
            return $next($request);
        }
        return response()->json([
            'data' => ['error' => 'forbidden']
        ], Response::HTTP_FORBIDDEN);
    }
}
