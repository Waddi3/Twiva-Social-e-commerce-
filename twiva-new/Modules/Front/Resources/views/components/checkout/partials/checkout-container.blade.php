@php
    $containerClass = 'bg-white flex justify-center items-center py-0 lg:py-2.5 px-0 lg:px-[60px] lg:shadow-dropdown';
if (isset($navPrev) || isset($navNext)) {
    $containerClass .= ' lg:justify-between';
}
@endphp

<nav class="{{ $containerClass }}">
    @if(isset($navPrev))
        <x-shop::checkout.utilities.button :attributes="$navPrev->attributes" text color="gray-850"
                                           class="hidden lg:flex px-3.5 gap-[18px]">
            {{ $navPrev }}
        </x-shop::checkout.utilities.button>
    @endif
    <div class="w-full lg:w-auto flex justify-center items-center border lg:border-0 border-solid border-gray-200 py-[5px] gap-0 xs:gap-[5px] lg:gap-2.5">
        <x-shop::checkout.utilities.button text class="!p-[5px] gap-2.5 text-base font-semibold" :color="$accountColor">
            <span class="{{ $accountClasses }}">
                {!! $accountContent !!}
            </span>
            <span>Account</span>
        </x-shop::checkout.utilities.button>
        <i class="text-base text-gray-400" data-feather="chevron-right" ></i>
        <x-shop::checkout.utilities.button text class="!p-[5px] gap-2.5 text-base font-semibold"
                                           :color="$shippingColor">
            <span class="{{ $shippingClasses }}">
                {!! $shippingContent !!}
            </span>
            <span>Shipping</span>
        </x-shop::checkout.utilities.button>
        <i class="text-base text-gray-400" data-feather="chevron-right" ></i>
        <x-shop::checkout.utilities.button text class="!p-[5px] gap-2.5 text-base font-semibold "
                                           :color="$paymentColor">
            <span class="{{ $paymentClasses }}">
                {!! $paymentContent !!}
            </span>
            <span>Payment</span>
        </x-shop::checkout.utilities.button>
        <i class="text-base text-gray-400" data-feather="chevron-right" ></i>
        <x-shop::checkout.utilities.button text class="!p-[5px] gap-2.5 text-base font-semibold "
                                           :color="$confirmColor">
            <span class="{{ $confirmClasses }}">
                {!! $confirmContent !!}
            </span>
            <span>Confirm</span>
        </x-shop::checkout.utilities.button>
    </div>
    @if(isset($navNext))
        <x-shop::checkout.utilities.button :attributes="$navNext->attributes" color="primary-700"
                                           class="hidden lg:flex px-10">
            {{ $navNext }}
        </x-shop::checkout.utilities.button>
    @endif
</nav>
<nav class="block lg:hidden bg-primary-25 flex justify-between items-center py-4 px-5">
    <div class="flex justify-start items-center gap-2.5 text-gray-850">
        <i data-feather="shopping-cart" class="text-gray-850 w-5 h-5"></i>
        <h6 class="text-sm font-semibold">Order Summary</h6>
    </div>
    <div class="flex justify-end items-center gap-2.5 text-xs font-semibold">
        <p class="text-gray-800">{{$productsData['quantity']}} items</p>
        <p class="text-primary-900">KSH {{number_format($productsData['total'])}}</p>
    </div>
</nav>
<section class="px-5 lg:px-[90px] pt-[25px] lg:pt-[35px] pb-0 lg:pb-[50px]">
    @if(\Illuminate\Support\Facades\Session::has('message'))
        <p id="toastMessage"
           class="text-center p-3 bg-green-200 text-green-700 rounded-md mt-6">{{\Illuminate\Support\Facades\Session::get('message')}}</p>
    @endif
    @if(\Illuminate\Support\Facades\Session::has('errorMessage'))
        <p id="toastMessage"
           class="text-center p-5 bg-primary-50 text-primary-700 rounded-md mt-6">{{\Illuminate\Support\Facades\Session::get('errorMessage')}}</p>
    @endif
    {{ $slot }}
</section>
