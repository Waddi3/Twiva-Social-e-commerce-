@props([
    'id' => Str::random(10),
    'label' => null,
    'placeholder' => null,
    'disabled' => false,
    'error' => null
])

@php
    $selectClasses = 'bg-transparent block w-full text-sm font-normal text-gray-500 border border-solid border-gray-350 rounded-lg p-[5px] focus:border-primary-900 focus:outline-0 appearance-none';
    if ($error) {
        $selectClasses .= 'bg-transparent !text-error-850 !border-error-850';
    }
@endphp

<div class="w-full relative">
    <i class=" absolute right-1.5 top-[50%] translate-y-[-50%] cursor-default select-none -z-10"
       data-feather="chevron-down"></i>
    <select
            {{ $attributes->class($selectClasses) }}
            id="{{ $id }}"
            @if($disabled) disabled @endif
    >
        @if($placeholder)
            <option selected hidden disabled value="">{{ $placeholder }}</option>
        @endif
        {{ $slot }}
    </select>
</div>
@if($error)
    <span class="block text-error-850 text-sm font-normal">{{ $error }}</span>
@endif
