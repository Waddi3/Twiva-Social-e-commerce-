@extends('buyer::app')

@section('title', 'Order has been completed')

@section('content')
    <div class="w-full flex flex-col items-center justify-start pt-[50px] lg:pt-[107px] mb-5">
        <div>
            <div>
                <div class="flex flex-col items-center justify-start gap-5 max-w-[560px] mb-6 lg:mb-12">
                    <span
                        class="bg-success-500 text-white w-[30px] h-[30px] flex justify-center items-center rounded-full">
                        <i data-feather="check-circle" class="w-5 h-5"></i>
                    </span>
                    <h6 class="text-gray-850 text-2xl text-center font-semibold">
                        Your order already has been purchased.
                    </h6>
                    <span>
                        Your receipt has been sent to you via email.
                    </span>
                    <x-shop::checkout.utilities.button color="primary-700" class="px-[50px]"
                                                       tag="a"
                                                       href="{{ $link }}">
                        Back to shopping
                    </x-shop::checkout.utilities.button>
                </div>
            </div>
        </div>
    </div>
@endsection
