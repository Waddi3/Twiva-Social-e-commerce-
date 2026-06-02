<table>
    <thead>
    <tr>
        <th>ID</th>
        <th>Buyer Name</th>
        <th>Buyer Email</th>
        <th>Buyer Phone number</th>
        <th>Buyer Address</th>
        <th>Product name</th>
        <th>Product price</th>
        <th>Quantity</th>
        <th>Order status</th>
        <th>Order data</th>
        <th>Business Name</th>
    </tr>
    </thead>
    <tbody>
    @foreach($sales as $sale)
        <tr>
            <td>{{$sale->id }}</td>
            <td>{{$sale->Order->buyer?->name ?? ($sale->Order->shippingAddress->contact_name ?? '--') }}</td>
            <td>{{$sale->Order->buyer?->email ?? ($sale->Order->shippingAddress->email ?? '--') }}</td>
            <td>{{$sale->Order->buyer?->mobile ?? ($sale->Order->shippingAddress->phone_number ?? '--') }}</td>
            <td>{{$sale->Order->shippingAddress->full_address ?? '--'  }}</td>
            <td>{{$sale->productFromInfluencerShop->product_title ?? '--'}}</td>
            <td>{{$sale->amount  ?? '--'}}</td>
            <td>{{$sale->quantity ?? '--'}}</td>
            <td>{{$sale->Order->statusString ?? '--'}}</td>
            <td>{{$sale->Order->business->name ?? '--' }}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>