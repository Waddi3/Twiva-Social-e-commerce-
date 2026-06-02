@extends('front::layouts.app', [ 'pageTitle' => 'About' ])

@section('content')
    <main>
        <section class="bg-white px-3 py-24 md:px-20">
            <h1 class="text-primary-900 text-6xl text-center font-semibold leading-[72px]">About Us</h1>
            <p class="text-gray-500 text-xl text-center font-normal">Bridging businesses with influencers</p>
        </section>
        <section class="bg-white px-3 py-5 md:p-[90px]">
            <div class="grid grid-cols-2 gap-16">
                <div class="col-span-2 md:col-span-1 order-2 md:order-1 md:h-full md:flex md:flex-col md:justify-center">
                    <h2 class="text-primary-900 text-5xl text-center font-normal leading-[60px] mb-5 md:text-left">
                        Who we are
                    </h2>
                    <p class="text-gray-750 text-xl text-center font-normal mb-5 md:text-left">
                        Twiva is an Influencer-powered Social Commerce platform where Businesses Market and Resell their
                        products and services through social media influencers.
                    </p>
                    <p class="text-gray-750 text-xl text-center font-normal md:text-left">
                        Twiva was created out of a need to help businesses gain increased access to digital markets,
                        authentic content packaging and sales conversion at zero costs.
                    </p>
                </div>
                <div class="col-span-2 md:col-span-1 order-1 md:order-2 flex items-center">
                    <img class="shadow-[0_32px_64px_-12px_rgba(16,24,40,0.2)] rounded-lg max-w-full"
                         src="{{ asset('front/images/about/intro.svg') }}" alt="twiva-about-intro"/>
                </div>
            </div>
        </section>
        <x-front::layout.resellers class="bg-white py-[30px] md:p-[60px]"/>
        <section class="bg-gray-50 px-3 py-5 md:p-[60px]">
            <h2 class="text-primary-900 text-3xl text-center font-normal leading-[38px] mb-[30px] md:mb-[60px] md:text-4xl md:leading-[44px]">
                How to start earning as an influencer on Twiva
            </h2>
            <div class="flex justify-center md:px-[160px] mb-[30px] md:mb-[60px]">
                <iframe
                        class="w-full h-[200px] md:h-[560px]"
                        src="https://www.youtube.com/embed/PaonlyymeZo"
                        title="YouTube video player"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen
                ></iframe>
            </div>
            <div class="flex justify-center">
                <a href="{{config('twiva.bases.app_url')}}">
                    <x-front::form.button shape="fill" class="md:!w-[410px]">
                        Get Started
                    </x-front::form.button>
                </a>
            </div>
        </section>
        <section class="bg-gray-50 px-3 py-5 md:p-[60px]">
            <h2 class="text-primary-900 text-3xl text-center font-normal leading-[38px] mb-[30px] md:mb-[60px] md:text-4xl md:leading-[44px]">
                How to start marketing and selling through Influencers
            </h2>
            <div class="flex justify-center md:px-[160px] mb-[30px] md:mb-[60px]">
                <iframe
                        class="w-full h-[200px] md:h-[560px]"
                        src="https://www.youtube.com/embed/_itScCGZC98"
                        title="YouTube video player"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen
                ></iframe>
            </div>
            <div class="flex justify-center">
                <a href="{{config('twiva.bases.app_url')}}">
                    <x-front::form.button shape="fill" class="md:!w-[410px]">
                        Get Started
                    </x-front::form.button>
                </a>
            </div>
        </section>
        <x-front::layout.blog class="bg-white py-[30px] px-3 md:p-[60px]"/>
    </main>
    <x-front::layout.footer/>
@endsection
