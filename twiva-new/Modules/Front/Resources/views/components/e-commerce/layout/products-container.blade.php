@php use Modules\System\Entities\ConstantSystem; @endphp
        <!-- resources/views/components/hot-deals.blade.php -->
<div class="bg-white py-10">
    <h2 class="text-4xl font-bold text-center text-gray-800 mb-10">
        <span class="text-primary-800 underline cursor-pointer"><a
                    href="{{ config('twiva.bases.url') }}">Market</a></span> and <span
                class="text-warning-800 underline cursor-pointer"><a
                    href="{{ config('twiva.bases.app_url') . "/#/auth/sign_up" }}">Sell</a></span> through Influencers
    </h2>
    <div class="container grid grid-cols-1 lg:flex lg:space-between gap-4 lg:gap-6 mx-auto lg:items-center">

        <div onclick="window.open('{{ ConstantSystem::getUrlBanner() }}','mywindow');"
             class="rounded-lg lg:flex-1 bg-no-repeat bg-cover bg-center lg:h-[450px] hidden lg:block cursor-pointer"
             style="background-image: url({{ ConstantSystem::getBanner() }}); background-position: center;">
            {{--<img src="{{asset('front/images/ecommerce/laborday.png')}}" alt="Labor day">--}}
        </div>
        <button onclick="window.open('{{ ConstantSystem::getUrlBanner() }}','mywindow');" class="cursor-pointer">
            <img src='{{ConstantSystem::getBanner()}}' class="lg:hidden w-full"/>
        </button>
        <div class="rounded-lg bg-primary-800 w-full lg:w-1/5 mx-auto p-2">
            <div class="wheel-container flex justify-center items-center lg:block h-5/6 overflow-hidden ">
                <div class="wheel-divider w-[200px] overflow-hidden relative float-right mx-auto lg:mr-9 h-[374px]">
                    <div class="wheel w-[400px] h-full absolute top-0 right-0">

                    </div>
                </div>
            </div>
            <div id='coupon-container' class="text-center text-white my-2 flex flex-col items-center justify-center"
                 style="display: none">
                <p class="text-xs">You won:</p>
                <p class="text-[#A6F4C5] font-semibold text-xl mb-2"><span id='coupon-value'>5</span> to your order
                    &#129395;</p>
                <p class="text-xs font-semibold mb-2">Congrats! Come back again tomorrow</p>
                <p id='coupon-text' class='text-sm mb-2' style="display: none">This is your coupon code: <span
                            id='coupon-code'></span></p>
                <button id='copy-coupon-button'
                        class="bg-[#E1E0E2] text-[#626163] font-semibold rounded-lg h-11 text-center cursor-pointer w-3/4 flex items-center justify-center gap-2">
                    <i data-feather="copy" class="w-5 h-5"></i>COPY COUPON CODE
                </button>
            </div>
            <p id='coupon-info-text' class='text-center text-white my-2  text-sm' style="display: none"></p>
            <div class="flex justify-center mb-4">
                <button class="wheel-button  bg-[#E1E0E2] text-[#626163] font-semibold rounded-lg h-11 text-center cursor-pointer w-3/4">
                    CLICK TO SPIN
                </button>
            </div>
        </div>
    </div>
</div>

<div id='hot-deals-container' class="container mx-auto py-10">
    <h2 class="text-3xl font-bold text-center text-primary-800 mb-6">Hot Deals</h2>
    <div id="hot-deals" class="grid grid-cols-1 lg:grid-cols-4 gap-6 lg:col-span-3"></div>
    <div class="flex justify-center mt-6">
        <button id="hot-deals-view-more"
                class="text-primary-700 bg-white px-6 py-2 rounded-lg border-2 font-semibold border-slate-300">
            View More
            <i data-feather="arrow-right" class="inline text-warning-800 ml-2"></i>
        </button>
    </div>
</div>

