@php use Modules\Order\Entities\Order; @endphp
@forelse($order->orderList as $product)
    <div class="grid grid-cols-3 gap-2.5 mt-2">
        <div class="col-span-1">
            <img src="{{ $product->businessProductDetails->mainImagePath ?? 'undefined' }}"
                 class="w-full h-full object-center object-contain"
                 alt="" width="110px" height="137px">
        </div>
        <div class=" gap-2.5 col-span-2">
            <div class="flex justify-between">
                <p class="text-black-100 text-sm font-semibold">{{$product->businessProductDetails->name ?? 'undefined'}}</p>
                <p class="text-black-100 text-sm font-semibold">item Total:
                    <span>KSH {{ number_format(is_null($product->product_discount_price) ? $product->product_retail_price : $product->product_discount_price) }}</span>
                </p>
            </div>

            @if($product->quantity)
                <p class="text-black-100 mt-3 text-sm font-semibold">Qty <span
                            class="font-thin">{{$product->quantity}}</span></p>
            @endif
            @if($product->size)
                <p class="text-black-100 mt-3 text-sm font-semibold">Size <span
                            class="font-thin">{{$product->sizeDetailes->name}}</span></p>
            @endif
            @if($product->color)
                <p class="text-black-100 mt-3 text-sm font-semibold">Color <span
                            class="font-thin">{{$product->colorDetailes->name}}</span></p>
            @endif
        </div>
    </div>
@empty
@endforelse
@if(in_array($order->order_status, [Order::PROCESSING_STATUS ,Order::DISPATCHED_STATUS]))
    <p class="text-black-100 mt-3 text-sm font-semibold">
        {{ $order->tentative_period_text() }}
    </p>
@endif

