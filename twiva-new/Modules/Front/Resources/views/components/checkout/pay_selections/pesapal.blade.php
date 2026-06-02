<div class="py-[25px] border-b border-solid border-gray-200">
    <label class="flex justify-between items-center text-gray-700 text-sm font-normal cursor-pointer">
        <span class="flex justify-start items-center gap-2">
            <input type="radio" name="payment_type" value="8" class="accent-blue-600" @change="change('pesapal')">
            <span>Pesapal</span>
        </span>
        <img width="110" height="110" src="{{ asset('shop/images/payments/pesapal-logo.svg') }}" alt="Pesapal">
    </label>
</div>

<div x-data="pesapal()" id="pesapal"
    class="pt-5 pb-1 lg:pb-[35px] flex flex-col gap-5 overflow-hidden transition-all ease-linear duration-150"
    :class="{ '!max-h-0 !pt-0 !pb-0': active !== 'pesapal' }">

    <div class=" ">
        <label for="pesapal_phone_number" class="form-label">Phone Number</label>
        <x-shop::utilities.input name="pesapal_phone_number" id="pesapal_phone_number"
            value="{{ old('pesapal_phone_number') }}" />
        <input id="pesapal_mobile" name="pesapal_mobile" type="hidden" value="{{ old('pesapal_mobile') }}" />
        @error('pesapal_phone_number')
            <p class="text-error-800">{{ $message }}</p>
        @enderror

        <p class="text-error-800 text-sm pesapal_phone_number_error"></p>
    </div>
</div>


@push('script')
    <script>
        initializePhone("#pesapal_phone_number", "#pesapal_mobile", "#pesapal_phone_number_error");
    </script>
@endpush
