<div class="w-full flex flex-col gap-5">
    <div class="grid grid-cols-2 gap-2.5">
        <div class="col-span-2">
            <h6 class="text-gray-800 text-lg font-semibold">Enter Your delivery information</h6>
        </div>
        <div class="col-span-2 lg:col-span-1 form-control">
            <label for="first_name" class="form-label">First Name</label>
            <x-shop::checkout.utilities.input required id="first_name" value="{{old('first_name')}}"
                                              name="first_name"/>
            @error('first_name')
            <span class="text-error-800 text-sm">{{$message}}</span>
            @enderror
        </div>
        <div class="col-span-2 lg:col-span-1 form-control">
            <label for="last_name" class="form-label">Last Name</label>
            <x-shop::checkout.utilities.input required id="last_name" value="{{old('last_name')}}"
                                              name="last_name"/>
            @error('last_name')
            <span class="text-error-800 text-sm">{{$message}}</span>
            @enderror
        </div>
        <div class="col-span-2 form-control">
            <label for="deliveryAddress" class="form-label">Delivery Address</label>
            <x-shop::checkout.utilities.input required value="{{old('delivery_address')}}" id="deliveryAddress"
                                              name="delivery_address"/>
            @error('delivery_address')
            <span class="text-error-800 text-sm">{{$message}}</span>
            @enderror
        </div>
        <div class="col-span-2 form-control">
            <label for="country" class="form-label">Country/Region</label>
            <x-shop::checkout.utilities.select id="country_id" name="country_id" class="px-3.5 py-2.5">
                @foreach($countries as $country)
                    <option {{$country->id == 113 ? 'selected' : ''}} value="{{$country->id}}"
                            class="p-2 bg-white">{{$country->name}}</option>
                @endforeach
            </x-shop::checkout.utilities.select>
            @error('country_id')
            <span class="text-error-800 text-sm">{{$message}}</span>
            @enderror
        </div>
        <div class="col-span-2 lg:col-span-1 form-control">
            <label for="county_id" class="form-label">County</label>
            <x-shop::checkout.utilities.select id="county_id" name="county_id" class="px-3.5 py-2.5">

            </x-shop::checkout.utilities.select>
            @error('county_id')
            <span class="text-error-800 text-sm">{{$message}}</span>
            @enderror

        </div>
        <div class="col-span-2 lg:col-span-1 form-control">
            <label for="city_id" class="form-label">City</label>
            <x-shop::checkout.utilities.select id="city_id" name="city_id" class="px-3.5 py-2.5">

            </x-shop::checkout.utilities.select>
            @error('city_id')
            <span class="text-error-800 text-sm">{{$message}}</span>
            @enderror
        </div>
    </div>
    <div class="grid grid-cols-1 gap-2.5">
        <div class="col-span-1">
            <h6 class="text-gray-800 text-lg font-semibold">Enter your contact details</h6>
        </div>
        <div class="col-span-1 form-control">
            <label for="email" class="form-label">Email Address (Optional)</label>
            <x-shop::checkout.utilities.input required id="email" name="email" value="{{ old('email') ?? $buyer?->email }}"/>
            @error('email')
            <span class="text-error-800 text-sm">{{$message}}</span>
            @enderror
        </div>
        <div class="col-span-1 form-control">
            <div class=" ">
                <label class="text-gray-700 text-sm " for="mobile_input">Phone Number</label>
                <x-shop::utilities.input name="mobile_input" id="mobile_input" value="{{ old('phone_number') ?? $buyer?->rep_mobile??$buyer?->mobile }}"/>
                <input id="phone_number" type="hidden" name="phone_number" value="{{ old('phone_number') ?? $buyer?->rep_mobile??$buyer?->mobile }}"/>
                <span id="mobile_input_error" class="text-error-800 text-sm absolute"></span>
                @error('phone_number')
                <span class="text-error-800 text-sm">{{$message}}</span>
                @enderror
            </div>
        </div>
    </div>

    {{-- @include('buyer::address.map') --}}

</div>
