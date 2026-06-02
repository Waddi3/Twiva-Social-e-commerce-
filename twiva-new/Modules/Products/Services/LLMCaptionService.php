<?php

namespace Modules\Products\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Str;

class LLMCaptionService {
    protected $httpClient;
    protected $answer;
    private string $message;

    private string $language = 'English';

    public function __construct()
    {
        $this->httpClient = new Client([
            'base_uri' => 'https://api.openai.com/v1/',
            'headers'  => [
                'Authorization' => 'Bearer ' . config('twiva.chat-gpt-key'),
                'Content-Type'  => 'application/json',
            ],
        ]);
    }

    public function setMessage($data): LLMCaptionService
    {
        $this->message = $this->prepareMessage($data);
        return $this;
    }

    public function setMessage2($message)
    {
        $this->message = $message;
        return $this;
    }
    public function getMessage()
    {
        return $this->message;
    }

    public function setLanguage(string $lang): LLMCaptionService
    {
        $this->language = $lang;
        return $this;
    }

    public function askToChatGpt()
    {
        try
        {
            $response = $this->httpClient->post('chat/completions', [
                'json' => [
                    'model'    => config('twiva.chat-gpt-model'),
                    'messages' => [
//                        ['role' => 'system', 'content' => 'You are'],
//                        ['role' => 'system', 'content' => 'Respond in ' . $this->language],
['role' => 'user', 'content' => $this->message],
                    ],
                ],
            ]);
            $result = json_decode($response->getBody()->getContents(), true);
            $this->answer = $result['choices'][0]['message']['content'];
        }
        catch (\Exception $exception)
        {
            logger("Modules/Products/Services/LLMCaptionService.php:39 => ", ['chat-gpt-key' => config('twiva.chat-gpt-key'), 'exception_code' => $exception->getCode(), 'exception_message' => $exception->getMessage()]);
        }
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAnswer()
    {
        if (!$this->answer)
        {
            return '';
        }
        return $this->answer;
    }

    private function prepareMessage($data)
    {
        /**
         * Create a highly optimized social media sales pitch with the following considerations:
         *
         * Character limit: 300
         * Tone: friendly, persuasive Language: Simple english
         * Intent: entice the customer to purchase and evoke FOMO
         * Inclusions: relevant emojis and hashtags
         *
         * Also embed the following link: {product link}.
         *
         * Utilize this data to inform the descriotion { product title}, { product description}, {Brand Name}, {Pricing Point}, {Discount}, {color}, {size}, and {product category}.
         */
        $message = "Create a highly optimized short social media sales pitch  with the following considerations:
within 200 characters
Tone: friendly, persuasive Language: {language}
Intent: entice the customer to purchase and evoke FOMO
Inclusions: relevant emojis and hashtags
Also embed the following link: {product_link}.
Utilize this data to inform the description product title:{product_title}, product description:{product_description}, Brand Name:{brand_name}, Pricing Point:{pricing_point}, Discount:{discount}, color:{color}, size:{size}, and product category:{product_category}.";
        $message = Str::replace('{product_link}', $data['product_link'], $message);
        $message = Str::replace('{product_title}', $data['product_title'], $message);
        $message = Str::replace('{product_description}', $data['product_description'], $message);
        $message = Str::replace('{brand_name}', $data['brand_name'], $message);
        $message = Str::replace('{pricing_point}', $data['pricing_point'], $message);
        $message = Str::replace('{discount}', $data['discount'], $message);
        $message = Str::replace('{color}', $data['color'], $message);
        $message = Str::replace('{size}', $data['size'], $message);
        $message = Str::replace('{product_category}', $data['product_category'], $message);
        $message = Str::replace('{language}', $this->language, $message);
        return $message;
    }
}
