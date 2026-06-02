<footer {{ $attributes }}>
    <div class="bg-primary-900">
        <div class="container mx-auto relative {{--px-3.5 pt-12 pb-[57px] md:pr-0 lg:pl-[115px] xl:pr-[115px] md:pt-0 md:pb-0--}}">
            <div class="grid grid-cols-5 gap-0">
                <div class="col-span-5 md:col-span-2 p-4 md:py-16 flex flex-col">
                    <img class="w-[46px] h-[50px] mb-5" src="{{ asset('front/images/logos/Logo-small.png') }}" alt="Twiva" />
                    <h2 class="text-white text-[30px] font-bold leading-[38px] md:text-5xl md:leading-[60px] md:font-normal mb-4">
                        Are you an <span class="text-warning-800">Influencer</span> or a <span class="text-warning-800">merchant</span>?
                    </h2>
                    <p class="text-white mb-4">TWIVA has an app especially for you! Download the Twiva mobile app today:</p>
                    <div class="flex">
                        <a href="https://play.google.com/store/apps/details?id=com.twiva.ke" title="Google Play">
                            <img src="{{ asset('front/images/badges/google-play.png') }}" class="w-[153px] h-12" alt="google-play"/>
                        </a>
                        <a href="https://apps.apple.com/app/id6444508375?ls=1" title="App Store" class="ml-4">
                            <img src="{{ asset('front/images/badges/app-store.png') }}" class="w-[153px] h-12"
                                 alt="app-store"/>
                        </a>
                    </div>
                </div>
                <div class="relative md:col-span-3 hidden md:block w-full h-full">
                    <div
                        class="absolute top-0 right-0 md:block w-full h-full bg-center bg-no-repeat bg-cover"
                        style="background-image: url('{{ asset('images/footer-bg.jpg') }}')"
                    >
                        {{--<div class=""></div>--}}
                    </div>
                    <div
                        class="absolute top-0 left-0 translate-x-[-8rem] md:block w-full h-full bg-center bg-no-repeat bg-contain"
                        style="background-image: url('{{ asset('front/landing/images/home-page/footer-banner-img.svg') }}')"
                    >

                    </div>
                </div>
            </div>
    </div>
    <div class="bg-gray-50 px-[42px] pt-6 pb-12 md:px-20 md:pt-16 md:pb-12">
        <div class="grid grid-cols-4 gap-[42px] md:gap-8 mb-[30px]">
            <div class="col-span-4 md:col-span-1">
                <img class="w-[98px] mb-4" src="{{ asset('front/images/logos/Logo.png') }}" alt="twiva" />
                <h4 class="font-semibold text-primary-800 mb-2">Our Services</h4>
                <ul class="text-gray-500 text-base font-semibold">
                    <li class="mb-3">
                        <a href="{{ route('home.social-commerce') }}">Social commerce</a>
                    </li>
                    <li class="mb-3">
                        <a href="{{ route('home.influencer-marketing') }}">Influencer campaign management</a>
                    </li>
                    <li class="mb-3">
                        <a href="{{ route('home.social-commerce') }}">Twitter Trends</a>
                    </li>
                    <li class="mb-3">
                        <a href="{{ route('home.social-commerce') }}">WhatsApp Seeding</a>
                    </li>
                    <li class="mb-3">
                        <a href="{{ route('home.services') . '#social-listening' }}">Social Listening</a>
                    </li>
                </ul>
            </div>
            <div class="col-span-4 md:col-span-1 lg:mt-10">
                <h4 class="font-semibold text-primary-800 mb-2">About us</h4>
                <ul class="text-gray-500 text-base font-semibold">
                    <li class="mb-3">
                        <a href="{{ route('home.about') }}">About Twiva</a>
                    </li>
                    <li class="mb-3">
                        <a href="{{ config('twiva.bases.app_url') . "/#/auth/sign_up/create_account/Business" }}">Join as a brand</a>
                    </li>
                    <li class="mb-3">
                        <a href="{{ config('twiva.bases.app_url') . "/#/auth/sign_up/create_account/Influencer" }}">Join as an influencer</a>
                    </li>
                    <li class="mb-3">
                        <a href="{{config('services.blogSite.url')}}">Blog</a>
                    </li>
                </ul>
            </div>
            <div class="col-span-4 md:col-span-1 lg:mt-10">
                <h4 class="font-semibold text-primary-800 mb-2">Support and Help</h4>
                <ul class="text-gray-500 text-base font-semibold">
                    <li class="mb-3">
                        <a href="{{route('home.faq')}}">FAQs</a>
                    </li>
                    <li class="mb-3">
                        <a href="{{route('home.policy')}}">Privacy Policy</a>
                    </li>
                    <li class="mb-3">
                        <a href="{{route('home.terms-and-conditions')}}">Terms and Conditionals</a>
                    </li>
                    <li class="mb-3">
                        <a href="{{route('home.banned')}}">Banned Products</a>
                    </li>
                    <li class="mb-3">
                        <a href="{{config('services.blogSite.url')}}/training">Training Material</a>
                    </li>

                </ul>
            </div>
            <div class="relative col-span-4 md:col-span-1 lg:mt-10">
                <h4 class="text-primary-800 font-semibold mb-2">
                    Contact Us
                </h4>
                <ul class="text-gray-500 text-base font-semibold mb-4">
                    <li class="mb-3">
                        <a href="tel:+254708088114">+254708088114</a>
                    </li>
                    <li class="mb-3">
                        <a href="mailto:info@twiva.co.ke">info@twiva.co.ke</a>
                    </li>
                    <li>Kose Heights, Argwings Kodhek Road,</li>
                    <li>Hurlingham, Nairobi, Kenya Office</li>
                    <li>No. W.3.2B on 3rd Floor</li>
                </ul>
                <div class="w-full md:w-auto block">
                    <h6 class="text-primary-800 font-semibold mb-2">Follow us</h6>
                    <div class="flex gap-5 flex-wrap">
                        <a href="https://www.facebook.com/twiva" target="_blank" class="footer-social">
                            <img src="{{ asset('front/images/social-icons/facebook.svg') }}" alt="facebook">
                        </a>
                        <a href="https://www.linkedin.com/company/twiva" target="_blank" class="footer-social">
                            <img src="{{ asset('front/images/social-icons/linkedin.svg') }}" alt="linkedin">
                        </a>
                        <a href="https://www.youtube.com/channel/UC2B-2juL3EjZVaOzreJ9Iow" target="_blank" class="footer-social">
                            <img src="{{ asset('front/images/social-icons/youtube.svg') }}" alt="youtube">
                        </a>
                        <a href="https://www.tiktok.com/@twiva_influence?is_from_webapp=1&sender_device=pc" target="_blank" class="footer-social">
                            <img src="{{ asset('front/images/social-icons/tiktok.svg') }}" alt="tiktok">
                        </a>
                        <a href="https://www.pinterest.com/Twiva_Influence/_created" target="_blank" class="footer-social">
                            <img src="{{ asset('front/images/social-icons/pinterest.svg') }}" alt="pinterest">
                        </a>
                        <a href="https://www.instagram.com/twiva_influence" target="_blank" class="footer-social">
                            <img src="{{ asset('front/images/social-icons/instagram.svg') }}" alt="instagram">
                        </a>
                        <a href="https://x.com/Twiva_influence" target="_blank" class="footer-social">
                            <img src="{{ asset('front/images/social-icons/x.svg') }}" alt="X">
                        </a>
                    </div>
                </div>
                <a class="fixed flex flex-col items-center bg-primary-50 right-3 bottom-3 w-[80px] h-[80px] rounded-full shadow-whatsapp border-[9px]
                 border-solid border-[rgba(204,204,204,0.5)] z-30" href="https://wa.me/254708088114">
                    <img class="w-[38px] h-[38px]" src="{{ asset('front/images/social-icons/whatsapp-colored.svg') }}" alt="whatsapp">
                    <span class="text-black-100 text-xs text-center font-bold">Talk to us</span>
                </a>
            </div>
        </div>
        <div class="flex flex-col md:flex-row items-center md:justify-between">
            <p class="text-gray-400 text-base font-normal mb-[30px] md:mb-0">© {{ date('Y') }} Twiva. All rights reserved.</p>
            
        </div>
    </div>
</footer>
