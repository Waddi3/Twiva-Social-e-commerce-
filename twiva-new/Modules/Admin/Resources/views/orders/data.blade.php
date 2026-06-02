@foreach ($orders as $order)
    <tr>

        <td><a href="{{ route('admin.orders.show', [$order->id]) }}">{{ $order->id }}</a></td>
        <td>
            <p>{{ $order->latestTransaction[0]->user->name ?? ($order->shippingAddress->contact_name ?? ("no shipping($order->id)")) }}</p>
            <p>(ID: {{ $order->latestTransaction[0]->user->id ?? 'Guest' }})</p>
        </td>
        {{-- <td>
            <p>{{ $order->shop->influencer->name ?? "Influencer" }}</p>
            <p>(ID: {{ $order->shop->influencer->id ?? "" }})</p>
        </td> --}}

        <td>
            @if($order->shop->user->type == 'Influencer')
            <p>{{ $order->shop->influencer->name ?? "Influencer" }}</p>
            <p>(ID: {{ $order->shop->influencer->id ?? "" }})</p>
            @else
            --
            @endif
        </td>
        <td>
            @if($order->shop->user->type == 'Business')
            <p>{{ $order->shop->user->name }}</p>
            <p>( ID: {{$order->shop->user->id }})</p>
            @else
            --
            @endif
        </td>
        
        <td>{{ count($order->orderDetails) }}</td>
        <td>KSH {{ number_format($order->order_amount) }}</td>
        <td>{{ $order->created_at->format('d/m/Y') }} |
            {{  $order->created_at->format('g:i A') }}</td>
        <td>{{ $order->updated_at->format('d/m/Y') }} |
            {{  $order->updated_at->format('g:i A') }}</td>
        {{--        @if(!$order->transactions->count())--}}
        {{--            @dd($order->transactions)--}}
        {{--        @endif--}}
        <td class="{{ $order->payment_status_transaction_class }}">
            <p>{{ $order->payment_status_transaction }}</p>
            {{--        <td class="{{ \Modules\Order\Entities\Order::PAYMENT_STATUSES_CLASSES[$order->payment_status] }}">--}}
            {{--            <p>{{ ($order->payment_status == \Modules\Order\Entities\Order::PAID) ? \Modules\Order\Entities\Order::PAYMENT_STATUS[$order->payment_status] . " ({$order->latestTransaction->first()?->gatewayName})" : \Modules\Order\Entities\Order::PAYMENT_STATUS[$order->payment_status] }}</p>--}}
        </td>
        <td class="{{$order->statusString == 'Delivered' ? 'eshop-active' :($order->statusString == 'Canceled'? 'eshop-failed' : 'in-process')}}">
            <p>{{$order->statusString}}</p>
        </td>
        <td>
            <a href="{{ route('admin.orders.show', [$order->id]) }}"><img
                        src="{{ asset('admin/images/icons/Frame7158.svg') }}" alt=""></a>
            <!-- Checked switch -->

        </td>
    </tr>
@endforeach
@if(count($orders)==0)
    <tr class="product-box-inner p-3" style="border:0;height:250px" id="nf">
        <td class="text-center p-5" colspan="9">
            <h3 style="font-size:20px;">
                You don’t have any orders
            </h3>
        </td>
    </tr>
@endif
<tr>
    <td colspan="100">
        <div class="pagination">
            <div class="col-sm-5 p-0">
                <!-- <div class="dataTables_info" id="example_info" role="status" aria-live="polite">Showing 1 to 10 of 57 entries</div></div> -->
                <div class="col-sm-7 p-0">
                    <div class="paging_simple_numbers" id="example_paginate">
                        {{ $orders->links('admin::layouts.pagination')}}
                    </div>
                </div>
            </div>
        </div>
    </td>
</tr>