<div class="container mx-auto pb-8 pt-8">
    <h2 class="text-3xl font-bold text-center text-primary-800 mb-6">Trending products</h2>
    <div id="trending-products" class="grid grid-cols-1 lg:grid-cols-4 gap-6 lg:col-span-3"></div>
    <div class="flex justify-center mt-6">
        <button id="trending-products-view-more"
                class="text-primary-700 bg-white px-6 py-2 rounded-lg border-2 font-semibold border-slate-300">
            View More
            <i data-feather="arrow-right" class="inline text-warning-800 ml-2"></i>
        </button>
    </div>
</div>

{{-- Trusted Influencers --}}
<div class="container mx-auto pb-8 pt-8">
    <h2 class="text-3xl font-bold text-center text-primary-800 mb-10">10K+ Trusted Influencers</h2>
    <div class="grid grid-cols-6 gap-6 col-span-3 w-9/12 md:w-3/5 mx-auto">
        <div class="lg:w-[80px] lg:h-[80px] w-[50px] h-[50px] rounded-full bg-cover bg-no-repeat"
             style="background-image: url({{asset('images/influencers/b983e7e4a345814a9d2ce4cc1e35786a.png')}});"></div>
        <div class="lg:w-[80px] lg:h-[80px] w-[50px] h-[50px] rounded-full bg-cover bg-no-repeat"
             style="background-image: url({{asset('images/influencers/17c83d31c01f078f9c70121ac23b1ba9.png')}});"></div>
        <div class="lg:w-[80px] lg:h-[80px] w-[50px] h-[50px] rounded-full bg-cover bg-no-repeat"
             style="background-image: url({{asset('images/influencers/220304ab5eacf5783e3c817237ec373a.png')}});"></div>
        <div class="lg:w-[80px] lg:h-[80px] w-[50px] h-[50px] rounded-full bg-cover bg-no-repeat"
             style="background-image: url({{asset('images/influencers/d3d4a0123480a8f2f7a6fa224b649767.png')}});"></div>
        <div class="lg:w-[80px] lg:h-[80px] w-[50px] h-[50px] rounded-full bg-cover bg-no-repeat"
             style="background-image: url({{asset('images/influencers/0bb97a4094bf3cf838e9cb4d7301c95b.png')}});"></div>
        <div class="lg:w-[80px] lg:h-[80px] w-[50px] h-[50px] rounded-full bg-cover bg-no-repeat"
             style="background-image: url({{asset('images/influencers/585a83e060e3b67bc48ee04e4d27e9a0.png')}});"></div>
        <div class="lg:w-[80px] lg:h-[80px] w-[50px] h-[50px] rounded-full bg-cover bg-no-repeat"
             style="background-image: url({{asset('images/influencers/8613564568c52ea604330935e5b73005.png')}});"></div>
        <div class="lg:w-[80px] lg:h-[80px] w-[50px] h-[50px] rounded-full bg-cover bg-no-repeat"
             style="background-image: url({{asset('images/influencers/f6cbad7604d1a0dfc19e577bd27f71fc.png')}});"></div>
        <div class="lg:w-[80px] lg:h-[80px] w-[50px] h-[50px] rounded-full bg-cover bg-no-repeat"
             style="background-image: url({{asset('images/influencers/41b989884416f9744b97a7bb66b60df1.png')}});"></div>
        <div class="lg:w-[80px] lg:h-[80px] w-[50px] h-[50px] rounded-full bg-cover bg-no-repeat"
             style="background-image: url({{asset('images/influencers/a8db9e9e19c4b4f7951644cbb5f6f287.png')}});"></div>
        <div class="lg:w-[80px] lg:h-[80px] w-[50px] h-[50px] rounded-full bg-cover bg-no-repeat"
             style="background-image: url({{asset('images/influencers/8c730028e3ad87cede1db4857e34e3bf.png')}});"></div>
        <div class="lg:w-[80px] lg:h-[80px] w-[50px] h-[50px] rounded-full bg-cover bg-no-repeat"
             style="background-image: url({{asset('images/influencers/275200045e81eafa81ea1218ad81c84e.png')}});"></div>
        <div class="lg:w-[80px] lg:h-[80px] w-[50px] h-[50px] rounded-full bg-cover bg-no-repeat"
             style="background-image: url({{asset('images/influencers/5d3e0621c6c2684b658506d87f5d495b.png')}});"></div>
        <div class="lg:w-[80px] lg:h-[80px] w-[50px] h-[50px] rounded-full bg-cover bg-no-repeat"
             style="background-image: url({{asset('images/influencers/119e11f2836b57d49ddd9030a742cf12.png')}});"></div>
        <div class="lg:w-[80px] lg:h-[80px] w-[50px] h-[50px] rounded-full bg-cover bg-no-repeat"
             style="background-image: url({{asset('images/influencers/6d7c559ed7834d5305f827d6c59da4f3.png')}});"></div>
        <div class="lg:w-[80px] lg:h-[80px] w-[50px] h-[50px] rounded-full bg-cover bg-no-repeat"
             style="background-image: url({{asset('images/influencers/9fb5c0049f321ea03431bd42e4d56a88.png')}});"></div>
        <div class="lg:w-[80px] lg:h-[80px] w-[50px] h-[50px] rounded-full bg-cover bg-no-repeat"
             style="background-image: url({{asset('images/influencers/e98c0e5372411de43e28f72db7e44748.png')}});"></div>
        <div class="lg:w-[80px] lg:h-[80px] w-[50px] h-[50px] rounded-full bg-cover bg-no-repeat"
             style="background-image: url({{asset('images/influencers/80024821c506b675b40698203acee173.png')}});"></div>
    </div>
    <div class="flex justify-center mt-14">
        <button class="text-white bg-primary-900 px-6 py-2 rounded-lg border-2 font-normal">
            <a href="{{ route('home.influencer-marketing') }}">
                Learn more about Influencer Marketing
            </a>
        </button>
    </div>
