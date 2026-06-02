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
        <div class="justify-content-center text-center mx-4 my-16 p-4 md:p-8 rounded-lg bg-white">
            <svg width="30" height="26" viewBox="0 0 30 26" fill="none" xmlns="http://www.w3.org/2000/svg"
                 class="inline">
                <path fill-rule="evenodd" clip-rule="evenodd"
                      d="M0 1.75C0 1.05964 0.559644 0.5 1.25 0.5H20C20.6904 0.5 21.25 1.05964 21.25 1.75V6.75H25C25.3315 6.75 25.6495 6.8817 25.8839 7.11612L29.6339 10.8661C29.8683 11.1005 30 11.4185 30 11.75V18C30 18.6904 29.4404 19.25 28.75 19.25H27.079C27.3489 19.8183 27.5 20.454 27.5 21.125C27.5 23.5412 25.5412 25.5 23.125 25.5C20.7088 25.5 18.75 23.5412 18.75 21.125C18.75 20.454 18.9011 19.8183 19.171 19.25H10.829C11.0989 19.8183 11.25 20.454 11.25 21.125C11.25 23.5412 9.29125 25.5 6.875 25.5C4.45875 25.5 2.5 23.5412 2.5 21.125C2.5 20.454 2.65106 19.8183 2.92102 19.25H1.25C0.559644 19.25 0 18.6904 0 18V1.75ZM18.75 16.75V3H2.5V16.75H18.75ZM21.25 16.75V9.25H24.4822L27.5 12.2678V16.75H21.25ZM6.875 19.25C5.83947 19.25 5 20.0895 5 21.125C5 22.1605 5.83947 23 6.875 23C7.91053 23 8.75 22.1605 8.75 21.125C8.75 20.0895 7.91053 19.25 6.875 19.25ZM23.125 19.25C22.0895 19.25 21.25 20.0895 21.25 21.125C21.25 22.1605 22.0895 23 23.125 23C24.1605 23 25 22.1605 25 21.125C25 20.0895 24.1605 19.25 23.125 19.25Z"
                      fill="#7A0D41"/>
            </svg>
            <h1 class="h1 font-semibold text-2xl text-center mt-4">Confirm Delivery</h1>

            <div class="w-4/5 lg:w-1/2 mx-auto md:pb-[52px] lg:pb-[74px] justify-content-center">
                <form method="get" class=" flex flex-col md:flex-row  w-full"
                      action="{{ route('buyer.delivery.code.insert') }}">
                    @csrf
                    <div class="flex flex-col gap-1.5 mb-2.5 w-full justify-items-center text-center items-center">
                        <div class="w-full pb-2 lg:pb-[10px]">
                            <p class="text-gray-900 text-lg mb-2.5 text-center mt-3">
                                Please add the <strong>5-digit</strong> code of your customer to view and confirm their
                                order details:
                            </p>
                        </div>
                        <div class="w-full text-left">
                            <label for="delivery_code" class="text-gray-700 text-sm font-normal text-left">Enter code here</label>
                        </div>
                        <div class="grid grid-cols-12 gap-2 w-full">
                            <x-shop::checkout.utilities.input id="delivery_code" name="delivery_code"
                                                              value="{{ request('delivery_code') ?? null }}"
                                                              required
                                                              class="col-span-12 md:col-span-10 justify-items-center text-center items-center"
                            />

                            <button type="submit"
                                    class="bg-primary-600 text-white rounded-lg flex justify-center items-center h-[44px]
                                    cursor-pointer col-span-12 md:col-span-2">
                                Submit
                            </button>
                        </div>
                        @if(isset($delivery_message))
                            <div class="w-full text-left">
                                <p class="text-error-800 text-sm">{{ $delivery_message }}</p>
                            </div>
                        @endif
                    </div>
                </form>
                @if(isset($delivery_code) || isset($error) || isset($message) )
                <form
                    class="grid grid-cols-12 gap-2 w-full rounded-lg bg-gray-100 justify-items-center p-3 text-center items-end mt-2"
                    method="POST"
                    action="{{ route('buyer.delivery.code.confirm', ['delivery_code' => request('delivery_code') ?? null]) }}"
                >
                    @csrf
                    <div class="flex flex-col col-span-12 md:col-span-8 w-full text-left">
                        <label class="text-gray-500 text-sm" for="mobile_input">Customer's Phone Number</label>
                        <x-shop::utilities.input name="mobile" placeholder="" id="mobile_input"
                                                 value="{{old('mobile') ?? null}}"/>
                        <input id="mobile" type="hidden" name="mobile" value=""/>
                        @error('mobile')
                        <span class="text-error-800 text-sm">{{$message}}</span>
                        @enderror
                        @error('phone_number')
                        <span class="text-error-800 text-sm">{{$message}}</span>
                        @enderror
                        <span id="mobile_input_error" class="text-error-800 text-sm"></span>
                    </div>
                    <div class="flex col-span-12 md:col-span-4 w-full">
                        <button type="submit"
                                class="secondary-btn w-full font-semibold text-sm rounded-lg flex justify-center items-center h-[40px]
                                    cursor-pointer">
                            Re-send delivery code
                        </button>
                    </div>
                    <input type="hidden" name="resend" value="1" />
                    @if( isset($error) )
                        <div class="col-span-12 w-full text-left">
                            <p class="text-error-800 text-sm">{{ $error }}</p>
                        </div>
                    @endif
                    @if( isset($message) )
                        <div class="col-span-12 w-full text-left">
                            <p class="text-green-500 text-sm">{{ $message }}</p>
                        </div>
                    @endif
                </form>
                @endif
            </div>
        </div>
    </x-shop::core.container>

@endsection
@push('script')
    <script src="{{asset('/shop/js/intl-tel-input/intlTelInput.js')}}"></script>
    <script src="{{asset('/shop/js/intl-tel-input/main.js')}}"></script>
    <script>
        initializePhone("#mobile_input", "#mobile");
    </script>
@endpush
