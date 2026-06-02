@php use Modules\System\Entities\ConstantSystem; @endphp
<div class="bg-white pt-10 pb-20">
    <div class="container mx-auto">
        <x-shop::core.section>
            <x-shop::partials.profile :platforms="$user->UserPlatforms" :userName="($shop->shop_name ?? $user->name)"
                                      :userDescription="($shop->shop_description)"
                                      :user-image="$shop->logoImage ?? asset('storage/images/products/1632146490.jpg')"
                                      :badges="$badges"
                                      :show-more-badges="$showMoreBadges"
                                      :tags="$tags" class="mb-[30px] md:mb-4 "/>
        </x-shop::core.section>
    </div>
    @if($shop->user->type !== 'Business')
        <div class="container grid grid-cols-1 lg:flex lg:space-between gap-4 lg:gap-6 mx-auto lg:items-center">
            <div class="rounded-lg lg:flex-1 bg-no-repeat bg-cover bg-center lg:h-[450px] hidden lg:block cursor-pointer"
                 onclick="window.open('{{ ConstantSystem::getUrlBanner() }}','mywindow');"
                 style="background-image: url({{ ConstantSystem::getBanner() }})">
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
                <div id='coupon-container'
                     class="text-center text-white my-2 flex flex-col items-center justify-center px-2"
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
                <p id='coupon-info-text' class='text-center text-white my-2 px-2 text-sm' style="display: none"></p>
                <div class="flex justify-center mb-4">
                    <button class="wheel-button  bg-[#E1E0E2] text-[#626163] font-semibold rounded-lg h-11 text-center cursor-pointer w-3/4">
                        CLICK TO SPIN
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

<div id="hot-deals-container" class="container mx-auto py-14">
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
<div class="hidden line-clamp-1"></div>

{{-- Products you might like --}}
<div class="container mx-auto py-8">
    <h2 class="text-3xl font-bold text-center text-primary-800 mb-10 mt-10">Products you might like</h2>
    <div class="flex space-x-2 mb-2">
        <input id="products_type" type="hidden" value="products">
        @if($hasServicesProduct)
            <button id="products-btn" class="active-btn">Products</button>
            <button id="services-btn" class="dis-active-btn">Services</button>
        @endif
    </div>
    <div id="products-might-like" class="grid grid-cols-1 lg:grid-cols-4 gap-6 lg:col-span-3">
    </div>
    <div class="px-4 mt-6">
        <button id="products-might-like-view-more"
                class="w-full text-primary-700 bg-primary-50 px-6 py-2 rounded-lg font-semibold">
            Load more
        </button>
    </div>
</div>
{{-- End Products you might like --}}

