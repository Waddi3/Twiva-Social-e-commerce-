<?php


namespace Modules\Platform\Http\Controllers\API\V2;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Modules\Platform\Entities\UserPlatform;
use Modules\Platform\Entities\Platform;
use Modules\Platform\Http\Resources\V2\InfluencerPlatformCollection;
use Modules\User\Entities\User;


class PlatformController
{
    public function index()
    {
        $platforms = UserPlatform::where('user_id', auth('api')->id())->get();
        $platforms = Platform::all();
        $data = InfluencerPlatformCollection::collection($platforms)->resource;

        $data = UserPlatform::addWhatsAppGroups($data);
        return response()->json(['data' => $data]);
    }

    public function twitterAuthorize(Request $request)
    {
        session(['twitter_user_id' => Auth::guard('api')->user()->id]);
        session(['twitter_platform' => $request->input('platform')]);
        return Socialite::driver('twitter')->redirect();
    }

    public function twitterAuthorizeCallback(Request $request)
    {
        $platform = session('twitter_platform');

        // Handle denied case
        if ($request->has('denied')) {
            if (session('platform') == 'mobile') {
                return redirect('apptwiva://social/twitter?' . http_build_query([
                    'status' => false,
                    'message' => 'Access denied',
                    'denied' => $request->denied
                ]));
            } else {
                $url = config('twiva.bases.app_url') . "/#/account_settings/social_media_connections/?" . http_build_query([
                    'status' => false,
                    'message' => 'Access denied',
                    'denied' => $request->denied
                ]);
                return redirect()->to($url);
            }
        }

        if (!session('twitter_user_id')) {
            if (session('platform') == 'mobile') {
                return redirect('apptwiva://social/twitter?' . http_build_query([
                    'status' => false,
                    'message' => 'User session not found',
                ]));
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'User session not found',
                ]);
            }
        }
        $user_id = session('twitter_user_id');

        $user = User::find($user_id);

        if ($user) {
            $twitterUser = Socialite::driver('twitter')->user();
            //            dd($twitterUser, $twitterUser->name, $twitterUser->nickname, $twitterUser->accessTokenResponseBody);
            if ($twitterUser) {
                // Remove old credentials
                $userPlatformIds = $user->UserPlatforms()
                    ->where('user_id', $user->id)
                    ->where('platform_id', Platform::TwitterID)
                    ->pluck('id');
                UserPlatform::destroy($userPlatformIds);

                UserPlatform::create([
                    'user_id' => $user->id,
                    'platform_id' => Platform::TwitterID,
                    'social_id' => $twitterUser->token,
                    'token' => $twitterUser->tokenSecret,
                    'name' => $twitterUser->name ?? '',
                    'additional_info' => $twitterUser
                ]);

                if ($platform == 'mobile') {
                    return redirect('apptwiva://social/twitter?' . http_build_query([
                        'status' => true,
                        'message' => 'User is authorized successfully',
                        'token' => $twitterUser->token,
                        'token_secret' => $twitterUser->tokenSecret
                    ]));
                } else {
                    $url = config('twiva.bases.app_url') . "/#/account_settings/social_media_connections/?token={$twitterUser->token}&token_secret={$twitterUser->tokenSecret}";
                    return redirect()->to($url);
                }
            }

            if ($platform == 'mobile') {
                return redirect('apptwiva://social/twitter?' . http_build_query([
                    'status' => false,
                    'message' => 'Authorization failed',
                ]));
            } else {
                $url = config('twiva.bases.app_url') . "/#/account_settings/social_media_connections/?" . http_build_query([
                    'status' => false,
                    'message' => 'Authorization failed',
                ]);
                return redirect()->to($url);
            }
        }

        if ($platform == 'mobile') {
            return redirect('apptwiva://social/twitter?' . http_build_query([
                'status' => false,
                'message' => 'User not found',
            ]));
        } else {
            $url = config('twiva.bases.app_url') . "/#/account_settings/social_media_connections/?" . http_build_query([
                'status' => false,
                'message' => 'User not found',
            ]);
            return redirect()->to($url);
        }
    }

    public function tiktokAuthorize(Request $request)
    {
        session(['tiktok_user_id' => Auth::guard('api')->user()->id]);
        session(['tiktok_platform' => $request->input('platform')]);
        return Socialite::driver('tiktok')->scopes(['user.info.profile', 'video.publish', 'user.info.stats', 'video.list', 'video.upload'])->redirect();
    }

    public function tiktokAuthorizeCallback(Request $request)
    {
        $platform = session('tiktok_platform');

        // Handle denied case
        if ($request->has('denied')) {
            if (session('platform') == 'mobile') {
                return redirect('apptwiva://social/tiktok?' . http_build_query([
                    'status' => false,
                    'message' => 'Access denied',
                    'denied' => $request->denied
                ]));
            } else {
                $url = config('twiva.bases.app_url') . "/#/account_settings/social_media_connections/?" . http_build_query([
                    'status' => false,
                    'message' => 'Access denied',
                    'denied' => $request->denied
                ]);
                return redirect()->to($url);
            }
        }

        if (!session('tiktok_user_id')) {
            if (session('platform') == 'mobile') {
                return redirect('apptwiva://social/tiktok?' . http_build_query([
                    'status' => false,
                    'message' => 'User session not found',
                ]));
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'User session not found',
                ]);
            }
        }
        $user_id = session('tiktok_user_id');

        $user = User::find($user_id);

        if ($user) {
            $tiktokUser = Socialite::driver('tiktok')->scopes(['user.info.profile', 'video.publish', 'user.info.stats', 'video.list', 'video.upload'])->user();
            if ($tiktokUser) {
                // Remove old credentials
                $userPlatformIds = $user->UserPlatforms()
                    ->where('user_id', $user->id)
                    ->where('platform_id', Platform::TikTokID)
                    ->pluck('id');
                UserPlatform::destroy($userPlatformIds);

                UserPlatform::create([
                    'user_id' => $user->id,
                    'platform_id' => Platform::TikTokID,
                    'social_id' => $tiktokUser->id,
                    'token' => $tiktokUser->token,
                    'name' => $tiktokUser->name ?? '',
                    'additional_info' => $tiktokUser
                ]);

                if ($platform == 'mobile') {
                    return redirect('apptwiva://social/tiktok?' . http_build_query([
                        'status' => true,
                        'message' => 'User is authorized successfully',
                        'token' => $tiktokUser->token,
                        'expires_id' => $tiktokUser->expiresIn
                    ]));
                } else {
                    $url = config('twiva.bases.app_url') . "/#/account_settings/social_media_connections/?token={$tiktokUser->token}&expires_id={$tiktokUser->expiresIn}";
                    return redirect()->to($url);
                }
            }

            if ($platform == 'mobile') {
                return redirect('apptwiva://social/tiktok?' . http_build_query([
                    'status' => false,
                    'message' => 'Authorization failed',
                ]));
            } else {
                $url = config('twiva.bases.app_url') . "/#/account_settings/social_media_connections/?" . http_build_query([
                    'status' => false,
                    'message' => 'Authorization failed',
                ]);
                return redirect()->to($url);
            }
        }

        if ($platform == 'mobile') {
            return redirect('apptwiva://social/tiktok?' . http_build_query([
                'status' => false,
                'message' => 'User not found',
            ]));
        } else {
            $url = config('twiva.bases.app_url') . "/#/account_settings/social_media_connections/?" . http_build_query([
                'status' => false,
                'message' => 'User not found',
            ]);
            return redirect()->to($url);
        }
    }
}
