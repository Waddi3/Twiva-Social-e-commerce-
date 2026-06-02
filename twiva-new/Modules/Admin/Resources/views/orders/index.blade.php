@extends('admin::layouts.app')
@section('title')
    Orders
@endsection
@section('content')
    <div class="" id="business-dashboard">
        <div class="dashboard_container">

            <div class="left_col">
                <div class="scroll-view">
                    <div class="clearfix"></div>

                    <!-- sidebar menu -->
                @include('admin::layouts.sidebar')
                <!-- /sidebar menu -->
                </div>
            </div>
            <!-- page content -->
            <div class="right_col dashboard-page" role="main">

                <!--********** Breadcrumb Start ***********-->
                <div class="page-title breadcrumb-wrapper">
                    <ul class="breadcrumb">
                        <li><a href="#">Orders</a></li>
                    </ul>
                </div>
                <!--**********  Breadcrumb End ***********-->


                <div class="catalog-page">
                    <div class="select-top select-search">
                        <div class="search-wrap">
                            <div class="search-panel">
                                <img src="{{ asset('admin/images/icons/search.svg') }}">
                                <input type="text" placeholder="Search by name, email, or ID..." id="keywords">
                            </div>
                            <select name="status" id="status" style="width: 250px" class="dropdown-toggle">
                                <option value="9">All Status</option>
                                @foreach($statues as $key=>$status)
                                    <option value="{{$key}}">{{$status}}</option>
                                @endforeach
                            </select>
                        </div>
                        <a href="{{route('admin.products.downloadCampaignsOrderCsv')}}" class="white-bttn w-full mr-2 py-3 px-5"
                           style="cursor: pointer">
                            Download campaign's order CSV
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table id="admin-table" class="table table-striped table-bordered table-sm" cellspacing="0"
                               width="100%">
                            <thead>
                            <tr>
                                <th class="sorting" data-sorting_type="asc" data-column_name="id">ID<span
                                            id="id_icon"></span><span class="fa fa-angle-down"></span></th>
                                <th>BUYER</th>
                                <th>INFLUENCER</th>
                                <th>BUSINESS</th>
                                <th>PRODUCT</th>
                                <th class="sorting" data-sorting_type="asc" data-column_name="order_amount">AMOUNT <span
                                            id="order_amount_icon"></span><span class="fa fa-angle-down"></span></th>
                                <th>DATED</th>
                                <th>LAST UPDATED</th>
                                <th>TRANSACTION STATUS</th>
                                <th>STATUS</th>
                                <th>ACTION</th>
                            </tr>
                            </thead>

                            <tbody>
                            @include('admin::orders.data')
                            </tbody>
                        </table>
                        <input type="hidden" name="hidden_page" id="hidden_page" value="1"/>
                        <input type="hidden" name="hidden_column_name" id="hidden_column_name" value="id"/>
                        <input type="hidden" name="hidden_sort_type" id="hidden_sort_type" value="asc"/>
                        <input type="hidden" name="hidden_status" id="hidden_status" value="9"/>
                        <input type="hidden" name="hidden_page_name" id="hidden_page_name" value="orders"/>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Modal -->
    <div class="modal fade" id="delete-product" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <div class="title">
                        <h4>
                            Update Subscription?
                        </h4>
                    </div>
                    <p>The subscription will be active immidiately and
                        will be charged accordingly in the next cycle. </p>
                </div>

                <div class="modal-footer ">
                    <button type="button" class="c-btn close-btn " data-dismiss="modal">Close</button>
                    <button class="purple-btn c-btn">Update</button>
                </div>
            </div>

        </div>
    </div>

    <!--End Modal -->
@endsection
@section('scripts')
    <script src="{{ asset('admin/js/orderfilter.js') }}"></script>
@endsection
