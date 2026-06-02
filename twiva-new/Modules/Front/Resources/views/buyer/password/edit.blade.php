@extends('front::e-commerce.layouts.app', [ 'page_title' => 'Change Password' ])

@section('content')
    <x-shop::core.container >
        <a href="{{route('e-commerce.buyer.settings-page')}}">
            <div class="border-gray-200 sm-down:border md:mt-2.5 md:mb-7 sm-down:bg-white">
                <button class="flex justify-center items-center p-2.5 text-gray-700">
                    <i class="w-5 h-5 text-gray-700" data-feather="arrow-left"></i>
                    <span class="ml-4">Back to  Settings</span>
                </button>
            </div>
        </a>
        <form action="{{route('e-commerce.buyer.password-change.post')}}" method="POST" >
            <div class="flex justify-between">
                <h1 class="h1 font-bold text-2xl">Change password </h1>
                <x-shop::form.button type="submit" class="px-[24px] hidden md:block" shape="fill">Update
                </x-shop::form.button>
            </div>
            <section class="mt-5 w-3/4">
                <p class="font-semibold"> Please enter a new password and confirm it to reset.</p>
                <div class="my-3 w-75">
                    @csrf
                    <label class="text-gray-700 text-sm " for="current_password">Current Password</label>
                    <x-shop::utilities.input type="password" name="current_password" id="current_password"/>
                    @error('current_password')
                    <span class="text-error-800 text-sm">{{$message}}</span>
                    @enderror
                </div>
                <div class="my-3">
                    <label class="text-gray-700 text-sm " for="password">New Password</label>
                    <x-shop::utilities.input type="password" name="password" id="password"/>
                    @error('password')
                    <span class="text-error-800 text-sm">{{$message}}</span>
                    @enderror
                </div>
                <div class="my-3">
                    <label class="text-gray-700 text-sm " for="password_confirmation">Confirm New Password</label>
                    <x-shop::utilities.input type="password" name="password_confirmation" id="password_confirmation"/>
                    @error('password_confirmation')
                    <span class="text-error-800 text-sm">{{$message}}</span>
                    @enderror
                </div>
                <x-shop::form.button type="submit" class="px-[24px] w-full hidden md:hidden xs-down:block" shape="fill">
                    Update
                </x-shop::form.button>
            </section>
        </form>
    </x-shop::core.container>

@endsection
@push('scripts')
    <script type="text/javascript" src="{{ mix('front/js/app.js') }}"></script>
@endpush
