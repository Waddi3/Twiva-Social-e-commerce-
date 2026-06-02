@extends('front::checkout.layouts.app')

@section('title', 'Checkout Account')

@section('content')
    <x-partials.checkout-container current="account" :productsData="$productsData['all_products']" :partners="$partners">
        <x-front::checkout.partials.checkout-grid
            :products-data="$productsData"
            quantity="{{$productsData['all_products']['quantity']}}"
            total="{{$productsData['all_products']['total']}}"
            :partners="$partners"
            :has-shipping-fees="$hasShippingFees"
        >
            <div class="w-full pb-[52px] lg:pb-[74px]">
                <form method="POST" action="{{route('ecommerce.login')}}">
                    @csrf
                    <div class="w-full border-b-2 border-solid border-gray-200 pb-10 lg:pb-[45px]">
                        @if(session('invoice'))
                            <h4 class="text-primary-900 text-2xl font-semibold mb-2.5">
                                Welcome to TWIVA, to continue, please create an account below:
                            </h4>
                        @else
                            <h4 class="text-primary-900 text-2xl font-semibold mb-2.5">
                                Login as an existing customer
                            </h4>
                        @endif
                        <p class="text-gray-800 text-lg font-semibold mb-2.5">Are you an existing customer? <a href="#">Login</a>.
                        </p>
                        <div class="flex flex-col gap-1.5 mb-2.5">
                            <label for="email" class="text-gray-700 text-sm font-normal">Email Address</label>
                            <x-shop::checkout.utilities.input id="email" name="email" value="{{old('email') ?? ''}}"/>
                            @error('email')
                            <p class="text-error-800 text-sm">{{$message}}</p>
                            @enderror
                        </div>
                        <div class="flex flex-col gap-1.5 mb-2.5">
                            <label for="email" class="text-gray-700 text-sm font-normal">Password</label>
                            <x-shop::checkout.utilities.input type="password" id="password" name="password"/>
                            @error('password')
                            <p class="text-error-800 text-sm">{{$message}}</p>
                            @enderror
                        </div>
                        <div class="flex space-x-2">
                            <x-shop::checkout.utilities.button type="submit" color="primary-700" class="!px-[83px]">
                                Login
                            </x-shop::checkout.utilities.button>
                            <x-shop::checkout.utilities.button tag="a"
                                                               href="{{route('e-commerce.buyer.register',['account' => request('account')])}}"
                                                               color="primary-50" class="!px-[83px]">Register
                            </x-shop::checkout.utilities.button>
                        </div>
                    </div>
                </form>
                <div class="pt-10 lg:pt-[28px]">
                    <p class="text-gray-800 text-lg font-semibold mb-5">Checkout as a guest</p>
                    <x-shop::checkout.utilities.button color="primary-50" class="!inline-flex !px-10"
                                                       :link="route('e-commerce.buyer.checkout.shipping')">
                        Continue as a guest
                    </x-shop::checkout.utilities.button>
                </div>
            </div>
            <x-slot:formPrev :link="route('ecommerce.index')">
                <i data-feather="arrow-left"></i>
                <span>Back to Shop</span>
            </x-slot:formPrev>
            <x-slot:formNextMobile :link="route('e-commerce.buyer.checkout.shipping')">
                Proceed to shipping
            </x-slot:formNextMobile>
        </x-front::checkout.partials.checkout-grid>
        <x-slot:navPrev :link="route('ecommerce.index')">
            <i data-feather="arrow-left"></i>
            <span>Back to Shop</span>
        </x-slot:navPrev>
        <x-slot:navNext :link="route('e-commerce.buyer.checkout.shipping')">
            Proceed to shipping
        </x-slot:navNext>
    </x-partials.checkout-container>
@endsection
@push('script')
    <script>
        $('.checkout_product_size:first, .checkout_product_color:first').trigger("change");
    </script>
@endpush
@if($is_preview?? false)
    @push('script')
        <script>
            $(document).ready(function () {
                $("[type='submit']").on('click', function (event) {
                    event.preventDefault();
                })
                $("button").on('click', function (event) {
                    event.preventDefault();
                })
                $("a").on('click', function (event) {
                    event.preventDefault();
                });

            })
        </script>
    @endpush
@endif
