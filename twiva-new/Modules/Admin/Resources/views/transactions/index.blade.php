@extends('admin::layouts.app')
@section('title')
    Twiva Transactions
@endsection
@section('content')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    {{--    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>--}}
    {{--    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>--}}
    <div class="" id="business-dashboard">
        <div class="dashboard_container">

            <!-- Left Column Start -->
            <div class="left_col">
                <div class="scroll-view">
                    <div class="clearfix"></div>

                    <!-- Sidebar menu -->
                @include('admin::layouts.sidebar')
                <!-- /Sidebar menu -->
                </div>
            </div>
            <!-- Left Column End -->


            <!-- page content -->
            <div class="right_col dashboard-page" role="main">

                <!--********** Breadcrumb Start ***********-->
                <div class="page-title breadcrumb-wrapper">
                    <ul class="breadcrumb">
                        <li><a href="#">Transaction</a></li>
                    </ul>
                </div>
                <!--**********  Breadcrumb End ***********-->

                <div class="catalog-page">
                    <div class="select-top select-search">
                        <div class="search-wrap">
                            <div class="search-panel" style="min-width: 310px !important;">
                                <img src="{{asset('admin/images/icons/search.svg')}}">
                                <input type="text" placeholder="Search by user name ....." id="keywords">
                            </div>

                            <div class="button-catalog">
                                <div class="drop-btn">
                                    <div class="select-btn p-0" style="width: 160px; border:0;">
                                        <select name="transaction_type" id="transaction_type"
                                                class="dropdown-toggle m-0" style="max-width:100%;width:100%;">
                                            <option value="">All Type</option>
                                            @foreach($transactionTypes as $value => $transactionType)
                                                <option value="{{$value}}">{{ $transactionType  }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="drop-btn">
                                    <div class="select-btn p-0" style="width: 160px; border:0;">
                                        <select name="transaction_status" id="transaction_status"
                                                class="dropdown-toggle m-0" style="max-width:100%;width:100%;">
                                            <option value="">All Status</option>
                                            @foreach($transactionStatuses as $value => $status)
                                                <option value="{{$value}}">{{ $status  }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

{{--                                <div class="datepicker-wrapper">--}}
{{--                                    <input type="text" name="daterange" value=""/>--}}
{{--                                </div>--}}
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="admin-table" class="table table-striped table-bordered table-sm" cellspacing="0"
                               width="100%">
                            <thead>
                            <tr>
                                <th class="sorting" data-sorting_type="desc" data-column_name="id">ID <span
                                            class="fa fa-angle-down"></span></th>
                                <th>TYPE</th>
                                <th class="sorting" data-sorting_type="asc" data-column_name="transaction_amount">AMOUNT
                                    <span class="fa fa-angle-down"></span></th>
                                <th>SENT / RECEIVED</th>
                                <th>TO / FROM</th>
                                <th>DATE</th>
                                <th>TIME</th>
                                <th>STATUS</th>
                            </tr>
                            </thead>

                            <tbody>
                            @include('admin::transactions.data')
                            </tbody>
                        </table>
                        <input type="hidden" name="hidden_page" id="hidden_page" value="1"/>
                        <input type="hidden" name="hidden_column_name" id="hidden_column_name" value="id"/>
                        <input type="hidden" name="hidden_sort_type" id="hidden_sort_type" value="desc"/>
                        <input type="hidden" name="hidden_page_name" id="hidden_page_name" value="transactions"/>
                        <input type="hidden" name="hidden_transaction_type" id="hidden_transaction_type" value=""/>
                        <input type="hidden" name="hidden_transaction_status" id="hidden_transaction_status" value=""/>
                    </div>
                </div>
            </div>
            <!-- /page content -->
        </div>
    </div>
    <script>
        let calender = $('input[name="daterange"]');
        calender.daterangepicker('setDate', 'today');
        $(calender).on('change', function () {
        });
    </script>

@endsection
@section('scripts')
    <script src="{{ asset('admin/js/transaction_filter.js') }}"></script>

@endsection
