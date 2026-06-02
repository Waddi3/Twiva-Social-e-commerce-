@props([
   'product' =>'',
   'isInCard' => 'false',
])
<div class="h-full rounded-bl-[15px] rounded-br-[15px] drop-shadow-card">
    <a href="{{route('shop.product.show',['product' => $product->id,'account' => request('account')])}}">
        <div class="w-full h-[300px] bg-[#f5f5f5] bg-center bg-no-repeat  cover-image bg-cover"
             style="background-image: url('{{$product->mainImagePath}}')"></div>
    </a>
    <div
        class="flex flex-col justify-between gap-2.5 h-[calc(100%-300px)] bg-white p-5 rounded-bl-[15px] rounded-br-[15px]">
        <a href="{{route('shop.product.show',['product' => $product->id,'account' => request('account')])}}">

            <div>
                <p class="mb-2.5 text-base font-semibold text-black-100 truncate">{{$product->name ?? ''}}</p>
                <p class="mb-2.5 text-base font-semibold text-black-100 truncate">{{$product->brand}}</p>
                @if($product->discount < $product->retail_price)
                    <p class="text-xs font-semibold ">
                        <span style="text-decoration: line-through;">
                           KSH {{number_format($product->retail_price ?? '')}}
                        </span>
                        <span class="ml-2 text-primary-900">
                            KSH {{number_format($product->discount)}}
                        </span>
                    </p>
                @else
                    <p class="text-xs font-semibold text-primary-900">KSH {{number_format($product->retail_price ?? '')}}</p>
                @endif

            </div>
        </a>
        <div class="flex justify-between items-center">
            <button
                x-data="toggling({{(in_array($product->id,array_keys(Session::get('cart') ?? []))) ? 'true': 'false}},{{($product->i}})"
                data-product="{{($product->i}}"
                class="h-[40px] w-[calc(100%-50px)] mx-1 flex justify-center items-center rounded-lg hover:drop-shadow-btn delete-toggle-btn"
                :class="isInCard ? ' bg-white': 'bg-primary-50'"
                @click="toggle"
            >
                <i x-show="isInCard" data-feather="check" class="w-5 h-5 text-gray-600"></i>
                <i x-show="!isInCard" data-feather="shopping-cart" class="w-5 h-5 text-primary-1000"></i>
                <span class="pl-2 text-primary-1000">
                     <span x-show="!isInCard">
                        Add To Cart
                    </span>
                    <span x-show="isInCard">
                        Remove
                    </span>
                </span>
            </button>
            <a href="{{(route('shop.product.buyNow',['account' => request('account'),'product_id' => $product->id]}}"
               class="h-[40px] w-[calc(100%-50px)] bg-primary-1000 text-base text-white flex justify-center
                items-center rounded-lg">
                Buy Now
            </a>
        </div>
    </div>
</div>
