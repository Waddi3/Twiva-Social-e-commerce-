@extends('admin::layouts.app')
@section('title')
Business Users
@endsection
@section('content')
<div class="" id="business-dashboard">
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
                    <li><a href="{{ url()->previous() }}" class="back-link">Business</a></li>
                    <li><img src="{{ asset('admin/images/icons/gray-right-arrow.svg') }}" alt=""></li>
                    <li><a href="#">Connected Influencer</a></li>
                </ul>
            </div>
            <!--**********  Breadcrumb End ***********-->

            <div class="catalog-page">
                    <div class="select-top select-search">
                        <div class="search-wrap">
                            <div class="search-panel">
                                <img src="{{ asset('admin/images/icons/search.svg') }}">
                                <input type="text" placeholder="Search by name, email, or ID...">
                            </div>
                        </div>
                    </div>

                <div class="table-responsive">
                    <table id="admin-table" class="table table-striped table-bordered table-sm" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>ESHOP</th>
                                <th>INFLUENCER</th>
                                <th>PRODUCT ADDED</th>
                                <th>SALE</th>

                            </tr>
                        </thead>

                        <tbody>
                            {{-- <tr>
                                <td>234235</td>
                                <td class="eshop-link">
                                    <p>Barone LLC.</p>
                                    <a href="">http://www.konmatfix.com</a>
                                </td>
                                <td>Albert Flores</td>
                                <td>30 Product</td>
                                <td>40 ($250.00)</td>
                            </tr> --}}
                            @foreach ($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td class="eshop-link">
                                    <p>{{ $user->shop->shop_name }}</p>
                                    {{-- <a href="">http://www.konmatfix.com</a> --}}
                                </td>
                                <td><a href="{{ route('admin.influencers.show', $user->id) }}">{{ $user->influencerDetails->name ?? 'NA' }}</a></td>
                                <td>{{ $user->influencerShopProducts ? count($user->influencerShopProducts) : 0}} Product</td>
                                <td>{{ count($user->businessTotalSales) }} (KSH {{number_format($user->businessTotalSales->sum('amount')) }})</td>
                            </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>

                <div class="pagination">
                    <div class="col-sm-5 p-0">
                        <!-- <div class="dataTables_info" id="example_info" role="status" aria-live="polite">Showing 1 to 10 of 57 entries</div></div> -->
                        <div class="col-sm-7 p-0">
                            <div class="dataTables_paginate paging_simple_numbers" id="example_paginate">
                                {{ $users->links('admin::layouts.pagination') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /page content -->
    </div>
</div>

@endsection
@section('scripts')
<script>
    $(document).ready(function() {
       $('#business-table').DataTable({
       "scrollX": true,
       "scrollY": false,
       });
       $('.dataTables_length').addClass('bs-select');
   });
</script>


@endsection
