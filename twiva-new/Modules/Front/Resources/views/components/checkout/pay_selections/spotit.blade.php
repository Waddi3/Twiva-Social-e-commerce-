<div class="py-[25px] border-b border-solid border-gray-200">
    <label class="flex justify-between items-center text-gray-700 text-sm font-normal cursor-pointer">
        <span class="flex justify-start items-center gap-2">
            <input type="radio" name="payment_type" value="6" class="accent-blue-600" @change="change('spotit')">
            <span>Spotit</span>
        </span>
        <img width="110" height="110" src="{{ asset('shop/images/payments/Spotit_Logo.svg') }}" alt="Spotit">
    </label>
</div>
<div x-data="spotit()" id="spotit"
    class="pt-5 pb-1 lg:pb-[35px] flex flex-col gap-5 overflow-hidden transition-all ease-linear duration-150"
    :class="{ '!max-h-0 !pt-0 !pb-0': active !== 'spotit' }">

    <div class=" ">
        <label for="spotit_phone_number" class="form-label">Phone Number</label>
        <x-shop::utilities.input name="spotit_phone_number" id="spotit_phone_number"
            value="{{ old('spotit_phone_number') }}" />
        <input id="spotit_mobile" name="spotit_mobile" type="hidden" value="{{ old('spotit_mobile') }}" />
        @error('spotit_phone_number')
            <p class="text-error-800">{{ $message }}</p>
        @enderror

        <p class="text-error-800 text-sm spotit_phone_number_error"></p>
    </div>


    <div class="w-full flex flex-col gap-4">
        <label class="text-gray-700 text-sm" for="partner">List of Banks</label>
        <x-shop::checkout.utilities.select id="spotit-list-of-banks" name="spotit_bank" class="pl-2 pr-8">
            <option disabled selected>Select a bank</option>
        </x-shop::checkout.utilities.select>
    </div>

    <div>
        <span class="text-gray-450">
            Spotit will send you a One Time Password by SMS to your phone number above, you will need to enter it on the
            next screen to confirm the order.
        </span>
    </div>
</div>


@push('script')
    <script>
        initializePhone("#spotit_phone_number", "#spotit_mobile", "#spotit_phone_number_error");
    </script>
    <script>
        $(document).ready(function() {
            let url = `{{ route('spotit-fetch-list-of-banks') }}`;
            $.ajax({
                url: url,
                method: 'GET',
                success: function(response) {
                    let $select = $('#spotit-list-of-banks');
                    $select.empty(); // Clear the current options

                    $.each(response, function(value, bank) {
                        $select.append($('<option></option>')
                            .val(`${bank.BankId}:${bank.BankName}`)
                            .text(bank.BankName));
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Request failed with status:', status, 'Error:', error);
                }
            });
        });
    </script>
@endpush
