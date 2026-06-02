@foreach($productsData['productsData'] as $item)
    <div class="grid grid-cols-3 gap-2.5">
        <div class="col-span-1">
            <img src="{{$item->mainImagePath}}"
                 class="w-full h-full object-center object-contain image" data-product="{{$item->id  }}">
        </div>
        <div class="flex flex-col gap-2.5 col-span-2">
            @if($item->discount)
                <h6 class="text-primary-1000 text-base font-semibold">KSH {{number_format($item->discount)}}</h6>
            @else
                <h6 class="text-primary-1000 text-base font-semibold">KSH {{number_format($item->retail_price)}}</h6>

            @endif

            <p class="text-black-100 text-sm font-semibold truncate">{{ $item->name }}</p>
            <div class="flex justify-start items-center gap-2.5">
                @if(count($item->sizeDetails))
                    <div class="w-2/5 flex justify-start items-center gap-3 @if($item->sizeDetails->count() == 1 and $item->sizeDetails->first()?->name == 'N/A') hidden @endif">
                        <label for="size">Size</label>
                        <x-shop::checkout.utilities.select class="checkout_product_size"
                                                           data-productID="{{$item->id}}" placeholder="Select Size"
                                                           name="select_size[{{$item->id}}]"
                                                           required="required"
                        >
                            {{--                            <option value="">Select Size</option>--}}
                            @foreach($item->sizeDetails as $size)
                                @if(isset($productsData['buyerProducts'][$item->id]['size_id']) && ( $productsData['buyerProducts'][$item->id]['size_id']  == $size->id ))
                                    <option value="{{$size->id}}"
                                            selected>{{strtoupper($size->name)}} </option>
                                @else
                                    <option
                                            @if($item->sizeDetails->count() == 1) selected @endif
                                    value="{{$size->id}}">{{strtoupper($size->name)}}</option>
                                @endif
                            @endforeach
                        </x-shop::checkout.utilities.select>

                    </div>
                @endif
                @if(count($item->colorDetails))
                    <div class="w-3/5 flex justify-start items-center gap-3 @if($item->colorDetails->count() == 1 and $item->colorDetails->first()?->name == 'N/A') hidden @endif">
                        <label for="color">Color</label>
                        <x-shop::checkout.utilities.select class="checkout_product_color"
                                                           data-productID="{{$item->id}}" placeholder="Select Color"
                                                           name="select_color[{{$item->id}}]"
                                                           required="required"
                        >
                            {{--                            <option value="">Select Color</option>--}}

                            @foreach($item->colorDetails as $color)
                                <option
                                        @if($item->colorDetails->count() == 1) selected @endif
                                value="{{$color->id}}"{{isset($productsData['buyerProducts'][$item->id]['color_id']) && ( $productsData['buyerProducts'][$item->id]['color_id']  == $color->id )? 'selected' :''}}>
                                    {{$color->name}}
                                </option>
                            @endforeach
                        </x-shop::checkout.utilities.select>
                    </div>
                @endif

            </div>
            <div class="flex justify-between items-center gap-3">

                <x-shop::partials.counter-product
                        :product-i-d="$productsData['buyerProducts'][$item->id]['product_id']"
                        :product-count="$productsData['buyerProducts'][$item->id]['quantity']"
                        submit="true"
                        class="!bg-gray-150 h-[30px] w-[calc(100%-34px)]"/>

                <x-shop::checkout.utilities.button icon>
                    <i data-feather="trash-2" class="text-error-800 w-[20px] h-[20px] deleteBtn"
                       data-from="Shipping" data-productID="{{$item->id}}"></i>
                </x-shop::checkout.utilities.button>
            </div>
        </div>
    </div>
@endforeach
