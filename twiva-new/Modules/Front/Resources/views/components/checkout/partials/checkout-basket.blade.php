<div>
    <div class="w-full flex justify-start gap-2.5 text-gray-800 text-lg font-semibold">
        <i data-feather="shopping-cart"></i>
        <h5> YOUR ORDER (<span class="total_items2">{{ $productsData['all_products']['quantity'] }}</span> items)</h5>
    </div>
    <div class="w-full flex flex-col gap-[30px]">

        {{-- @if ($productsData['type'] == 'Business')
            <x-shop::checkout.partials.checkout-basket.business-checkout-cards :productsData="$productsData['all_products']"/>
        @elseif($productsData['type'] == 'Influencer')
            <x-shop::checkout.partials.checkout-basket.influencer-checkout-cards :productsData="$productsData['all_products']"/>

        @endif --}}
        <x-front::checkout.partials.checkout-basket.influencer-checkout-cards :productsData="$productsData['influencer_products']" />
        <x-front::checkout.partials.checkout-basket.business-checkout-cards :productsData="$productsData['business_products']" />

    </div>
    <div class="w-full flex flex-col gap-4">
        <div class="flex justify-between text-lg font-semibold">
            <span class="text-gray-450">Items Total</span>
            <span id="total_items_price" class="total_items_price text-primary-900">KSH
                {{ number_format($productsData['all_products']['total']) }} </span>
        </div>
        <div class="flex justify-between text-lg font-semibold">
            <span class="text-gray-450">Shipping Fees</span>
            <span class="text-primary-900 shipping_fees" {{-- id="shipping_fees" --}}> KSH </span>
        </div>
        <div class="flex justify-between text-lg font-semibold" id="transaction_fees_div" style="display: none">
            <span class="text-gray-450">Transaction Fees</span>
            <span class="text-primary-900 transaction_fees" {{-- id="transaction_fees" --}}>KSH </span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-750 text-lg font-semibold">TOTAL</span>
            <span {{-- id="total_with_shipping_fees" --}} class="text-primary-900 text-2xl font-bold total_with_shipping_fees">KSH </span>
            <span {{-- id="total_with_shipping_fees_and_transaction_fees" --}} style="display: none"
                class="text-primary-900 text-2xl font-bold total_with_shipping_fees_and_transaction_fees">KSH </span>
        </div>

        <div class="flex justify-between with-coupon"
            @if (cache()->get(session('coupon_key')) &&
                    cache()->get(session('coupon_key'))['coupon'] &&
                    (cache()->get(session('coupon_key'))['buyer']?->id == auth('buyer')->id() ||
                        session()->getId() == cache()->get(session('coupon_key'))['session_id'])) @else style="display: none" @endif>
            <span class="text-gray-750 text-lg font-semibold">TOTAL</span>
            <span {{-- id="total_with_shipping_fees" --}} class="text-primary-900 text-2xl font-bold coupon_with_shipping_fees">
                KSH {{ number_format(session('coupon_with_shipping_fees')) }}
            </span>
        </div>
    </div>

    @if ($hasShippingFees)
        <div class="w-full flex flex-col gap-4">
            <label class="text-gray-700 text-sm" for="partner">Delivery Option</label>
            <x-shop::checkout.utilities.select {{-- onchange="updatePartner()" --}} id="partner" name="partner" class="pl-2 pr-8">
                @foreach ($partners as $index => $partner)
                    <option value="{{ $partner->id }}" @if ($partner->id == \Illuminate\Support\Facades\Session::get('partner_id')) selected @endif>
                        {{ $partner->name }}
                    </option>
                @endforeach
            </x-shop::checkout.utilities.select>
        </div>
    @endif

    @include('shop::checkout.coupon')
</div>
