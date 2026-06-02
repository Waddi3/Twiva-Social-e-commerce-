@extends('buyer::app')

@section('title', 'Checkout Shipping')

@section('content')

    <x-partials.checkout-container current="account" :productsData="$productsData">
        <x-shop::checkout.partials.checkout-grid :products-data="$productsData" quantity="{{$productsData['quantity']}}"
                                                 total="{{$productsData['total']}}">
            <form method="POST" action="{{route('buyer.verify.action',['account' => request('account')])}}">
                @csrf
                <div class="w-full border-b-2 border-solid border-gray-200 pb-10 lg:pb-[45px]">
                    <h4 class="text-primary-900 text-2xl font-semibold mb-2.5">
                        Welcome back! Please login below to continue your order.
                    </h4>
                    <p class="text-gray-800 text-lg font-semibold mb-2.5">
                        Singning in as
                        <span class="text-primary-900 text-2xl font-semibold mb-2.5">{{ $buyer->name }}</span>.
                    </p>
                    <p class="text-gray-800 text-md font-semibold mb-2.5">
                        We have sent you a <b>one-time (OTP) code</b> to you via SMS (<b>endgin in</b>
                        <span
                            class="text-primary-900 text-md font-semibold mb-2.5">{{ \Illuminate\Support\Str::mask($buyer->mobile, '*', 0, \Illuminate\Support\Str::length($buyer->mobile) - 4) }}</span>).
                        Please add the code below to continue registering your account.
                    </p>
                    <div class="flex flex-col gap-1.5 mb-2.5">
                        <label for="code" class="text-gray-700 text-sm font-normal">Type down the OTP code here:</label>
                        <x-shop::checkout.utilities.input id="code" name="code" value="{{old('code') ?? ''}}"/>
                        @error('code')
                        <p class="text-error-800 text-sm">{{$message}}</p>
                        @enderror
                    </div>
                    <div class="flex space-x-2">
                        <x-shop::checkout.utilities.button type="submit" color="primary-700" class="!px-[83px]">
                            Authenticate
                        </x-shop::checkout.utilities.button>
                        {{--                        <x-shop::checkout.utilities.button tag="a"--}}
                        {{--                                                           href="{{route('buyer.register',['account' => request('account')])}}"--}}
                        {{--                                                           color="primary-50" class="!px-[83px]">Register--}}
                        {{--                        </x-shop::checkout.utilities.button>--}}

                        <div id="timer"
                             class="flex justify-center items-center disabled:opacity-30 bg-primary-50 text-primary-700 text-sm font-normal rounded-lg p-2.5 !px-[83px] cursor-pointer">
                            01:00
                        </div>
                        <div
                            class="flex justify-center items-center disabled:opacity-30 bg-primary-50 text-primary-700 text-sm font-normal rounded-lg p-2.5 !px-[83px] cursor-pointer"
                            id="restart"
                            style="display:none;">
                    <span>
                        Resend Code
                    </span>
                        </div>

                    </div>
                </div>
            </form>

        </x-shop::checkout.partials.checkout-grid>

        <x-slot:navPrev :link="route('shop.product.index',['account'=>request('account')])">
            <i data-feather="arrow-left"></i>
            <span>Back to Shop</span>
        </x-slot:navPrev>
        <x-slot:navNext type="submit">
            Proceed to payment
        </x-slot:navNext>
    </x-partials.checkout-container>
@endsection

@push('script')
    <script>
        $(document).ready(function () {
            var timeLeft = 60; // 60 seconds

            function updateTimer() {
                var minutes = Math.floor(timeLeft / 60);
                var seconds = timeLeft % 60;
                $('#timer').text((minutes < 10 ? '0' : '') + minutes + ':' + (seconds < 10 ? '0' : '') + seconds);
            }

            function countdown() {
                updateTimer();
                if (timeLeft == 0) {
                    clearInterval(timerInterval);
                    $('#restart').show();
                    $('#timer').hide();
                    return;
                }
                timeLeft--;
            }

            var timerInterval = setInterval(countdown, 1000);

            $('#restart').click(function () {
                $.ajax({
                    url: `{{ route('buyer.resend.verify.code',['account' => request('account')]) }}`,
                    success: function (data) {
                        timeLeft = 60;
                        $('#restart').hide();
                        $('#timer').show();
                        timerInterval = setInterval(countdown, 1000);
                    }
                })
            });
        });
    </script>
@endpush
