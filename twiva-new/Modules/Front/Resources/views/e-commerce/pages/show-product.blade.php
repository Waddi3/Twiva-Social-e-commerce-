@extends('front::e-commerce.layouts.app', [
    'page_title' => 'Social Commerce',
])

@section('meta')
    <meta property="og:title" content="{{ $product->product_title }}" />
    <meta property="og:type" content="article" />
    <meta property="og:image"
        content="{{ $product->productMainImage?->imageUrl ?? $product->businessProductMainImage?->imageUrl }}" />
    <meta property="og:description" content="{{ $product->description }}" />
@endsection

@section('content')
    <x-shop::core.container>
        <a href="{{ route('ecommerce.index') }}">
            <div class="border-gray-200 sm-down:border md:mt-2.5 md:mb-7 sm-down:bg-white">
                <button class="flex justify-center items-center p-2.5 text-gray-500">
                    <i class="w-5 h-5 text-gray-900" data-feather="arrow-left"></i>
                    <span class="ml-4">Back to Shop</span>
                </button>
            </div>
        </a>
        <div class="grid grid-cols-12 gap-2.5 md:gap-14 mb-4">

            <div class="col-span-12 md:col-span-5">

                <img class="w-full h-[435px] md:h-[400px] bg-[#f5f5f5] bg-zcenter bg-no-repeat bg-cover" id="main_image"
                    src="{{ $product->productMainImage?->imageUrl ?? $product->businessProductMainImage?->imageUrl }}"
                    alt="" />
                <section class="flex overflow-x-auto  space-x-1">
                    @foreach ($product->influencerProductImages as $image)
                        <img id="main_image"
                            class=" h-[100px] md:h-[100px] bg-[#f5f5f5] bg-center bg-no-repeat bg-cover side-image cursor-pointer"
                            src="{{ $image->image_url }}" alt="" />
                    @endforeach
                </section>
            </div>

            <div class="col-span-12 md:col-span-7 px-3.5">
                <h1 class="text-black-100 text-3xl font-bold mb-2.5 break-words">{{ $product->name }}</h1>
                @if ($product->discount < $product->retail_price)
                    <p class=" text-2xl font-bold mb-2.5 md:mb-[22px]">
                        <span style="text-decoration: line-through;">
                            KSH {{ number_format($product->retail_price) }}
                        </span>
                        <span class="text-primary-900 text-2xl font-bold mb-2.5 md:mb-[22px]">
                            KSH {{ number_format($product->discount) }}
                        </span>
                    </p>
                @else
                    <p class="text-primary-900 text-2xl font-bold mb-2.5 md:mb-[22px]">
                        KSH {{ number_format($product->retail_price) }}
                    </p>
                @endif
                <div class="mb-2.5 md:mb-[30px]">
                    <h6 class="text-black-100 text-base font-bold mb-2.5">Details</h6>
                    <p class="text-black-100 text-sm font-normal">{{ $product->description }}</p>
                </div>
                <div class="mb-2.5 md:mb-[30px] @if (is_null($product->brand)) hidden @endif">
                    <h6 class="text-black-100 text-base font-bold mb-2.5">Brand</h6>
                    <p class="text-black-100 text-sm font-normal">{{ $product->brand }}</p>
                </div>
                @if ($product->specification)
                    <div class="mb-2.5 md:mb-[30px]">
                        <h6 class="text-black-100 text-base font-bold mb-2.5">Specification</h6>
                        @foreach (explode(',', $product->specification) as $specification)
                            <p class="text-black-100 text-sm font-normal">{{ $specification }}</p>
                        @endforeach
                    </div>
                @endif
                <div class="flex space-x-4">
                    @if (count($product->sizeDetails))
                        <div
                            class="flex justify-start items-center mb-2.5 md:mb-[30px] @if ($product->sizeDetails->count() == 1 and $product->sizeDetails->first()?->name == 'N/A') hidden @endif">
                            <span class="text-black-100 text-lg font-semibold mr-3">Size</span>
                            <x-shop::checkout.utilities.select id="productSize" class="pl-2 pr-8" placeholder="Select Size">
                                @foreach ($product->sizeDetails as $size)
                                    <option value="{{ $size->id }}" @if ($product->sizeDetails->count() == 1) selected @endif>
                                        {{ \Illuminate\Support\Str::upper($size->name) }}
                                    </option>
                                @endforeach
                            </x-shop::checkout.utilities.select>
                        </div>
                    @endif
                    @if ($product->colorDetails)
{{--                        <div--}}
{{--                            class="flex justify-start items-center mb-2.5 md:mb-[30px] @if ($product->colorDetails->count() == 1 and $product->colorDetails->first()?->name == 'N/A') hidden @endif">--}}
{{--                            <span class="text-black-100 text-lg font-semibold mr-3">Color</span>--}}
{{--                            <x-shop::checkout.utilities.select id="productColor" class="pl-2 pr-8"--}}
{{--                                placeholder="Select Color">--}}
{{--                                @foreach ($product->colorDetails as $color)--}}
{{--                                    <option value="{{ $color->id }}" @if ($product->colorDetails->count() == 1) selected @endif>--}}
{{--                                        {{ $color->name }}--}}
{{--                                    </option>--}}
{{--                                @endforeach--}}
{{--                            </x-shop::checkout.utilities.select>--}}
{{--                        </div>--}}

                            @if (!$product->colorDetails->isEmpty() && !($product->colorDetails->count() == 1 && $product->colorDetails->first()?->name == 'N/A'))
                                <div class="flex justify-start items-center mb-2.5 md:mb-[30px]">
                                    <span class="text-black-100 text-lg font-semibold mr-3">Color:</span>
                                    <x-shop::checkout.utilities.select id="productColor" class="pl-2 pr-8" placeholder="Select Color">
                                        @foreach ($product->colorDetails as $color)
                                            <option value="{{ $color->id }}" @if ($product->colorDetails->count() == 1) selected @endif>
                                                {{ $color->name }}
                                            </option>
                                        @endforeach
                                    </x-shop::checkout.utilities.select>
                                </div>
                            @endif
                    @endif
                </div>

                @include('shop::share.shipment-delivery')

                <div class="flex justify-start items-center mb-2.5 md:mb-[30px]">
                    <span class="text-black-100 text-lg font-semibold">Qty</span>
                    <div id="singleProductQuantity">
                        @include('shop::components.views._singleProduct')
                    </div>
                </div>

                @include('shop::share.share-links')

                <div class="grid grid-cols-12 gap-2.5">
                    <div class="col-span-6 md:col-span-4 lg:md:col-span-3">
                        <button id="addFromShowBtn"
                            class="flex justify-center items-center w-full h-[60px] bg-primary-50 text-primary-700 rounded-lg">
                            <i class="w-6 h-6" data-feather="shopping-cart"></i>
                            <span class="ml-3">Add to Cart</span>
                        </button>
                    </div>
                    <div class="col-span-6 md:col-span-4 lg:md:col-span-3">
                        <a href="{{ route('e-commerce.shop.product.buyNow', ['product_id' => $product->id, 'user_type' => $user_type]) }}"
                            class="flex justify-center items-center w-full h-[60px] bg-primary-600 text-white rounded-lg">
                            Buy Now
                        </a>
                    </div>
                    <input id="user_type" type="hidden" value="{{$user_type}}" />
                </div>
            </div>
        </div>
        <x-shop::core.section class="mb-5 ">
            <h4 class="text-xl font-bold">Similar Products/Customer also buys</h4>
        </x-shop::core.section>
        <x-shop::core.section class="grid grid-cols-12 gap-y-[0.9375rem] md:gap-x-[1.5625rem] md:gap-y-[1.875rem] pb-20">
            @foreach ($products as $product)
                <div class="col-span-12 md:col-span-4 lg:col-span-3">
                    <x-front::e-commerce.layout.elements.product-card :id="$product->id" :userType="$product?->user?->type" :image="$product->productMainImage?->imageUrl ?? $product->businessProductMainImage?->imageUrl"
                        :discount="$product->discount_rate" :userImage="$product?->user?->profile_image" :userName="$product?->user?->name" :productName="$product->name" :productPrice="$product->product_price"
                        :retailPrice="$product->retail_price"
                        :shipmentTagColor="$product->shipmentTagColor ?? $product->actualDetail->shipmentTagColor($product->actualDetail)"
                        :shipmentTagBackgroundColor="$product->shipmentTagBackgroundColor ?? $product->actualDetail->shipmentTagBackgroundColor($product->actualDetail)"
                        :shipmentTagText="$product->shipmentTagText ?? $product->actualDetail->shipmentTagText($product->actualDetail)"/>
                </div>
            @endforeach
        </x-shop::core.section>
    </x-shop::core.container>
