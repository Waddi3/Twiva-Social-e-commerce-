@php use Modules\User\Entities\User; @endphp
@extends('admin::layouts.app')
@section('title')
    Twiva Influencers
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
                    <ul class="breadcrumb">
                        <li><a href="#">Influencers</a></li>
                    </ul>
                </div>
                @include('admin::layouts.message')
                <!--**********  Breadcrumb End ***********-->
                <div class="catalog-page">
                    <div class="select-top select-search">
                        <div class="search-wrap">
                            <div class="search-panel">
                                <img src="{{ asset('admin/images/icons/search.svg') }}">
                                <input type="text" placeholder="Search by name, email, or ID..." id="keywords">
                            </div>

                            <div class="button-catalog">
                            </div>
                        </div>

                        <div class="button-catalog">
                            <a href="{{ route('admin.influencers.create') }}">
                                <button class="purple-btn"><img src="{{ asset('admin/images/icons/plus.svg') }}">Create
                                    New
                                </button>
                            </a>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="admin-table" class="table table-striped table-bordered table-sm" cellspacing="0"
                               width="100%">
                            <thead>
                            <tr>
                                <th class="sorting" data-sorting_type="desc" data-column_name="id">ID<span
                                            id="id_icon"></span><span class="fa fa-angle-down"></span></th>
                                <th>NAME</th>
                                <th>Phone No.</th>
                                <th class="sorting" data-sorting_type="desc" data-column_name="email">EMAIL <span
                                            id="email_icon"></span><span class="fa fa-angle-down"></span></th>
                                <th>JOINED</th>
                                <th>ESHOP NAME</th>
                                <th>
                                    <p id="sort-by-product" class="">
                                        PRODUCT <span>-</span>
                                    </p>
                                    <p id="sort-by-product-count-down" class=""
                                       style="display:none">
                                        PRODUCT <span class="fa fa-angle-down"/>
                                    </p>
                                    <p id="sort-by-product-count-up" class=""
                                       style="display:none">
                                        PRODUCT <span class="fa fa-angle-up"/>
                                    </p>

                                </th>
                                <th>TOTAL SALES</th>
                                <th>This month sales</th>
                                <th>REDEEM PRICE PAYOUT</th>
                                <th>Published Posts</th>
                                <th>STATUS</th>
                                <th>SEND CREDENTIALS</th>
                                <th>Change Password</th>
                                <th>ACTION</th>
                                {{-- <th>Credentials Info</th> --}}
                            </tr>
                            </thead>
                            <tbody>
                            @include('admin::influencer.data')
                            </tbody>
                        </table>
                        <input type="hidden" name="hidden_page" id="hidden_page" value="1"/>
                        <input type="hidden" name="hidden_column_name" id="hidden_column_name" value="id"/>
                        <input type="hidden" name="hidden_sort_type" id="hidden_sort_type" value="desc"/>
                        <input type="hidden" name="hidden_status" id="hidden_status" value="2"/>
                        <input type="hidden" name="hidden_subscription" id="hidden_subscription" value="0"/>
                        <input type="hidden" name="hidden_type" id="hidden_type"
                               value="{{ User::InfluencerType }}"/>
                        <input type="hidden" name="hidden_eshop_status" id="hidden_eshop_status" value="2"/>
                    </div>

                </div>
            </div>
            <!-- /page content -->
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function () {
            $('#business-table').DataTable({
                "scrollX": true,
                "scrollY": false,
            });
            $('.dataTables_length').addClass('bs-select');
        });
    </script>
    <script src="{{ asset('admin/js/filter.js') }}"></script>
    <script src="{{ asset('admin/js/influencersFilter.js') }}"></script>

    <script>
        $(document).on('click', '.btn-login', function (e) {
            let user_id = $(this).data('id');
            e.preventDefault();
            $.ajax({
                type: "POST",
                dataType: "json",
                url: "/user/" + user_id + '/login-as',
                data: {
                    "_token": "{{ csrf_token() }}",
                },
                success: function (data) {
                    console.log(data);
                    window.open(data.url, '_blank');
                },
                error: function (data) {
                    console.log(data)
                }
            });
        });
    </script>
    <script>
        $(document).on('change', '.toggle-status', function (event) {
            event.preventDefault();
            let status = $(this).prop('checked') === true ? 1 : 0;
            let status_name = status ? 'Active' : 'Inactive';
            let check = confirm('Are you sure you want to ' + status_name);
            if (check === false) {
                status ? $(this).prop("checked", false) : $(this).prop("checked", true)
                return;
            }
            let user_id = $(this).data('id');
            $.ajax({
                type: "GET",
                dataType: "json",
                url: '/changeStatus',
                data: {'status': status, 'user_id': user_id},
                success: function (data) {
                    console.log(data.success)
                    if (status == 1) {
                        $('.eshop-status-' + user_id).removeClass('eshop-failed');
                        $('.eshop-status-' + user_id).addClass('eshop-active').html('<p>Active</p>')
                    } else {
                        $('.eshop-status-' + user_id).removeClass('eshop-active');
                        $('.eshop-status-' + user_id).addClass('eshop-failed').html('<p>Inactive</p>')
                    }
                }
            });
        })
    </script>
@endsection
