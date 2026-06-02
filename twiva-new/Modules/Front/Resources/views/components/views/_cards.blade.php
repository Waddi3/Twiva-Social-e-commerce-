@if(isset($products))
    @foreach($products  as $product)
        <div class="col-span-12">
            @if(str_contains(get_class($product),  "InfluencerShopProducts"))
                <x-front::items.drawes-basket-item :product="$product"/>
            @else
                <x-front::items.business-drawes-basket-item :product="$product"/>
            @endif
        </div>

    @endforeach
@endif
