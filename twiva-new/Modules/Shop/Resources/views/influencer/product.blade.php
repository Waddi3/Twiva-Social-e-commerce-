@extends('shop::layouts.app')
@section('meta')
    <meta property="og:title" content="{{$product->product_title}}"/>
    <meta property="og:type" content="article"/>
    <meta property="og:image"
          content="{{$product->productMainImage?->imageUrl ?? $product->businessProductMainImage?->imageUrl}}"/>
    <meta property="og:description" content="{{$product->product_description}}"/>
@endsection
@section('title', $product->product_title)
@section('content')
    <x-shop::core.container>
        <a href="{{route('shop.product.index',['account' => request('account')])}}">
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
                     src="{{$product->productMainImage?->imageUrl ?? $product->businessProductMainImage?->imageUrl}}"/>
                <section class="flex overflow-x-auto  space-x-1">
                    @foreach($product->influencerProductImages as $image)
                        <img
                                class=" h-[100px] md:h-[100px] bg-[#f5f5f5] bg-center bg-no-repeat bg-cover side-image cursor-pointer"
                                src="{{$image->image_url}}" alt=""/>
                    @endforeach
                </section>
            </div>
            <div class="col-span-12 md:col-span-7 px-3.5">
                <h1 class="ttext-black-100 text-3xl font-bold mb-2.5 break-words">{{$product->product_title}}</h1>
                @if($product->productDetails->discount)
                    <span style="text-decoration: line-through;">
                           KSH {{ number_format(100 * $product->product_price / $product->productDetails->discount, 2) }}
                        </span>
                @endif
                <p class="text-primary-900 text-2xl font-bold mb-2.5 md:mb-[22px]">KSH {{ number_format($product->product_price) }}</p>

                <div class="mb-2.5 md:mb-[30px]">
                    <h6 class="text-black-100 text-base font-bold mb-2.5">
                        {{$product->actualDetail->is_event_ticket_product ? 'Event Details' : 'Details'}}
                    </h6>
                    @if($product->actualDetail->is_event_ticket_product)
                        <div class="mb-2">
                            <div class="flex space-x-12 mb-2">
                                <div class="flex space-x-1">
                                    <img src="{{asset('shop/images/EventProducts/calendar.svg')}}" alt="">
                                    <p>{{$product->actualDetail->eventTicketProduct->date->format('d M Y')}}</p>
                                </div>
                                <div class="flex space-x-1">
                                    <img src="{{asset('shop/images/EventProducts/clock.svg')}}" alt="">
                                    <p>
                                        {{$product->actualDetail->eventTicketProduct->start_time}}
                                        -
                                        {{$product->actualDetail->eventTicketProduct->end_time}}
                                    </p>
                                </div>
                            </div>
                            <div class="flex space-x-1">
                                <img src="{{asset('shop/images/EventProducts/map-pin.svg')}}" alt="">
                                <p>{{$product->actualDetail->eventTicketProduct->location}}</p>
                            </div>
                        </div>
                    @endif

                    @if($product->product_description)
                        <p class="text-black-100 text-base font-bold mb-2.5">
                            Description
                        </p>
                        <p class="text-black-100 text-sm font-normal">{{$product->product_description}}</p>
                    @endif
                </div>
                <div class="mb-2.5 md:mb-[30px] @if(is_null($product->productDetails->brand)) hidden @endif">
                    <h6 class="text-black-100 text-base font-bold mb-2.5">Brand</h6>
                    <p class="text-black-100 text-sm font-normal">{{ $product->productDetails->brand }}</p>
                </div>
                @if($product->actualDetail->specification)
                    <div class="mb-2.5 md:mb-[30px]">
                        <h6 class="text-black-100 text-base font-bold mb-2.5">Specification</h6>
                        @foreach(explode(',',$product->actualDetail->specification) as $specification)
                            <p class="text-black-100 text-sm font-normal">{{$specification}}</p>
                        @endforeach
                    </div>

                @endif
                @if(!$product->actualDetail->is_event_ticket_product)
                    <div class="flex space-x-4">
                        @if($product->actualDetail->sizeDetails)
                            <div
                                    class="flex justify-start items-center mb-2.5 md:mb-[30px] @if($product->actualDetail->sizeDetails->count() == 1 and $product->actualDetail->sizeDetails->first()?->name == 'N/A') hidden @endif">
                                <span class="text-black-100 text-lg font-semibold mr-3">Size</span>
                                <x-shop::checkout.utilities.select id="productSize" class="pl-2 pr-8"
                                                                   placeholder="Select Size">
                                    @foreach($product->actualDetail->sizeDetails as $size)
                                        <option value="{{ $size->id }}"
                                                @if($product->actualDetail->sizeDetails->count() == 1) selected @endif>
                                            {{\Illuminate\Support\Str::upper($size->name)}}
                                        </option>
                                    @endforeach
                                </x-shop::checkout.utilities.select>
                            </div>
                        @endif
                        @if($product->actualDetail->colorDetails)
                            {{-- <div
                                    class="flex justify-start items-center mb-2.5 md:mb-[30px] @if($product->actualDetail->colorDetails->count() == 1 and $product->actualDetail->colorDetails->first()?->name == 'N/A') hidden @endif">
                                <span class="text-black-100 text-lg font-semibold mr-3">Color</span>
                                <x-shop::checkout.utilities.select id="productColor" class="pl-2 pr-8"
                                                                   placeholder="Select Color">
                                    @foreach($product->actualDetail->colorDetails as $color)
                                        <option value="{{ $color->id }}"
                                                @if($product->actualDetail->colorDetails->count() == 1) selected @endif>
                                            {{ $color->name }}
                                        </option>
                                    @endforeach
                                </x-shop::checkout.utilities.select>
                            </div> --}}

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
                @endif

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
                        <button id="buyFromShowBtn"
                                class="flex justify-center items-center w-full h-[60px] bg-primary-600 text-white rounded-lg">
                            Buy Now
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <x-shop::core.section class="mb-5 ">
            <h4 class="text-xl font-bold">Similar Products/Customer also buys</h4>
        </x-shop::core.section>
        <x-shop::core.section
                class="grid grid-cols-12 gap-y-[0.9375rem] md:gap-x-[1.5625rem] md:gap-y-[1.875rem] pb-20">
            @foreach($products as $product)
                <div class="col-span-12 md:col-span-4 lg:col-span-3">
                    <x-shop::partials.product-card :product="$product"/>
                </div>
            @endforeach
        </x-shop::core.section>
    </x-shop::core.container>

@endsection
@push('script')
    <script>
        $(".side-image").on('click', function () {
            if (!window.dragging) {
                let newImage = $(this).attr('src');
                $("#main_image").fadeOut("fast", function () {
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
        let startDragging = function (e) {
            mouseDown = true;
            startX = e.pageX - slider.offsetLeft;
            scrollLeft = slider.scrollLeft;
            setTimeout(() => {
                window.dragging = true;
            }, 300)
        };
        let stopDragging = function (event) {
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
