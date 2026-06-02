<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Order Receipt</title>
<style>
    body { font-family: Arial, sans-serif; }
    .container { width: 100%; padding: 20px; }
    .header { text-align: center; }
    .details { margin-bottom: 20px; }
    .items { width: 100%; border-collapse: collapse; }
    .items th, .items td { border: 1px solid #ddd; padding: 8px; }
    .items th { background-color: #f2f2f2; }
</style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>Order Receipt</h1>
        <p><strong>Order Number:</strong> {{ $order->id }}</p>
         <p><strong>Date and Time of Purchase:</strong> {{ Carbon\Carbon::parse($order->created_at)->format('Y-m-d') }}</p>
    </div>

    <div class="details">
        <h3>Customer Information</h3>
        <p><strong>Name:</strong> {{ $order->buyerData->name ?? 'Not Available' }}</p>
        <p><strong>Email:</strong> {{ $order->buyerData->email ?? 'Not Available' }}</p>
        <p><strong>Phone Number:</strong> {{ $order->buyer->phone_number ?? 'Not Available' }}</p>
    </div>

    <div class="details">
        <h3>Shipping Address</h3>
        <p>{{ $order->shippingAddress->fullAddress ?? 'Not Available' }}</p>
    </div>

    <div class="details">
        <h3>Items Purchased</h3>
        <table class="items">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Price per Item</th>
                    <th>Total Price per Item</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $subtotal = 0;
                @endphp
                @foreach($order->orderDetails as $item)
                    @php
                        $totalPricePerItem = $item->quantity * $item->price;
                        $subtotal += $totalPricePerItem;
                    @endphp
                    <tr>
                        <td>@foreach ($order->orderList as $product)
                            @if($order->shop->user->type == \Modules\User\Entities\User::InfluencerType)
                            {{ $product->productFromInfluencerShop?->product_title ?? 'Not Available'}}
                            @endif
                            @if($order->shop->user->type == \Modules\User\Entities\User::BusinessType)
                            {{$product->businessProductDetails->name ?? 'Not Available'}}
                            @endif
                            @endforeach
                        </td>

                        <td>{{ $item->quantity }}</td>
                        <td>{{ number_format($item->price, 2) }}</td>
                        <td>{{ number_format($totalPricePerItem, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="details">
        <h3>Pricing Details</h3>
        <p><strong>Subtotal:</strong> {{ number_format($subtotal, 2) }}</p>
        <p><strong>Discount:</strong> {{ number_format($order->discount ?? 0, 2) }}</p>
        <p><strong>Shipping Costs:</strong> {{ number_format($order->shipping_costs ?? 0, 2) }}</p>
        <p><strong>Total Amount:</strong> {{ number_format($subtotal - ($order->discount ?? 0) + ($order->shipping_costs ?? 0), 2) }}</p>
    </div>

    <div class="details">
        <h3>Transaction Information</h3>
        <p><strong>Transaction ID:</strong> {{ $order->transactions->first()->transaction_id ?? 'Not Available' }}</p>
    </div>
</div>

</body>
</html>
