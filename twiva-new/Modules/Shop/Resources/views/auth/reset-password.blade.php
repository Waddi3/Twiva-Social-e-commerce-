@extends('shop::auth.app')

@section('title','Reset Password')
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
            class="form-container mt-36">
        <div class="text-center items-center">
            <img width="154.44px" height="50px" class="mx-auto" src="{{asset('shop/images/logos/Logo.png')}}"
                 alt="">
            <p class="font-bold mt-3 ">Enter Password</p>
            <p class="font-normal mt-3 ">Enter a new password below.</p>
        </div>

        <form action="{{route('buyer.reset-password.action',['account' => request('account')])}}" method="POST">
            @csrf
            <input type="hidden" name="token" value="{{request('token')}}">
            <div class="my-3">
                <label class="text-gray-500 text-sm " for="password">New password</label>
                <x-shop::utilities.input type="password" name="password" id="password"/>
                @error('password')
                <span class="text-error-800 text-sm">{{$message}}</span>
                @enderror
            </div>
            <div class="my-3">
                <label class="text-gray-500 text-sm " for="password_confirmation">Confirm new password</label>
                <x-shop::utilities.input type="password" name="password_confirmation" id="password_confirmation"/>
                @error('password')
                <span class="text-error-800 text-sm">{{$message}}</span>
                @enderror
            </div>
            @if(\Illuminate\Support\Facades\Session::has('message'))
                <div class="my-3 text-center items-center">
                    <p class="text-success-800">{{\Illuminate\Support\Facades\Session::get('message') ?? null}}</p>
                </div>
            @endif
            <x-shop::form.button type="submit" tag="button" shape="fill" class="w-full">
                Confirm and login
            </x-shop::form.button>
        </form>

    </section>

@endsection