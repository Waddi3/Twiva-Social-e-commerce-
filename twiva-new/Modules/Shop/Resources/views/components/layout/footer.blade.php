<footer class="mt-10">
    <div class="bg-gray-50 px-[42px] pt-6 pb-12 md:px-20 md:pt-16 md:pb-12">
        <div class="grid grid-cols-4 gap-[42px] md:gap-8 mb-[30px]">
            <div class="col-span-4 md:col-span-1">
                <img class="w-[98px] mb-4" src="{{ asset('front/images/logos/Logo.png') }}" alt="twiva" />
                <h4 class="font-semibold text-primary-800 mb-2">Our Services</h4>
                <ul class="text-gray-500 text-base font-semibold">
                    <li class="mb-3">
                        <a href="#">Social commerce</a>
                    </li>
                    <li class="mb-3">
                        <a href="#">Influencer campaign management</a>
                        {{--<a href="{{ config('services.blogSite.url') }}">Blog</a>--}}
                    </li>
                    <li class="mb-3">
                        <a href="#">Twitter Trends</a>
                        {{--<a href="{{ route('home.faq') }}">FAQs</a>--}}
                    </li>
                    <li class="mb-3">
                        <a href="#">WhatsApp Seeding</a>
                        {{--<a href="https://blog.twiva.co.ke/training">Training</a>--}}
                    </li>
                    <li class="mb-3">
                        <a href="#">Social Listening</a>
                    </li>
                </ul>
            </div>
            <div class="col-span-4 md:col-span-1 mt-10">
                <h4 class="font-semibold text-primary-800 mb-2">About us</h4>
                <ul class="text-gray-500 text-base font-semibold">
                    <li class="mb-3">
                        <a href="#">About Twiva</a>
                    </li>
                    <li class="mb-3">
                        <a href="#">Join as a brand</a>
                    </li>
                    <li class="mb-3">
                        <a href="#">Join as an influencer</a>
                    </li>
                    <li class="mb-3">
                        <a href="#">Blog</a>
                    </li>
                    {{--<li class="mb-3">
                        <a href="--}}{{--{{ route('home.policy') }}--}}{{--">Privacy Policy</a>
                    </li>
                    <li class="mb-3">
                        <a href="--}}{{--{{ route('home.terms-and-conditions') }}--}}{{--">Terms & Conditions</a>
                    </li>
                    <li class="mb-3">
                        <a href="--}}{{--{{ route('home.banned') }}--}}{{--">Banned Products</a>
                    </li>--}}

                </ul>
            </div>
            <div class="col-span-4 md:col-span-1 mt-10">
                <h4 class="font-semibold text-primary-800 mb-2">Support and Help</h4>
                <ul class="text-gray-500 text-base font-semibold">
                    <li class="mb-3">
                        <a href="#">FAQs</a>
                    </li>
                    <li class="mb-3">
                        <a href="#">Privacy Policy</a>
                    </li>
                    <li class="mb-3">
                        <a href="#">Terms and Conditionals</a>
                    </li>
                    <li class="mb-3">
                        <a href="#">Banned Products</a>
                    </li>
                    <li class="mb-3">
                        <a href="#">Training Material</a>
                    </li>

                </ul>
            </div>
            <div class="relative col-span-4 md:col-span-1">
                <h6 class="text-black-300 text-xl font-bold leading-[30px] mb-4">
                    Contact Us
                </h6>
                <ul class="text-gray-500 text-base font-semibold">
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
                <a class="fixed flex flex-col items-center bg-primary-50 right-3 bottom-3 w-[80px] h-[80px] rounded-full shadow-whatsapp border-[9px]
                 border-solid border-[rgba(204,204,204,0.5)] z-30" href="https://wa.me/254708088114">
                    <img class="w-[38px] h-[38px]" src="{{ asset('front/images/social-icons/whatsapp-colored.svg') }}" alt="whatsapp">
                    <span class="text-black-100 text-xs text-center font-bold">Talk to us</span>
                </a>
            </div>
        </div>
        <div class="flex flex-col md:flex-row items-center md:justify-between">
            <p class="text-gray-400 text-base font-normal mb-[30px] md:mb-0">© {{ date('Y') }} Twiva. All rights reserved.</p>
            <div class="w-full md:w-auto flex items-center justify-between">
                <h6 class="text-black-300 text-xl font-bold leading-[30px] md:mr-5">Follow us</h6>
                <div class="flex gap-5">
                    <a href="https://www.facebook.com/twiva" target="_blank" class="footer-social">
                        <img src="{{ asset('front/images/social-icons/facebook.svg') }}" alt="facebook">
                    </a>
                    <a href="https://www.linkedin.com/company/twiva" target="_blank" class="footer-social hidden md:block">
                        <img src="{{ asset('front/images/social-icons/linkedin.svg') }}" alt="linkedin">
                    </a>
                    <a href="https://www.youtube.com/channel/UC2B-2juL3EjZVaOzreJ9Iow" target="_blank" class="footer-social hidden md:block">
                        <img src="{{ asset('front/images/social-icons/youtube.svg') }}" alt="youtube">
                    </a>
                    <a href="https://www.tiktok.com/@twiva_influence?is_from_webapp=1&sender_device=pc" target="_blank" class="footer-social hidden md:block">
                        <img src="{{ asset('front/images/social-icons/tiktok.svg') }}" alt="tiktok">
                    </a>
                    <a href="https://www.pinterest.com/Twiva_Influence/_created" target="_blank" class="footer-social hidden md:block">
                        <img src="{{ asset('front/images/social-icons/pinterest.svg') }}" alt="pinterest">
                    </a>
                    <a href="https://www.instagram.com/twiva_influence" target="_blank" class="footer-social">
                        <img src="{{ asset('front/images/social-icons/instagram.svg') }}" alt="instagram">
                    </a>
                    <a href="https://x.com/Twiva_influence" target="_blank" class="footer-social">
                        <img width="16px" src="{{ asset('front/images/social-icons/x.svg') }}" alt="X">
                    </a>
                </div>
            </div>
        </div>
    </div>
</footer>
