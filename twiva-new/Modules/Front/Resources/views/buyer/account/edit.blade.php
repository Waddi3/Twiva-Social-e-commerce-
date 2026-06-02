@extends('front::e-commerce.layouts.app', [ 'page_title' => 'Update account info' ])

@section('content')
    <x-shop::core.container class="sm:pl-10">
        <a href="{{route('e-commerce.buyer.settings-page')}}">
            <div class="border-gray-200 sm-down:border md:mt-2.5 md:mb-7 sm-down:bg-white">
                <button class="flex justify-center items-center p-2.5 text-gray-700">
                    <i class="w-5 h-5 text-gray-700" data-feather="arrow-left"></i>
                    <span class="ml-4">Account Setting</span>
                </button>
            </div>
        </a>
        <form method="POST" id="form"
              action="{{route('e-commerce.buyer.account.update')}}">
            @csrf
            <div class="block md:flex md:justify-between">
                <h1 class="h1 font-semibold text-2xl">Account Info </h1>
                <x-shop::form.button type="submit" class="px-[24px] hidden md:block" shape="fill">Update
                </x-shop::form.button>
            </div>
            <section class="mt-5 w-3/4">
                <p class="text-lg font-semibold">Your information</p>
                <div class="grid xs-down:grid-cols-1 xs-down:gap-1  grid-cols-2 gap-10">
                    <div class="my-3 ">
                        <label class="text-gray-700 text-sm" for="first_name">Name</label>
                        <x-shop::utilities.input value="{{ $buyer->name }}" name="name" id="name"/>
                        @error('name')
                        <span class="text-error-800 text-sm">{{$message}}</span>
                        @enderror
                    </div>
                </div>
            </section>

            <section class="pt-9 w-3/4">
                <p class="text-lg font-semibold">Your information</p>
                <div class="my-3">
                    <x-shop::utilities.radio type="radio" name="class" value="INDIVIDUAL"
                                             id="buyer_class_individual"
                                             label="Individual"
                        {{--                                             @if($buyer->class == 'INDIVIDUAL') checked="checked" @endif--}}
                    />
                    <x-shop::utilities.radio type="radio" name="class" value="RETAILER"
                                             id="buyer_class_retailer"
                                             label="Retailer"
                        {{--                                             @if($buyer->class == 'RETAILER') checked="checked" @endif--}}
                    />
                </div>

                <div class="my-3 buyer_class" @if($buyer->class != 'RETAILER') style="display: none" @endif>
                    <label class="text-gray-500 text-sm " for="rep_name">Rep Name</label>
                    <x-shop::utilities.input name="rep_name" id="rep_name" value="{{ $buyer->rep_name ?? null }}"/>
                    @error('rep_name')
                    <span class="text-error-800 text-sm ">{{ $message }}</span>
                    @enderror
                </div>

                <div class="my-3 buyer_class" @if($buyer->class != 'RETAILER') style="display: none" @endif>
                    <label class="text-gray-500 text-sm " for="retail_store_name">Retail Store Name</label>
                    <x-shop::utilities.input name="retail_store_name" id="retail_store_name"
                                             value="{{ $buyer->retail_store_name ?? null }}"/>
                    @error('retail_store_name')
                    <span class="text-error-800 text-sm ">{{ $message }}</span>
                    @enderror
                </div>

                <div class="my-3 buyer_class" @if($buyer->class != 'RETAILER') style="display: none" @endif>
                    <label class="text-gray-500 text-sm " for="rep_phone_number">Rep Phone Number</label>
                    <x-shop::utilities.input name="rep_mobile" placeholder="" id="rep_phone_number"
                                             value="{{ \Illuminate\Support\Str::length($buyer->rep_mobile) > 3 ? $buyer->rep_mobile : null}}"/>
                    <input id="rep_mobile" type="hidden" name="rep_mobile" value=""/>
                    @error('rep_mobile')
                    <span class="text-error-800 text-sm ">{{ $message }}</span>
                    @enderror
                    <span id="rep_phone_number_error" class="text-error-800 text-sm"></span>
                </div>
            </section>

            <x-shop::form.button type="submit" class="px-[24px] w-full hidden md:hidden xs-down:block " shape="fill">
                Update
            </x-shop::form.button>
        </form>
    </x-shop::core.container>

