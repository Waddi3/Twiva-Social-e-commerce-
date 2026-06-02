@extends('shop::auth.app')

@section('title','Verify phone number')
@section('content')
    <section>
        <x-shop::form.button
            type="a" shape="tag" tag="a"
            href="{{route('shop.product.index',['account' =>request('account')])}}"
            class="flex sm:justify-center sm:items-center sm:mx-auto lg:mx-0 p-2.5 text-white lg:justify-start">
            <i class="w-5 h-5 text-white" data-feather="arrow-left"></i>
            <span class="ml-4">Back to shop</span>
        </x-shop::form.button>
    </section>
    <section
        class="form-container p-4">
        <div class="text-center items-center">
            <img width="154.44px" height="50px" class="mx-auto" src="{{asset('shop/images/logos/Logo.png')}}"
                 alt="">
            <p class="font-bold mt-3 ">Verify your number using sent OTP code:</p>
            @if( !$submit )
                <p class="font-bold mt-3 ">Too many attempts, please try again in {{ $remainTime }} minutes</p>
            @endif

        </div>

        <form action="{{route('buyer.verify.action',['account' => request('account')])}}" method="POST">
            @csrf
            @if( $submit )
                <div class="my-3">
                    <label class="text-gray-500 text-sm " for="email">OTP code</label>
                    <x-shop::utilities.input name="code" id="code" placeholder="123456"/>
                    @error('code')
                    <span class="text-error-800 text-sm">{{$message}}</span>
                    @enderror
                </div>

                <x-shop::form.button type="submit" tag="button" shape="fill" class="w-full">
                    Verify
                </x-shop::form.button>
                <div id="timer"
                     class="py-2.5 px-[18px] bg-primary-600 text-white rounded-lg w-full text-center mt-2 cursor-pointer">
                    01:00
                </div>
                <div
                    class="py-2.5 px-[18px] bg-primary-600 text-white rounded-lg w-full text-center mt-2 cursor-pointer"
                    id="restart"
                    style="display:none;">
                    <span>
                        Resend Code
                    </span>
                </div>
            @endif
        </form>
    </section>

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
