@foreach($products as $product)

    <div class="col-span-12 md:col-span-4 lg:col-span-3">

        @if(str_contains(get_class($product),  "InfluencerShopProducts"))
            <x-shop::partials.product-card :is-in-card="in_array($product->id,(Session::get('cart') ?? []))"
                                           :product="$product"/>
        @else
            <x-shop::partials.business-product-card :is-in-card="in_array($product->id,(Session::get('cart') ?? []))"
                                                    :product="$product"/>
        @endif
    </div>

@endforeach
