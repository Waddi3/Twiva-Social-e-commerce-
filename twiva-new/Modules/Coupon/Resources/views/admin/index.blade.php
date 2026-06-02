@extends('admin::layouts.app')
@section('title')
    Twiva Coupons
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
                        <li><a href="#">Coupons</a></li>
                    </ul>
                </div>
                @include('admin::layouts.message')
                <!--**********  Breadcrumb End ***********-->
                <div class="catalog-page">
                    <div class="select-top select-search">
                        <div class="search-wrap">
                            <div class="search-panel">
                                <img src="{{ asset('admin/images/icons/search.svg') }}">
                                <input type="text" placeholder="Search by name, code, or ID..." id="keywords" value="{{ request('query')}}">
                            </div>

                            <div class="button-catalog">
                            </div>
                        </div>

                        <div class="button-catalog">
                            <a href="{{ route('admin.coupons.create') }}">
                                <button class="purple-btn"><img src="{{ asset('admin/images/icons/plus.svg') }}">
                                    Create New
                                </button>
                            </a>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="admin-table" class="table table-striped table-bordered table-sm" cellspacing="0"
                            width="100%">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>NAME</th>
                                    <th>code</th>
                                    <th>max usage</th>
                                    <th>all use</th>
                                    <th>type</th>
                                    <th>amount</th>
                                    <th>expiry_at</th>
                                    <th>Active</th>
                                    <th>bypass the minimum</th>
                                    <th>ACTION</th>
                                </tr>
                            </thead>
                            <tbody>
                                @include('coupon::admin.data')
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
            <!-- /page content -->
        </div>
    </div>
@endsection
@section('scripts')
    {{-- <script src="{{ asset('admin/js/filter.js') }}"></script> --}}


    <script>
        $(document).on('keyup', '#keywords', function() {
            let query = $('#keywords').val();
            let additionalParams = {
                'page': 1,
                'query': query
            }
            let route = `{{ route('admin.coupons.index') }}`
            let url = route + '?' + $.param(additionalParams);
            history.pushState(null, '', url);
            $.ajax({
                url: url,
                success: function(data) {
                    $('tbody').html('');
                    $('tbody').html(data);
                }
            })
        })
    </script>

    <script>
        $(document).on('change', '.toggle-activation', function(event) {
            event.preventDefault();
            let activation = $(this).prop('checked') === true ? 1 : 0;
            let status_name = activation ? 'Active' : 'Inactive';
            let check = confirm('Are you sure you want to ' + status_name);
            if (check === false) {
                activation ? $(this).prop("checked", false) : $(this).prop("checked", true)
                return;
            }
            let coupon_id = $(this).data('id');
            $.ajax({
                type: "GET",
                dataType: "json",
                url: `{{ route('admin.coupons.activation') }}`,
                data: {
                    'active': activation,
                    'coupon_id': coupon_id
                },
                success: function(data) {
                    // if (status == 1) {
                    //     $('.eshop-status-' + user_id).removeClass('eshop-failed');
                    //     $('.eshop-status-' + user_id).addClass('eshop-active').html('<p>Active</p>')
                    // } else {
                    //     $('.eshop-status-' + user_id).removeClass('eshop-active');
                    //     $('.eshop-status-' + user_id).addClass('eshop-failed').html('<p>Inactive</p>')
                    // }
                }
            });
        })
    </script>

    <script>
        $(document).on('change', '.toggle-bypass-the-minimum', function(event) {
            event.preventDefault();
            let status = $(this).prop('checked') === true ? 1 : 0;
            let status_name = status ? 'Active' : 'Inactive';
            let check = confirm('Are you sure you want to ' + status_name);
            if (check === false) {
                status ? $(this).prop("checked", false) : $(this).prop("checked", true)
                return;
            }
            let coupon_id = $(this).data('id');
            $.ajax({
                type: "GET",
                dataType: "json",
                url: `{{ route('admin.coupons.toggle.bypass.the.minimum') }}`,
                data: {
                    'status': status,
                    'coupon_id': coupon_id
                },
                success: function(data) {
                    // if (status == 1) {
                    //     $('.eshop-status-' + user_id).removeClass('eshop-failed');
                    //     $('.eshop-status-' + user_id).addClass('eshop-active').html('<p>Active</p>')
                    // } else {
                    //     $('.eshop-status-' + user_id).removeClass('eshop-active');
                    //     $('.eshop-status-' + user_id).addClass('eshop-failed').html('<p>Inactive</p>')
                    // }
                }
            });
        })
    </script>
@endsection
