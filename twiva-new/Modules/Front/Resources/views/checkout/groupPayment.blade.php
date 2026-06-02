@extends('front::checkout.layouts.app')

@section('title', 'Group Payment')

@section('content')

    <x-partials.checkout-container current="payment"
                                   :productsData="$productsData['all_products']"
                                   :partners="collect()"
    >
        <x-front::checkout.partials.checkout-grid :products-data="$productsData"
                                                  quantity="{{ $productsData['all_products']['quantity'] }}"
                                                  total="{{ $productsData['all_products']['total'] }}"
                                                  :partners="collect()"
                                                  :has-shipping-fees="false"
                                                  :is-group-pay-page="true"
                                                  :request-data="$requestData"
        >
            <form  action="{{route('group-pay',['uuid'=>request('uuid')])}}" method="POST">
                @csrf
                <div class="w-full pb-5">
                    <h6 class="text-gray-800 text-lg font-semibold">How would you like to pay?</h6>
                    <div x-data="radioExpander()" x-init="sections = ['mpesa', 'flutterwave']">
                        <div x-data="lipalater()"
                             class="pt-5 pb-1 lg:pb-[60px] overflow-hidden transition-all ease-linear duration-150 max-h-[850px] lg:max-h-[620px]"
                             :class="{ '!max-h-0 !pt-0 !pb-0': active !== 'lipalater' }">

                            <div
                                    class="flex flex-col gap-5 text-gray-850 text-sm text-center lg:text-start font-normal mb-10">
                                <p><span class="font-bold">Register</span> a new account at LipaLater.</p>
                                <p>It will only take a few minutes!</p>
                            </div>
                            <div class="flex justify-center lg:justify-start items-center gap-[5px]">
                                <span class="text-gray-600 text-sm font-semibold">Powered by</span>
                                <img class="h-[15px]" src="{{ asset('shop/images/payments/Lipalater.svg') }}"
                                     alt="Lipalater">
                            </div>
                        </div>
                        <x-front::checkout.pay_selections.group_buying
                                is_group_paying="'on'"
                                :is_disabled="true"
                        />

                        <div class="py-[25px] border-b border-solid border-gray-200">
                            <label
                                    class="flex justify-between items-center text-gray-700 text-sm font-normal cursor-pointer">
                                <span class="flex justify-start items-center gap-2">
                                    <input type="radio" name="payment_type" value="1" class="accent-blue-600"
                                           @change="change('mpesa')">
                                    <span>MPesa</span>
                                </span>
                                <img src="{{ asset('shop/images/payments/MPesa.svg') }}" alt="MPesa">
                            </label>
                        </div>

                        <div x-data="mpesa()" id="mpesa"
                             class="pt-5 pb-1 lg:pb-[35px] flex flex-col gap-5 overflow-hidden transition-all ease-linear duration-150"
                             :class="{ '!max-h-0 !pt-0 !pb-0': active !== 'mpesa' }">
                            <div class=" ">
                                <label for="phone" class="form-label">Phone Number</label>
                                <x-shop::utilities.input name="mobile_input" id="mobile_input"
                                                         value="{{ old('phone_number') }}"/>
                                <input id="phone_number" name="phone_number" type="hidden"
                                       value="{{ old('phone_number') }}"/>
                                @error('phone_number')
                                <p class="text-error-800">{{ $message }}</p>
                                @enderror

                                <p class="text-error-800 text-sm mobile_input_error"></p>


                            </div>
                            <x-shop::checkout.utilities.button type="submit" class="px-[76px] w-max submit-form-btn"
                                                               color="primary-700">
                                Confirm
                            </x-shop::checkout.utilities.button>
                            <div>

                            </div>
                        </div>
                        <div class="py-[25px] border-b border-solid border-gray-200">
                            <label
                                    class="flex justify-between items-center text-gray-700 text-sm font-normal cursor-pointer">
                                <span class="flex justify-start items-center gap-2">
                                    <input type="radio" name="payment_type" value="2" id="payment-type-flutterwave"
                                           class="accent-blue-600" @change="change('flutterwave')">
                                    <span>Flutterwave</span>
                                </span>
                                <img src="{{ asset('shop/images/payments/Flutterwave.svg') }}" alt="Flutterwave">
                            </label>
                        </div>
                        <div id="flutterwave"
                             class="pt-5 pb-[30px] overflow-hidden transition-all ease-linear duration-150"
                             :class="{ '!max-h-0 !pt-0 !pb-0': active !== 'flutterwave' }">
                            <x-shop::checkout.utilities.button class="!inline-flex gap-2.5 mb-5 submit-form-btn"
                                                               color="primary-900">
                                <span>Use the secure checkout of Flutterwave</span>
                                <i data-feather="external-link"></i>
                            </x-shop::checkout.utilities.button>
                            <div class="flex justify-start items-center gap-[5px]">
                                <span class="text-gray-600 text-sm font-semibold">Powered by</span>
                                <img class="h-[30px]" src="{{ asset('shop/images/payments/Flutterwave.svg') }}"
                                     alt="Flutterwave">
                            </div>
                        </div>
{{--                        <x-front::checkout.pay_selections.spotit/>--}}
                    </div>
                </div>
                @error('amount_payment_group_buying')
                <p class="form_error_message text-sm text-error-800">{{ $message }}</p>
                @enderror
                <p class="form_error_message text-sm text-error-800"></p>
                <div class="flex justify-center">
                    <x-shop::checkout.utilities.button class="!inline-flex gap-2.5 mb-5 submit-form-bt"
                                                       type="submit"
                                                       color="primary-900">
                        <span>Confirm</span>
                    </x-shop::checkout.utilities.button>
                </div>
            </form>

        </x-front::checkout.partials.checkout-grid>
    </x-partials.checkout-container>
