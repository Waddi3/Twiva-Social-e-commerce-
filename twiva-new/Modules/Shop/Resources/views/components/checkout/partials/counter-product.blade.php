<div x-data="counter()" {{ $attributes->class('bg-white flex justify-start items-center rounded-lg border border-solid border-gray-200') }}>
    <button class="w-1/3 h-full flex justify-center items-center rounded-tl-lg rounded-bl-lg" @click="minus">
        <i class="w-5 h-5 text-gray-900" data-feather="minus"></i>
    </button>
    <span class="w-1/3 h-full flex justify-center items-center text-gray-700" x-text="count"></span>
    <button class="w-1/3 h-full flex justify-center items-center rounded-tr-lg rounded-br-lg" @click="plus">
        <i class="w-5 h-5 text-gray-900" data-feather="plus"></i>
    </button>
</div>
