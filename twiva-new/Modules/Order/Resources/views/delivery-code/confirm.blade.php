@extends('shop::layouts.app')

@section('title','Order Delivery')

@section('content')
    <x-shop::core.container>
        <a href="{{ route('shop.product.index',['account' => request('account')]) }}">
            <div class="border-gray-200 sm-down:border md:mt-2.5 md:mb-7 sm-down:bg-white">
                <button class="flex justify-center items-center p-2.5 text-gray-700">
                    <i class="w-5 h-5 text-gray-700" data-feather="arrow-left"></i>
                    <span class="ml-4">Back to Shop</span>
                </button>
            </div>
        </a>
        <div class="block mt-10 justify-content-center">
            <h1 class="h1 font-semibold text-2xl text-center">Confirm Delivery My Order</h1>
        </div>
        <div class="my-5">
            <form
                action="{{ route('buyer.delivery.code.confirm', ['account' => request('account'), 'delivery_code' => request('delivery_code')]) }}"
                method="POST">
                @csrf
                <div class="justify-items-center w-full text-center my-8 text-4xl">
                <span class="text-center text-error-800 ">
                    This action can not be undo
                </span>
                </div>
                <div class="justify-items-center w-full mx-auto flex justify-center items-center">
                    <button type="submit" id="proceedToCheckoutBtn"
                            class="bg-primary-600 text-white rounded-lg h-[44px] w-1/2 px-4">
                        Confirm Delivery
                    </button>
                </div>
            </form>
        </div>
    </x-shop::core.container>

@endsection