@endsection
@section('teleport')
    <x-front::views.auth-modal/>
@endsection
@push('script')
    <script>
        $("[x-data^='counter']").parent().remove()
        $('select').parent().parent().remove()
    </script>
    <script>
        let iti;
        let input = document.querySelector("#mobile_input"),
            output = document.querySelector("#phone_number");

        function initializePhone() {
            iti = window.intlTelInput(input, {
                nationalMode: true,
                utilsScript: "{{ asset('shop/js/intl-tel-input/utils.js') }}",
                initialCountry: "ke"
            });

            iti.promise.then(function () {
                handleChange();
            });

            let handleChange = function () {
                let phoneNumber = iti.telInput.value.replace('+' + iti.selectedCountryData.dialCode, '')
                let phoneCode = iti.selectedCountryData.dialCode
                if (phoneNumber[0] === '0') {
                    phoneNumber = phoneNumber.slice(1)
                }

                output.value = phoneCode + phoneNumber;


                console.log(output.value)
                let phone = iti.telInput.value;
                if (phone.length > 0) {
                    if (iti.isValidNumber()) {
                        $(".mobile_input_error").text(null);
                    } else {
                        $(".mobile_input_error").html("Please Enter a valid phone number");
                    }
                } else {
                    $(".mobile_input_error").text(null);
                }
            };
            // listen to "keyup", but also "change" to update when the user selects a country
            input.addEventListener('change', handleChange);
            input.addEventListener('keyup', handleChange);
        }

        initializePhone();
    </script>
    <script>
        @if (old('payment_type') > 0)
        console.log("{{ old('payment_type') }}")
        $("input[name='payment_type'][value=" + "{{ old('payment_type') }}" + "]").click()
        @else
        $("input[name='payment_type'][value='1']").click()
        @endif
    </script>
    <script>
        function handelTransactionFees() {
            if ($("input[name='payment_type']:checked").val() === '2') {
                console.log($("input[name='payment_type']:checked").val())
                $("#transaction_fees_div").show()
                $("#total_with_shipping_fees").hide()
                $("#total_with_shipping_fees_and_transaction_fees").show()
            } else {
                $("#transaction_fees_div").hide()
                $("#total_with_shipping_fees").show()
                $("#total_with_shipping_fees_and_transaction_fees").hide()
            }
        }

        $(document).ready(() => {
            handelTransactionFees()
            $("input[name='payment_type']").on("click", function () {
                handelTransactionFees()
            })

            $(".submit-form-btn").on('click', function (e) {
                e.preventDefault();
                if ($("input[name='payment_type']:checked").val() == null) {
                    $(".form_error_message").text("The payment type field is required.")
                    $(".mobile_input_error").text("");
                    return false;
                } else if ($("input[name='payment_type']:checked").val() === '1') {
                    if (iti.isValidNumber()) {
                        $("#form").submit()
                    } else {
                        $(".mobile_input_error").html("Please Enter a valid phone number");
                        return false;
                    }
                } else if ($("input[name='payment_type']:checked").val() === '2') {
                    $("#form").submit()
                } else if ($("input[name='payment_type']:checked").val() === '5' &&
                    "{{ auth('buyer')->check() }}") {
                    $("#form").submit()
                } else if ($("input[name='payment_type']:checked").val() === '5' &&
                    "{{ !auth('buyer')->check() }}") {
                    $("#auth-modal").show().css('display', 'grid')
                } else if ($("input[name='payment_type']:checked").val() === '6') {
                    $("#form").submit()
                } else if ($("input[name='payment_type']:checked").val() === '7') {
                    $("#form").submit()
                }
            })
        });
    </script>

@endpush
