@if(isset($products))
    @foreach($products  as $product)
        <div class="col-span-12">
            @if(str_contains(get_class($product),  "InfluencerShopProducts"))
                <x-shop::items.drawes-basket-item :product="$product"/>
            @else
                <x-shop::items.business-drawes-basket-item :product="$product"/>
            @endif
        </div>

    @endforeach
@endif