<div class="flex justify-start items-center py-[5px]">
    <div class="md:py-1.5">
        <div class="w-[50px] h-[50px] bg-[#f5f5f5] bg-center bg-no-repeat bg-contain"
             style="background-image: url('{{$product->productMainImage?->imageUrl ?? $product->businessProductMainImage?->imageUrl}}')"></div>
    </div>
    <div class="sm-down:flex justify-between items-center sm-down:w-full ml-2.5">
        <h5 class="text-base text-black-200 font-semibold md:mb-2.5">{{ $product->product_title ? \Illuminate\Support\Str::limit($product->product_title,20) : $product->id }}</h5>
        <div class="flex justify-start items-center sm-down:hidden">
            <x-shop::partials.counter-basket :quantity="$product->quantity" :productID="$product->id"/>
            <button class="ml-2.5 deleteBtn" data-is-directly-clicked="true" data-productID="{{$product->id}}">
                <i class="w-5 h-5 text-error-800" data-feather="trash-2"></i>
            </button>
        </div>
        <div class="flex md:hidden">
            <x-shop::partials.counter-basket :quantity="$product->quantity" :productID="$product->id"/>
            <button class=" deleteBtn" data-is-directly-clicked="true" data-productID="{{$product->id}}">
                <i class="w-5 h-5 text-error-800" data-feather="trash-2"></i>
            </button>
        </div>

    </div>
</div>
