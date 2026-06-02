<div x-data {{ $attributes->class('fixed top-0 left-0 right-0 z-50 bg-gray-25 flex flex-row justify-between items-center shadow-navbar')->only('class')  }}>
    <x-partials.logo />
    <button class="relative" @click="$dispatch('drawers-basket-toggle')">
        <i data-feather="shopping-cart" class="w-[30px] h-[30px]"></i>
        <span class="badge absolute top-[-8px] right-[-8px]">0</span>
    </button>
</div>
