<nav
    x-data="navbar()"
    {{ $attributes->class('h-[60px] bg-gray-25 xl:h-20 px-3 xl:px-20 py-3.5 flex justify-between xl:shadow-navbar z-[100]') }}
    :class="{ 'fixed': fixed, 'top-0': fixed, 'right-0': fixed, 'left-0': fixed }"
    @drawers-menu-toggled.window="fix"
>
    <div class="h-full flex justify-start items-center">
        <x-front::form.button class="block xl:hidden border-primary-900" shape="icon"
                              @click="$dispatch('drawers-menu-toggle')">
            <i class="w-4 h-4 text-primary-900" data-feather="menu"></i>
        </x-front::form.button>
        @if(!Route::is('ecommerce.index'))
            <a href="{{ route('ecommerce.index') }}"><img class="w-[100px] xl:w-[155px] ml-[30px] xl:ml-0"
                                                    src="{{ asset('front/images/logos/Logo.png') }}" alt="twiva-logo"/></a>
        @else
            <img class="w-[100px] xl:w-[155px] ml-[30px] xl:ml-0" src="{{ asset('front/images/logos/Logo.png') }}"
                 alt="twiva-logo"/>
        @endif
        <div class="hidden xl:flex justify-start gap-8 ml-10 text-gray-500 font-semibold text-lg">
            <a href="{{ route('ecommerce.index') }}" {{ Route::is('ecommerce.index') ? 'active-nav' : '' }}>Home</a>
            <a href="{{ route('home.social-commerce') }}" {{ Route::is('home.social-commerce') ? 'active-nav' : '' }}>Social
                Commerce</a>
            <a href="{{ route('home.influencer-marketing') }}" {{ Route::is('home.influencer-marketing') ? 'active-nav' : '' }}>Influencer
                Marketing</a>
            <a href="{{ route('home.services') }}" {{ Route::is('home.services') ? 'active-nav' : '' }}>Our Services</a>
            <a href="https://blog.twiva.co.ke/training">Training</a>

        </div>
    </div>
    <div class="h-full hidden xl:flex justify-end items-center gap-3">
        <x-front::form.button tag="a" href="{{route('ecommerce.login')}}">
            Log in
        </x-front::form.button>
        <x-front::form.button tag="a" href="{{config('twiva.bases.app_url')}}" shape="fill">
            Start Earning
        </x-front::form.button>
    </div>
</nav>
