@props([
'position' => 'left',
])

@php
    // Settings
    $class = $position === 'left' ? 'left-0' : 'right-0';
    $class .= ' top-[60px]';
    $translate = $position === 'left' ? 'translate-x-full-inverse' : 'translate-x-full';
@endphp
<div x-data="drawer()" :id="id" class="hidden" @drawers-menu-toggle.window="toggle">
    <div class="fixed inset-0 bg-black-100 opacity-20 z-40 backdrop-blur-lg" :class="{ 'hidden': !show }"
         @click="toggle"></div>
    <div
        {{ $attributes->class('fixed w-full md:w-1/2 lg:w-1/3 bottom-0 z-50 transition-transform')->class($class)->only('class') }}
        :class="{ '{{ $translate }}': !show }"
    >
        <div class="bg-gray-25 w-full h-full p-8 flex flex-col justify-between">
            <ul class="flex flex-col gap-5 text-xl">
                <li>
                    <a href="{{ route('ecommerce.index') }}" {{ Route::is('ecommerce.index') ? 'active-nav' : '' }}>Home</a>
                </li>
                <li>
                    <a href="{{ route('home.social-commerce') }}" {{ Route::is('home.social-commerce') ? 'active-nav' : '' }}>Social
                        Commerce</a>
                </li>
                <li>
                    <a href="{{ route('home.influencer-marketing') }}" {{ Route::is('home.influencer-marketing') ? 'active-nav' : '' }}>Influencer
                        Marketing</a>
                </li>
                <li>
                    <a href="{{ route('home.services') }}" {{ Route::is('home.services') ? 'active-nav' : '' }}>Our Services</a>
                </li>
                <li>
                    <a href="https://blog.twiva.co.ke/training">Training</a>

                </li>
            </ul>
            <div class="flex justify-center items-center gap-3">
                <x-front::form.button tag="a" href="{{config('twiva.bases.app_url')}}" shape="fill">
                    Log in
                </x-front::form.button>
                <x-front::form.button tag="a" href="{{config('twiva.bases.app_url')}}" shape="fill">
                    Start Earning
                </x-front::form.button>
            </div>
        </div>
    </div>
</div>
