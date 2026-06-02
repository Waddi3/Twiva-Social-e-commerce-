@extends('shop::layouts.app-without-login')

@section('title','Report an issue')

@section('content')
    <x-shop::core.container>
        <section class="mt-5">
            <div class="grid xs-down:grid-cols-1 xs-down:gap-1 grid-cols-1 gap-4">
                <section>
                    <div class="order-index bg-white py-6 rounded-lg px-8 mt-3">
                        <div class="block mt-10 justify-content-center pb-4 mb-6">
                            <div class="text-center">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#039855"
                                     class="w-8 h-8 text-center inline">
                                    <path fill-rule="evenodd"
                                          d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm13.36-1.814a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z"
                                          clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <h1 class="h1 font-semibold text-2xl text-center mt-2">Report has been submitted.</h1>
                            <p class="mt-2 text-center">
                                The appropriate supplier and customers has been notified.
                            </p>
                            <a href="{{ url()->previous() }}"
                               class="text-black rounded-lg flex justify-center items-center h-[44px] cursor-pointer my-2 w-full
                               border border-solid border-gray-300 mt-4
                               ">
                                Back to delivery page
                            </a>
                        </div>
                    </div>
                </section>
            </div>
        </section>

    </x-shop::core.container>

@endsection
