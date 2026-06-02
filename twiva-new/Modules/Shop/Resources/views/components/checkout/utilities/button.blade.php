@props([
    'tag' => null,
    'type' => 'button',
    'text' => false,
    'outlined' => false,
    'link' => false,
    'icon' => false,
    'color' => 'primary-700',
    'disabled' => false
])

@php
$tag = $tag ?: ($link ? 'a' : 'button');
$config = [
    'fill' => [
        'primary-50' => 'bg-primary-50 text-primary-700',
        'primary-400' => 'bg-primary-400 text-white',
        'primary-700' => 'bg-primary-700 text-white',
        'primary-900' => 'bg-primary-900 text-white',
        'gray-125' => 'bg-gray-125 text-black-100',
        'gray-450' => 'bg-gray-450 text-white',
        'gray-850' => 'bg-gray-850 text-white',
        'success-500' => 'bg-success-500 text-white'
    ],
    'outlined' => [
        'primary-50' => 'bg-transparent border border-solid border-primary-50 text-primary-50',
        'primary-400' => 'bg-transparent border border-solid border-primary-400 text-primary-400',
        'primary-700' => 'bg-transparent border border-solid border-primary-700 text-primary-700',
        'primary-900' => 'bg-transparent border border-solid border-primary-900 text-primary-900',
        'gray-125' => 'bg-transparent border border-solid border-gray-125 text-gray-125',
        'gray-450' => 'bg-transparent border border-solid border-gray-450 text-gray-450',
        'gray-850' => 'bg-transparent border border-solid border-gray-850 text-gray-850',
        'success-500' => 'bg-transparent border border-solid border-success-500 text-success-500'
    ],
    'text' => [
        'primary-50' => 'bg-transparent text-primary-50',
        'primary-400' => 'bg-transparent text-primary-400',
        'primary-700' => 'bg-transparent text-primary-700',
        'primary-900' => 'bg-transparent text-primary-900',
        'gray-125' => 'bg-transparent text-gray-125',
        'gray-450' => 'bg-transparent text-gray-450',
        'gray-850' => 'bg-transparent text-gray-850',
        'success-500' => 'bg-transparent text-success-500'
    ]
];
$class = ['flex', 'justify-center', 'items-center', 'disabled:opacity-30'];
if ($outlined) {
    $class = array_merge($class, [$config['outlined'][$color]]);
} elseif ($text) {
    $class = array_merge($class, [$config['text'][$color]]);
} elseif (!$icon) {
    $class = array_merge($class, [$config['fill'][$color]]);
}
if (!$icon) {
    $class = array_merge($class, ['text-sm', 'font-normal', 'rounded-lg', 'p-2.5']);
}
@endphp

<{{ $tag }} type="{{ $type }}" {{ $attributes->class($class) }} @if($link) href="{{ $link }}" @endif @if($disabled) disabled @endif>
    {{ $slot }}
</{{ $tag }}>
