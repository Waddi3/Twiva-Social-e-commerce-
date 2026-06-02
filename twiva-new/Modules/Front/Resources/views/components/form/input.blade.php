<div {{ $attributes->class('flex items-center h-[44px] w-auto px-3.5 py-2.5 rounded-lg border border-solid border-gray-300 focus-within:border-primary-900 bg-white') }}>
    @if($attributes->has('icon'))
        <i data-feather="{{ $attributes->get('icon') }}" class="w-[18] mr-2 text-gray-950"></i>
    @endif
    <input id="{{ $attributes->get('inputid', '') }}" type="{{ $attributes->get('type', 'text') }}" class="w-full focus:outline-0 placeholder:text-gray-500 placeholder:text-base" placeholder="{{ $attributes->get('placeholder') }}">
</div>