</div>
{{-- End Trusted Influencers --}}

{{-- Boost Sales --}}
<div class="bg-primary-900 mt-8 py-8">
    <h2 class="text-3xl font-bold text-center text-white mb-8">Boost Your Sales with:</h2>
    <div class="container grid grid-cols-1 lg:grid-cols-5 gap-6 col-span-3 place-items-center mx-auto">

        <div class="bg-white rounded-lg overflow-hidden">
            <div class="relative">
                <img class="w-full h-80 object-cover"
                     src="{{asset('images/social/92f1dd19716e15b5a4877ba76ab36f97.png')}}" alt="Influencers Marketing">
            </div>
            <div class="p-4">
                <p class="text-primary-900 text-lg font-semibold mt-2">Influencers Marketing</p>
                <div class="flex items-center mt-2">
                    <a href="{{ route('home.influencer-marketing') }}" class="text-warning-800 text-base">
                        Learn more
                        <i data-feather="arrow-right" class="inline text-warning-800 ml-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg overflow-hidden">
            <div class="relative">
                <img class="w-full h-80 object-cover"
                     src="{{asset('images/social/7aee746697a1c30057ddab4ae837d220.png')}}" alt="WhatsApp Seeding">
            </div>
            <div class="p-4">
                <p class="text-primary-900 text-lg font-semibold mt-2">WhatsApp Seeding</p>
                <div class="flex items-center mt-2">
                    <a href="{{ route('home.social-commerce') }}" class="text-warning-800 text-base">
                        Learn more
                        <i data-feather="arrow-right" class="inline text-warning-800 ml-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg overflow-hidden">
            <div class="relative">
                <img class="w-full h-80 object-cover"
                     src="{{asset('images/social/abf0356403c23c1ee1c3d0597e599135.png')}}" alt="Twitter Trends">
            </div>
            <div class="p-4">
                <p class="text-primary-900 text-lg font-semibold mt-2">Twitter Trends</p>
                <div class="flex items-center mt-2">
                    <a href="{{ route('home.social-commerce') }}" class="text-warning-800 text-base">
                        Learn more
                        <i data-feather="arrow-right" class="inline text-warning-800 ml-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg overflow-hidden">
            <div class="relative">
                <img class="w-full h-80 object-cover"
                     src="{{asset('images/social/d6e3b02b6f1c18bdf9e369ad697e2920.png')}}" alt="Content creation">
            </div>
            <div class="p-4">
                <p class="text-primary-900 text-lg font-semibold mt-2">Content creation</p>
                <div class="flex items-center mt-2">
                    <a href="{{ route('home.influencer-marketing') . '#content-creation' }}"
                       class="text-warning-800 text-base">
                        Learn more
                        <i data-feather="arrow-right" class="inline text-warning-800 ml-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg overflow-hidden">
            <div class="relative">
                <img class="w-full h-80 object-cover"
                     src="{{asset('images/social/a86ee02f3d3c6a462ebfad12d57b60e7.png')}}" alt="Product Image">
            </div>
            <div class="p-4">
                <p class="text-primary-900 text-lg font-semibold mt-2">Social Listening</p>
                <div class="flex items-center mt-2">
                    <a href="{{ route('home.services') . '#social-listening' }}" class="text-warning-800 text-base">
                        Learn more
                        <i data-feather="arrow-right" class="inline text-warning-800 ml-1"></i>
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>
{{-- End Boost Sales --}}

