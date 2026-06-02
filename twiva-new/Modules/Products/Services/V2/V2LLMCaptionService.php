<?php

namespace Modules\Products\Services\V2;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class V2LLMCaptionService {
    protected $httpClient;
    protected $answer;
    private string $message;
    protected $brand_theme;
    protected $data;


    const BRAND_THEME_ATTRIBUTES = [
        'voice',
        'tone',
        'style',
        'structure',
        'location',
        'city_id',
        'latitude',
        'longitude',
        'min_age',
        'max_age',
        'gender',
        'languages',
        'interests',
    ];

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


    public function setBrandTheme($brand_theme): V2LLMCaptionService
    {
        $this->brand_theme = $brand_theme;
        return $this;
    }


    public function generateQuestion($data = null)
    {
        $this->message = $this->prepareMessage($data);
        return $this;
    }

    public function getQuestion()
    {
        return $this->message;
    }

    public function runRequest()
    {
        try
        {
            $response = $this->httpClient->post('chat/completions', [
                'json' => [
                    'model'    => config('twiva.chat-gpt-model'),
                    'messages' => [
                        [
                            'role'    => 'user',
                            'content' => $this->message
                        ],
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

    public function getAnswer()
    {
        if (!$this->answer)
        {
            return '';
        }
        return $this->answer;
    }

    public function getAnswers()
    {
        if (!$this->answer)
        {
            return '';
        }

        // Split the text into an array using regular expressions
        $messagesArray = preg_split('/\d+\.\s/', $this->answer, -1, PREG_SPLIT_NO_EMPTY);

        // Convert the array into a Laravel collection for easier manipulation
        // $messagesCollection = collect($messagesArray);

        return $messagesArray;
    }

    private function prepareMessage($data)
    {
        /**
         * Create a highly optimized social media sales pitch with the following considerations:
         *
         * Character limit: 100
         * Tone: friendly, persuasive Language: Simple english
         * Intent: entice the customer to purchase and evoke FOMO
         * Inclusions: relevant emojis and hashtags
         *
         * Also embed the following link: {product link}.
         *
         * Utilize this data to inform the description { product title}, { product description}, {Brand Name}, {Pricing Point}, {Discount}, {color}, {size}, and {product category}.
         */
        $message = "
            Create a highly optimized short social media sales pitch with the following considerations:
            within 100 characters in {languages}

            Voice: {voice},
            Tone: {tone},
            Style: {style},
            Structure: {structure},
            Location: {location},
            City_id: {city_id},
            Latitude: {latitude},
            Longitude: {longitude},
            Min age: {min_age},
            Max age: {max_age},
            Gender: {gender},
            Interests: {interests},


            Inclusions: relevant emojis and hashtags
            Also embed the following link: {product_link}.
            Utilize this data to inform the description

            product title: {product_title},
            product description: {product_description},
            Brand Name: {brand_name},
            Pricing Point: {pricing_point},
            Discount: {discount},
            color: {color},
            size: {size}, and
            product category: {product_category}.

            get 4 different examples
        ";

        $message = Str::replace('{product_link}', $data['product_link'], $message);
        $message = Str::replace('{product_title}', $data['product_title'], $message);
        $message = Str::replace('{product_description}', $data['product_description'], $message);
        $message = Str::replace('{brand_name}', $data['brand_name'], $message);
        $message = Str::replace('{pricing_point}', $data['pricing_point'], $message);
        $message = Str::replace('{discount}', $data['discount'], $message);
        $message = Str::replace('{color}', $data['color'], $message);
        $message = Str::replace('{size}', $data['size'], $message);
        $message = Str::replace('{product_category}', $data['product_category'], $message);

        foreach (self::BRAND_THEME_ATTRIBUTES as $attribute)
        {
            $message = Str::replace("{" . $attribute . "}", $this->brandTheme($attribute), $message);
        }

        return $message;
    }

    public function brandTheme($attribute)
    {
        if ($attribute == 'city_id')
        {
            return $this->brand_theme->city->name;
        }
        if ($attribute == 'languages')
        {
            return collect(json_decode($this->brand_theme->languages))->pluck('name')->implode(', ');
        }
        if ($attribute == 'interests')
        {
            return collect(json_decode($this->brand_theme->interests))->pluck('name')->implode(', ');
        }
        return $this->brand_theme?->$attribute;
    }
}
