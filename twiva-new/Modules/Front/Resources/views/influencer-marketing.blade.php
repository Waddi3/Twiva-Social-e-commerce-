@extends('front::layouts.app', [ 'pageTitle' => 'Influencer Marketing' ])

@section('content')
    <main>
        <section class="bg-white grid grid-cols-2 gap-0 px-4 pt-2.5 pb-[60px] md:pr-12 md:pl-20 md:py-0">
            <div class="col-span-2 md:col-span-1 order-2 md:order-1 md:pl-8 md:h-full md:flex md:flex-col md:justify-center">
                <h1 class="text-gary-900 text-3xl text-center font-normal leading-[38px] mb-6 md:text-6xl md:text-left md:font-semibold md:leading-[72px]">
                    Collaborate with the Right Creators
                </h1>
                <p class="text-gray-600 text-xl text-center font-normal leading-[30px] mb-6 md:text-left">
                    Harness the power of nano and micro-influencers at Scale. Start your first influencer marketing
                    campaign.
                </p>
                <div class="mb-5 md:mb-12">
                    <a href="{{config('twiva.bases.app_url')}}">
                        <x-front::form.button class="w-full md:w-auto" shape="fill">
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
            <div class="col-span-2 md:col-span-1 order-1 md:order-2 mb-5 md:mb-0">
                <img src="{{ asset('front/images/influencer-marketing/intro.png') }}"
                     alt="twiva-influencer-marketing-intro"/>
            </div>
        </section>
        <section class="bg-white px-3 py-[30px] md:p-[60px]">
            <h2 class=" text-gray-900 text-[30px] text-center font-normal leading-[38px] mb-[30px] md:text-5xl md:leading-[60px]">
                How Our Influencer Marketing Works
            </h2>
            <p class="text-gray-600 text-xl text-center font-normal mb-[30px]">
                We offer End To End Influencer Marketing services. Twiva enables you to activate the right influencers
                to collaborate with on an influencer marketing campaign. The platform removes the human bias by allowing
                you to select influencers by platform, sector, content type, follower count, engagement rate, influencer
                demographic info, and by influencer audience demographic info.”
            </p>
            <div class="flex justify-center mb-[30px]">
                <img src="{{ asset('front/images/influencer-marketing/how-work.svg') }}" alt="how-works">
            </div>
            <div class="flex justify-center">
                <a href="{{config('twiva.bases.app_url')}}">
                    <x-front::form.button class="bg-primary-50 !text-primary-700 rounded-lg md:w-[300px] md:h-20">
                        Get Started
                    </x-front::form.button>
                </a>
            </div>
        </section>
        <section class="bg-white px-4 py-[60px] md:px-[60px] md:py-6">
            <div x-data="scroller()" x-init="activeClass = 'tab-active'"
                 class="flex justify-start gap-2 md:justify-center py-5 overflow-x-auto mb-10">
                <x-front::form.button class="whitespace-nowrap tab-active" data-target="influencersActivation"
                                      @click="scroll">Influencers Activation
                </x-front::form.button>
                <x-front::form.button class="whitespace-nowrap" data-target="contentCreation" @click="scroll">Content
                    Creation
                </x-front::form.button>
                <x-front::form.button class="whitespace-nowrap" data-target="campaignManagement" @click="scroll">
                    Campaign Management
                </x-front::form.button>
                <x-front::form.button class="whitespace-nowrap" data-target="monitoring" @click="scroll">Monitoring
                </x-front::form.button>
                <x-front::form.button class="whitespace-nowrap" data-target="analysisAndReporting" @click="scroll">
                    Analysis and Reporting
                </x-front::form.button>
            </div>
            <div class="grid gap-[30px] md:gap-24 px-1 md:px-8">
                <div class="grid grid-cols-2 gap-[30px] md:gap-24" id="influencersActivation">
                    <div class="col-span-2 md:col-span-1 order-2 md:order-1 flex flex-col justify-center">
                        <h2 class="text-gray-950 text-[30px] font-normal leading-[38px] mb-4">Influencers
                            Activation</h2>
                        <p class="text-gray-800 text-xl font-normal mb-8">
                            Create a campaign brief, Twiva recommends influencers you should work with, and instantly
                            get accurate engagement analytics for each selected influencer.
                        </p>
                        <div class="flex">
                            <a href="{{config('twiva.bases.app_url')}}">
                                <x-front::form.button shape="fill">Join Twiva</x-front::form.button>
                            </a>
                        </div>
                    </div>
                    <div class="col-span-2 md:col-span-1 order-1 md:order-2 flex justify-center">
                        <img src="{{ asset('front/images/influencer-marketing/influencers-activation.svg')  }}"
                             alt="influencers-activation">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-[30px] md:gap-24" id="contentCreation">
                    <div class="col-span-2 md:col-span-1 order-2 flex flex-col justify-center">
                        <h2 class="text-gray-950 text-[30px] font-normal leading-[38px] mb-4" id="content-creation">Content Creation</h2>
                        <p class="text-gray-800 text-xl font-normal mb-1">
                            We provide a detailed influencer content strategy plan. We also offer content creation for
                            Micro, Small and Medium Enterprises (MSMEs) such as product photography, promo videos, and
                            influencer and brand content creation.
                        </p>
                        <p class="text-gray-800 text-xl font-normal mb-8">
                            The Twiva ultra-modern studio is a state-of-the-art facility fully equipped with advanced
                            production equipment and resources.
                        </p>
                        <div class="flex">
                            <a href="{{config('twiva.bases.app_url')}}">
                                <x-front::form.button shape="fill">Join Twiva</x-front::form.button>
                            </a>
                        </div>
                    </div>
                    <div class="col-span-2 md:col-span-1 order-1 flex justify-center">
                        <img src="{{ asset('front/images/influencer-marketing/content-creation.svg')  }}"
                             alt="content-creation">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-[30px] md:gap-24" id="campaignManagement">
                    <div class="col-span-2 md:col-span-1 order-2 md:order-1 flex flex-col justify-center">
                        <h2 class="text-gray-950 text-[30px] font-normal leading-[38px] mb-4">Campaign Management</h2>
                        <p class="text-gray-800 text-xl font-normal mb-8">
                            Twiva manages your campaign end-to-end and closely monitors performance to make sure you
                            have a return on your investment.
                        </p>
                        <div class="flex">
                            <a href="{{config('twiva.bases.app_url')}}">
                                <x-front::form.button shape="fill">Join Twiva</x-front::form.button>
                            </a>
                        </div>
                    </div>
                    <div class="col-span-2 md:col-span-1 order-1 md:order-2 flex justify-center">
                        <img src="{{ asset('front/images/influencer-marketing/campaign-management.svg')  }}"
                             alt="campaign-management">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-[30px] md:gap-24" id="monitoring">
                    <div class="col-span-2 md:col-span-1 order-2 flex flex-col justify-center">
                        <h2 class="text-gray-950 text-[30px] font-normal leading-[38px] mb-4">Monitoring</h2>
                        <p class="text-gray-800 text-xl font-normal mb-8">
                            Cut through the noise of social media and know what people are saying about your products
                            and services. It might just be the insight you need to increase your sales.
                        </p>
                        <div class="flex">
                            <a href="{{config('twiva.bases.app_url')}}">
                                <x-front::form.button shape="fill">Join Twiva</x-front::form.button>
                            </a>
                        </div>


                    </div>
                    <div class="col-span-2 md:col-span-1 order-1 flex justify-center">
                        <img src="{{ asset('front/images/influencer-marketing/monitoring.svg')  }}" alt="monitoring">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-[30px] md:gap-24" id="analysisAndReporting">
                    <div class="col-span-2 md:col-span-1 order-2 md:order-1 flex flex-col justify-center">
                        <h2 class="text-gray-950 text-[30px] font-normal leading-[38px] mb-4">Analysis and
                            Reporting</h2>
                        <p class="text-gray-800 text-xl font-normal mb-8">
                            Measure what matters with Untitled’s easy-to-use reports. You can filter, export, and drill
                            down on the data in a couple of clicks.
                        </p>
                        <div class="flex">
                            <a href="{{config('twiva.bases.app_url')}}">
                                <x-front::form.button shape="fill">Join Twiva</x-front::form.button>
                            </a>
                        </div>
                    </div>
                    <div class="col-span-2 md:col-span-1 order-1 md:order-2 flex justify-center">
                        <img src="{{ asset('front/images/influencer-marketing/analysis-and-reporting.svg')  }}"
                             alt="analysis-and-reporting">
                    </div>
                </div>
            </div>
        </section>
        <x-front::layout.clients class="bg-white py-[60px] px-0 md:p-[60px]"/>
    </main>
    <x-front::layout.footer/>
@endsection
