@extends('shop::layouts.app')

@section('title','Settings')

@section('content')
    <x-shop::core.container>
        <a class="mb-24" href="{{route('shop.product.index',['account' => request('account')])}}">
            <div class="border-gray-200 sm-down:border md:mt-2.5 md:mb-7 sm-down:bg-white">
                <button class="flex justify-center items-center p-2.5 text-gray-500">
                    <i class="w-5 h-5 text-gray-900" data-feather="arrow-left"></i>
                    <span class="ml-4">Back to Shop</span>
                </button>
            </div>
        </a>

        <div class="mt-5">
            <h1 class="h1 font-bold text-2xl">Account Settings</h1>
            <section class="xs-down:block sm:flex xs-down:space-x-0  sm:space-x-4 mt-5">
                <a href="{{route('buyer.account.index',['account' => request('account')])}}"
                   class="border p-4  flex justify-between md:mr-2">
                    <span class="ml-4 font-semibold">Account Setting</span>
                    <i class="w-5 h-5 font-bold text-gray-900 ml-24" data-feather="chevron-right"></i>
                </a>
                <a href="{{route('buyer.address.index',['account' => request('account')])}}"
                   class="border p-4  flex justify-between md:mr-2">
                    <span class="ml-4 font-semibold">Manage Address</span>
                    <i class="w-5 h-5 font-bold text-gray-900 ml-24" data-feather="chevron-right"></i>
                </a>
                <a href="{{route('buyer.password-change',['account' => request('account')])}}"
                   class="border p-4 flex justify-between md:mr-2 xs-down:mt-3 md:mt-0">
                    <span class="ml-4 font-semibold">Change Password</span>
                    <i class="w-5 h-5 font-bold text-gray-900 ml-24" data-feather="chevron-right"></i>
                </a>

            </section>
        </div>

    </x-shop::core.container>

@endsection
