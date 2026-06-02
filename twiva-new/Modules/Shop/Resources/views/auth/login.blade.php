@extends('shop::auth.app')

@section('title','Login')
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
            <p class="font-bold mt-3 ">Existing Customer? Login.</p>
        </div>

        <form action="{{route('buyer.login.action',['account' => request('account')])}}" method="POST">
            @csrf
            <div class="my-3">
                <label class="text-gray-500 text-sm " for="email">Email or Phone Number</label>
                <x-shop::utilities.input name="email" id="email" placeholder="inof@twiva.com or 712123456"/>
                @error('email')
                <span class="text-error-800 text-sm">{{$message}}</span>
                @enderror
                @error('mobile')
                <span class="text-error-800 text-sm">{{$message}}</span>
                @enderror
            </div>
            <div class="my-3">
                <label class="text-gray-500 text-sm " for="password">Password</label>
                <x-shop::utilities.input type="password" name="password" id="password"/>
                @error('password')
                <span class="text-error-800 text-sm">{{$message}}</span>
                @enderror
            </div>
            <x-shop::form.button type="submit" tag="button" shape="fill" class="w-full">
                login
            </x-shop::form.button>
        </form>
        <div class="mt-3 text-center items-center">
            <a class="text-primary-700 font-bold"
               href="{{route('buyer.forget_password',['account' => request('account')])}}">Forgot password</a>
        </div>
        <div class="mt-5 text-center items-center">
            <p class="text-gray-800 font-bold">Don't have account?
                <a class="font-bold text-primary-700"
                   href="{{route('buyer.register',['account' => request('account')])}}"> Register now.</a>
            </p>
        </div>
    </section>

@endsection
