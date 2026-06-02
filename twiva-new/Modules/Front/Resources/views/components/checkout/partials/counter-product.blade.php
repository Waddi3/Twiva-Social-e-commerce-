<div id="counter-parent" x-data="counter({{ $productCount }},{{$productID }},'{{ $submit }}', '{{$userType}}')"
     data-count="{{ $productCount ?? 1 }}"
     data-product_id="{{$productID}}" {{ $attributes->class('flex justify-end items-center')->only('class') }}>
    <button
        class="w-2/5 h-full  flex justify-center items-center bg-white rounded-tl-lg rounded-bl-lg border-gray-200 border border-r-0"
        @click="minus">
        <i class="w-5 h-5 text-gray-900" data-feather="minus"></i>
    </button>
    {{--    <span class="w-1/3 h-full flex justify-center items-center bg-white text-gray-700 border-gray-200 border-y total_items2"--}}
    {{--          ></span>--}}
    <input type="number" data-product_id="{{ $productID }}" min="1" {{--id="quantity_product_{{ $productID }}"--}}
    value="{{ $productCount ?? 1 }}"
           class="w-1/5 h-full flex justify-center items-center bg-white text-gray-700 border-gray-200 border-y update_count_quantity_of_product quantity_product_{{ $productID }}">
    <button
        class="w-2/5 h-full flex justify-center items-center bg-white rounded-tr-lg rounded-br-lg border-gray-200 border border-l-0"
        @click="plus">
        <i class="w-5 h-5 text-gray-900" data-feather="plus"></i>
    </button>

</div>