{{-- Become an influencer --}}
<div class="relative bg-primary-50 py-8 h-[480px] mb-[400px] md:mb-0">
    <div class="right-0 w-full md:w-3/5 h-[420px] bg-no-repeat bg-center bg-cover float-right"
         style="background-image: url({{asset('images/58236643b3337e3c8d2a3079e152ba15.jpg')}}); background-position: center;"></div>

    <div class="absolute top-0 left-0 w-full h-[480px] py-8">
        <div class="container grid grid-cols-1 md:grid-cols-2 gap-6 mx-auto">
            <div class="flex flex-col mt-[70px] order-2 md:order1">
                <div class="rounded-lg bg-white flex flex-row my-2 h-20 font-semibold text-2xl items-center shadow">
                    <span class="w-10 h-10 text-xl rounded-full text-center bg-warning-50 mx-5 text-primary-600 leading-10">1</span>
                    <h4 class="text-gray-900">Find products and gigs on Twiva</h4>
                </div>
                <div class="rounded-lg bg-white flex flex-row my-2 h-20 font-semibold text-2xl items-center shadow">
                    <span class="w-10 h-10 text-xl rounded-full text-center bg-warning-50 mx-5 text-primary-600 leading-10">2</span>
                    <h4 class="text-gray-900">Promote them online.</h4>
                </div>
                <div class="rounded-lg bg-white flex flex-row my-2 h-20 font-semibold text-2xl items-center shadow">
                    <span class="w-10 h-10 text-xl rounded-full text-center bg-warning-50 mx-5 text-primary-600 leading-10">3</span>
                    <h4 class="text-gray-900">Earn!</h4>
                </div>
            </div>

            <div class="flex flex-col mt-10 h-[350px] bg-yellow-transparent px-8 py-4 order-1 md:order-2">
                <svg class="my-4" width="45" height="50" viewBox="0 0 45 50" fill="none"
                     xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                          d="M12.4335 8.03532L22.3472 2.48372L25.0638 4.00499L10.6351 13.2381C8.1004 14.8601 7.4849 18.271 9.29524 20.6628C10.8993 22.7818 13.8629 23.3518 16.1506 21.9813L22.3445 18.2705L41.9651 30.1317C42.0195 30.1646 42.0756 30.1926 42.1328 30.216L42.0946 34.9619L22.9357 24.1675C22.3641 23.8456 21.6374 24.0436 21.3124 24.6098C20.9874 25.1762 21.1873 25.8963 21.7588 26.2183L40.8506 36.9748L36.1234 39.7209L15.7994 28.103C15.2299 27.7775 14.5019 27.971 14.1733 28.5353C13.8448 29.0995 14.0401 29.8209 14.6096 30.1464L33.7604 41.0937L32.261 41.9647L29.3127 43.6157L9.05342 32.0349C8.48389 31.7093 7.75592 31.9029 7.42736 32.4672C7.0988 33.0314 7.29413 33.7527 7.86362 34.0783L26.906 44.9635L22.3472 47.5163L12.4335 41.9648L2.61029 36.2582L2.51972 25L2.61029 13.7418L12.4335 8.03532ZM42.1548 27.4828L42.1748 25L42.0841 13.7418L32.261 8.03532L27.4031 5.31495C27.3884 5.32529 27.3734 5.33537 27.3582 5.34515L11.9268 15.2198C10.5519 16.0997 10.218 17.9499 11.2 19.2473C12.0701 20.3968 13.6777 20.706 14.9187 19.9625L28.8741 11.6018C29.4367 11.2647 30.1686 11.4433 30.5087 12.0008C30.849 12.5583 30.6687 13.2834 30.106 13.6205L24.6405 16.8949L42.1548 27.4828ZM23.1345 0.205086L33.4583 5.98633L43.688 11.929C44.1718 12.2101 44.4707 12.7216 44.4751 13.2761L44.5695 25L44.4751 36.7239C44.4707 37.2784 44.1718 37.7899 43.688 38.071L33.4583 44.0137L23.1345 49.7949C22.6461 50.0684 22.0483 50.0684 21.56 49.7949L11.2361 44.0137L1.00652 38.071C0.522681 37.7899 0.223792 37.2784 0.21933 36.7239L0.125 25L0.21933 13.2761C0.223792 12.7216 0.522682 12.2101 1.00652 11.929L11.2361 5.98633L21.56 0.205086C22.0483 -0.0683621 22.6461 -0.0683621 23.1345 0.205086Z"
                          fill="white"/>
                </svg>
                <h3 class="my-3 text-white text-4xl font-semibold">Become an Influencer!</h3>
                <p class="text-white text-xl">Anzisha biz na 0 investment!</p>
                <div class="mt-8">
                    <a href="{{ config('twiva.bases.app_url') . "/#/auth/sign_up/create_account/Influencer" }}"
                       class="rounded-lg bg-primary-50 text-primary-600 px-12 py-3 font-semibold float-left">Register
                        today</a>
                </div>
                <div class="mt-3">
                    <a href="{{ config('twiva.bases.app_url') }}" class="text-white text-semibold">
                        Learn more
                        <i data-feather="arrow-right" class="inline text-white ml-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- End Become an influencer --}}

