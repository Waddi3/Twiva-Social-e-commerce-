@props([
    'tag' => 'button',
    'href' => '#',
    'shape' => 'transparent' // transparent, fill, border, icon
])

@php
    if ($shape === 'border') {
        $class = 'py-2.5 px-[18px] border border-solid';
    } elseif ($shape === 'fill') {
        $class = 'py-2.5 px-[18px] bg-primary-600 text-white rounded-lg';
    } elseif ($shape === 'icon') {
        $class = 'p-1.5 border border-solid rounded-full';
    } else {
        $class = 'py-2.5 px-[18px] text-gray-500';
    }
@endphp

<a href="{{$href}}" {{ $attributes->class($class) }}>
{{ $slot }}
</a>
