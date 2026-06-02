<?php


namespace Modules\Platform\Http\Controllers\API\V1;


use Exception;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Modules\Platform\Entities\UserPlatform;
use Modules\Platform\Entities\Platform;
use Modules\Platform\Http\Requests\ConnectPlatformRequest;
use Modules\Platform\Http\Resources\InfluencerPlatformCollection;
use Modules\Platform\Repositories\PlatformRepository;

class PlatformController
{
    public function index()
    {
        $platforms = UserPlatform::where('user_id', auth('api')->id())->get();
        $data = InfluencerPlatformCollection::collection($platforms)->resource;

        $data = UserPlatform::addWhatsAppGroups($data);

        return response()->json(['data' => $data]);
    }

    public function create(ConnectPlatformRequest $request)
    {
        $data = $request->validated();

        $platFromDriver = $this->getPlatformDriver((int)$data['platform_id']);
        try {
            if ($data['platform_id'] == Platform::TwitterID) {
                $userFromPlatFrom = Socialite::driver($platFromDriver)
                    ->userFromTokenAndSecret($data['token'], $data['secret']);
            } else {
                $userFromPlatFrom = Socialite::driver($platFromDriver)
                    ->userFromToken($data['token']);
            }
        } catch (Exception $exception) {
            Log::debug('Error while connecting ' . $platFromDriver, ['exception' => $exception, 'data' => $data]);
            return response()->json(['data' => 'Token is invalid !'], 422);
        }
        //        Log::debug('$userFromPlatFrom', ['$userFromPlatFrom' => $userFromPlatFrom]);
        $status =  $this->storePlatform($data['platform_id'], $userFromPlatFrom);
        $status_msg = $status ? ' Connected !' : ' Not Connected !';
        $status_code = $status ? 200 : 422;
        if ($status) {
            auth('api')->user()->updatePrivilege(config('twiva.privilege.connected-with-social-media'));
        }
        return response()->json(['data' => Platform::getPlatformName($data['platform_id']) . $status_msg], $status_code);
    }

    private function getPlatformDriver($platform_id)
    {
        switch ($platform_id) {
            case Platform::TwitterID:
                return 'twitter';
            case (Platform::InstagramID):
            case (Platform::FacebookID):
                return 'facebook';
            case Platform::YoutubeID:
                return 'youtube';
        }
    }

    private function storePlatform($platform_id, $userFromPlatFrom)
    {
        if ($platform_id == Platform::FacebookID) {
            return PlatformRepository::storeFacebook(auth('api')->user(), $userFromPlatFrom);
        } else if ($platform_id == Platform::InstagramID) {
            return PlatformRepository::storeInstagram(auth('api')->user(), $userFromPlatFrom);
        } else if ($platform_id == Platform::TwitterID) {
            return PlatformRepository::storeTwitter(auth('api')->user(), $userFromPlatFrom);
        } else if ($platform_id == Platform::YoutubeID) {
            return PlatformRepository::storeYouTube(auth('api')->user(), $userFromPlatFrom);
        }
    }
}
