@extends('shop::checkout.layouts.app')

@section('title', 'Checkout Shipping')

@section('content')

    <x-partials.checkout-container current="shipping" :productsData="$productsData" :partners="$partners">
        <x-shop::checkout.partials.checkout-grid
            :products-data="$productsData"
            quantity="{{$productsData['quantity']}}"
            total="{{$productsData['total']}}"
            :partners="$partners"
        >
            <form id="form" action="{{route('buyer.checkout.store-shipping-info',['account' =>request('account')])}}"
                  method="POST">
                @csrf

                <div class="w-full pb-8 lg:pb-[60px]">
                    @auth('buyer')
                        <div x-data="radioExpander()"
                             x-init="sections = ['exists', 'new']; active = '{{old('address_type') ?? 'exists' }}'">
                            <div class="pb-4 border-b border-solid border-gray-200">
                                <label class="text-gray-700 text-lg font-semibold cursor-pointer">
                                    <input
                                        type="radio"
                                        {{$addresses && $addresses->isEmpty() ? 'disabled' : ''}}
                                        name="address_type"
                                        value="exists"
                                        class="accent-blue-600"
                                        {{(!old('address_type') || old('address_type')) == 'exists' ? 'checked' :'' }}
                                        @change="change('exists')"
                                    > Use existing address
                                </label>
                            </div>
                            <div id="exists"
                                 class="pt-5 pb-[30px] flex flex-col gap-5 overflow-hidden transition-all ease-linear duration-150"
                                 :class="{ '!max-h-0 !pt-0 !pb-0' : active !== 'exists' }">
                                @foreach($addresses as $address)
                                    <label
                                        class="bg-gray-125 flex justify-start items-center gap-5 p-5 cursor-pointer rounded-[10px]">
                                        <input type="radio" name="address_id" class="accent-blue-600"
                                               value="{{ $address['id'] }}">
                                        <div class="flex flex-col gap-2.5">
                                            <h6 class="text-gray-850 text-2xl font-semibold">{{$address->contactName}}</h6>
                                            <p class="text-gray-850 text-base font-normal">{{$address->fullAddress}}</p>
                                            <p class="text-gray-850 text-base font-normal">Contact
                                                No. {{$address->phone_number}}</p>
                                        </div>
                                    </label>
                                @endforeach
                                @error('address_id')
                                <p class="text-error-800 text-sm">{{$message}}</p>
                                @enderror
                            </div>
                            <div class="pb-4 pt-9 border-b border-solid border-gray-200">
                                <label class="text-gray-700 text-lg font-semibold cursor-pointer">
                                    <input
                                        type="radio"
                                        name="address_type"
                                        value="new"
                                        {{--                                            {{$addresses->isEmpty()? 'checked' : ''}}--}}
                                        class="accent-blue-600"
                                        {{old('address_type') == 'new' ? 'checked' :'' }}

                                        @change="change('new')"
                                    > Use a different address
                                </label>
                            </div>
                            <div id="new" class="pt-5 overflow-hidden transition-all ease-linear duration-150"
                                 :class="{ '!max-h-0 !pt-0' : active !== 'new'}">
                                @include('shop::components.checkout.partials.shipping-form')
                            </div>
                        </div>
                    @else
                        @include('shop::components.checkout.partials.shipping-form')
                    @endauth
                </div>
                <div class="w-full flex flex-col gap-5 mb-10">
                    <div class="grid">
                        <div class="col-span-2 lg:col-span-1 form-control">
                            <div class="">
                                <h6 class="text-gray-800 text-lg font-semibold">
                                    Provide Delivery Instruction (Optional)
                                </h6>
                            </div>
                            {{--                            <div class="form-field-full">--}}
                            <textarea
                                class="flex border border-solid border-gray-300 focus-within:border-primary-900 bg-white focus:outline-0 p-4"
                                name="delivery_instructions" rows="7"
                            >{{ old('delivery_instructions') ?? '' }}</textarea>
                            {{--                            </div>--}}
                            {{--                            <x-shop::checkout.utilities.input required id="delivery_instructions"--}}
                            {{--                                                              value="{{ old('delivery_instructions') }}"--}}
                            {{--                                                              name="delivery_instructions"--}}
                            {{--                                                              placeholder="Enter Delivery Instructions"--}}
                            {{--                            />--}}
                            @error('delivery_instructions')
                            <span class="text-error-800 text-sm">{{$message}}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                <x-slot:formPrev :link="route('shop.product.index',['account'=>request('account')])">
                    <i data-feather="arrow-left"></i>
                    <span>Back to Shop</span>
                </x-slot:formPrev>`

                <x-slot:formNext class="submit-form-btn" type="submit">
                    Proceed to payment
                </x-slot:formNext>
                <x-slot:formNextMobile class="submit-form-btn" type="submit">
                    Proceed to payment
                </x-slot:formNextMobile>
            </form>

        </x-shop::checkout.partials.checkout-grid>

        <x-slot:navPrev :link="route('shop.product.index',['account'=>request('account')])">
            <i data-feather="arrow-left"></i>
            <span>Back to Shop</span>
        </x-slot:navPrev>
        <x-slot:navNext class="submit-form-btn" type="submit">
            Proceed to payment
        </x-slot:navNext>
    </x-partials.checkout-container>
@endsection
@push('script')
    <script>
        let iti = initializePhone("#mobile_input", "#phone_number", "#mobile_input_error");
    </script>
    <script>
        @if($addresses && $addresses->isEmpty() ?? false)
        $("input[value='new']").click()
        @endif
        $(".submit-form-btn").on('click', () => {
            if ($("input[name='address_type']:checked").val() === "new" && iti.isValidNumber()) {
                $("#form").submit();
            } else {
                $("#form").submit();
            }
        })
    </script>
@endpush
