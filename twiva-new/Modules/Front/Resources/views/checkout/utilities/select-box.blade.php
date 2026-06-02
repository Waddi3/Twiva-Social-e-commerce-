<div {{ $attributes->class('flex items-center h-[44px] w-auto p-2.5 rounded-lg border border-solid border-gray-300 focus-within:border-primary-900 bg-white')->only('class') }}>
    @if($attributes->has('icon'))
        <i data-feather="{{ $attributes->get('icon') }}" class="w-4 h-4 mr-2.5"></i>
    @endif
    <select id="{{$attributes->get('id')}}" value="{{$attributes->get('value')}}" name="{{$attributes->get('name')}}"
            type="{{ $attributes->get('type', 'text') }}"
            class="w-full focus:outline-0 placeholder:text-gray-500 placeholder:text-base bg-white"
            placeholder="{{ $attributes->get('placeholder') }}">

        {{$slot}}


    </select>

</div>
