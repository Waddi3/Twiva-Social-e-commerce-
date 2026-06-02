<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{asset('front/images/fav.png')}}" type="image/x-icon">

    <link href="{{ mix('/shop/css/app.css') }}" rel="stylesheet">

    @stack('style')

    @hasSection('title')
        <title>@yield('title')</title>
    @elseif(isset($pageTitle))
        <title>@yield($pageTitle)</title>
    @else
        <title>{{ config('app.name') }}</title>
    @endif
</head>
<body class="bg-[#8A0E49]">
<div class="sm:px-20 pt-5">
    @yield('content')

</div>
<script type="text/javascript" src="{{ mix('shop/js/app.js') }}"></script>
<script src="{{asset('shop/js/intl-tel-input/jquery.steps.min.js')}}"></script>
<script src="{{asset('/shop/js/intl-tel-input/intlTelInput.js')}}"></script>
<script src="{{asset('/shop/js/intl-tel-input/main.js')}}"></script>
@stack('script')
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-R0WGKNNDK7"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-R0WGKNNDK7');
</script>
</body>
</html>
