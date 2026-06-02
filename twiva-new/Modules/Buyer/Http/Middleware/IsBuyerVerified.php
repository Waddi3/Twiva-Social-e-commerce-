<?php

namespace Modules\Buyer\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Str;

class IsBuyerVerified
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (Response|RedirectResponse) $next
     * @return Response|RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (Str::contains(\request()->path(), 'get-card-items'))
        {
            return $next($request);
        }
        if (Str::contains(\request()->path(), 'verify'))
        {
            return $next($request);
        }
        $buyer = Auth::guard('buyer')->user();
        if ($buyer && (!$buyer->is_verified || is_null($buyer->is_verified)))
        {
            if(is_null(request('account'))){
                return redirect()->route('e-commerce.buyer.verify', ['account' => $request->account]);
            }
            return redirect()->route('buyer.verify', ['account' => $request->account]);
        }
        return $next($request);
    }
}
