<div class="h-full rounded-bl-[15px] rounded-br-[15px] drop-shadow-card">
    <div class="w-full h-[300px] bg-[#f5f5f5] bg-center bg-no-repeat bg-contain"
         style="background-image: url('{{ asset('images/product.png') }}')"></div>
    <div class="flex flex-col justify-between gap-2.5 h-[calc(100%-300px)] bg-white p-5 rounded-bl-[15px] rounded-br-[15px]">
        <div>
            <p class="mb-2.5 text-base font-semibold text-black-100">Women’s Sports Tee, Red</p>
            <p class="text-xs font-semibold text-primary-900">KSH 2000</p>
        </div>
        <div class="flex justify-between items-center">
            <button
                    x-data="toggling()"
                    class="h-[40px] w-[40px] flex justify-center items-center rounded-lg hover:drop-shadow-btn"
                    :class="show ? ' bg-white': 'bg-primary-50'"
                    @click="toggle"
            >
                <i x-show="show" data-feather="check" class="w-5 h-5 text-gray-600"></i>
                <i x-show="!show" data-feather="shopping-cart" class="w-5 h-5 text-primary-1000"></i>
            </button>
            <a class="h-[40px] w-[calc(100%-50px)] bg-primary-1000 text-base text-white flex justify-center items-center rounded-lg"
               href="{{route('shop.product.buyNow',['account' => request('account'),'product_id' => $product->id])}}">>
                Buy Now
            </a>
        </div>
    </div>
</div>
