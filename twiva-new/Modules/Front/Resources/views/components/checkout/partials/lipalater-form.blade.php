<div {{ $attributes->class('grid grid-cols-3 gap-5') }}>
    <div class="col-span-3 lg:col-span-1 form-control">
        <label for="id" class="form-label">ID Number</label>
        <x-shop::checkout.utilities.input id="id" x-model="form.id" />
    </div>
    <div class="col-span-3 lg:col-span-1 form-control">
        <label for="period" class="form-label">Repayment Period</label>
        <x-shop::checkout.utilities.select id="period" x-model="form.period" class="px-3.5 py-2.5">
            <option value="" selected hidden>Select Period</option>
            <option value="4">4 months</option>
        </x-shop::checkout.utilities.select>
    </div>
    <div class="col-span-3 lg:col-span-1 form-control">
        <label for="mobile" class="form-label">Mobile Number</label>
        <x-shop::checkout.utilities.input id="mobile" x-model="form.mobile">
            <x-slot:suffix class="flex">
                <x-shop::checkout.utilities.button
                    text
                    color="primary-900"
                    class="uppercase !p-0 whitespace-nowrap"
                    x-bind:disabled="!valid.sendCode || loading"
                    @click="sendCode"
                >
                    send code
                </x-shop::checkout.utilities.button>
            </x-slot:suffix>
        </x-shop::checkout.utilities.input>
    </div>
    <div
        class="col-span-3 flex flex-col gap-5 overflow-hidden transition-all ease-linear duration-150 max-h-[150px] lg:max-h-[175px]"
        x-bind:class="{ '!max-h-0 !pb-0' : !code }"
    >
        <p class="text-gray-500 text-sm font-normal">A code has been sent to your SMS. Please enter below to verify your account.</p>
        <div class="col-span-3 lg:col-span-1 form-control">
            <label for="code" class="form-label">Enter Code Here</label>
            <x-shop::checkout.utilities.input x-mask="999 999" x-model="form.code" id="code" placeholder="--- ---" />
        </div>
    </div>
    <div class="col-span-3 flex justify-center lg:justify-start">
        <x-shop::checkout.utilities.button
            color="primary-50"
            class="px-[42px]"
            x-bind:disabled="!valid.form  || loading"
            @click="save">
            Verify Account
        </x-shop::checkout.utilities.button>
    </div>
</div>
