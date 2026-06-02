@extends('front::layouts.app', [ 'pageTitle' => 'Social Commerce' ])

@section('content')
    <main>
        <section class="bg-white grid grid-cols-2 gap-0 px-3 py-24 md:pr-12 md:pl-20 md:py-0">
            <div class="col-span-2 md:col-span-1 md:pl-8 md:h-full md:flex md:flex-col md:justify-center">
                <h1 class="hidden text-gray-900 text-6xl text-left font-semibold leading-[72px] md:block">
                    Earn anywhere, anytime using your phone
                </h1>
                <h1 class="text-gary-900 text-3xl text-left font-normal leading-[38px] mb-6 md:hidden">
                    Earn every time you sell
                </h1>
                <p class="text-gray-600 text-xl text-left font-normal leading-[30px] mb-6">
                    Twiva enables anyone to become an influencer and to start their online business without investing
                    any capital.
                </p>
                <div class="mb-12">
                    <a href="{{config('twiva.bases.app_url')}}">
                        <x-front::form.button shape="fill">
                            Start Selling
                        </x-front::form.button>
                    </a>
                </div>
                <p class="text-gray-600 text-sm text-left font-normal mb-5">
                    Get the Twiva mobile app:
                </p>
                <div class="flex justify-start">
                    <a href="https://play.google.com/store/apps/details?id=com.twiva.ke" title="Google Play">
                        <img src="{{ asset('front/images/badges/google-play.png') }}" class="w-[153px] h-12"
                             alt="google-play"/>
                    </a>
                    <a href="https://apps.apple.com/app/id6444508375?ls=1" title="App Store" class="ml-4">
                        <img src="{{ asset('front/images/badges/app-store.png') }}" class="w-[153px] h-12"
                             alt="app-store"/>
                    </a>
                </div>
            </div>
            <div class="hidden md:block col-span-2 md:col-span-1 mb-5 md:mb-0">
                <img src="{{ asset('front/images/social-commerce/intro.svg') }}" alt="twiva-social-commerce-intro"/>
            </div>
        </section>
        <section class="bg-white px-3 py-[60px] md:px-[60px] md:pt-[60px] md:pb-3.5">
            <h2 class=" text-gray-900 text-[30px] text-center font-normal leading-[38px] mb-[30px] md:text-5xl md:leading-[60px]">
                How Social Commerce Works
            </h2>
            <p class="text-gray-600 text-xl text-center font-normal mb-[30px]">
                Once you list products on Twiva, influencers start reselling them on their social media accounts. If
                1000 influencers, each with 3 accounts, select a product, it would be listed on 3000 online shops
                without you incurring any upfront cost.
            </p>
            <div class="flex justify-center mb-[30px]">
                <img src="{{ asset('front/images/social-commerce/how-works.svg') }}" alt="how-works">
            </div>
            <div class="flex justify-center">
                <a href="{{config('twiva.bases.app_url')}}">
                    <x-front::form.button class="bg-primary-50 !text-primary-700 rounded-lg md:w-[300px] md:h-20">
                        Start Selling
                    </x-front::form.button>
                </a>
            </div>
        </section>
        <x-front::layout.resellers class="bg-white py-[30px] md:p-[60px]"/>
        <section class="bg-white py-5 px-3 md:p-[60px]">
            <h2 class="text-primary-900 text-[30px] text-center font-normal leading-[38px] mb-5 mx:text-5xl md:leading-[60px] md:mb-[15px]">
                Benefits of Being a Twiva Influencer/Reseller
            </h2>
            <p class="text-gray-600 text-xl text-center font-normal mb-5 md:mb-[30px]">
                All you need is a social media account and a mobile phone to start earning
            </p>
            <div class="grid grid-cols-8 gap-5">
                <div class="col-span-full md:col-span-2 p-[30px] bg-gray-50 rounded-[10px] col-start-1 md:col-start-2">
                    <div class="flex justify-center mb-2">
                        <i class="text-primary-900 w-[27px] h-[27px]" data-feather="star"></i>
                    </div>
                    <h4 class="text-primary-900 text-xl text-center font-bold">
                        1. Start your own online business
                    </h4>
                    <p class="text-gray-600 text-xl text-center font-normal md:text-gray-800 md:text-lg">
                        Our Influencers/Resellers earn $250 per month on average
                    </p>
                </div>
                <div class="col-span-full md:col-span-2 p-[30px] bg-gray-50 rounded-[10px]">
                    <div class="flex justify-center mb-2">
                        <i class="text-primary-900 w-[27px] h-[27px]" data-feather="dollar-sign"></i>
                    </div>
                    <h4 class="text-primary-900 text-xl text-center font-bold">
                        2. Start at Zero cost
                    </h4>
                    <p class="text-gray-600 text-xl text-center font-normal md:text-gray-800 md:text-lg">
                        Create a free account on twiva, select products you want to sell on your social media pages and
                        make money every time someone buys from you.
                    </p>
                </div>
                <div class="col-span-full md:col-span-2 p-[30px] bg-gray-50 rounded-[10px]">
                    <div class="flex justify-center mb-2">
                        <i class="text-primary-900 w-[27px] h-[27px]" data-feather="award"></i>
                    </div>
                    <h4 class="text-primary-900 text-xl text-center font-bold">
                        3. Earn a commission on all sales
                    </h4>
                    <p class="text-gray-600 text-xl text-center font-normal md:text-gray-800 md:text-lg">
                        Earn 5-10% in commissions for every successful sale you make
                    </p>
                </div>
            </div>
        </section>
        <x-front::layout.social-commerce-clients class="bg-white pt-[60px] pb-5 px-0 md:p-[60px]"/>
    </main>
    <x-front::layout.footer/>
@endsection
