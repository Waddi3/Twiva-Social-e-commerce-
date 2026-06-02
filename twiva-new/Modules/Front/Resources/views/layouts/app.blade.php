<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Twiva - Influencer Marketing - Social Commerce
We are an award-winning Influencer marketing company and social commerce platform connecting Businesses with social media
influencers. Market and sell through influencers.">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{asset('favicon.ico')}}" type="image/x-icon">
    <title>{{ $pageTitle ?? config('app.name') }}</title>
    <link href="{{ mix('front/css/app.css') }}" rel="stylesheet">
    <link rel="shortcut icon" href="{{asset('front/images/fav.png')}}" type="image/x-icon">
    <link rel="stylesheet" href="{{asset('shop/css/intl-tel-input/intlTelInput.min.css')}}">
    <link rel="shortcut icon" href="{{asset('front/images/fav.png')}}" type="image/x-icon">

    <style>
        .iti__flag {
            background-image: url("/shop/images/flags.png");
        }
    </style>

    <script async src="https://www.googletagmanager.com/gtag/js?id=AW-298982560"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }

        gtag('js', new Date());

        gtag('config', 'AW-298982560');
    </script>
</head>
<body>
<x-front::layout.navbar/>
<x-front::layout.drawer/>
{{--<x-front::e-commerce.layout.user/>--}}
@yield('content')
</body>
</html>
