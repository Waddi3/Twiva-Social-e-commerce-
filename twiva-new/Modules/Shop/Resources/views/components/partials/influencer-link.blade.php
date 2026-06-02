@props([
    'link' => 'demiwikinson.twiva.com'
])
<div {{ $attributes->class('relative')->only('class') }} x-data="clipboard()">
    <div
            class="flex h-[40px] md:!w-full mobile:!left-0 relative z-10 transition-[width,left] ease-linear duration-150 cursor-pointer"
            :style="'left:' + (copied ? '-95px;' : '0;') + 'width:' + (copied ? 'calc(100% - 95px);' : '100%;')"
            @click="copy"
    >
        <span
                class="bg-white rounded-l-md w-[calc(100%-40px)] h-full flex justify-start items-center px-2.5 py-2 border border-r-0 border-solid border-gray-300"
                :class="{ '!border-primary-900': copied }"
        >
            <input class="text-base text-gray-500 copy-inner-text focus:outline-0 cursor-pointer"
                   value="{{$link.'.'.config('twiva.bases.shop_domain')}}" readonly>
        </span>
        <button class="h-full w-[40px] bg-gray-200 rounded-r-md flex justify-center items-center">
            <i data-feather="copy" class="w-[18px] h-[18px]"></i>
        </button>
    </div>
    <span class="z-0 h-[40px] transition-[right] ease-linear duration-200 flex items-center text-base text-primary-900 absolute right-0 top-0"
          :style="{ right: copied ? '0' : '30px' }">Link copied!</span>
</div>
