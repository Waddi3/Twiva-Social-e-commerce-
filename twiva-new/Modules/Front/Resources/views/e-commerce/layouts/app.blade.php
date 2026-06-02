@php use Modules\Products\Repositories\ECommerce\ECommerceCategoryRepository; @endphp
        <!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ $content_discription ?? 'Twiva - Influencer Marketing - Social Commerce
We are an award-winning Influencer marketing company and social commerce platform connecting Businesses with social media
influencers. Market and sell through influencers.' }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <link href="{{ mix('front/css/app.css') }}" rel="stylesheet">
    <link rel="shortcut icon" href="{{asset('front/images/fav.png')}}" type="image/x-icon">

    <link rel="stylesheet" href="{{asset('shop/css/intl-tel-input/intlTelInput.min.css')}}">
    <link rel="shortcut icon" href="{{asset('front/images/fav.png')}}" type="image/x-icon">

    <style>
        .iti__flag {
            background-image: url("/shop/images/flags.png");
        }
    </style>

    @stack('meta')
    <title>{{ $page_title ?config('app.name') . " - $page_title" : config('app.name') }}</title>

    @stack('styles')
    @stack('style')
    @stack('pre-scripts')
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
@php
    $categoryRepository = new ECommerceCategoryRepository();
    $navBarCategories = $categoryRepository->getSubCategories([]);
    $featuredCategories = $categoryRepository->getFeaturedCategories();
@endphp
{{--<x-front::layout.drawer/>--}}
<x-front::e-commerce.layout.navbar :categories="$navBarCategories"/>
<x-front::e-commerce.layout.basket/>
<x-front::e-commerce.layout.user/>
<x-front::e-commerce.layout.categories :categories="$navBarCategories" :featuredCategories="$featuredCategories"/>
@yield('content')
<script src="{{asset('shop/js/intl-tel-input/jquery.steps.min.js')}}"></script>
<script src="{{asset('/shop/js/intl-tel-input/intlTelInput.js')}}"></script>
@stack('scripts')
<script>
    $('#search-form').on("submit", function (e) {
        let keyword = $('#search-keyword').val();
        if (keyword == "" || keyword == null) {
            e.preventDefault();
        }
    });
</script>
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-R0WGKNNDK7"></script>
<script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments);
    }

    gtag('js', new Date());

    gtag('config', 'G-R0WGKNNDK7');
</script>
</body>
</html>
