<x-shop::utilities.drawer class="flex flex-col justify-between items-start bg-gray-25 md:bg-white" name="basket">
    <div class="w-full h-[calc(100%-118px)]">
        <div class="py-[15px] px-[20px] md:px-[80px] flex flex-row justify-between items-center md:hidden">
            <x-shop::partials.logo/>
            <button @click="toggle">
                <i class="w-5 h-5 text-gray-900" data-feather="x"></i>
            </button>
        </div>
        <div
            class="flex justify-between items-center bg-white mb-5 md:mb-2.5 border-gray-200 sm-down:border px-4 md:px-5 md:pt-5 sm-down:py-3.5">
            <h4 class="text-base md:text-xl text-gray-500 md:text-black-100 font-normal md:font-semibold">My Cart</h4>
            <button class="sm-down:hidden" @click="toggle">
                <i class="w-5 h-5 text-gray-900" data-feather="x"></i>
            </button>
            <button class="text-base text-gray-500 font-normal md:hidden">
                Clear Cart
            </button>
        </div>
        <div id="cardDivContent"
             class="grid grid-cols-12 gap-2.5 max-h-[calc(100%-136px)] md:max-h-[calc(100%-38px)] overflow-auto px-4 md:px-5">
            @include('shop::components.views._cards')
        </div>
    </div>
    <div class="w-full p-5 sm-down:bg-white border-gray-200 sm-down:border">
        <div class="flex justify-between items-center text-base mb-2.5">
            <div class="font-bold">
                <span class="text-black-200">Total: </span><span class="text-primary-800" id="total_price">KSH 0</span>
            </div>
            <span class="font-normal text-black-200" id="total_items">
                <span class="total_items2">
                0
                </span>
                items</span>
        </div>
        <a id="proceedToCheckoutBtn" href="{{route('buyer.checkout.account',['account' =>request('account')])}}"
           class="bg-primary-600 text-white rounded-lg flex justify-center items-center h-[44px] w-full">
            Proceed to checkout
        </a>
    </div>
</x-shop::utilities.drawer>
