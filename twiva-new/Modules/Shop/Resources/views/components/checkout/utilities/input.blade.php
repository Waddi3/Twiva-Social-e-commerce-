@props([
    'type' => 'text',
    'error' => null
])

@php
$inputClasses = 'w-full focus:outline-0 placeholder:text-gray-500 placeholder:text-base';
$containerClasses = 'relative flex items-center h-[44px] w-auto p-2.5 rounded-lg border border-solid border-gray-300 focus-within:border-primary-900 bg-white cursor-text';
if ($type === 'number') { $inputClasses .= ' hide-number-arrows'; }
if ($error) {
    $inputClasses .= ' !text-error-850';
    $containerClasses .= ' !border-error-850';
}
@endphp

<div {{ $attributes->class($containerClasses)->only('class') }}>
    @if(isset($prefix))
        <div {{ $prefix->attributes }}>
            {{ $prefix }}
        </div>
    @endif
    <input name="{{$attributes->get('name')}}" type="{{ $type }}" {{ $attributes->except('class') }} class="{{ $inputClasses }}">
    @if(isset($suffix))
        <div {{ $suffix->attributes }}>
            {{ $suffix }}
        </div>
    @endif
</div>
@if($error)
    <span class="block text-error-850 text-sm font-normal">{{ $error }}</span>
@endif
