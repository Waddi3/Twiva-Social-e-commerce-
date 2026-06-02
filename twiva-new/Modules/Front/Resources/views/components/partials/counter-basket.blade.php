@props([
    'productID'=> 0,
    'quantity'=> 0,
    'userType' => ''
    ])

<div
    x-data="counter({{$quantity}},{{$productID}},'true','{{$userType}}')" {{ $attributes->class('flex justify-end items-center')->only('class') }}>
    <button class="w-[34px] h-[29px] flex justify-center items-center bg-gray-150 rounded-tl-lg rounded-bl-lg"
            @click="minus">
        <i class="w-5 h-5 text-black-200" data-feather="minus"></i>
    </button>
    {{--    <span class="min-w-[34px] h-[29px] flex justify-center items-center bg-gray-150 text-black-200"--}}
    {{--          x-text="count"></span>--}}
    <input type="number" data-product_id="{{$productID}}" min="1" id="quantity_product_{{ $productID }}"
           value="{{ $quantity ?? 1 }}"
           class="w-1/5 h-full flex justify-center items-center bg-white text-gray-700 border-gray-200 border-y update_count_quantity_of_product">
    <button class="w-[34px] h-[29px] flex justify-center items-center bg-gray-150 rounded-tr-lg rounded-br-lg"
            @click="plus">
        <i class="w-5 h-5 text-black-200" data-feather="plus"></i>
    </button>
</div>
