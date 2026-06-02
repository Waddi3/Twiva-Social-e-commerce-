{{--@props([
   'product' =>'',
   'isInCard' => 'false',
])--}}
<div class="bg-white rounded-lg shadow overflow-hidden mx-auto product-item min-w-0">
    <div class="relative">
        <a href="{{route('shop.product.show', ['product' => $id, 'user_type' => $userType, 'account' => explode('.',request()->host())[0]])}}">
            <img class="w-full h-80 object-cover" src="{{ $image }}" alt="Product Image">
        </a>
        @if(!is_null($discount) && $discount != 0)
            <div class="absolute top-2 right-2 w-14 h-14 bg-orange-500 text-white text-center leading-[2.7rem] font-lg font-semibold text-base px-2 py-1 shadow rounded-full">
                -{{ round($discount, 0) }}%
            </div>
        @endif
    </div>
    <div class="p-4">
        <p class="text-slate-600 text-lg font-semibold mt-2 line-clamp-1 w-[250px]">{{ $productName }}</p>
        <div class="flex items-center mt-2">
            @if( !is_null($retailPrice) && $retailPrice != $productPrice )
                <span class="text-gray-500 text-base font-semibold line-through">{{ number_format($retailPrice) }} KSH</span>
                <span class="text-primary-900 text-base font-semibold ml-2">{{ number_format($productPrice) }} KSH</span>
            @else
                <span class="text-gray-500 text-base font-semibold">{{ number_format($productPrice) }} KSH</span>
            @endif
            @if(!is_null($discount) && $discount != 0)
                <span class="text-orange-500 text-base ml-2">-{{ round($discount, 0) }}%</span>
            @endif

        </div>
        <div class="grid grid-cols-5 gap-4 items-center justify-between mt-4">
            <button class="bg-warning-50 text-center text-warning-800 w-11 h-11 rounded-lg add-to-cart"
                    @click="toggle"
                    x-data="toggling(false,{{$id}},'{{$userType}}')"
                    data-product="{{$id}}"
            >
                <i x-show="isInCard" data-feather="check" class="w-5 h-5 bg-warning-50 text-warning-800 inline"></i>
                <i x-show="!isInCard" data-feather="shopping-cart"
                   class="w-5 h-5 bg-warning-50 text-warning-800 inline"></i>

            </button>
            <a href="{{route('shop.product.buyNow', ['product_id' => $id,'account' => str_replace('.','',request('account'))])}}"
               class="bg-primary-1000 col-span-4 text-white font-semibold px-4 py-2 rounded-lg h-11 text-center">Buy
                Now</a>
        </div>
        <div class="mt-2">
            <span style="color: {{ $shipmentTagColor }}; background-color: {{ $shipmentTagBackgroundColor }}; padding: 2px 4px; border-radius: 5px;">{{ $shipmentTagText }}</span>
        </div>
    </div>
</div>
