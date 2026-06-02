@php
    use Modules\Products\Repositories\ECommerce\ECommerceCategoryRepository;$categories = Modules\Home\Entities\Category::with('subcategories')->latest()->get();
    $categoryRepository = new ECommerceCategoryRepository();
    $navBarCategories = $categoryRepository->getSubCategories([]);
    $featuredCategories = $categoryRepository->getFeaturedCategories();

    $account = explode('.',request()->host())[0];
@endphp

        <!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @isset($shop)
        {!! $shop->facebook_domain_verification_code !!}
    @endisset
    @yield('meta')
    <link rel="shortcut icon" href="{{asset('front/images/fav.png')}}" type="image/x-icon">

    <link href="{{ mix('shop/css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('shop/css/intl-tel-input/intlTelInput.min.css')}}">
    <style>
        .iti__flag {
            background-image: url("/shop/images/flags.png");
        }
    </style>
    @stack('style')
    @stack('styles')

    @hasSection('title')
        <title>@yield('title')</title>
    @elseif(isset($pageTitle))
        <title>@yield($pageTitle)</title>
    @else
        <title>{{ config('app.name') }}</title>
    @endif
</head>
<body class="relative bg-gray-25 {{--pt-[62px] md:pt-20--}}">
<x-shop::partials.navbar class="py-[15px] px-[20px] md:px-[80px]" :categories="$navBarCategories"/>
<x-front::e-commerce.layout.user/>
<x-front::e-commerce.layout.basket/>

<x-shop::layout.categories :categories="$navBarCategories" :featuredCategories="$featuredCategories"
                           :account="$account ?? ''"/>
@yield('content')
<div class="py-8">
</div>
{{-- <x-shop::layout.footer/> --}}
<a class="fixed flex flex-col items-center bg-primary-50 right-3 bottom-3 w-[80px] h-[80px] rounded-full shadow-whatsapp border-[9px]
    border-solid border-[rgba(204,204,204,0.5)] z-30" href="https://wa.me/254708088114">
    <img class="w-[38px] h-[38px]" src="{{ asset('front/images/social-icons/whatsapp-colored.svg') }}" alt="whatsapp">
    <span class="text-black-100 text-xs text-center font-bold">Talk to us</span>
</a>
@stack('scripts_before')
<script type="text/javascript" src="{{ mix('shop/js/app.js') }}"></script>
<script src="{{asset('shop/js/intl-tel-input/jquery.steps.min.js')}}"></script>
<script src="{{asset('/shop/js/intl-tel-input/intlTelInput.js')}}"></script>
<script>
    $(document).ready(function () {
        setTimeout(function () {
            $("#toastMessage").fadeOut('fast')
        }, 3000)
    })

</script>

<script>
    $(document).ready(function () {
        $("#country_id").on('change', function () {
            let country_id = $(this).val()
            let CountiesRoute = "{{route('buyer.counties.list',['account' => request('account'),'country_id' => 'COUNTRY_ID' ])}}"
            CountiesRoute = CountiesRoute.replace('COUNTRY_ID', country_id)
            axios.get(CountiesRoute).then(function ({data}) {
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
                let CitiesRoute = "{{route('buyer.cities.list',['account' => request('account'),'state_id' => 'COUNTY_ID' ])}}"
                CitiesRoute = CitiesRoute.replace('COUNTY_ID', county_id)
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
            let CitiesRoute = "{{route('buyer.cities.list',['account' => request('account'),'state_id' => 'COUNTY_ID' ])}}"
            CitiesRoute = CitiesRoute.replace('COUNTY_ID', county_id)
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
            })
        });
    });
    setTimeout(function () {
        $("#country_id").change()

    }, 300)
</script>

@stack('scripts')
@stack('script')
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
