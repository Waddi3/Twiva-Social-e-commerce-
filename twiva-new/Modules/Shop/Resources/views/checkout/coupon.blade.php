{{-- @auth('buyer') --}}
<div class="coupon-section">
    <p class=" text-md font-semibold">Coupon</p>
    <div class="mb-8">
        <input id="coupon-code"
            class="coupon-input w-1/3 focus:outline-0 placeholder:text-gray-500 placeholder:text-sm  placeholder:font-normal items-center h-[44px] p-2.5 rounded-lg border border-solid border-gray-300 focus-within:border-primary-900 bg-white"
            type="text"
            value="{{ cache()->get(session('coupon_key')) ? session('coupon_code') : session()->forget('coupon_key') }}"
            placeholder="Please type your coupon">
        <button id="check-coupon"
            class="check-coupon justify-center items-center disabled:opacity-30 bg-primary-700 text-white text-sm font-normal rounded-lg p-2.5 px-4">
            Check Coupon
        </button>
        <span class="invalid-feedback invalid-coupon text-error-800 block" role="alert" style="display: none">
            <strong class="coupon-error-message">Please check you coupon code</strong>
        </span>
    </div>
</div>
@push('script')
    <script>
        $(document).ready(function() {
            $('.invalid-coupon').hide();
        });
        $(document).off('click', '.check-coupon').on('click', '.check-coupon', function() {
            $('.invalid-coupon').hide();
            let coupon_code = $(this).closest('.coupon-section').find('.coupon-input').val();
            let account = `{{ request('account') }}`;
            let additionalParams = {
                'coupon_code': coupon_code,
            }
            let route =
                `{{ route('buyer.check.coupon.validation', ['account' => addDotToSubDomain(request('account'))]) }}`
            let url = route + '?' + $.param(additionalParams);
            $.ajax({
                url: url,
                success: function(data) {
                    if (data.is_validate) {
                        $(".total_with_shipping_fees").addClass('line-through');
                        $(".coupon_with_shipping_fees").text("KSH " + data.coupon_with_shipping_fees);
                        $(".with-coupon").show();
                        // $('.check-coupon').prop("disabled", true);
                    } else {
                        console.log(data.error_message);
                        $('.coupon-error-message').text(data.error_message);
                        $('.invalid-coupon').show();
                    }
                }
            })
        });
    </script>
@endpush
{{-- @endauth --}}
