<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="{{ asset('shop/css/app.css') }}" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{asset('shop/css/intl-tel-input/intlTelInput.min.css')}}">
    <link rel="shortcut icon" href="{{asset('front/images/fav.png')}}" type="image/x-icon">

    <style>
        .iti__flag {
            background-image: url("/shop/images/flags.png");
        }
    </style>
    @hasSection('title')
        <title>@yield('title')</title>
    @elseif(isset($pageTitle))
        <title>{{ $pageTitle }}</title>
    @else
        <title>{{ config('app.name') }}</title>
    @endif
</head>
<body>
<nav class="bg-gray-25 lg:bg-primary-900 h-[60px] lg:!h-20 flex justify-between lg:justify-center items-center py-3.5 lg:py-6 pl-3 pr-[18px]">
    <img class="hidden lg:block max-h-full" src="{{ asset('shop/images/logos/Logo-white.svg') }}" alt="twiva-logo">
    <img class="block lg:hidden max-h-full" src="{{ asset('shop/images/logos/Logo.png') }}" alt="twiva-logo">
    <a href="#" class="block lg:hidden">
        <i data-feather="x" class="text-gray-800 w-8 h-8"></i>
    </a>
</nav>
@yield('content')
<script type="text/javascript" src="{{ asset('front/js/app.js') }}"></script>
<script src="{{asset('shop/js/intl-tel-input/jquery.steps.min.js')}}"></script>
<script src="{{asset('/shop/js/intl-tel-input/intlTelInput.js')}}"></script>

@stack('script')
<script>

    $(document).ready(function () {
        $("#country_id").on('change', function () {
            let country_id = $(this).val()
            let CountiesRoute = `{{route('e-commerce.buyer.counties.list')}}?country_id=${country_id}`
            CountiesRoute = CountiesRoute.replace('COUNTRY_ID', country_id)
            axios.get(CountiesRoute).then(function ({data}) {
                console.log(data);
                var $el = $("#county_id");
                $el.empty(); // remove old options
                $el.append($("<option></option>")
                    .attr('disabled', 'disabled')
                    .attr('selected', 'selected')
                    .attr("value", null)
                    .text('select please'));
                $.each(data, function (key, value) {
                    $el.append($("<option></option>")
                        .attr("value", value['id']).text(value['name']));
                });
                let county_id = $("#county_id").val()
                let CitiesRoute = `{{route('e-commerce.buyer.cities.list')}}?state=${county_id}`
                axios.get(CitiesRoute).then(function ({data}) {
                    var $el = $("#city_id");
                    $el.empty(); // remove old options
                    $el.append($("<option></option>")
                        .attr('disabled', 'disabled')
                        .attr('selected', 'selected')
                        .attr("value", null)
                        .text('select please'));
                    $.each(data, function (key, value) {
                        $el.append($("<option></option>")
                            .attr("value", value['id']).text(value['name']));
                    });

                });
            })
        });

        $("#county_id").on('change', function () {
            let county_id = $(this).val()
            let CitiesRoute = `{{route('e-commerce.buyer.cities.list')}}?state_id=${county_id}`
            axios.get(CitiesRoute).then(function ({data}) {
                var $el = $("#city_id");
                $el.empty(); // remove old options
                $el.append($("<option></option>")
                    .attr('disabled', 'disabled')
                    .attr('selected', 'selected')
                    .attr("value", null)
                    .text('select please'));
                $.each(data, function (key, value) {
                    $el.append($("<option></option>")
                        .attr("value", value['id']).text(value['name']));
                });

                common.getUserCart();
            })
        });
    });
    setTimeout(function () {
        $("#country_id").change()
    }, 300)
    $(document).ready(function () {
        setTimeout(function () {
            $("#toastMessage").fadeOut('fast')
        }, 3000)
    })

</script>
@yield('teleport')
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
