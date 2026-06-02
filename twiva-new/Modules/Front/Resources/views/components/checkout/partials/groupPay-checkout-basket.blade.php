<div>
    <div class="w-full flex justify-start gap-2.5 text-gray-800 text-lg font-semibold">
        <i data-feather="shopping-cart"></i>
        <h5> YOUR ORDER (<span class="total_items2">{{ $productsData['all_products']['quantity'] }}</span> items)</h5>
    </div>
    <div class="w-full flex flex-col gap-[30px]">
        <x-front::checkout.partials.checkout-basket.influencer-checkout-cards
                :productsData="$productsData['influencer_products']"
        />
        <x-front::checkout.partials.checkout-basket.business-checkout-cards
                :productsData="$productsData['business_products']"
        />

    </div>
    <div class="w-full flex flex-col gap-4">
        <div class="flex justify-between text-lg font-semibold">
            <span class="text-gray-450">Items Total</span>
            <span class="text-primary-900">
               KSH {{number_format($requestData['items_total'])}}
            </span>
        </div>
        <div class="flex justify-between text-lg font-semibold">
            <span class="text-gray-450">Shipping Fees</span>
            <span class="text-primary-900 ">KSH {{number_format($requestData['shipping_fee'])}} </span>
        </div>
        <div class="flex justify-between text-lg font-semibold">
            <span class="text-gray-450">Total</span>
            <span class="text-primary-900 "> KSH {{number_format($requestData['total'])}}</span>
        </div>
        <div class="flex justify-between text-lg font-semibold">
            <span class="text-gray-450">Amount already paid</span>
            <span class="text-primary-900 "> KSH {{number_format($requestData['amount_already_paid'])}}</span>
        </div>
        <div class="flex justify-between text-lg font-semibold">
            <span class="text-gray-450">Your contribution</span>
            <span class="text-primary-900 "> KSH {{number_format($requestData['your_contribution'])}}</span>
        </div>
        <div class="flex justify-between text-lg font-semibold">
            <span class="text-gray-450">Amount pending</span>
            <span class="text-primary-900 "> KSH {{number_format($requestData['amount_pending'])}}</span>
        </div>


    </div>


</div>
