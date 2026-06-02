@extends('shop::layouts.app')

@section('title','Edit Address')

@section('content')
    <x-shop::core.container class="sm:pl-10">
        <a href="{{route('buyer.address.index',['account' => request('account')])}}">
            <div class="border-gray-200 sm-down:border md:mt-2.5 md:mb-7 sm-down:bg-white">
                <button class="flex justify-center items-center p-2.5 text-gray-700">
                    <i class="w-5 h-5 text-gray-700" data-feather="arrow-left"></i>
                    <span class="ml-4">Manage Address</span>
                </button>
            </div>
        </a>
        <form method="POST" id="form"
              action="{{route('buyer.address.update',['account' =>request('account'),'shippingAddress' => $address->id])}}">
            @csrf
            <div class="block md:flex md:justify-between">
                <h1 class="h1 font-semibold text-2xl">Add New Address </h1>
                <x-shop::form.button type="submit" class="px-[24px] hidden md:block" shape="fill">Update
                </x-shop::form.button>
            </div>
            <section class="mt-5 w-3/4">
                <p class="text-lg font-semibold">Your delivery information</p>
                <div class="grid xs-down:grid-cols-1 xs-down:gap-1  grid-cols-2 gap-10">
                    <div class="my-3 ">
                        <label class="text-gray-700 text-sm" for="first_name">First Name</label>
                        <x-shop::utilities.input value="{{$address->first_name}}" name="first_name" id="first_name"/>
                        @error('first_name')
                        <span class="text-error-800 text-sm">{{$message}}</span>
                        @enderror
                    </div>
                    <div class="md:my-3 ">
                        <label class="text-gray-700 text-sm" for="last_name">Last Name</label>
                        <x-shop::utilities.input value="{{$address->last_name}}" name="last_name" id="last_name"/>
                        @error('last_name')
                        <span class="text-error-800 text-sm">{{$message}}</span>
                        @enderror
                    </div>
                </div>
                <div class="my-3 ">
                    <label class="text-gray-700 text-sm " for="delivery_address">Delivery Address</label>
                    <x-shop::utilities.input name="delivery_address" value=" {{$address->delivery_address}} "
                                             id="delivery_address"/>
                    @error('delivery_address')
                    <span class="text-error-800 text-sm">{{$message}}</span>
                    @enderror
                </div>
                <div class="my-3 ">
                    <label class="text-gray-700 text-sm " for="country_id">Country/Region</label>
                    <x-shop::utilities.select-box name="country_id" id="country_id">
                        @foreach($countries as $country)
                            <option {{$address->country_id == $country->id ? 'selected' :'' }}  value="{{$country->id}}"
                                    class="p-2 bg-white">{{$country->name}}</option>
                        @endforeach
                    </x-shop::utilities.select-box>
                    @error('country_id')
                    <span class="text-error-800 text-sm">{{$message}}</span>
                    @enderror
                </div>
                <div class="grid xs-down:grid-cols-1 xs-down:gap-1 grid-cols-2 gap-10">
                    <div class="my-3 ">
                        <label class="text-gray-700 text-sm " for="county_id">County</label>
                        <x-shop::utilities.select-box name="county_id" id="county_id">

                        </x-shop::utilities.select-box>

                    </div>
                    <div class="md:my-3">
                        <label class="text-gray-700 text-sm " for="city_id">City</label>
                        <x-shop::utilities.select-box name="city_id" id="city_id">

                        </x-shop::utilities.select-box>

                        @error('city')
                        <span class="text-error-800 text-sm">{{$message}}</span>
                        @enderror
                    </div>
                </div>


            </section>

            <section class="pt-9 w-3/4">
                <p class="text-lg font-semibold">Enter your contact details</p>
                <div class="my-3 ">
                    <label class="text-gray-700 text-sm " for="email">Email Address (Optional)</label>
                    <x-shop::utilities.input name="email" value="{{ $address->email }}" id="email"/>
                    @error('email')
                    <span class="text-error-800 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div class=" ">
                    <label class="text-gray-700 text-sm " for="mobile_input">Phone Number</label>
                    <x-shop::utilities.input id="mobile_input"
                                             value="{{old('phone_number') ?? $address->mobile}}"/>
                    <input id="phone_number" type="hidden" name="phone_number" value="{{ $address->mobile}}"/>
                    @error('phone_number')
                    <span class="text-error-800 text-sm">{{$message}}</span>
                    @enderror
                    <span id="mobile_input_error" class="text-error-800 text-sm"></span>

                </div>
            </section>

            {{-- @include('buyer::address.map') --}}

            <x-shop::form.button type="submit" class="px-[24px] w-full hidden md:hidden xs-down:block " shape="fill">
                Update
            </x-shop::form.button>
        </form>
    </x-shop::core.container>

@endsection
@push('script')
    <script>
        initializePhone("#mobile_input", "#phone_number", "#mobile_input_error");
    </script>
    <script>
        $("#form").on('submit', (e) => {
            if (iti.isValidNumber())
            {
                // $("#form").submit();
            }
            else
            {
                e.preventDefault()
                $("#mobile_input").focus();
                $("#mobile_input_error").html("Please Enter a valid phone number");
            }
        })
    </script>
@endpush
