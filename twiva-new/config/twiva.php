<?php
return [
    'bases' => [
        'url' => env('APP_URL', 'https://twiva.co.ke/'),
        'home_url' => env('APP_URL', 'https://home.twiva.co.ke/'),
        'business_domain' => '{subdomain}.' . env('APP_DOMAIN', 'twiva.co.ke'),
        'app_url' => env('FLUTTER_URL', 'https://app.twiva.co.ke/'),
        'shop_domain' => env('SHOP_DOMAIN', 'twiva.com'),
        /*
         * the date_of_start_new_system key is the date we have to ignore all old orders done before it in calculate winnings payment
         */
        'date_of_start_new_system' => env('DATE_OF_START_NEW_SYSTEM'),
    ],
    'chat-gpt-key' => env('OPENAI_API_KEY', ''),
    'chat-gpt-model' => env('OPENAI_API_MODEL', 'gpt-3.5-turbo'),
    'privilege' => [
        'connected-with-social-media' => env('PRIVILEGE_CONNECTED_WITH_SOCIAL_MEDIA', 1),
        'new-registration-user' => env('PRIVILEGE_NEW_REGISTRATION_USER', 500),
        'referral-user' => env('PRIVILEGE_REFERRAL_USER', 5),
        'publish-in-platform' => env('PUBLISH_IN_PLATFORM', 2),
    ],
    'spin-wheel' => [
        'times-every-day' => env('SPIN_WHEEL_TIMES_EVERY_DAY', 10),
        'value-of-the-total-order-should-be-n-times-the-coupon-value' => env('VALUE_OF_THE_TOTAL_ORDER_SHOULD_BE_N_TIMES_THE_COUPON_VALUE', 20),
        'winner-percent' => env('spin_wheel_winner_percent', 40),
    ],
    'e-commerce' => [
        'cache_time_per_minutes' => env('E_COMMERCE_CACHE_TIME_PER_MINUTES', 60),
    ],
    'campaign' => [
        'price_for_whats_app_campaign' => env('PRICE_FOR_WHATS_APP_CAMPAIGN', 2000),
    ],
    'ali-express' => [
        'business-account' => env('ALI_EXPRESS_EMAIL_BUSINESS_ACCOUNT', 'ali-express@twiva.com'),
        'redirect_uri' => env('ALI_EXPRESS_CALLBACK_URL', 'https://www.twiva.com/aliexpress/callback-url'),
        'app_key' => env('ALI_EXPRESS_APP_KEY', '509712'),
        'app_secret' => env('ALI_EXPRESS_APP_SECRET', "KaJoFgc7CsJTEgAgnevWq2EZgW6suQ8Q"),
        'url_rest' => env('ALI_EXPRESS_URL_REST', "https://api-sg.aliexpress.com/rest"),
        'url_sync' => env('ALI_EXPRESS_URL_SYNC', "https://api-sg.aliexpress.com/sync"),
        'percentage_profit_retail_price' => env('PERCENTAGE_PROFIT_RETAIL_PRICE', 15),
        'percentage_profit_discount_price' => env('PERCENTAGE_PROFIT_RETAIL_PRICE', 50),
        'default_category_name' => env('ALI_EXPRESS_DEFAULT_CATEGORY_NAME', 'Others'),
        'default_sub_category_name' => env('ALI_EXPRESS_DEFAULT_SUB_CATEGORY_NAME', 'Others'),
        'logistics_address' => [
            "local" => env('ALI_EXPRESS_LOGISTICS_TWIVA_ADDRESS_LOCAL', 'en_US'),
            "address" => env('ALI_EXPRESS_LOGISTICS_TWIVA_ADDRESS_ADDRESS', "Twiva, Standard Group Centre, OFF Mombasa Road, Nairobi, Kenya"),
            "city" => env('ALI_EXPRESS_LOGISTICS_TWIVA_ADDRESS_CITY', "Nairobi"),
            "country" => env('ALI_EXPRESS_LOGISTICS_TWIVA_ADDRESS_COUNTRY', "KE"),
            "province" => env('ALI_EXPRESS_LOGISTICS_TWIVA_ADDRESS_PROVINCE', "Nairobi"),
            "contact_person" => env('ALI_EXPRESS_LOGISTICS_TWIVA_ADDRESS_CONTACT_PERSON', 'customers@twiva.co.ke'), // customers@twiva.co.ke
            "zip" => env('ALI_EXPRESS_LOGISTICS_TWIVA_ADDRESS_ZIP', '00100'),
            "country" => env('ALI_EXPRESS_LOGISTICS_TWIVA_ADDRESS_COUNTRY', 'KE'),
            "phone_country" => env('ALI_EXPRESS_LOGISTICS_TWIVA_ADDRESS_PHONE_COUNTRY', '+254'),
            "mobile_no" => env('ALI_EXPRESS_LOGISTICS_TWIVA_ADDRESS_MOBILE_NO', '708088114'),
        ],
    ],
    'spotit' => [
        'SPOTIT_HOST' => env('SPOTIT_HOST', 'https://uat.craftsilicon.com/SpotitBnplApi'),
        'ConsumerKey' => env('SPOTIT_CONSUMER_KEY', 'SpotitSTK'),
        'ConsumerSecret' => env('SPOTIT_CONSUMER_SECRET', 'pass@123'),
        'BusinessShortCode' => env('SPOTIT_BUSINESS_SHORT_CODE', '4023745'),
        'PassKey' => env('SPOTIT_PASS_KEY', 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919'),
        'Action' => env('SPOTIT_ACTION', 'NEWSALE'),
    ],
    'pesapal' => [
        'sandbox_host' => env('PESAPAL_SANDBOX_HOST', 'https://cybqa.pesapal.com'),
        'live_host' => env('PESAPAL_LIVE_HOST', 'https://pay.pesapal.com'),
        'sandbox_uri' => env('PESAPAL_SANDBOX_URI', '/pesapalv3/api/'),
        'live_uri' => env('PESAPAL_LIVE_URI', '/v3/api/'),
        'ConsumerKey' => env('PESAPAL_CONSUMER_KEY', 'qkio1BGGYAXTu2JOfm7XSXNruoZsrqEW'),
        'ConsumerSecret' => env('PESAPAL_CONSUMER_SECRET', 'osGQ364R49cXKeOYSpaOnT++rHs='),
        'BusinessShortCode' => env('PESAPAL_BUSINESS_SHORT_CODE', '4023745'),
        'PassKey' => env('PESAPAL_PASS_KEY', 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919'),
        'Action' => env('PESAPAL_ACTION', 'NEWSALE'),
        'sandbox' => env('PESAPAL_SANDBOX', true),
    ]
];
