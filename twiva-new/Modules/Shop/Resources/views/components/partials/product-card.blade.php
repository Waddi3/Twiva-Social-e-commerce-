@props([
   'product' =>'',
   'isInCard' => 'false',
])
<div class="bg-white rounded-lg shadow overflow-hidden mx-auto">
    <a href="{{route('shop.product.show',['product' => $product->id,'account' => request('account')])}}">
        <div class="w-full h-[300px] bg-center bg-no-repeat  cover-image bg-cover relative"
             style="background-image: url('{{$product->productMainImage?->imageUrl ?? $product->businessProductMainImage?->imageUrl}}')">
        </div>
    </a>
    <div
            class="flex flex-col justify-between gap-2.5 h-[calc(100%-300px)] bg-white p-5 rounded-bl-[15px] rounded-br-[15px]">
        <a href="{{route('shop.product.show',['product' => $product->id,'account' => request('account')])}}">

            <div>
                <p class="mb-2.5 text-slate-600 text-lg font-semibold line-clamp-1 truncate w-[250px]">{{$product->product_title ?? ''}}</p>
                <p class="mb-2.5 text-base font-semibold text-black-100 truncate">{{ $product->productDetails->brand }}</p>
                <p class="text-xs font-semibold ">
                    @if($product->productDetails->discount)
                        <span class="text-gray-500 text-base font-semibold line-through">
                           KSH {{ number_format($product->product_price + ($product->productDetails->retail_price - $product->productDetails->discount)) }}
                            {{--                           KSH {{ number_format(100 * $product->product_price / $product->productDetails->discount, 2) }}--}}
                        </span>
                    @endif
                    <span class="text-primary-900 text-base font-semibold ml-2">
                                KSH {{ number_format($product->product_price) }}
                        </span>
                    @if($product->productDetails->discount)
                        {{-- todo: uncomment --}}
                        {{--<span class="text-orange-500 text-base ml-2">
                        -7%
                        </span>--}}
                    @endif
                </p>
            </div>
        </a>
        <div class="grid grid-cols-5 gap-4 items-center justify-between">
            <button
                    x-data="toggling({{(in_array($product->id,array_keys(\Session::get('cart') ?? []))) ? 'true': 'false'}},{{$product->id}})"
                    data-product="{{$product->id}}"
                    class="bg-warning-50 text-center text-warning-800 w-11 h-11 rounded-lg delete-toggle-btn"
                    @click="toggle"
            >

                <i x-show="isInCard" data-feather="check" class="w-5 h-5 bg-warning-50 text-warning-800 inline"></i>
                <i x-show="!isInCard" data-feather="shopping-cart" class="w-5 h-5 bg-warning-50 text-warning-800 inline"></i>
            </button>
            <a href="{{route('shop.product.buyNow',['account' => request('account'),'product_id' => $product->id])}}"
               class="bg-primary-1000 col-span-4 text-white font-semibold px-4 py-2 rounded-lg h-11 text-center">
                Buy Now
            </a>
        </div>
        <div class="mt-2">
            <span style="color: {{ $product->actualDetail->shipmentTagColor($product->actualDetail) }}; background-color: {{ $product->actualDetail->shipmentTagBackgroundColor($product->actualDetail) }}; padding: 2px 4px; border-radius: 5px;">{{ $product->actualDetail->shipmentTagText($product->actualDetail) }}</span>
        </div>
    </div>
</div>
