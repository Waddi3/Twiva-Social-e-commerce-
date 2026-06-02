@foreach ($product->eShopConnected->unique('shop_id') as $eshop)
    <tr>
        <td>{{ $eshop->influencerShop->id }}</td>
        <td class="eshop-link">
            <p>{{ $eshop->influencerShop->shop_name }}</p>
            {{-- <a href="">http://www.konmatfix.com</a> --}}
        </td>
        <td>{{ $eshop->influencerShop->influencer->influencerDetails->name??'NA' }}</td>
        <td>{{ $eshop->influencerShop->influencer->email }}</td>
        <?php $summing = DB::select("SELECT * FROM `orders_details` as sales WHERE (product_id IN
        (SELECT id FROM `influencer_shop_product` where product_id = $product->id ))
        AND influencer_id = $eshop->influencer_id ") ?>
        <td>{{ count($summing) }} (KSH {{ number_format(array_sum(array_column($summing, 'amount'))) }})</td>
    </tr>
@endforeach
