<div {{ $attributes->class('flex items-center h-[44px] w-auto p-2.5 rounded-lg  focus-within:border-primary-900')->only('class') }}>
    <input id="{{ $attributes->get('id') }}" {{ $attributes->has('required') ? 'required' :'' }}
    {{ $attributes->has('checked') ? 'checked' :'' }}
    value="{{ $attributes->get('value') }}" name="{{ $attributes->get('name') }}"
           type="{{ $attributes->get('type', 'radio') }}"
           class="focus:outline-0 placeholder:text-gray-500 placeholder:text-base mx-2"
           placeholder="{{ $attributes->get('placeholder') }}"
           @if(old('type') == $attributes->get('value')) checked="checked" @endif
    >
    <label class="text-gray-500 text-sm "
           for="{{ $attributes->get('id') }}">{{ $attributes->has('label') ? $attributes->get('label') : '' }}</label>
</div>
