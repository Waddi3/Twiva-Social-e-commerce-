@extends('front::layouts.app', [ 'pageTitle' => 'Our Services' ])

@section('content')
    <main>
        <section class="bg-white grid grid-cols-2 gap-0 px-3.5 py-[50px] md:px-20 md:py-0">
            <div class="col-span-2 md:col-span-1 md:h-full md:flex md:flex-col md:justify-center mb-2.5 md:mb-0">
                <h1 class="text-primary-900 text-3xl text-center font-bold leading-[38px] mb-2.5 md:text-6xl md:text-left md:font-semibold md:leading-[72px] md:mb-6">
                    Our Services
                </h1>
                <p class="text-gray-600 text-xl text-center font-normal leading-[30px] md:text-left">
                    We create lasting partnerships between brands and influencers.
                </p>
            </div>
            <div class="col-span-2 md:col-span-1">
                <img src="{{ asset('front/images/services/intro.png') }}" alt="twiva-services-intro"/>
            </div>
        </section>
        <section class="bg-white py-[60px] px-2.5 md:px-[60px]">
            <h2 class="text-gray-900 text-3xl text-center font-normal leading-[38px] mb-[30px] md:text-5xl md:leading-[60px]">
                Core Services
            </h2>
            <p class="text-gray-600 md:hidden text-xl text-center font-normal mb-[60px]">
                Businesses list products, influencers add them to their Twiva shops and resell them on all their social
                media pages
            </p>
            <div x-data="scroller()" x-init="activeClass = 'tab-active'"
                 class="flex justify-start md:justify-center gap-2 py-5 overflow-x-auto mb-2.5 md:mb-0">
                <x-front::form.button class="whitespace-nowrap tab-active" data-target="socialCommere" @click="scroll">
                    Social Commerce
                </x-front::form.button>
                <x-front::form.button class="whitespace-nowrap" data-target="influencersMarketing" @click="scroll">
                    Influencer Marketing
                </x-front::form.button>
                <x-front::form.button class="whitespace-nowrap" data-target="socialListening" @click="scroll">Social
                    Listening
                </x-front::form.button>
                <x-front::form.button class="whitespace-nowrap" data-target="reporting" @click="scroll">Reporting
                </x-front::form.button>
                <x-front::form.button class="whitespace-nowrap" data-target="training" @click="scroll">Training
                </x-front::form.button>
                <x-front::form.button class="whitespace-nowrap" data-target="content" @click="scroll">Content
                </x-front::form.button>
            </div>
            <div class="grid gap-[30px] md:gap-24 px-1 md:px-8">
                <div class="grid grid-cols-2 gap-[30px] md:gap-24" id="socialCommere">
                    <div class="col-span-2 md:col-span-1 order-2 md:order-1 flex flex-col justify-center">
                        <h2 class="text-gray-950 text-[30px] font-normal leading-[38px] mb-4">Social Commerce</h2>
                        <p class="text-gray-800 text-xl font-normal mb-8">
                            We enable businesses to sell more products through thousands of online
                            influencers/resellers. All you need to do is list your products. Influencers add them to
                            their Twiva shops and resell them across multiple social media platforms. Influencers earn a
                            commission only when they sell a product.
                        </p>
                        <div class="flex">
                            <a href="{{ route('home.social-commerce') }}">
                            <x-front::form.button class="bg-primary-50 !text-primary-700 rounded-lg">Learn More
                            </x-front::form.button>
                            </a>

                            <a href="{{config('twiva.bases.app_url')}}">
                                <x-front::form.button class="ml-3.5" shape="fill">Join Twiva</x-front::form.button>
                            </a>
                        </div>
                    </div>
                    <div class="col-span-2 md:col-span-1 order-1 md:order-2">
                        <img src="{{ asset('front/images/services/social-commerce.svg')  }}" alt="social-commerce">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-[30px] md:gap-24" id="influencersMarketing">
                    <div class="col-span-2 md:col-span-1 order-2 flex flex-col justify-center">
                        <h2 class="text-gray-950 text-[30px] font-normal leading-[38px] mb-4">Influencers Marketing</h2>
                        <p class="text-gray-800 text-xl font-normal mb-8">
                            We allow you to choose the influencers you want to work with. Simply create a campaign
                            brief, select as many influencers as you wish and get accurate engagement analytics for each
                            influencer.
                        </p>
                        <div class="flex">
                            <a href="{{route('home.influencer-marketing')}}">
                                <x-front::form.button class="bg-primary-50 !text-primary-700 rounded-lg">Learn More
                                </x-front::form.button>
                            </a>
                            <a href="{{config('twiva.bases.app_url')}}">
                                <x-front::form.button class="ml-3.5" shape="fill">Join Twiva</x-front::form.button>
                            </a>
                        </div>
                    </div>
                    <div class="col-span-2 md:col-span-1 order-1">
                        <img src="{{ asset('front/images/services/influencers-marketing.svg')  }}"
                             alt="influencers-marketing">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-[30px] md:gap-24" id="socialListening">
                    <div class="col-span-2 md:col-span-1 order-2 md:order-1 flex flex-col justify-center">
                        <h2 class="text-gray-950 text-[30px] font-normal leading-[38px] mb-4" id="social-listening">Social Listening</h2>
                        <p class="text-gray-800 text-xl font-normal mb-8">
                            We monitor influencer marketing campaigns, measure impact and offer real-time tracking using
                            advanced social listening tools.
                        </p>
                        <div class="flex">
                            <a href="{{config('twiva.bases.app_url')}}">

                                <x-front::form.button shape="fill">Join Twiva</x-front::form.button>
                            </a>
                        </div>
                    </div>
                    <div class="col-span-2 md:col-span-1 order-1 md:order-2">
                        <img src="{{ asset('front/images/services/social-listening.svg')  }}" alt="social-listening">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-[30px] md:gap-24" id="reporting">
                    <div class="col-span-2 md:col-span-1 order-2 flex flex-col justify-center">
                        <h2 class="text-gray-950 text-[30px] font-normal leading-[38px] mb-4">Reporting</h2>
                        <p class="text-gray-800 text-xl font-normal mb-8">
                            We prepare comprehensive campaign reports highlighting success metrics such as reach,
                            impressions, engagement, sentiment analysis etc.
                        </p>
                        <div class="flex">
                            <a href="{{config('twiva.bases.app_url')}}">

                                <x-front::form.button shape="fill">Join Twiva</x-front::form.button>
                            </a>
                        </div>
                    </div>
                    <div class="col-span-2 md:col-span-1 order-1">
                        <img src="{{ asset('front/images/services/reporting.svg')  }}" alt="reporting">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-[30px] md:gap-24" id="training">
                    <div class="col-span-2 md:col-span-1 order-2 md:order-1 flex flex-col justify-center">
                        <h2 class="text-gray-950 text-[30px] font-normal leading-[38px] mb-4">Training</h2>
                        <p class="text-gray-800 text-xl font-normal mb-8">
                            We offer weekly, monthly and quarterly trainings for businesses and influencers in areas
                            such as; online brand positioning and content creation masterclasses
                        </p>
                        <div class="flex">
                            <a href="{{config('twiva.bases.app_url')}}">

                                <x-front::form.button shape="fill">Join Twiva</x-front::form.button>
                            </a>
                        </div>
                    </div>
                    <div class="col-span-2 md:col-span-1 order-1 md:order-2">
                        <img src="{{ asset('front/images/services/training.svg')  }}" alt="training">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-[30px] md:gap-24" id="content">
                    <div class="col-span-2 md:col-span-1 order-2 flex flex-col justify-center">
                        <h2 class="text-gray-950 text-[30px] font-normal leading-[38px] mb-4">Content</h2>
                        <p class="text-gray-800 text-xl font-normal mb-8">
                            The Twiva Studio is an ultra-modern facility designed to help businesses and influencers
                            market and sell better by creating uniquely packaged content.
                        </p>
                        <div class="flex">
                            <a href="{{config('twiva.bases.app_url')}}">

                                <x-front::form.button shape="fill">Join Twiva</x-front::form.button>
                            </a>
                        </div>
                    </div>
                    <div class="col-span-2 md:col-span-1 order-1">
                        <img src="{{ asset('front/images/services/content.svg')  }}" alt="content">
                    </div>
                </div>
            </div>
        </section>
        <x-front::layout.clients class="bg-white py-[60px] px-0 md:p-[60px]"/>
    </main>
    <x-front::layout.footer/>
@endsection
