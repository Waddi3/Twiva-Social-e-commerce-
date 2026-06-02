@forelse($order->orderList as $product)
<div class="grid grid-cols-3 gap-2.5 mt-2">
    <div class="col-span-1">
        {{--@php dd($product->productDetails); @endphp--}}
        <img src="{{ !is_null($product->businessProductDetails) ? $product->businessProductDetails->mainImagePath : $product->productDetails->mainImagePath }}"
             class="w-full h-full object-center object-contain"
             alt="" width="110px" height="110px">
    </div>
    <div class="gap-2.5 col-span-2">
        <div class="flex justify-between">
            <p class="text-black-100 text-lg font-semibold mt-2">{{ !is_null($product->businessProductDetails) ? $product->businessProductDetails->name : $product->productDetails->product_title}}</p>
        </div>

        <p class="dark-red mt-2 text-lg">
            KSH {{ number_format(is_null($product->product_discount_price) ? $product->amount : $product->product_discount_price) }}
        </p>
    </div>
</div>
@empty
@endforelse

<div class="order-total w-full flex flex-col">
    <div class="flex justify-between mt-2">
        <span class="text-gray-400 font-semibold text-lg">Items total:</span>
        <span class="dark-red font-semibold text-lg">KSH {{ number_format($order->order_amount) }}</span>
    </div>
    <div class="flex justify-between mt-2">
        <span class="text-gray-400 font-semibold text-lg">Shipping fees:</span>
        <span class="dark-red font-semibold text-lg">KSH {{ number_format($order->shipping_amount) }}</span>
    </div>
    <div class="flex justify-between mt-2">
        <span class="text-black-100 font-semibold text-lg">Total:</span>
        <span class="dark-red font-bold text-lg">KSH {{ number_foramt($order->order_amount + $order->shipping_amount) }}</span>
    </div>
</div>

{{--@if(in_array($order->order_status, [\Modules\Order\Entities\Order::PROCESSING_STATUS ,\Modules\Order\Entities\Order::DISPATCHED_STATUS]))
<p class="text-black-100 mt-3 text-sm font-semibold">
    {{ $order->tentative_period_text() }}
</p>
@endif--}}
