@component('mail::message')

    Hi {{$buyerName}}.

    Your orders have been placed.
    @foreach($orders as $order)

    #{{$order->id}}:
        Order amount:{{$order->order_amount}}
        Shipping amount:{{$order->shipping_amount}}

@component('mail::table')
    | Product Name     |  Amount   |  Quantity       |
    | :----------------|:----------| :---------------|
@foreach($order->orderDetails as $product)
    @if($product->businessProductDetails)
        {{$product->businessProductDetails->name}} | {{$product->amount}} | {{$product->quantity}}
    @else
        {{$product->productFromInfluencerShop->product_title}} | {{$product->amount}} | {{$product->quantity}}
    @endif
@endforeach

@endcomponent
@endforeach
    <span style="font-weight: bold">Twiva team,</span>
    <p style="text-align: center; margin-top: 5px;border-top: 1px; border-color: gray">
        <small style="text-align: center">
            This is an automatically generated email, please do not reply.<br>
            © Twiva 2022, All Rights Reserved.
        </small>
    </p>

@endcomponent
