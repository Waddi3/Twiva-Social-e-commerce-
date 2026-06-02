<?php

namespace Modules\Products\Services;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class ChatGPTService
{
    protected $httpClient;

    public function __construct()
    {
        $this->httpClient = new Client([
            'base_uri' => 'https://api.openai.com/v1/',
            'headers' => [
                'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    public function askToChatGpt()
    {

        /**
         * curl https://api.openai.com/v1/completions \
         * -H "Content-Type: application/json" \
         * -H "Authorization: Bearer $OPENAI_API_KEY" \
         * -d '{
         * "model": "gpt-3.5-turbo-instruct",
         * "prompt": "Say this is a test",
         * "max_tokens": 7,
         * "temperature": 0
         * }'
         */
        $message = 'write description text marketing for this product "200 MP Camera"';
//        logger("Modules/Products/Services/ChatGPTService.php:25 => ", ['message' => $message]);
        var_dump("response of https://api.openai.com/v1/completions");
        try {
            $response = $this->httpClient->post('completions', [
                'json' => [
                    'model' => 'gpt-3.5-turbo-instruct',
                    'prompt' => $message,
                    'max_tokens' => 7,
                    'temperature' => 0,
                ],
            ]);
//            logger("Modules/Products/Services/ChatGPTService.php:37 => ", ['response-getBody' => $response]);
            var_dump($response);
            var_dump(PHP_EOL);
            var_dump("-----------------------------------------------------------------------------------");
            var_dump($response->getBody());
            var_dump("-----------------------------------------------------------------------------------");
            var_dump(PHP_EOL);
//            return json_decode($response->getBody(), true)['choices'][0]['message']['content'];
        } catch (Exception $exception) {
            var_dump($exception);
//            logger("Modules/Products/Services/ChatGPTService.php:40 => ", ['exception' => $exception->getMessage()]);
        }

        var_dump("<br>");
        var_dump("<br>");
        var_dump("<br>");

        var_dump("response of https://api.openai.com/v1/chat/completions");

        try {
            $response = $this->httpClient->post('chat/completions', [
                'json' => [
                    'model' => 'gpt-3.5-turbo',
                    'messages' => [
                        ['role' => 'system', 'content' => 'You are'],
                        ['role' => 'user', 'content' => $message],
                    ],
                ],
            ]);
            $result = json_decode($response->getBody()->getContents(), true);
            var_dump($result);
//            return json_decode($response->getBody(), true)['choices'][0]['message']['content'];
        } catch (Exception $exception) {
            var_dump($exception);
        }
        var_dump("<br>");
        var_dump("<br>");
        var_dump("<br>");
    }
}
