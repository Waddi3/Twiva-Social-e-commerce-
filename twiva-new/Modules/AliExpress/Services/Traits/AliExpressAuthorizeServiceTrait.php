<?php


namespace Modules\AliExpress\Services\Traits;

use Exception;
use Illuminate\Support\Arr;
use Modules\AliExpress\Exceptions\AliExpressServiceException;
use Modules\AliExpress\Services\AliExpressClasses\IopClient;
use Modules\AliExpress\Services\AliExpressClasses\IopRequest;

trait AliExpressAuthorizeServiceTrait
{

    public function usersAuthorize($uri = 'https://api-sg.aliexpress.com/oauth/authorize?')
    {
        // https://dev.twiva.co.ke/aliexpress/users-authorize
        $params = [
            'response_type' => 'code',
            'force_auth' => 'true',
            'redirect_uri' => $this->configs['redirect_uri'],
            'client_id' => $this->configs['app_key']
        ];

        $query_string = Arr::query($params);

        $url = $uri . $query_string;

        return redirect()->to($url);
    }

    public function callbackUrl($url)
    {
        logger("Ali Express Callback-url => ", ['request-all' => request()->all()]);
        return redirect()->to($url);
    }

    public function generateToken()
    {

        try {
            $client = new IopClient($this->configs['url_rest'], $this->configs['app_key'], $this->configs['app_secret']);
            $request = new IopRequest('/auth/token/create');
            $request->addApiParam('code', request()->query('code'));

            $response = json_decode($client->execute($request));

            $user = getAliExpressAccount();
            if (isset($response->access_token)) {
                $user->aliExpressTokens()->updateOrCreate([], [
                    'access_token' => $response->access_token,
                    'refresh_token' => $response->refresh_token,
                    'response' => $response,
                ]);
            } else {
                // $user->aliExpressTokens()->delete();
            }

            $this->logger->info('AliExpress Token Generated', ['response' => $response]);
            return $response;
        } catch (Exception $exception) {
            $this->logger->error('Token Generation Error', ['error' => $exception->getMessage()]);
            throw new AliExpressServiceException('Error generating token', 0, $exception);
        }
    }

    public function refreshToken()
    {
        try {
            $refresh_token = $this->getAccessToken('refresh_token');
            $client = new IopClient($this->configs['url_rest'], $this->configs['app_key'], $this->configs['app_secret']);
            $request = new IopRequest('/auth/token/refresh');
            $request->addApiParam('refresh_token', $refresh_token);

            $response = json_decode($client->execute($request));

            $user = getAliExpressAccount();
            if (isset($response->access_token)) {
                $user->aliExpressTokens()->updateOrCreate([], [
                    'access_token' => $response->access_token,
                    'refresh_token' => $response->refresh_token,
                    'response' => $response,
                ]);
            } else {
                // $user->aliExpressTokens()->delete();
            }

            $this->logger->info('AliExpress Token Generated', ['response' => $response]);
            return $response;
        } catch (Exception $exception) {
            $this->logger->error('Token Generation by refresh token Error', ['error' => $exception->getMessage()]);
            throw new AliExpressServiceException('Error generating token by refresh token', 0, $exception);
        }
    }
}
