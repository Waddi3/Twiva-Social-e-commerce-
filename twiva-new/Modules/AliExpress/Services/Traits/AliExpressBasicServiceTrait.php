<?php


namespace Modules\AliExpress\Services\Traits;

use Exception;
use Modules\AliExpress\Exceptions\AliExpressServiceException;
use Modules\AliExpress\Services\AliExpressClasses\IopClient;
use Modules\AliExpress\Services\AliExpressClasses\IopRequest;

trait AliExpressBasicServiceTrait
{

    protected function getAccessToken($token = 'access_token'): string
    {
        $user = getAliExpressAccount();
        $accessToken = $user->aliExpressTokens?->$token;

        if (is_null($accessToken)) {
            throw new Exception("Ali Express account doesn't have an access token yet, please refer to " . route('ali.express.user.authorize'));
        }

        return $accessToken;
    }

    protected function executeApiRequest(string $apiMethod, array $params = [], $accessToken = null, $method)
    {
        $client = new IopClient($this->configs['url_sync'], $this->configs['app_key'], $this->configs['app_secret']);
        $request = new IopRequest($apiMethod, $method);

        foreach ($params as $key => $value) {
            $request->addApiParam($key, $value);
        }

        return json_decode($client->execute($request, $accessToken));
    }

    protected function calApi($api, $parameters, $method = 'POST')
    {
        try {
            $accessToken = $this->getAccessToken();
            $params = $parameters;
            return $this->executeApiRequest($api, array_filter($params), $accessToken, $method);
        } catch (Exception $exception) {
            $this->logger->error("Failed to $api", ['error' => $exception->getMessage()]);
            throw new AliExpressServiceException("Error $api  -- " . $exception->getMessage(), 0, $exception);
        }
    }
}