{{-- Become an merchant --}}
<div class="relative bg-warning-50 py-8 h-[480px] mb-[400px] md:mb-0">
    <div class="left-0 w-full md:w-3/5 h-[420px] bg-no-repeat bg-center bg-cover"
         style="background-image: url({{asset('images/2d7ce7c30bd9d586f64571f6b0e39107.jpg')}}); background-position: center;"></div>

    <div class="absolute top-0 left-0 w-full h-[480px] py-8">
        <div class="container grid grid-cols-1 md:grid-cols-2 gap-6 mx-auto">

            <div class="flex flex-col mt-10 h-[350px] bg-primary-transparent px-8 py-4">
                <svg class="my-4" width="45" height="50" viewBox="0 0 45 50" fill="none"
                     xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                          d="M12.4335 8.03532L22.3472 2.48372L25.0638 4.00499L10.6351 13.2381C8.1004 14.8601 7.4849 18.271 9.29524 20.6628C10.8993 22.7818 13.8629 23.3518 16.1506 21.9813L22.3445 18.2705L41.9651 30.1317C42.0195 30.1646 42.0756 30.1926 42.1328 30.216L42.0946 34.9619L22.9357 24.1675C22.3641 23.8456 21.6374 24.0436 21.3124 24.6098C20.9874 25.1762 21.1873 25.8963 21.7588 26.2183L40.8506 36.9748L36.1234 39.7209L15.7994 28.103C15.2299 27.7775 14.5019 27.971 14.1733 28.5353C13.8448 29.0995 14.0401 29.8209 14.6096 30.1464L33.7604 41.0937L32.261 41.9647L29.3127 43.6157L9.05342 32.0349C8.48389 31.7093 7.75592 31.9029 7.42736 32.4672C7.0988 33.0314 7.29413 33.7527 7.86362 34.0783L26.906 44.9635L22.3472 47.5163L12.4335 41.9648L2.61029 36.2582L2.51972 25L2.61029 13.7418L12.4335 8.03532ZM42.1548 27.4828L42.1748 25L42.0841 13.7418L32.261 8.03532L27.4031 5.31495C27.3884 5.32529 27.3734 5.33537 27.3582 5.34515L11.9268 15.2198C10.5519 16.0997 10.218 17.9499 11.2 19.2473C12.0701 20.3968 13.6777 20.706 14.9187 19.9625L28.8741 11.6018C29.4367 11.2647 30.1686 11.4433 30.5087 12.0008C30.849 12.5583 30.6687 13.2834 30.106 13.6205L24.6405 16.8949L42.1548 27.4828ZM23.1345 0.205086L33.4583 5.98633L43.688 11.929C44.1718 12.2101 44.4707 12.7216 44.4751 13.2761L44.5695 25L44.4751 36.7239C44.4707 37.2784 44.1718 37.7899 43.688 38.071L33.4583 44.0137L23.1345 49.7949C22.6461 50.0684 22.0483 50.0684 21.56 49.7949L11.2361 44.0137L1.00652 38.071C0.522681 37.7899 0.223792 37.2784 0.21933 36.7239L0.125 25L0.21933 13.2761C0.223792 12.7216 0.522682 12.2101 1.00652 11.929L11.2361 5.98633L21.56 0.205086C22.0483 -0.0683621 22.6461 -0.0683621 23.1345 0.205086Z"
                          fill="white"/>
                </svg>
                <h3 class="my-3 text-white text-4xl font-semibold">Become a merchant!</h3>
                <p class="text-white text-xl">Gain access to thousands of influencers and millions of buyers!</p>
                <div class="mt-8">
                    <a href="{{ config('twiva.bases.app_url') . "/#/auth/sign_up/create_account/Business" }}"
                       class="rounded-lg bg-primary-50 text-primary-600 px-12 py-3 font-semibold float-left">Register
                        today</a>
                </div>
                <div class="mt-3">
                    <a href="{{ config('twiva.bases.app_url') }}" class="text-white text-semibold">
                        Learn more
                        <i data-feather="arrow-right" class="inline text-white ml-1"></i>
                    </a>
                </div>
            </div>

            <div class="flex flex-col mt-[70px]">
                <div class="rounded-lg bg-white flex flex-row my-2 h-20 font-semibold text-2xl items-center shadow">
                    <span class="w-10 h-10 text-xl rounded-full text-center bg-warning-50 mx-5 text-primary-600 leading-10">1</span>
                    <h4 class="text-gray-900">List your product and services</h4>
                </div>
                <div class="rounded-lg bg-white flex flex-row my-2 h-20 font-semibold text-2xl items-center shadow">
                    <span class="w-10 h-10 text-xl rounded-full text-center bg-warning-50 mx-5 text-primary-600 leading-10">2</span>
                    <h4 class="text-gray-900">Influencers market and sell</h4>
                </div>
                <div class="rounded-lg bg-white flex flex-row my-2 h-20 font-semibold text-2xl items-center shadow">
                    <span class="w-10 h-10 text-xl rounded-full text-center bg-warning-50 mx-5 text-primary-600 leading-10">3</span>
                    <h4 class="text-gray-900">Increase awareness and sales</h4>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- End Become an merchant --}}

