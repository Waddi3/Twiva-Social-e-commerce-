@php use Modules\User\Entities\User; @endphp
@php use Modules\Order\Entities\Order; @endphp
@extends('admin::layouts.app')
@section('title')
    Orders
@endsection
@section('content')
    <div class="">
        <div class="dashboard_container">

            <!-- Left Column Start -->
            <div class="left_col">
                <div class="scroll-view">
                    <div class="clearfix"></div>

                    <!-- sidebar menu -->
                    @include('admin::layouts.sidebar')
                    <!-- /sidebar menu -->
                </div>
            </div>
            <!-- Left Column End -->

            <!-- page content -->
            <div class="right_col dashboard-page" role="main">
                <!--********** Breadcrumb Start ***********-->
                <div class="page-title breadcrumb-wrapper">
                    <ul class="breadcrumb breadcrumb-link">
                        <li><a href="{{ route('admin.orders.index') }}" class="back-link">Order</a></li>
                        <li><img src="{{ asset('admin/images/icons/gray-right-arrow.svg') }}" alt=""></li>
                        <li><a href="#">Order Details</a></li>
                    </ul>
                </div>
                <!--**********  Breadcrumb End ***********-->

                <div class="title-wrapper">
                    <h4>
                        Order Detail
                    </h4>
                </div>
                @if(!$order)
                    <div class="invoice-wrapper">
                        <div class="invoice-header-wrapper">
                            <div class="invoice-detial-wrapper">
                                Order Not Found
                            </div>
                        </div>
                    </div>
                @else

                    <div class="invoice-wrapper" id="invoice-wrapper">
                        <div class="invoice-header-wrapper">
                            <div class="invoice-detial-wrapper">
                                <ul>
                                    <li style="display: block; !important">
                                        Order No. {{ $order->id }}
                                    </li>
                                    <li style="display: block; !important">
                                        Order by: {{ $order->buyer?->name ?? $order->shippingAddress->contact_name }}
                                    </li>
                                    <li style="display: block; !important">
                                        {{ $order->created_at->format('M j, Y') }} {{ $order->created_at->format('g:i A') }}
                                    </li>
                                    <li style="display: block; !important">
                                        Order Status:
                                        {{$order->statusString}}
                                    </li>
                                    <li style="display: block; !important">
                                        Order Delivery Code:
                                        {{$order->delivery_code}}
                                    </li>
                                    @if(in_array($order->order_status, [Order::PROCESSING_STATUS ,Order::DISPATCHED_STATUS]))
                                        <li style="display: block; !important">
                                            {{ $order->tentative_period_text() }}
                                        </li>
                                    @endif
                                </ul>
                            </div>
                            <div class="invoice-print-btn-wrapper">
                                <a>
                                    <button class="print-btn white-bttn b-none" onclick="printDiv('invoice-wrapper')">
                                        <img src="{{ asset('admin/images/icons/np_print_3053742_000000 1.svg') }}"
                                             alt="">
                                        PRINT
                                    </button>
                                </a>
                                <a href="{{ route('order.detail.pdf', $order->id) }}">
                                    <button class="download-btn white-bttn b-none"><img
                                                src="{{ asset('admin/images/icons/np_save_2209758_000000 1.svg') }}"
                                                alt="">
                                        Download
                                    </button>
                                </a>
                            </div>
                        </div>

                        <div class="order-itmes-table-wrapper table-wrapper">
                            <table class="table table-responsive">
                                <thead>
                                <tr>
                                    <th scope="col">Order Items</th>
                                    <th scope="col">Price</th>
                                    <th scope="col">Qty</th>
                                    <th scope="col">Total</th>
                                    <th scope="col">Seller</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($order->orderDetails as $orderDetail)
                                    @if($order->shop->user()->withTrashed()->first()->type == User::InfluencerType)
                                        <tr>
                                            <td width="50px" scope="row">
                                                <div class="product-img-nane-wrapper" data-toggle="collapse"
                                                     data-target="#product-{{ $orderDetail->id }}">
                                                    <div class="img">
                                                        <img
                                                                src="{{ config('app.media_url') . $orderDetail?->productFromInfluencerShop?->productMainImage?->imageUrl  }}"
                                                                alt="">
                                                    </div>
                                                    <div class="name">
                                                        <span>  {{ $orderDetail->productFromInfluencerShop?->product_title }}</span>
                                                        <span>(Track Your Order)</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td width="50px">
                                                KSH {{ number_format($orderDetail->amount) }}</td>
                                            <td width="50px">{{ $orderDetail->quantity }}</td>
                                            <td width="50px">
                                                KSH {{ number_format($orderDetail->amount *  $orderDetail->quantity)}}</td>
                                            <td width="50px">
                                                <a href="{{route('admin.business.show',['business' => $orderDetail->productDetails->actualDetail->business->id ])}}">
                                                    {{$orderDetail->productDetails->actualDetail->business->name}}
                                                </a>
                                            </td>
                                        </tr>
                                    @else
                                        <tr>
                                            <td width="50px" scope="row">
                                                <div class="product-img-nane-wrapper" data-toggle="collapse"
                                                     data-target="#product-{{ $orderDetail->id }}">
                                                    <div class="img">
                                                        <img
                                                                src="{{ config('app.media_url') . $orderDetail->businessProductDetails->mainImagePath  }}"
                                                                alt="">
                                                    </div>
                                                    <div class="name">
                                                        <span>  {{ $orderDetail->businessProductDetails->name }}</span>
                                                        <span>(Track Your Order)</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td width="50px">
                                                KSH {{ number_format($orderDetail->amount) }}</td>
                                            <td width="50px">{{ $orderDetail->quantity }}</td>
                                            <td width="50px">
                                                KSH {{ number_format($orderDetail->amount *  $orderDetail->quantity)}}</td>
                                            <td width="50px">
                                                <a href="{{route('admin.business.show',['business' => $orderDetail->businessProductDetails->user->id ])}}">
                                                    {{$orderDetail->businessProductDetails->user->name}}
                                                </a>
                                            </td>
                                        </tr>

                                    @endif

                                    <tr class="product-tracker">
                                        <td scope="row">
                                            <div class="order-tracking-wrapper collapse"
                                                 id="product-{{ $orderDetail->id }}">
                                                <div class="title-wrapper">
                                                    <h4>
                                                        Order Tracking
                                                    </h4>
                                                </div>
                                                <div class="order-tracker-item-wrapper">
                                                    <ul id="progressbar">
                                                        <li class="step0 {{$orderDetail->order->order_status === 0 ? 'active' : ''}}"
                                                            id="step1">Placed
                                                        </li>
                                                        <li class="step0 {{$orderDetail->order->order_status === 1 ? 'active' : ''}}"
                                                            id="step2">Shipped
                                                        </li>
                                                        <li class="step0 {{$orderDetail->order->order_status == 2 ? 'active' : ''}}"
                                                            id="step3">Transit
                                                        </li>
                                                        <li class="step0 {{$orderDetail->order->order_status == 3 ? 'active' : ''}}"
                                                            id="step5">Delivered
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach

                                </tbody>
                            </table>

                            <div class="total-amount-wrapper">
                                <p class="subtotal">
                                    <span>Subtotal</span>
                                    <span>KSH {{ number_format($order->order_amount) }}</span>
                                </p>
                                <p class="shipping ">
                                    <span>Shipping </span>
                                    <span> KSH {{ number_format($order->shipping_amount) }}</span>
                                </p>
                                <p class="grand-total ">
                                    <span>Grand Total </span>
                                    <span>KSH {{ number_format($order->shipping_amount + $order->order_amount) }}</span>
                                </p>

                            </div>

                            <div class="address-wrapper">
                                <div class="row">
                                    <div class="col-md-4">
                                        <p>Transactions</p>
                                        @if($order->payment_status == Order::PAY_ON_DELIVERY)
                                            <div class="address-item payment-status">
                                                <p>Payment Method: Pay on deliver</p>
                                                <p>Transaction ID: --</p>
                                                <p>Payment Status: Waiting for payment</p>
                                            </div>
                                        @endif
                                        @foreach($order->transactions as $transaction)
                                            <div class="address-item payment-status">
                                                <p>Payment Method: {{$transaction->gatewayName}}</p>
                                                <p>Transaction ID: {{ $transaction->transaction_id ?? '--'}}</p>
                                                <p>Payment Status: {{$transaction->transactionStatus['title']}}</p>
                                            </div>
                                        @endforeach

                                    </div>
                                    <div class="col-md-4">
                                        <div class="address-item payment-status">
                                            <p>Shipping Address:</p>
                                            @if($order->shippingAddress)
                                                <p class="font-italic">Name: <span
                                                            class="font-normal">{{ $order->shippingAddress->contact_name }}</span>
                                                </p>
                                                <p class="font-italic">Address: <span
                                                            class="font-normal">{{ $order->shippingAddress->fullAddress }}</span>
                                                </p>
                                                <p class="font-italic">Phone: <span
                                                            class="font-normal">{{ $order->shippingAddress->phone_number }}</span>
                                                </p>
                                                @if($order->shippingAddress->latitude and $order->shippingAddress->longitude)
                                                    <p class="font-italic">Location:
                                                        <span class="font-normal">
                                                        <a href="https://www.google.com/maps?q={{ $order->shippingAddress->latitude }},{{ $order->shippingAddress->longitude }}"
                                                           target="_blank">
                                                            <img src="{{ asset('admin/images/icons/address.svg') }}"
                                                                 alt="">
                                                            Open
                                                        </a>
                                                        </span>
                                                    </p>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                    @if($order->delivery_instructions)
                                        <div class="col-md-4">
                                            <div class="address-item payment-status">
                                                <p>Delivery Instructions</p>
                                                <p class="font-italic">
                                                    <span class="font-normal">
                                                        {{ $order->delivery_instructions }}
                                                    </span>
                                                </p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- <div class="order-tracking-wrapper">
                              <div class="title-wrapper">
                                  <h4>
                                      Order Tracking
                                  </h4>
                              </div>
                              <div class="order-tracker-item-wrapper">
                                  <ul id="progressbar">
                                      <li class="step0 active " id="step1">Dispatch</li>
                                      <li class="step0 " id="step2">Shipped</li>
                                      <li class="step0 " id="step3">Transit</li>
                                      <li class="step0 " id="step4">Out for Delivery</li>
                                      <li class="step0 " id="step5">Delivered</li>
                                  </ul>
                              </div>
                            </div> -->
                        </div>
                    </div>
                @endif

                <!-- <div class="btn-wrapper">
                 <button class="white-bttn">
                    Cancel Order
                 </button>
                 <button class="purple-btn">
                    Dispatch Order
                 </button>
             </div> -->

                <!-- /page content -->
            </div>
        </div>
        @endsection
        @section('scripts')
            <script>
                function printDiv(divName) {
                    $('.invoice-print-btn-wrapper').hide();
                    var printContents = document.getElementById(divName).innerHTML;
                    var originalContents = document.body.innerHTML;

                    document.body.innerHTML = printContents;

                    window.print();
                    document.body.innerHTML = originalContents;
                    $('.invoice-print-btn-wrapper').show();

                }

                $('.invoice-print-btn-wrapper').show();

            </script>
@endsection
