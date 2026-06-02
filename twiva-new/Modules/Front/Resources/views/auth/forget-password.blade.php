@extends('shop::auth.app')

@section('title','Forgot Password')
@section('content')
    <section>
        <x-shop::form.button
            type="a" shape="tag" tag="a"
            href="{{route('ecommerce.index')}}"
            class="flex sm:justify-center sm:items-center sm:mx-auto lg:mx-0 p-2.5 text-white lg:justify-start">
            <i class="w-5 h-5 text-white" data-feather="arrow-left"></i>
            <span class="ml-4">Back to shop</span>
        </x-shop::form.button>
    </section>
    <section
        class="form-container mt-36 p-4">
        <div class="text-center items-center">
            <img width="154.44px" height="50px" class="mx-auto" src="{{asset('shop/images/logos/Logo.png')}}"
                 alt="">
            <p class="font-bold mt-3 ">Forgot Password</p>
            <p class="font-normal mt-3 ">Please enter your registered email address. We will send you a link to reset
                your
                password.</p>
        </div>

        <form action="{{route('ecommerce.forget_password.action')}}" method="POST">
            @csrf
            <div class="my-3">
                <label class="text-gray-500 text-sm " for="email">Email Address or Phone Number</label>
                <x-shop::utilities.input name="email" id="email"/>
                @error('email')
                <span class="text-error-800 text-sm">{{$message}}</span>
                @enderror

            </div>
            @if(\Illuminate\Support\Facades\Session::has('message'))
                <div class="my-3 text-center items-center">
                    <p class="text-success-800">{{\Illuminate\Support\Facades\Session::get('message') ?? null}}</p>
                </div>
            @endif
            <x-shop::form.button type="submit" tag="button" shape="fill" class="w-full">
                Send Reset Link
            </x-shop::form.button>
        </form>
        <div class="mt-3 text-center items-center">
            <a class="text-primary-700 font-bold"
               href="{{route('ecommerce.login')}}">Back to login</a>
        </div>
    </section>

@endsection
