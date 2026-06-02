@extends('front::layouts.app', [ 'pageTitle' => 'Home' ])

@section('content')
    <main class="grid">
        <section class="bg-white grid grid-cols-2 gap-0 px-[18px] py-[30px] md:pr-12 md:pl-20 md:py-0 order-1">
            <div
                class="col-span-2 md:col-span-1 order-2 md:order-1 md:pl-8 md:h-full md:flex md:flex-col md:justify-center">
                <h1 class="text-black-100 text-3xl text-center font-bold leading-[41px] md:text-6xl md:text-left md:font-semibold md:leading-[72px] mb-6">
                    Market and Sell through Influencers
                </h1>
                <p class="text-gray-600 text-sm text-center font-normal md:text-xl md:text-left md:leading-[30px] mb-3.5 md:mb-12">
                    influencer-powered social commerce platform
                </p>
                <div class="mb-3.5 md:mb-12">
                    <a href="{{config('twiva.bases.app_url')}}">
                        <x-front::form.button href="{{config('twiva.bases.app_url')}}"
                                              class="w-full md:w-auto" shape="fill">
                            Join Twiva
                        </x-front::form.button>
                    </a>
                </div>
                <p class="text-gray-600 text-sm text-center font-normal md:text-left mb-5">
                    Get the Twiva mobile app:
                </p>
                <div class="flex justify-center md:justify-start px-4 md:px-0">
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
            <div class="col-span-2 md:col-span-1 order-1 md:order-2 pt-4 pb-8 md:pt-9 md:pb-[70px]">
                <img src="{{ asset('front/images/home/intro.svg') }}" alt="twiva-home-intro"/>
            </div>
        </section>
        <section class="bg-gray-50 py-[30px] px-4 md:p-[60px] order-2">
            <h2 class="text-primary-900 text-3xl text-center font-normal leading-[60px] mb-5 md:text-5xl md:mb-10">
                How Twiva Works
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-8 gap-[30px] text-xl text-center mb-5 md:mb-[60px]">
                <div class="col-span-1 md:col-span-3 col-start-1 md:col-start-2">
                    <h4 class="text-primary-900 font-bold">Social commerce:</h4>
                    <p class="text-gray-800 font-normal">
                        Businesses register and list products. Influencers add the products to their Twiva shops and
                        market and sell them across all their social media pages.
                    </p>
                </div>
                <div class="col-span-1 md:col-span-3 col-start-1 md:col-start-5">
                    <h4 class="text-primary-900 font-bold">Influencer marketing:</h4>
                    <p class="text-gray-800 font-normal">
                        Businesses create influencer campaign briefs on the platform and select the influencers they
                        want to work with.
                    </p>
                </div>
            </div>
            <div class="flex justify-center items-center md:pl-[6%]">
                <img src="{{ asset('front/images/home/how-work.png') }}" alt="how-work"/>
            </div>
        </section>
        <section class="bg-white py-5 md:p-[60px] order-3">
            <h2 class="text-primary-900 text-2xl text-center font-semibold mb-5 px-4 md:text-5xl md:font-normal md:leading-[60px] md:px-0">
                Why we love what we do
            </h2>
            <p class="text-gray-600 text-xl font-normal text-center mb-[30px] px-4 md:mb-5 md:px-0">
                We help SMEs increase their sales and give influencers dignified digital jobs by connecting the two!
            </p>
            <div class="grid grid-cols-3 gap-0">
                <div class="col-span-3 md:col-span-1 flex flex-col items-center p-[30px]">
                    <img class="w-[140px] h-[140px] rounded-full mb-[30px]"
                         src="{{ asset('front/images/home/why-love-kana.png') }}" alt="kana"/>
                    <p class="text-primary-800 text-lg text-center font-normal mb-[5px]">Felix, Owner</p>
                    <h5 class="text-primary-900 text-2xl text-center font-bold mb-[5px]">KANA</h5>
                    <p class="text-gray-800 text-xl text-center font-normal mb-[5px]">Goat milk beauty products</p>
                    <p class="text-gray-800 text-base text-center font-normal">
                        I first heard of Twiva when I had just launched a new product into the market. I wasn't sure how
                        I would partner with influencers. Upon signing up on the platform and listing my products, I was
                        able to get influencers to market and sell all my units at no cost!Twiva was also kind enough to
                        help me create free content for my products in their ultra-modern studio.
                    </p>
                </div>
                <div class="col-span-3 md:col-span-1 flex flex-col items-center p-[30px]">
                    <img class="w-[140px] h-[140px] rounded-full mb-[30px]"
                         src="{{ asset('front/images/home/why-love-nairobi-shoes.png') }}" alt="nairobi-shoes"/>
                    <p class="text-primary-800 text-lg text-center font-normal mb-[5px]">David, Owner</p>
                    <h5 class="text-primary-900 text-2xl text-center font-bold mb-[5px]">Nairobi Shoes</h5>
                    <p class="text-gray-800 text-xl text-center font-normal mb-[5px]">Trendy Men’s Footwear</p>
                    <p class="text-gray-800 text-base text-center font-normal">
                        I run a men’s shoes business in Nairobi. Knowing I have numerous competitors, I was hoping to
                        get more business and better content. Twiva allowed me to work with hundreds of influencers for
                        free just by signing up on their platform. They also took professional photos of my shoes which
                        are now on high demand.
                    </p>
                </div>
                <div class="col-span-3 md:col-span-1 flex flex-col items-center p-[30px]">
                    <img class="w-[140px] h-[140px] rounded-full mb-[30px]"
                         src="{{ asset('front/images/home/why-love-thlokomelo-designs.png') }}"
                         alt="thlokomelo-designs"/>
                    <p class="text-primary-800 text-lg text-center font-normal mb-[5px]">Paulina, Owner</p>
                    <h5 class="text-primary-900 text-2xl text-center font-bold mb-[5px]">Thlokomelo Designs</h5>
                    <p class="text-gray-800 text-xl text-center font-normal mb-[5px]">Apparel & Clothing</p>
                    <p class="text-gray-800 text-base text-center font-normal">
                        I create trendy, elegant, classic and chic leather products. This is why I needed the right
                        exposure to reach my target audience which is mainly women in the corporate sector. Twiva was
                        just the boost I needed! Not only did I register on their platform and work with influencers at
                        no cost, they also helped me produce good quality content for my products.
                    </p>
                </div>
            </div>
        </section>
        <x-front::layout.clients class="bg-white py-[60px] px-0 md:p-[60px] order-5 md:order-4"/>
        <x-front::layout.partners class="bg-white py-[60px] px-3 md:px-[60px] order-6 md:order-5"/>
        <x-front::layout.blog class="bg-white py-[30px] px-3 md:p-[60px] order-4 md:order-6"/>
        <section
            class="bg-primary-1000 relative px-0 pt-0 pb-0 md:px-[88px] md:pt-[77px] md:pb-[115px] rounded-t-lg md:rounded-t-none order-7">
            <div
                class="md:absolute md:inset-0 bg-cover bg-no-repeat bg-right-center min-h-[375px] flex justify-start items-end px-3.5 py-11 rounded-t-lg md:rounded-t-none md:px-0 md:py-0 z-0"
                style="background-image: url('{{ asset('front/images/home/pexels-george-milton.png') }}')"
            >
                <h3 class="text-white md:hidden text-[30px] font-normal leading-[38px]">News & Events</h3>
            </div>
            <div
                class="relative grid grid-cols-3 mt-[-22px] md:mt-0 gap-0 bg-white md:bg-transparent rounded-t-lg md:rounded-t-none z-10">
                <div class="col-span-3 lg:col-span-2">
                    <h3 class="text-white text-5xl leading-[60px] font-normal hidden md:block mb-5">Stay updated</h3>
                    <div class="bg-white p-5 md:p-6 rounded-lg">
                        <div class="grid grid-cols-2 gap-[30px]">
                            <div class="col-span-2 md:col-span-1">
                                <h6 class="text-primary-900 text-base text-center font-bold mb-1 md:text-left">Get our
                                    weekly bulletin</h6>
                                <p class="text-gray-950 text-xs text-center font-normal md:text-left">
                                    Be in the know every time we have business offers for MSMEs, discounts, influencer
                                    marketing events and exciting influencer gigs!
                                </p>
                            </div>
                            <div class="col-span-2 md:col-span-1 flex justify-start flex-col md:flex-row">
                                <div class="mb-3 md:mb-0">
                                    <label class="text-gray-750 text-sm font-normal mb-1.5">Subscribe to updates</label>
                                    <x-front::form.input type="email" inputid="subscriber-email" icon="mail"
                                                         placeholder="you@untitledui.com"/>
                                </div>
                                <div class="md:flex items-end md:pl-3">
                                    <x-front::form.button id="subscriber-btn" type="submit" shape="fill" class="w-full">
                                        Subscribe
                                    </x-front::form.button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <x-front::layout.footer/>
@endsection
