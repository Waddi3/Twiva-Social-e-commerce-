@props(['platforms' => []])
<div {{ $attributes->only('class') }}>
    @foreach($platforms as $platform)
        <a href="{{$platform->socialLink}}" target="_blank" {{$platform->id}}
           title="{{$platform->platform->name}}">
            <img class="w-[20px] h-[20px] md:w-[30px] md:h-[30px]  ml-2.5"
                 src="{{ asset($platform->platform->icon) }}" alt="{{$platform->platform->name}}">
        </a>
    @endforeach

</div>
