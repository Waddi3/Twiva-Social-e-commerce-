@props([
    'is_group_paying' => in_array(old('is_group_paying'), ['on', '1']) ??false,
    'is_disabled' => false
])
<section
        x-data="{ isGroupPaying: {{ json_encode($is_group_paying) }} }">
    <div class="py-[25px] border-b border-solid border-gray-200">
        <label class="flex justify-between items-center text-gray-700 text-sm font-normal cursor-pointer">
            <span class="flex justify-start items-center gap-2">
                <input
                        {{ $is_group_paying ? 'checked' : ''}}
                        {{ $is_disabled ? 'disabled' : ''}}
                        x-model="isGroupPaying"
                        @change="$dispatch('update-isGroupPaying',isGroupPaying)"
                        type="checkbox"
                        name="is_group_paying"
                        id="is_group_paying"
                        class="accent-blue-600"
                >
                <p><span class="font-bold">Group Buying</span> (Pay part of the total now, settle the balance later, or invite others to split the cost with you.)</p>
            </span>
            <img width="40" height="40" src="{{ asset('shop/images/payments/group-buying.jpeg') }}"
                 alt="Group Buying">
        </label>
    </div>
    <div x-show="isGroupPaying" id="group_buying"
         class="pt-5 pb-1 lg:pb-[35px] flex flex-col gap-5 overflow-hidden transition-all ease-linear duration-150"
    >

        <div>
            <span class="text-gray-450">
             Please note that orders placed under the Group Buying option will be processed once the
             full payment for the order has been received.
            </span>
        </div>

        <div class=" ">
            <label for="amount_payment_group_buying" class="form-label">Pay Amount</label>
            <x-shop::utilities.input name="amount_payment_group_buying" id="amount_payment_group_buying"
                                     value="{{ old('amount_payment_group_buying') }}"
                                     type="number" min="10"/>
            @error('group_buying')
            <p class="text-error-800">{{ $message }}</p>
            @enderror
        </div>
    </div>


</section>