<h2 class="hidden lg:block text-[30px] font-semibold leading-8 pb-9">Checkout</h2>
<div class="grid grid-cols-1 lg:grid-cols-8 gap-0 gap-[50px]">
    <div class="col-span-1 lg:col-span-5">
        {{ $slot }}
        <div class="w-full hidden lg:flex justify-between items-center">
            @if(isset($formPrev))
                <x-shop::checkout.utilities.button :attributes="$formPrev->attributes" text color="gray-850"
                                                   class="hidden lg:flex px-3 gap-[18px]">
                    {{ $formPrev }}
                </x-shop::checkout.utilities.button>
            @endif
            @if(isset($formNext))
                <x-shop::checkout.utilities.button :attributes="$formNext->attributes" color="primary-700"
                                                   class="!px-10">
                    {{ $formNext }}
                </x-shop::checkout.utilities.button>
            @endif
        </div>

        <div class="flex flex-col gap-2.5 lg:hidden p-5 !mx-[-20px] border border-solid border-gray-300">
            <div class="flex justify-between">
{{--                <span class="text-black-100 text-base font-bold">Total: <span--}}
{{--                        class="total_items_price text-primary-900">KSH {{$total}}</span></span>--}}
{{--                <span class="text-gray-850 text-base font-normal"><span class="total_items2">{{$quantity}}</span> items</span>--}}
            </div>
            @if(isset($formNextMobile))
                <x-shop::checkout.utilities.button :attributes="$formNextMobile->attributes" color="primary-700"
                                                   class="w-full">
                    {{ $formNextMobile }}
                </x-shop::checkout.utilities.button>
            @endif
        </div>
    </div>
    <div class="lg:block lg:col-span-3" id="your_order_section">
        <x-shop::checkout.partials.checkout-basket :products-data="$productsData" :partners="$partners" />
    </div>
</div>
