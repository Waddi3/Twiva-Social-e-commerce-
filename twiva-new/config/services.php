<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],
    'facebook' => [
        'client_id' => env('FACEBOOK_APP_ID'),
        'client_secret' => env('FACEBOOK_APP_SECRET'),
        'redirect' => env('FACEBOOK_REDIRECT'),
    ],
    'instagram' => [
        'client_id' => env('Instagram_APP_ID'),
        'client_secret' => env('Instagram_APP_SECRET'),
        'redirect' => env('Instagram_REDIRECT'),
    ],
    'instagrambasic' => [
        'client_id' => env('Instagram_APP_ID'),
        'client_secret' => env('Instagram_APP_SECRET'),
        'redirect' => env('Instagram_REDIRECT'),
    ],
    'google' => [
        'client_id' => env('Google_APP_ID'),
        'client_secret' => env('Google_APP_SECRET'),
        'redirect' => env('Google_REDIRECT'),
    ],
    'twitter' => [
        'client_id' => env('Twitter_APP_ID'),
        'client_secret' => env('Twitter_APP_SECRET'),
        'access_token' => env('TWITTER_ACCESS_TOKEN'),
        'redirect' => env('Twitter_REDIRECT'),
    ],
    'tiktok' => [
        'client_id' => env('TIKTOK_CLIENT_ID'),
        'client_secret' => env('TIKTOK_CLIENT_SECRET'),
        'redirect' => env('TIKTOK_REDIRECT_URI')
    ],
    'youtube' => [
        'client_id' => env('Google_APP_ID'),
        'client_secret' => env('Google_APP_SECRET'),
        'redirect' => env('Google_REDIRECT')
    ],
    'youtube_api_key' => env('youtube_api_key'),

    'business' => [
        'domain' => env('BUSINESS_DOMAIN'),
        'site' => 'https://' . env('BUSINESS_DOMAIN'),
    ],
    'Twilio' => [
        'sid' => env("TWILIO_ACCOUNT_SID", ''),
        'token' => env("TWILIO_AUTH_TOKEN", ''),
        'from' => env("TWILIO_AUTH_FROM", ''),
        'service_sid' => env('TWILIO_SERVICE_SID', '')

    ],
    'Flutterwave' => [
        'public_key' => env("FLW_PUBLIC_KEY", ''),
        'secret_key' => env("FLW_SECRET_KEY", ''),
        'encryption_key' => env("FLW_SECRET_HASH", ''),
    ],
    'MPESA' => [
        'MPESA_BASE_URL' => env("MPESA_BASE_URL", ''),
        'MPESA_STKPUSH_URL' => env("MPESA_STKPUSH_URL", ''),
        'MPESA_SHORT_CODE' => env("MPESA_SHORT_CODE", ''),
        'MPESA_PASS_KEY' => env("MPESA_PASS_KEY", ''),
        'MPESA_O_AUTH_URL' => env("MPESA_O_AUTH_URL", ''),
        'MPESA_CONSUMER_KEY' => env("MPESA_CONSUMER_KEY", ''),
        'MPESA_CONSUMER_SECRET' => env("MPESA_CONSUMER_SECRET", ''),
        'MPESA_B2C_CONSUMER_KEY' => env("MPESA_B2C_CONSUMER_KEY", ''),
        'MPESA_B2C_CONSUMER_SECRET' => env("MPESA_B2C_CONSUMER_SECRET", ''),
        'MPESA_TRANSACTION_MINI_APP_CONSUMER_KEY' => env("MPESA_TRANSACTION_MINI_APP_CONSUMER_KEY", ''),
        'MPESA_TRANSACTION_MINI_APP_CONSUMER_SECRET' => env("MPESA_TRANSACTION_MINI_APP_CONSUMER_SECRET", ''),
        'MPESA_ENVIRONMENT' => env("MPESA_ENVIRONMENT", ''),
        'MPESA_INITIATOR_NAME' => env("MPESA_INITIATOR_NAME", ''),
        'MPESA_INITIATOR_PASSWORD' => env("MPESA_INITIATOR_PASSWORD", ''),
        'MPESA_YOUR_SECURITY_CREDENTIAL' => env("MPESA_YOUR_SECURITY_CREDENTIAL", ''),
        'MPESA_TILL_NUMBER' => env("MPESA_TILL_NUMBER", ''),
        'MPESA_INITIATOR_NAME_TRANSACTION_STATUS' => env("MPESA_INITIATOR_NAME_TRANSACTION_STATUS", ''),
        'MPESA_INITIATOR_PASSWORD_TRANSACTION_STATUS' => env("MPESA_INITIATOR_PASSWORD_TRANSACTION_STATUS", ''),
        'MPESA_B2C_SHORT_CODE' => env("MPESA_B2C_SHORT_CODE", ''),
    ],
    'blogSite' => [
        'url' => env('BLOG_SITE', 'https://blog.twiva.co.ke/'),
    ],
    'social-links' => [
        'facebook' => 'https://www.facebook.com/twiva/',
        'twitter' => 'https://x.com/Twiva_influence',
        'instagram' => 'https://www.instagram.com/twiva_influence/',
        'whatsapp' => 'https://wa.me/254708088114',
        'linkedin' => 'https://ke.linkedin.com/company/twiva',
        'youtube' => 'https://www.youtube.com/channel/UC2B-2juL3EjZVaOzreJ9Iow',
        'tiktok' => 'https://www.tiktok.com/@twiva_influence?lang=en',
        'google-play' => 'https://play.google.com/store/apps/details?id=com.twiva.ke',
        'apple-store' => 'https://apps.apple.com/ke/app/twiva/id6444508375'

    ],
    'buyer' => [
        'url' => env('SHOPPING_SUBDOMAIN_URL', 'https://buyer.twiva.co.ke/'),
    ],
    'apiSite' => [
        'url' => env('API_SITE_URL', 'https://api.twiva.co.ke/'),
    ],


];
