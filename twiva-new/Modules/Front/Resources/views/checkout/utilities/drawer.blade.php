@php
    // Settings
    $name = $attributes->get('name', 'drawer');
    $desktopOffsetTop = $attributes->get('desktop-offset-top', true);
    $mobileOffsetTop = $attributes->get('mobile-offset-top', false);
    $position = $attributes->get('position', 'right');
    $class = $position === 'right' ? 'right-0 left-0 md:left-unset' : 'left-0 right-0 md:right-unset';
    $class .= $desktopOffsetTop ? ' md:top-20' : '';
    $class .= $mobileOffsetTop ? ' top-[62px]' : ' top-0';
    $translate = $position === 'right' ? 'translate-x-full' : 'translate-x-full-inverse';
@endphp
<div
    x-data="drawer()"
    :id="id"
    {{ $attributes->class('fixed bottom-0 md:w-[334px] shadow-drawer z-50 transition-transform hidden')->class($class)->only('class') }}
    :class="{ '{{ $translate }}': !show }"
    @drawers-{{ $name }}-toggle.window="toggle"
>
    {{ $slot }}
</div>
