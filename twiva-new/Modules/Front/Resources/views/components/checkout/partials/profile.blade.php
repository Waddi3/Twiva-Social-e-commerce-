<div {{ $attributes->class('grid grid-cols-12')->only('class') }}>
    <div class="col-span-12 md:col-span-8 flex justify-start mb-10 md:mb-0">
        <div class="w-[95px] md:w-[140px]">
            <img src="{{ asset('images/Avatar.png') }}" alt="avatar" class="rounded-full w-[95px] h-[95px] md:w-[140px] md:h-[140px] mb-[5px] md:mb-0">
            <x-partials.scocial-media class="flex md:hidden justify-center" />
        </div>
        <div class="ml-[18px] md:ml-[30px] w-[calc(100%-113px)] md:w-[calc(100%-170px)]">
            <h4 class="mb-[5px] text-black-200 text-xl md:text-2xl font-semibold">Demi Wikinson</h4>
            <x-utilities.expander>
                Welcome to my store! Go and shop my specially selected products!
                Welcome to my store! Go and shop my specially selected products!
                Welcome to my store! Go and shop my specially selected products!
                Welcome to my store! Go and shop my specially selected products!
                Welcome to my store! Go and shop my specially selected products!
                Welcome to my store! Go and shop my specially selected products!
                <x-slot:show>
                    Show More
                </x-slot:show>
                <x-slot:hide>
                    Show Less
                </x-slot:hide>
            </x-utilities.expander>
        </div>
    </div>
    <div class="col-span-12 md:col-span-4 pt-0 md:pt-2.5">
        <div class="md:flex md:justify-end mb-6 md:mb-0">
            <x-partials.influencer-link class="w-auto md:w-[280px]" />
        </div>
        <x-partials.scocial-media class="hidden md:flex justify-end py-5" />
        <div class="flex justify-start md:justify-end flex-wrap gap-2.5 py-3 px-2.5 md:px-0 md:py-0 bg-gray-50 md:bg-transparent border-y md:border-y-0 border-solid border-gray-200">
            @foreach($tags as $tag)
                <x-partials.tag :tag="$tag" />
            @endforeach()
        </div>
    </div>
</div>
