<div x-data="dropdown()" x-init="selected = 'newest'; items = site_variables.dropdowns.products_sorting" {{ $attributes->class('relative')->only('class') }}>
    <button :id="id" class="flex justify-center items-center h-[44px] w-full" @click="toggle">
        <svg class="w-4 h-4 md:mr-4" viewBox="0 0 18 12" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" clip-rule="evenodd" d="M0.666656 0.999959C0.666656 0.539722 1.03975 0.166626 1.49999 0.166626H16.5C16.9602 0.166626 17.3333 0.539722 17.3333 0.999959C17.3333 1.4602 16.9602 1.83329 16.5 1.83329H1.49999C1.03975 1.83329 0.666656 1.4602 0.666656 0.999959ZM3.16666 5.99996C3.16666 5.53972 3.53975 5.16663 3.99999 5.16663H14C14.4602 5.16663 14.8333 5.53972 14.8333 5.99996C14.8333 6.4602 14.4602 6.83329 14 6.83329H3.99999C3.53975 6.83329 3.16666 6.4602 3.16666 5.99996ZM5.66666 11C5.66666 10.5397 6.03975 10.1666 6.49999 10.1666H11.5C11.9602 10.1666 12.3333 10.5397 12.3333 11C12.3333 11.4602 11.9602 11.8333 11.5 11.8333H6.49999C6.03975 11.8333 5.66666 11.4602 5.66666 11Z" fill="#101828"/>
        </svg>
        <span class="text-gray-500 hidden md:block" x-text="currentItem"></span>
    </button>
    <div x-show="show" class="absolute right-0 top[44px] min-w-[133px] bg-white rounded-md text-gray-700 font-normal text-sm z-10 shadow-dropdown">
        <ul class="!p-0">
            <template x-for="(item, index) in items" :key="index">
                <li class="px-4 py-2.5 select-none cursor-pointer hover:bg-gray-100" x-text="item" @click="changeItem(index)"></li>
            </template>
        </ul>
    </div>
</div>
