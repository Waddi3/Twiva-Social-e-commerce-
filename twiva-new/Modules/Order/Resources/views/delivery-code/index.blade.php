@extends('shop::layouts.app-without-login-delivery')
@push('style')
    <link rel="stylesheet" href="{{asset('shop/css/intl-tel-input/intlTelInput.min.css')}}">
    <style>
        .iti__flag {
            background-image: url("/shop/images/flags.png");
        }
    </style>
@endpush
@section('title','Order Delivery')

@section('content')
    <x-shop::core.container>
        @if(($order->payment_status == Modules\Order\Entities\Order::PAID && $order->order_status == Modules\Order\Entities\Order::DELIVERED_STATUS) || ($order->payment_status == Modules\Order\Entities\Order::PAY_ON_DELIVERY && $order->order_status == Modules\Order\Entities\Order::DELIVERED_STATUS))
            <section class="mt-5">
                <div class="grid xs-down:grid-cols-1 xs-down:gap-1 grid-cols-1 gap-4">
                    <section>
                        <div class="order-index bg-white py-6 rounded-lg px-8 mt-3">
                            <div class="block mt-10 justify-content-center pb-4 mb-6">
                                <div class="text-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#039855"
                                         class="w-8 h-8 text-center inline">
                                        <path fill-rule="evenodd"
                                              d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm13.36-1.814a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z"
                                              clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <h1 class="h1 font-semibold text-2xl text-center mt-2">Order has successfully paid and verified.</h1>
                                <p class="mt-2 text-center">
                                    Please provide goods to the customer.
                                </p>
                            </div>
                        </div>
                    </section>
                </div>
            </section>
        @else
            <section class="mt-5">
                <div class="grid xs-down:grid-cols-1 xs-down:gap-1 grid-cols-3 gap-4">
                    <section class="col-span-2">
                        <div data-orderID="{{ $order->id }}"
                             class="order-index bg-white py-6 rounded-lg px-8 mt-3">

                            <div class="block mt-2 justify-content-center border-b border-solid border-gray-300 pb-4 mb-6">
                                <h1 class="h1 font-semibold text-2xl text-center">ORDER NUMBER #{{ $order->id }}</h1>
                            </div>

                            {{-- Customer details --}}
                            <div class="flex flex-col border-b border-solid border-gray-300 pb-4">
                                <h2 class="sub-header">Customer details</h2>
                                <p class="mt-2">
                                    <strong>Delivery type:</strong>
                                    Express
                                </p>

                                <p class="mt-2">
                                    <strong>Contact Number:</strong>
                                    {{ $order?->buyer?->phone_code }} - {{ $order?->buyer?->phone_number }}
                                </p>

                                <p class="mt-2">
                                    <strong>Contact Email:</strong>
                                    {{ $order?->buyer?->email }}
                                </p>

                                <p class="mt-2">
                                    <strong>Delivery Address:</strong>
                                    @php $address = $order->shippingAddress()->first(); @endphp
                                    {{ $address?->delivery_address }}
                                    @if( !is_null($address) && !is_null($address->latitude) && !is_null($address->longitude) )
                                        (<span class="view-map link">
                                        <a href="https://maps.google.com/?q={{$address->latitude}},{{$address->longitude}}" target="_blank">VIEW ON MAP</a></span>)
                                    @endif
                                </p>
                            </div>
                            {{-- End Customer details --}}

                            {{-- Report issue --}}
                            <div class="flex justify-between border-b border-solid border-gray-300 pb-6 mt-6"
                            >
                                <p class="flex">
                                    <span class="mr-2 " data-feather="tag"></span>
                                    Does the package have any issues?
                                </p>

                                <a class="font-bold link cursor-pointer"
                                   onclick="report_issue.showModal()"
                                >
                                    Report an issue
                                </a>

                            </div>
                            {{-- End Report issue --}}

                            @if(
                                $order->payment_status == Modules\Order\Entities\Order::PAID
                                &&
                                $order->order_status == Modules\Order\Entities\Order::DISPATCHED_STATUS
                                )
                            {{-- Payment details pre-paid --}}
                            <form
                                class="payment-detail flex flex-col border-gray-300 pb-4 mt-6"
                                method="POST"
                                action="{{route('buyer.delivery.payment')}}"
                            >
                                <h2 class="sub-header">Payment details</h2>
                                <p class="mt-2">
                                    <strong>Payment type:</strong>
                                    <span class="payment-text">Pre-paid</span>
                                </p>

                                <div class="flex content-center bg-gray-100 flex p-3 rounded-lg mt-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#039855"
                                         class="w-8 h-8">
                                        <path fill-rule="evenodd"
                                              d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm13.36-1.814a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z"
                                              clip-rule="evenodd"/>
                                    </svg>
                                    <div class="order-paid flex flex-col ml-3">
                                        <h1>This order has already been paid!</h1>
                                        <p>Please provide goods to the customer then confirm this delivery one done.</p>
                                    </div>
                                </div>
                                @csrf
                                <div class="my-5">
                                    <button type="submit" id="proceedToCheckoutBtn"
                                       class="bg-primary-600 text-white rounded-lg flex justify-center items-center h-[44px] w-64 cursor-pointer"
                                    >
                                        Confirm Delivery
                                    </button>
                                </div>
                                <input type="hidden" name="order_id" value="{{ $order->id }}" />
                            </form>
                            {{-- End payment details pre-paid --}}

                            @else
                            {{-- Payment details have to paid --}}
                            <form
                                class="payment-detail flex flex-col border-gray-300 pb-4 mt-6"
                                method="POST"
                                action="{{route('buyer.delivery.payment')}}"
                            >
                                <h2 class="sub-header">Payment details</h2>
                                <p class="mt-2">
                                    <strong>Payment type:</strong>
                                    <span class="payment-text">Pay on Delivery</span>
                                </p>

                                <div class="flex items-center content-center bg-gray-100 flex p-3 rounded-lg mt-1">
                                    <p class="text-gray-600 inline">TOTAL</p>
                                    <h2 class="ml-2 dark-red inline">KSH {{ number_format($order->order_amount + $order->shipping_amount) }}</h2>
                                </div>

                                <div class="form-control border-b border-solid border-gray-300">
                                    <x-shop::utilities.radio
                                        type="radio" name="payment_method" value="mpesa"
                                        id="mpesa_method"
                                        label="MPESA"
                                        checked
                                    />
                                </div>

                                <div id="mpesa_wrapper" class="form-wrapper-list form-control mb-6">
                                    <div class="form-field p-3">
                                        <label class="text-gray-500 text-sm " for="mobile_input">Phone Number</label>
                                        <x-shop::utilities.input name="mobile" placeholder="" id="mobile_input"
                                                                 value="{{old('mobile') ?? null}}"/>
                                        <input id="mobile" type="hidden" name="mobile" value=""/>
                                        @error('mobile')
                                        <span class="text-error-800 text-sm ">{{$message}}</span>
                                        @enderror
                                        @error('phone_number')
                                        <span class="text-error-800 text-sm ">{{$message}}</span>
                                        @enderror
                                        <span id="mobile_input_error" class="text-error-800 text-sm"></span>
                                    </div>

                                    <div class="my-5">
                                        <button type="submit" id="proceedToCheckoutBtn"
                                                class="bg-primary-600 text-white rounded-lg flex justify-center items-center h-[44px] w-64 cursor-pointer"
                                        >
                                            Confirm
                                        </button>
                                    </div>
                                </div>

                                <div class="form-control border-b border-solid border-gray-300">
                                    <x-shop::utilities.radio
                                        type="radio" name="payment_method" value="paycash"
                                        id="paycash_method"
                                        label="Pay Cash"
                                    />
                                    <div id="paycash_wrapper"
                                        class="flex sm:flex-row flex-col justify-between content-center p-3 rounded-lg border border-solid border-gray-400 hidden">
                                        <p class="content-center">Customer is paying by <span
                                                class="font-bold dark-red">Cash</span>. Click to confirm total amount
                                            has been paid.</p>
                                        <button type="submit"
                                                class="content-center font-semibold col-span-2 bg-primary-600 text-white rounded-lg flex justify-center items-center h-[44px] cursor-pointer my-2 w-40">
                                            Confirm payment
                                        </button>
                                    </div>
                                </div>
                                @csrf
                                <input type="hidden" name="total_amount" value="{{ $order->order_amount + $order->shipping_amount }}" />
                                <input type="hidden" name="order_id" value="{{ $order->id }}" />
                            </form>
                            {{-- End payment details have to paid --}}
                            @endif

                        </div>
                    </section>

                    {{-- Sidebar --}}
                    <section class="h-fit md:block bg-white border-primary-900 rounded-lg orderDetailsList mt-3"
                             id="orderDetails">
                        <div class=" rounded-lg flex flex-col justify-start items-start gap-[30px] p-[30px] h-fit">
                            <div class="w-full flex justify-start gap-2.5 text-gray-800 text-lg font-semibold">
                                <i data-feather="shopping-cart"></i>
                                <h5 class="font-bold">ORDER ({{ count($order->orderDetails) }} items)</h5>
                            </div>
                            <x-order::order-from.order-items :order="$order"/>
                        </div>
                    </section>
                    {{-- End sidebar --}}

                </div>
            </section>
        @endif

        <!-- Open the modal using ID.showModal() method -->
        {{--<dialog id="confirm_model"
                class="modal order-index bg-white py-6 rounded-lg  shadow-md  px-8 my-auto selected-order-index md:w-1/4 sm:w-full h-fit">
            <div class="modal-box w-full">
                <h3 class="font-bold text-lg">Warning!</h3>
                <p class="py-1">This action cannot be undone.</p>
                <div class="modal-action">
                    <form method="POST" class="flex flex-col sm:flex-row w-full"
                          action="{{ route('buyer.delivery.code.confirm', ['delivery_code' => request('delivery_code')]) }}">
                        @csrf
                        <!-- if there is a button in form, it will close the modal -->
                        <button type="submit"
                                class="bg-primary-600 text-white rounded-lg flex justify-center items-center h-[44px] sm:w-2/5 cursor-pointer my-2 mx-auto w-full">
                            Confirm
                        </button>
                        <button type="button" onclick="confirm_model.close()"
                                class="btn bg-gray-200 text-sm font-semibold text-gray-900 rounded-lg flex justify-center items-center h-[44px] sm:w-2/5 cursor-pointer my-2 mx-auto w-full">
                            Close
                        </button>
                    </form>
                </div>
            </div>
        </dialog>--}}

        {{-- Report an issue modal --}}
        <dialog id="report_issue"
                class="modal order-index bg-white py-6 rounded-lg shadow-md px-8 my-auto md:w-1/4 sm:w-full h-fit">
            <div class="modal-box w-full relative">
                <h3 class="text-center font-bold text-lg dark-red">Report an issue</h3>
                <a class="cursor-pointer p-1 rounded-lg hover:bg-gray-100 absolute top-0 right-0"
                   onclick="report_issue.close()">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                         stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                    </svg>
                </a>
                <p class="py-1 text-center mt-2">ORDER NUMBER #{{ $order->id }}</p>
                <div class="modal-action">
                    <form method="POST" class="grid grid-cols-2 gap-2.5 sm:flex-row w-full"
                          action="{{ route('buyer.delivery.issue') }}">
                        @csrf
                        <div class="col-span-2 form-control">
                            <label for="county_id" class="form-label">What is the issue?</label>
                            <x-shop::checkout.utilities.select id="issue" name="issue" class="px-3.5 py-2.5">
                                <option value="damaged" selected>Package is damaged</option>
                                <option value="lost">Package is lost/missing</option>
                            </x-shop::checkout.utilities.select>
                            @error('issue')
                            <span class="text-error-800 text-sm">{{$message}}</span>
                            @enderror
                        </div>
                        <div class="col-span-2 form-control rounded-lg p-2 issue-alert">
                            If the package is damaged, you must return the product back to the supplier and will be
                            re-delivered to the user.
                        </div>
                        <div class="col-span-2 form-control">
                            <input type="hidden" name="order_id" value="{{ $order->id }}"/>
                        </div>
                        <!-- if there is a button in form, it will close the modal -->
                        <button type="submit"
                                class="col-span-2 bg-primary-600 text-white rounded-lg flex justify-center items-center h-[44px] cursor-pointer my-2 w-full">
                            Submit
                        </button>
                    </form>
                </div>
            </div>
        </dialog>
        {{-- End Report an issue modal --}}

    </x-shop::core.container>

@endsection
@push('script')
    <script src="{{asset('/shop/js/intl-tel-input/intlTelInput.js')}}"></script>
    <script src="{{asset('/shop/js/intl-tel-input/main.js')}}"></script>
    <script>
        initializePhone("#mobile_input", "#mobile");


        $('#paycash_method').click(function() {
            $('#mpesa_wrapper').addClass('hidden');
            $('#paycash_wrapper').removeClass('hidden');
        });

        $('#mpesa_method').click(function() {
            $('#paycash_wrapper').addClass('hidden');
            $('#mpesa_wrapper').removeClass('hidden');
        });
    </script>
@endpush
