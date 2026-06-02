<?php

namespace Modules\Shop\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class SameDomainMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $account = explode('.', request()->getHost())[0];
        $products = array_values(Session::get('cart') ?? []);
        $products = collect($products);
        $products = $products->pluck('shop_domain');
        if (count($products) && $account != $products[0])
        {
            if (auth('buyer')->check()) {
                auth('buyer')->user()->update([
                    'cart' => null,
                    'cart_last_updated_date' => null,
                ]);
            }
            Session::remove('cart');
        }

        return $next($request);
    }
}
