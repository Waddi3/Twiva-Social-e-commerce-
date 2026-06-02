<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        body {

            font-family: "Source Sans Pro", sans-serif;
            color: #27272A;
        }

        .dashboard_container .invoice-wrapper {
            background: #FFFFFF;
            box-shadow: 0px 2px 8px rgb(91 80 255 / 10%);
            border-radius: 4px;
            padding: 24px;
            margin-bottom: 37px;
            margin: 0 40px 15px 24px !important;
            margin: auto;
        }

        #order-receipt .invoice-wrapper {
            width: 100%;
            margin: 24px auto !important;
        }

        .dashboard_container .invoice-wrapper .invoice-header-wrapper {
            margin-bottom: 48px;
            height: 130px;
        }

        .dashboard_container .invoice-wrapper .invoice-header-wrapper .invoice-detial-wrapper ul li p {
            margin: 0px;
        }

        table {
            width: 100%;
            border: 1px solid #ddd;
            position: relative;
        }

        .order-itmes-table-wrapper {
            padding: 0 26px;
        }

        .table-wrapper .table thead {
            background: rgba(245, 245, 245, 0.54);
        }

        .table-wrapper .table tbody tr {
            background: #FFFFFF;
            border: 1px solid #F2F2F2;
            box-sizing: border-box;
        }

        #order-receipt .invoice-wrapper .table tbody tr td:nth-child(odd) {
            padding: 12px !important;
        }

        #order-receipt .invoice-wrapper .table tbody tr td:nth-child(even) {
            padding: 0 !important;
            text-align: center;
        }

        #order-receipt .invoice-wrapper .table tbody tr td:nth-child(odd) {
            padding: 12px !important;
        }

        #order-receipt .invoice-wrapper .table tbody tr td {
            border-bottom: 1px solid #ddd;
            font-size: 14px;
        }

        #order-receipt .invoice-wrapper .table tbody tr:last-child td {
            border: none;
        }

        .order-itmes-table-wrapper .address-wrapper {
            margin-bottom: 27px;
        }

        .order-itmes-table-wrapper .address-wrapper .address-item {
            padding: 24px;
            background: rgba(245, 245, 245, 0.54);
        }

        .order-itmes-table-wrapper .address-wrapper .address-item p:first-child {
            opacity: 0.9;
            font-weight: 500;
            line-height: 19px;
        }
    </style>
</head>

<body>
<div class="dashboard_container" id="order-receipt">
    <div class="right_col dashboard-page" role="main">
        <div class="invoice-wrapper">
            <div class="invoice-header-wrapper">
                <div class="invoice-detial-wrapper" x style="width: 70%;  float: left">
                    <p>Order No. {{ $order->id }}</p>
                    <p> Order by: {{ $order->buyer?->name ?? $order->shippingAddress->contact_name }}</p>
                    <p>{{ $order->created_at->format('M j, Y') }} {{ $order->created_at->format('g:i A') }}</p>
                    <p>Order Status:{{$order->statusString}}</p>
                </div>
                <div class="invoice-print-btn-wrapper " style="width: 30%; float: left; text-align: right">
                    <img src="{{asset('shop/images/logos/Logo.png')}}">
                </div>
            </div>

            <div class="order-itmes-table-wrapper table-wrapper">
                <table class="table table-responsive">
                    <thead>
                    <tr>
                        <th scope="col">Order Items</th>
                        <th scope="col">Price</th>
                        <th scope="col">Qty</th>
                        <th scope="col">Total</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($order->orderDetails as $orderDetail)
                        <tr>
                            <td scope="row">
                                <div class="product-img-nane-wrapper">
                                    <div class="name">
                                                <span>
                                                    {{ $orderDetail->productFromInfluencerShop->product_title }}
                                                </span>
                                        <span></span>
                                    </div>
                                </div>
                            </td>
                            <td>KSH {{ number_format($orderDetail->productFromInfluencerShop->product_price) }}</td>
                            <td>{{ $orderDetail->quantity }}</td>
                            <td>KSH {{ number_format($orderDetail->amount *  $orderDetail->quantity) }}</td>
                        </tr>
                    @endforeach

                    </tbody>
                </table>

                <div class="total-amount-wrapper" style=" width:100%;">
                    <p class="subtotal" style="width: 200px; margin-left:auto ">
                        <span>Subtotal</span>
                        <span> KSH {{ number_format($order->order_amount) }}</span>
                    </p>
                    <p class="shipping" style="width: 200px; margin-left:auto">
                        <span>Shipping </span>
                        <span> KSH {{number_format($order->shipping_amount)}}</span>
                    </p>
                    <p class="grand-total " style="width: 200px; margin-left:auto">
                        <span>Grand Total </span>
                        <span> KSH {{ number_format($order->order_amount + $order->shipping_amount)}}</span>
                    </p>

                </div>

                <div class="address-wrapper">
                    <div class="row">
                        <div class="col-md-4" style="width: 50%; float: left;">
                            @foreach($order->transactions as $transaction)
                                <div class="address-item payment-status">
                                    <p>Payment Method: {{$transaction->gatewayName}}</p>
                                    <p>Transaction ID: {{ $transaction->transaction_id ?? '--'}}</p>
                                    <p>Payment Status: {{$transaction->transactionStatus['title']}}</p>
                                </div>
                            @endforeach
                        </div>
                        <div class="col-md-4" style="width: 50%; float: left;">
                            <div class="address-item payment-status">
                                <p class="font-italic">Name: <span
                                            class="font-normal">{{ $order->shippingAddress->contact_name }}</span></p>
                                <p class="font-italic">Address: <span
                                            class="font-normal">{{ $order->shippingAddress->fullAddress }}</span></p>
                                <p class="font-italic">Phone: <span
                                            class="font-normal">{{ $order->shippingAddress->phone_number }}</span></p>
                            </div>
                        </div>

                    </div>
                </div>


            </div>
        </div>

    </div>


</div>


</body>

</html>
