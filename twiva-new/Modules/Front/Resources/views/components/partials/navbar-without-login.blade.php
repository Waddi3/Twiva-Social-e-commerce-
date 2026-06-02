<div x-data {{ $attributes->class('fixed top-0 left-0 right-0 z-50 bg-gray-25 flex flex-row justify-between items-center shadow-navbar')->only('class')  }}>
    <x-shop::partials.logo/>
    <div class="flex justify-between space-x-[30px]">
        @auth('buyer')
            <button class="relative " @click="$dispatch('drawers-user-toggle')">
                <i data-feather="user" class="w-[30px] h-[30px]"></i>
            </button>
        @endauth

    </div>
</div>
