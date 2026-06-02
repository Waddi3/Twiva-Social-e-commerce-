<div class="grid grid-cols-3 gap-[30px]">
    @foreach($feeds as $feed)
        <feed class="col-span-3 md:col-span-1">
            <div class="w-full h-[227px] rounded-[10px] mb-5 bg-no-repeat bg-cover bg-center"
                 style="background-image: url('{{ $feed['image'] }}')"></div>
            <h3 class="text-gray-950 text-lg font-semibold mb-4">
                {{$feed['title']}}
            </h3>
            <p class="text-gray-800 text-lg font-normal mb-4">
                {!!  $feed['excerpt']!!}
            </p>
            <x-front::form.button target="_blank" tag="a" href="{{$feed['link']}}"
                                  class="w-full md:w-auto text-center bg-primary-50 !text-primary-700 text-base font-normal rounded-lg">
                Read More
            </x-front::form.button>
        </feed>
    @endforeach

</div>