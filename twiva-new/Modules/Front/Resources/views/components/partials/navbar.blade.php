<div x-data {{ $attributes->class('fixed top-0 left-0 right-0 z-50 bg-gray-25 flex flex-row justify-between items-center shadow-navbar')->only('class')  }}>
    <x-shop::partials.logo/>
    <div class="flex justify-between space-x-[30px]">
        <div class="xs-down:hidden md:block">
        </div>
        <button class="relative " @click="$dispatch('drawers-basket-toggle')">
            <i data-feather="shopping-cart" class="w-[30px] h-[30px]"></i>
            <span id="basketCount" class="basketCount badge absolute top-[-5px] right-[-5px]">0</span>
        </button>
        @auth('buyer')
            <button class="relative " @click="$dispatch('drawers-user-toggle')">
                <i data-feather="user" class="w-[30px] h-[30px]"></i>
            </button>
        @endauth
        @guest('buyer')
            <div class="flex space-x-2">
                <x-shop::form.button tag="a" shape="fill"
                                     class="w-full bg-primary-600 text-white text-center rounded-lg"
                                     href="{{route('buyer.login',['account' =>request('account')])}}">
                    Login
                </x-shop::form.button>
                <x-shop::form.button tag="a" shape="border"
                                     class="w-full bg-primary-50 text-primary-700 text-center rounded-lg whitespace-nowrap"
                                     href="{{route('buyer.register',['account' =>request('account')])}}">
                    New Customer
                </x-shop::form.button>
            </div>
        @endguest

    </div>
</div>
