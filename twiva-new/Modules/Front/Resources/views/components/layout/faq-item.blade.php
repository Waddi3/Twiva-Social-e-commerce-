<div x-data="expander()" {{ $attributes->class('w-full shadow-faq-item') }}>
    <div :class="{ 'w-full flex justify-between items-center p-[15px] rounded-t-[5px] cursor-pointer': true, 'bg-white': !show, 'bg-primary-1000': show, 'rounded-b-[5px]': !show }" @click="toggle">
        <h6 :class="{ 'text-xl font-bold': true, 'text-primary-1000': !show, 'text-white': show }">{{ $title }}</h6>
        <button x-show="!show" class="w-5 h-5 text-primary-1000">
            <i data-feather="plus"></i>
        </button>
        <button x-show="show" class="w-5 h-5 text-white">
            <i data-feather="minus"></i>
        </button>
    </div>
    <div :id="id" :class="{ 'w-full bg-white text-gray-800 text-xl font-normal rounded-b-[5px] overflow-hidden transition-[max-height] ease-linear duration-150': true, '!max-h-0': !show }">
        <div class="p-5 faq-content">{{ $slot }}</div>
    </div>
</div>
