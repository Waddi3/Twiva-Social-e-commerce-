<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ORDER NUMBER {{ $order->id }}</title>
    <style>
        body {
            margin: 10px;
        }
        .container {
            width: 900px;
            margin: 0 auto;
        }
        .main-title {
            font-size: 18px;
            text-align: center;
            font-weight: bold;
        }
        .section {
            display: block;
            padding: 10px 0;
            border-bottom: 1px solid #b4b4b4;
        }
        .key, .value {
            font-size: 12px;
            display: inline;
        }
        .status {
            color: #027A48;
            background: #ECFDF3;
            padding: 4px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
<div class="container">
    <h1 class="main-title">ORDER NUMBER #{{ $order->id }}</h1>
    <div class="section">
        <div class="row">
            <strong class="key">Order Status:</strong>
            <p class="value status">{{$order->statusString}}</p>
        </div>
    </div>
    <div class="section">
        <div class="row">
            <strong class="key">Customer name:</strong>
            <p class="value">{{$order->shippingAddress->contactName}}</p>
        </div>
        <div class="row">
            <strong class="key">Delivery type:</strong>
            <p class="value">{{$order->shippingAddress->contactName}}</p>
        </div>
        <div class="row">
            <strong class="key">Contact number:</strong>
            <p class="value">{{$order->shippingAddress->phone_number}}</p>
        </div>
        <div class="row">
            <strong class="key">Delivery address:</strong>
            <p class="value">{{$order->shippingAddress->fullAddress}}</p>
        </div>
        <div class="row">
            <strong class="key">Paid on:</strong>
            <p class="value">{{$order->order_date}}</p>
        </div>
    </div>
</div>
</body>
</html>
