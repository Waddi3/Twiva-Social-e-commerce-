@extends('order::layouts.app')

@section('title', 'Checkout Confirm')

@section('content')
    <div>
        <div class="w-full flex flex-col items-center justify-start pt-[50px] lg:pt-[107px] mb-5">
            <div>
                <div
                    class="h-[calc(100vh-160px)] lg:h-[calc(100vh-156px)] absolute right-0 left-0 bottom-0 top-[6px] bg-white flex flex-col gap-6 justify-center items-center z-[100]">
                    <p class="text-primary-900 text-lg font-normal">
                        Processing payment...
                    </p>

                    <span class="checkout-loading"></span>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        setInterval(() => {
            axios
                .get(window.location.origin + '/checkout/check-status')
                .then((data) => {
                    if (data.data.transaction_status === 1 || data.data.transaction_status === -1) {
                        window.location.href = "{{ route('buyer.delivery.payment.confirm', ['order_id' => $order->id]) }}"
                    }
                })
        }, 5000);

    </script>
@endpush
