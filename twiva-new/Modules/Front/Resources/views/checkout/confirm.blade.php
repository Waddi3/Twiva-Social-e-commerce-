@extends('front::checkout.layouts.app')

@section('title', 'Checkout Confirm')

@section('content')
    <x-partials.checkout-container current="confirm"
                                   :productsData="$productsData['all_products']"
                                   :partners="$partners"
    >
        <div x-data="confirm()" class="w-full flex flex-col items-center justify-start pt-[50px] lg:pt-[107px] mb-5">
            <template x-if="loading">
                <div class="h-[calc(100vh-160px)] lg:h-[calc(100vh-156px)] absolute right-0 left-0 bottom-0 top-[6px] bg-white flex flex-col gap-6 justify-center items-center z-[100]">
                    <p class="text-primary-900 text-lg font-normal">
                        Processing payment...
                    </p>

                    <span class="checkout-loading"></span>
                </div>
            </template>
            <template x-if="!loading">
                <div>
                    <template x-if="status">
                        <div>
                            <div class="flex flex-col items-center justify-start gap-5 max-w-[560px] mb-6 lg:mb-12">
                    <span class="bg-success-500 text-white w-[30px] h-[30px] flex justify-center items-center rounded-full">
                        <i data-feather="check-circle" class="w-5 h-5"></i>
                    </span>
                                <h6 class="text-gray-850 text-2xl text-center font-semibold">Your order was
                                    successful!</h6>
                                <x-shop::checkout.utilities.button color="primary-700" class="px-[50px]"
                                                                   tag="a"
                                                                   href="{{route('ecommerce.index')}}">
                                    Back to shopping
                                </x-shop::checkout.utilities.button>
                            </div>
                            @if($groupPayLink)
                                <p class="text-center pb-2">
                                    You can
                                    <span class="text-primary-600 font-bold"
                                          id="copyText"
                                          style="cursor: pointer;"
                                          onclick="copyToClipboard('{{ $groupPayLink }}')"
                                    >
                                      Invite Others
                                    </span>
                                    and complete your group purchase!
                                </p>
                                <p id="status" class="text-center text-green-500"></p>
                            @endif
                            <div class="bg-gray-125 flex flex-col justify-start items-start gap-[30px] p-[30px] max-w-[400px]">
                                <div class="w-full flex justify-start gap-2.5 text-gray-800 text-lg font-semibold">
                                    <i data-feather="shopping-cart"></i>
                                    <h5>YOUR Orders Numbers
                                        #{{implode(',',\Illuminate\Support\Facades\Session::get('orders_ids'))}}</h5>
                                </div>
                                <div class="w-full flex flex-col gap-3">
                                    @foreach($orders as $order)
                                        <h5> #{{$order->id}}</h5>
                                        @if($order->shop->user->type == 'Influencer')
                                            @foreach($order->orderDetails as $product)
                                                <div class="grid grid-cols-4 gap-3.5">
                                                    <div class="col-span-1">
                                                        <img src=" {{$product->productDetails->productMainImage?->imageUrl }} "
                                                             class="w-full h-full object-center object-contain">
                                                    </div>
                                                    <div class="flex flex-col justify-center gap-2.5 col-span-3">
                                                        <h6 class="text-primary-1000 text-base font-semibold">
                                                            KSH {{number_format($product->fullAmount)}}</h6>
                                                        <p class="text-black-100 text-sm font-semibold truncate">{{ $product->productDetails->product_title }}</p>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @elseif($order->shop->user->type == 'Business')
                                            @foreach($order->orderDetails as $product)
                                                <div class="grid grid-cols-4 gap-3.5">
                                                    <div class="col-span-1">
                                                        <img src=" {{$product->businessProductDetails->mainImagePath }} "
                                                             class="w-full h-full object-center object-contain">
                                                    </div>
                                                    <div class="flex flex-col justify-center gap-2.5 col-span-3">
                                                        <h6 class="text-primary-1000 text-base font-semibold">
                                                            KSH {{number_format($product->fullAmount)}}</h6>
                                                        <p class="text-black-100 text-sm font-semibold truncate">{{ $product->businessProductDetails->name }}</p>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                            @if($groupPaymentRequestData)
                                <div class="w-full flex flex-col gap-4">
                                    <div class="flex justify-between text-lg font-semibold">
                                        <span class="text-gray-450">Items Total</span>
                                        <span class="text-primary-900">KSH {{number_format($groupPaymentRequestData['items_total'])}}</span>
                                    </div>
                                    <div class="flex justify-between text-lg font-semibold">
                                        <span class="text-gray-450">Shipping Fees</span>
                                        <span class="text-primary-900 ">KSH {{number_format($groupPaymentRequestData['shipping_fee'])}} </span>
                                    </div>
                                    <div class="flex justify-between text-lg font-semibold">
                                        <span class="text-gray-450">Total</span>
                                        <span class="text-primary-900 "> KSH {{number_format($groupPaymentRequestData['total'])}}</span>
                                    </div>
                                    <div class="flex justify-between text-lg font-semibold">
                                        <span class="text-gray-450">Amount already paid</span>
                                        <span class="text-primary-900 "> KSH {{number_format($groupPaymentRequestData['amount_already_paid'])}}</span>
                                    </div>
                                    <div class="flex justify-between text-lg font-semibold">
                                        <span class="text-gray-450">Your contribution</span>
                                        <span class="text-primary-900 "> KSH {{number_format($groupPaymentRequestData['your_contribution'])}}</span>
                                    </div>
                                    <div class="flex justify-between text-lg font-semibold">
                                        <span class="text-gray-450">Amount pending</span>
                                        <span class="text-primary-900 "> KSH {{number_format($groupPaymentRequestData['amount_pending'])}}</span>
                                    </div>
                                </div>
                            @else

                                <div class="w-full flex flex-col gap-4">
                                    <div class="flex justify-between text-lg font-semibold">
                                        <span class="text-gray-450">Items Total</span>
                                        <span class="text-primary-900">KSH {{number_format($orders->sum('order_amount'))}}</span>
                                    </div>
                                    <div class="flex justify-between text-lg font-semibold">
                                        <span class="text-gray-450">Shipping Fees</span>
                                        <span class="text-primary-900">KSH {{number_format($orders->sum('shipping_amount'))}}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-750 text-lg font-semibold">TOTAL</span>
                                        <span class="text-primary-900 text-2xl font-bold">KSH {{number_format($orders->sum('order_amount')+$orders->sum('shipping_amount'))}}</span>
                                    </div>
                                    @if ($orders->sum('coupon_discount'))
                                        <div class="flex justify-between">
                                            <span class="text-gray-750 text-lg font-semibold">TOTAL With Coupon</span>
                                            <span class="text-primary-900 text-2xl font-bold">KSH {{number_format($orders->sum('order_amount') + $orders->sum('shipping_amount')  - $orders->sum('coupon_discount'))}}</span>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </template>
                    <template x-if="!status">
                        <div class="flex flex-col items-center justify-start gap-5 max-w-[560px]">
                            <span class="bg-error-850 text-white w-[30px] h-[30px] flex justify-center items-center rounded-full">
                                <i data-feather="x" class="w-5 h-5"></i>
                            </span>
                            <h6 class="text-gray-850 text-2xl text-center font-semibold">Your payment was not
                                successful.</h6>
                            <p class="text-gray-850 text-base text-center font-normal">Something went wrong when we were
                                processing
                                your payment. Please check if you have enough sufficient funds or contact your bank.</p>
                            <x-shop::checkout.utilities.button color="primary-700" class="px-[50px]" tag="a"
                                                               href="{{route('ecommerce.index')}}">
                                Back to shopping
                            </x-shop::checkout.utilities.button>
                        </div>
                    </template>
                </div>
            </template>
        </div>
    </x-partials.checkout-container>
@endsection

@push('script')
    <script>

        function copyToClipboard(text) {
            console.log("copyToClipboardFallback text =>", text)
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(text).then(
                    () => {
                        document.getElementById("status").innerText = "Copied to clipboard!";
                    },
                    (err) => {
                        document.getElementById("status").innerText = "Failed to copy text.";
                        console.error("Failed to copy text: ", err);
                    }
                );
            } else {
                const textarea = document.createElement("textarea");
                textarea.value = text;
                textarea.style.position = "fixed";
                document.body.appendChild(textarea);
                textarea.focus();
                textarea.select();

                try {
                    document.execCommand("copy");
                    document.getElementById("status").innerText = "Copied to clipboard!";
                } catch (err) {
                    document.getElementById("status").innerText = "Failed to copy text.";
                    console.error("Fallback copy failed: ", err);
                }
                document.body.removeChild(textarea);
            }
        }
    </script>

@endpush
