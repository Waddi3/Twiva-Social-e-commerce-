<?php

namespace Modules\Buyer\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\User\Entities\User;
use Symfony\Component\HttpFoundation\Response;

class ApiBuyerCheckSellerInfo
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

        $app_id = $request->header('app-id');
        $app_secret = $request->header('app-secret');

        if (is_null($app_id) or is_null($app_secret)) {
            return response()->json([
                "message" => "Check your inputs",
                "errors" => [
                    'error' => 'forbidden',
                    'description' => [
                        'Please add credentials info.'
                    ],
                ],
                // 'data' => [
                //     'error' => 'forbidden',
                //     'description' => 'Please add credentials info.',
                // ]
            ], Response::HTTP_FORBIDDEN);
        }


        $user = User::findWithApp($app_id, $app_secret)->first();

        if (!$user) {
            return response()->json([
                "message" => "Check your inputs",
                    "errors" => [
                        'error' => 'forbidden',
                        'description' => [
                            'Your credentials info or access token not related.'
                        ],
                    ],
                // 'data' => [
                //     'error' => 'forbidden',
                //     'description' => 'Your credentials info or access token not related.',
                // ]
            ], Response::HTTP_FORBIDDEN);
        }
        return $next($request);
    }
}
