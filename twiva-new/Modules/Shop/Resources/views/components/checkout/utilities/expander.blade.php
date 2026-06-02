<div x-data="expander()">
    <p :id="id" :class="{ 'mb-[5px] text-gray-700 text-sm md:text-lg overflow-hidden transition-[max-height] ease-linear duration-150': true,'!max-h-5': !show, 'md:!max-h-7': !show }">
        {{ $slot }}
    </p>
    <button class="text-primary-800 text-xs md:text-lg font-bold" x-text="show ? '{{ $hide }}' : '{{ $show }}'" @click="toggle"></button>
</div>
