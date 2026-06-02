@php use Modules\Order\Entities\Order; @endphp
@extends('front::e-commerce.layouts.app', [
    'page_title' => 'My orders',
])

@section('title','Orders')

@section('content')
    <x-front::core.container>
        <a href="{{route('ecommerce.index')}}">
            <div class="border-gray-200 sm-down:border md:mt-2.5 md:mb-7 sm-down:bg-white">
                <button class="flex justify-center items-center p-2.5 text-gray-700">
                    <i class="w-5 h-5 text-gray-700" data-feather="arrow-left"></i>
                    <span class="ml-4">Back to Shop</span>
                </button>
            </div>
        </a>
        <div class="block md:flex md:justify-between mt-10">
            <h1 class="h1 font-semibold text-2xl">My Orders </h1>
        </div>
        <section class="mt-5 ">
            <div class="grid xs-down:grid-cols-1 xs-down:gap-1  grid-cols-2  gap-4">
                <section>
                    @forelse($orders as $order)
                        <div data-orderID="{{$order->id}}"
                             class="order-index bg-white py-6 rounded-lg  shadow-md  px-8 mt-3">
                            <div class="flex justify-between ">
                                <p class="flex font-semibold">
                                    <span class="mr-2 " data-feather="tag"></span>Order ID :
                                    <span class="font-normal">&nbsp; {{$order->id}}</span>
                                </p>
                                <p class="flex font-semibold">
                                    {{$order->qty}} items <span class="ml-3" data-feather="chevron-right"></span>
                                </p>
                            </div>

                            <div class="my-5 flex justify-between mx-2">
                                <p class="my-2 font-semibold">Order Amount: <span
                                            class="font-normal">KSH {{number_format($order->order_amount + $order->shipping_amount)}}</span>
                                </p>
                                <p class="my-2 font-semibold">Order Date: <span
                                            class="font-normal">{{$order->created_at->format('M d Y')}}</span></p>
                                <p class="my-2 rounded-lg px-2 font-normal text-sm h-fit
                                     {{($order->status_string == "Delivered" ) ? 'bg-green-200 text-green-700 ':'bg-gray-300 text-gray-900'  }}">
                                    {{$order->status_string}}
                                </p>
                            </div>
                            @if(in_array($order->order_status, [Order::PROCESSING_STATUS ,Order::DISPATCHED_STATUS]))
                                <p class="text-black-100 mt-3 text-xs font-thin">
                                    {{ $order->tentative_period_text() }}
                                </p>
                            @endif
                        </div>
                    @empty
                    @endforelse
                </section>
                <section class="h-fit xs-down:hidden md:block" id="orderDetails">

                </section>
            </div>
        </section>
    </x-front::core.container>

@endsection

@push('scripts')
    <script type="text/javascript" src="{{ mix('front/js/app.js') }}"></script>
    <script>
        $(".order-index").click(function () {
            $(".order-index").removeClass('selected-order-index')
            let element = $(this);
            $(this).addClass('selected-order-index')

            let OrderID = $(this).data('orderid')
            let OrderRoute = "{{route('e-commerce.buyer.order.show',['order' => 'OrderID' ])}}"
            OrderRoute = OrderRoute.replace('OrderID', OrderID)
            console.log(OrderRoute)
            axios.get(OrderRoute).then(function ({data}) {
                $(".orderDetailsList").remove()
                $("#orderDetails").html(data.view).addClass("border-primary-900 border-2 rounded-lg")
                let view = data.view.replace('CLASSESS', 'md:hidden')
                element.append(view)

            })

        })


    </script>
@endpush