@endsection
@push('scripts')
    <script type="text/javascript" src="{{ mix('front/js/app.js') }}"></script>
    <script>
        // let iti;
        //
        // function initializeRepPhone(inputEl, outputEl, messageEl = null) {
        //     let input = document.querySelector(inputEl)
        //     let output = document.querySelector(outputEl);
        //     iti = window.intlTelInput(input, {
        //         nationalMode: true,
        //         utilsScript: "/shop/js/intl-tel-input/utils.js",
        //         initialCountry: "ke"
        //     });
        //     iti.promise.then(function () {
        //         handleChangeRep();
        //     });
        //
        //     let handleChangeRep = function () {
        //         if (input.value.includes('-') || input.value.includes(iti.selectedCountryData.dialCode + '-') || input.value.includes(iti.selectedCountryData.dialCode)) {
        //             console.log(input.value, iti.selectedCountryData.dialCode)
        //             input.value = input.value.replaceAll("+" + iti.selectedCountryData.dialCode, "")
        //             input.value = input.value.replaceAll(iti.selectedCountryData.dialCode + '-', "");
        //             input.value = input.value.replaceAll(iti.selectedCountryData.dialCode, "");
        //             input.value = input.value.replaceAll("0" + iti.selectedCountryData.dialCode, "")
        //             input.value = input.value.replaceAll("+", "")
        //             input.value = input.value.replaceAll("-", "")
        //         }
        //         if (input.value[0] === '0') {
        //             input.value = input.value.slice(1).replace(' ', '')
        //         }
        //         if (messageEl && input.value.length > 7) {
        //             $('span:contains("Please enter a valid phone number")').html("");
        //             $(messageEl).html("Please enter a valid phone number")
        //         }
        //         if (iti.isValidNumber()) {
        //             $(messageEl).html("")
        //             $('span:contains("Please enter a valid phone number")').html("");
        //         }
        //         let temp = iti.telInput.value.replace(iti.selectedCountryData.dialCode, '');
        //         output.value = "+" + iti.selectedCountryData.dialCode + '-' + temp.replace('+', '');
        //     };
        //
        //     input.addEventListener('change', handleChangeRep);
        //     input.addEventListener('keyup', handleChangeRep);
        //     return iti
        // }

        initializePhone("#rep_phone_number", "#rep_mobile", "#rep_phone_number_error");
    </script>


    <script>
        $(document).ready(function () {
            var init_checked = $('input[name="class"]').val();
            if (init_checked == "RETAILER") {
                $('.buyer_class').show();
            }
        });
        $(document).ready(function () {
            $('input[name="class"]').change(function () {
                var selectedOption = $(this).val();
                if (selectedOption == "INDIVIDUAL") {
                    $('.buyer_class').hide();
                    $('#rep_name').prop('required', false);
                    $('#rep_phone_number').prop('required', false);
                } else if (selectedOption == "RETAILER") {
                    $('.buyer_class').show();
                    $('#rep_name').prop('required', true);
                    $('#rep_phone_number').prop('required', true);
                }
            });

            $('#rep_phone_number').keyup();
        });

        $(document).ready(function () {
            var checked_value = `{{ $buyer->class }}`;
            if (checked_value == "RETAILER") {
                $('#buyer_class_retailer').attr('checked', 'checked');
            } else {
                $('#buyer_class_individual').attr('checked', 'checked');
            }
        });
    </script>
@endpush
@push('script')
    {{--    <script>--}}
    {{--        initializePhone("#mobile_input", "#phone_number", "#mobile_input_error");--}}
    {{--    </script>--}}



@endpush
