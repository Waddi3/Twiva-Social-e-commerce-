<?php


namespace Modules\Platform\Services;


use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Modules\Platform\Entities\Platform;
use Modules\Platform\Entities\StoryPost;
use Modules\User\Entities\User;

class  MetaServices
{

    public const version = 'v20.0';
    public static function getLongLifeToken($token)
    {
        try {
            $client = new Client();
            $request = $client->request('GET', 'https://graph.facebook.com/' . self::version . '/oauth/access_token', [
                'query' => [
                    'grant_type' => 'fb_exchange_token',
                    'client_id' => config('services.facebook.client_id'),
                    'client_secret' => config('services.facebook.client_secret'),
                    'fb_exchange_token' => $token,
                ]
            ]);
            $contents = json_decode($request->getBody()->getContents());
            return $contents->access_token;
        } catch (Exception | GuzzleException) {
        }
        return null;
    }
}
