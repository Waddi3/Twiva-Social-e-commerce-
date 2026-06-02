@php use Modules\User\Entities\User; @endphp
<section class="orderDetailsList CLASSESS">
    <div class=" rounded-lg flex flex-col justify-start items-start gap-[30px] p-[30px] h-fit">
        <div class="w-full flex justify-start gap-2.5 text-gray-800 text-lg font-semibold">
            <i data-feather="shopping-cart"></i>
            <h5 class="font-bold">Order ID: {{$order->id}}</h5>
            <a
            href="{{ route('buyer-receipt.pdf', [$order->id]) }}"
            class="btn btn-primary"
            style="color: rgb(160, 25, 25); margin-left: 80px; text-decoration: none; padding: 0px 20px; border-radius: 5px; font-size: 18px; font-weight: bold;"
        >
            Download your receipt
        </a>
        </div>
        @if($order->shop->user->type == User::InfluencerType)
            <x-order::order-from.influencer :order="$order"/>
        @endif
        @if($order->shop->user->type == User::BusinessType)
            <x-order::order-from.business :order="$order"/>
        @endif
        @if($order->group_payment_link)
        <div class="text-center mx-auto w-full">
            <a  target="_blank" href="{{$order->group_payment_link}}">
                <span class="font-bold">Payment Link:</span>
                <span class="text-primary-600">{{$order->group_payment_link}}</span>
            </a>
        </div>
        @endif
    </div>
</section>