{{-- Brands on Twiva --}}
<div class="hidden md:block bg-primary-900 mt-16 py-20">
    <h2 class="text-3xl font-bold text-center text-white mb-10">Brands on Twiva</h2>
    <div class="container flex flex-col mx-auto">
        <div class="flex flex-row justify-between">
            <div class="bg-white rounded-lg overflow-hidden bg-white">
                <img src="{{asset('brands/LG.jpg')}}" alt="LG" class="max-h-20 p-2"/>
            </div>
            <div class="rounded-lg overflow-hidden bg-white">
                <img src="{{asset('brands/mika.png')}}" alt="Mika" class="max-h-20 p-2"/>
            </div>
            <div class="rounded-lg overflow-hidden bg-white">
                <img src="{{asset('brands/canon.jpg')}}" alt="Canon" class="max-h-20 p-2"/>
            </div>
            <div class="rounded-lg overflow-hidden">
                <img src="{{asset('brands/Vitron.jpg')}}" alt="Vitron" class="max-h-20"/>
            </div>
            <div class="rounded-lg overflow-hidden">
                <img src="{{asset('brands/InStyle.jpg')}}" alt="InStyle" class="max-h-20"/>
            </div>
            <div class="rounded-lg overflow-hidden bg-white">
                <img src="{{asset('brands/samsung.jpg')}}" alt="Samsung" class="max-h-20 p-1"/>
            </div>
            <div class="rounded-lg overflow-hidden">
                <img src="{{asset('brands/klabels.png')}}" alt="Klabels" class="max-h-20"/>
            </div>
            <div class="rounded-lg overflow-hidden">
                <img src="{{asset('brands/vivo.jpg')}}" alt="Vivo" class="max-h-20"/>
            </div>
        </div>
        <div class="flex flex-row justify-between mt-8">
            <div class="bg-white rounded-lg overflow-hidden bg-white">
                <img src="{{asset('brands/ramtons.png')}}" alt="Ramtons" class="max-h-20"/>
            </div>
            <div class="rounded-lg overflow-hidden">
                <img src="{{asset('brands/decathlon.png')}}" alt="Decathlon" class="max-h-20"/>
            </div>
            <div class="rounded-lg overflow-hidden bg-white">
                <img src="{{asset('brands/Kings.jpg')}}" alt="Kings Collection" class="max-h-20 p-2"/>
            </div>
            <div class="rounded-lg overflow-hidden bg-white">
                <img src="{{asset('brands/Haier.jpg')}}" alt="Haier" class="max-h-20 p-2"/>
            </div>
            <div class="rounded-lg overflow-hidden bg-white">
                <img src="{{asset('brands/tecno.png')}}" alt="TECNO" class="max-h-20 p-2"/>
            </div>
            <div class="rounded-lg overflow-hidden bg-white">
                <img src="{{asset('brands/Hotpoint.jpg')}}" alt="Hotpoint" class="max-h-20 p-2"/>
            </div>
        </div>
    </div>
</div>
{{-- End Brands on Twiva --}}

{{-- Products you might like --}}
<div class="container mx-auto py-8">
    <h2 class="text-3xl font-bold text-center text-primary-800 mb-10 mt-10">Products you might like</h2>
    <div id="products-might-like" class="grid grid-cols-1 lg:grid-cols-4 gap-6 lg:col-span-3">
        {{--@foreach ($productsMightLike as $product)
            <x-front::e-commerce.layout.elements.product-card
                :image="$product['image']"
                :discount="$product['discount']"
                :userImage="$product['userImage']"
                :userName="$product['userName']"
                :productName="$product['productName']"
                :originalPrice="$product['originalPrice']"
                :discountedPrice="null"
            />
        @endforeach--}}
    </div>
    <div class="px-4 mt-6">
        <button id="products-might-like-view-more"
                class="w-full text-primary-700 bg-primary-50 px-6 py-2 rounded-lg font-semibold">
            Load more
        </button>
    </div>
</div>
{{-- End Products you might like --}}

