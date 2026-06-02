@extends('shop::auth.app')
@push('style')
    <link rel="stylesheet" href="{{asset('shop/css/intl-tel-input/intlTelInput.min.css')}}">
    <style>
        .iti__flag {
            background-image: url("/shop/images/flags.png");
        }
    </style>
@endpush
@section('title','Register')
@section('content')
    <section>
        <x-shop::form.button type="a" shape="tag" tag="a"
                             href="{{route('shop.product.index',['account' =>request('account')])}}"
                             class="flex sm:justify-center sm:items-center sm:mx-auto lg:mx-0 p-2.5 text-white lg:justify-start">
            <i class="w-5 h-5 text-white" data-feather="arrow-left"></i>
            <span class="ml-4">Back to shop</span>
        </x-shop::form.button>
    </section>
    <section class="form-container p-4">
        <div class="text-center items-center">
            <img width="154.44px" height="50px" class="mx-auto" src="{{asset('shop/images/logos/Logo.png')}}"
                 alt="">
            <p class="font-bold mt-3 ">Create an account</p>
        </div>
        <form action="{{route('buyer.register.action',['account' => request('account')])}}" method="POST">
            @csrf

            <div class="my-3">
                <label class="text-gray-500 text-sm " for="name">Full Name</label>
                <x-shop::utilities.input name="name" id="name" value="{{old('name') ?? null}}"/>
                @error('name')
                <span class="text-error-800 text-sm ">{{$message}}</span>
                @enderror
            </div>
            <div class="my-3">
                <label class="text-gray-500 text-sm " for="email">Email Address (Optional)</label>
                <x-shop::utilities.input name="email" id="email" value="{{old('email') ?? null}}"/>
                @error('email')
                <span class="text-error-800 text-sm ">{{$message}}</span>
                @enderror
            </div>
            <div class="my-3">
                <label class="text-gray-500 text-sm " for="mobile_input">Phone Number</label>
                <x-shop::utilities.input name="mobile" placeholder="" id="mobile_input"
                                         value="{{old('mobile') ?? null}}"/>
                <input id="mobile" type="hidden" name="mobile" value=""/>
                @error('mobile')
                <span class="text-error-800 text-sm ">{{$message}}</span>
                @enderror
                @error('phone_number')
                <span class="text-error-800 text-sm ">{{$message}}</span>
                @enderror
                <span id="mobile_input_error" class="text-error-800 text-sm"></span>
            </div>
            <div class="my-3">
                <label class="text-gray-500 text-sm " for="password">Password</label>
                <x-shop::utilities.input type="password" name="password" id="password"/>
                @error('password')
                <span class="text-error-800 text-sm ">{{$message}}</span>
                @enderror
            </div>
            <div class="my-3">
                <label class="text-gray-500 text-sm " for="password_confirmation">Password Confirmation</label>
                <x-shop::utilities.input type="password" name="password_confirmation" id="password_confirmation"/>
            </div>

            <x-shop::form.button type="submit" tag="button" shape="fill" class="w-full">
                Create an account
            </x-shop::form.button>
        </form>
        <div class="mt-5 text-center items-center">
            <p class="text-gray-800 font-bold">Already have account?
                <a class="font-bold text-primary-700" href="{{route('buyer.login',['account' => request('account')])}}">
                    login.
                </a>
            </p>
        </div>
    </section>
@endsection
@push('script')
    <script>
        initializePhone("#mobile_input", "#mobile");
        // initializePhone("#rep_phone_number", "#rep_mobile", "#rep_phone_number_error");
    </script>
@endpush