@endsection
@push('scripts')
    <script type="text/javascript" src="{{ mix('front/js/shop.js') }}"></script>

    <script>
        $(".side-image").on('click', function() {
            if (!window.dragging) {
                let newImage = $(this).attr('src');
                $("#main_image").fadeOut("fast", function() {
                    $("#main_image").attr('src', "")
                        .attr('src', newImage)
                        .fadeIn("fast");
                })
            }
        })
    </script>
    <script>
        const slider = document.querySelector('.overflow-x-auto');
        let mouseDown = false;
        let startX, scrollLeft;
        let startDragging = function(e) {
            mouseDown = true;
            startX = e.pageX - slider.offsetLeft;
            scrollLeft = slider.scrollLeft;
            setTimeout(() => {
                window.dragging = true;
            }, 300)
        };
        let stopDragging = function(event) {
            mouseDown = false;
            setTimeout(() => {
                window.dragging = false;
            }, 300)

        };

        slider.addEventListener('mousemove', (e) => {
            e.preventDefault();
            if (!mouseDown) {
                return;
            }
            const x = e.pageX - slider.offsetLeft;
            const scroll = x - startX;
            slider.scrollLeft = scrollLeft - scroll;
        });

        // Add the event listeners
        slider.addEventListener('mousedown', startDragging, false);
        slider.addEventListener('mouseup', stopDragging, false);
        slider.addEventListener('mouseleave', stopDragging, false);
    </script>
@endpush
