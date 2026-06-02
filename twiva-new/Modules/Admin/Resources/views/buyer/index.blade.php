@extends('admin::layouts.app')
@section('title')
    Twiva Buyers
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
                        <li><a href="#">Buyer</a></li>
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
                                <option value="2">All Status</option>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>


                        </div>

                    </div>

                    <div class="table-responsive">
                        <table id="admin-table" class="table table-striped table-bordered table-sm" cellspacing="0"
                               width="100%">
                            <thead>
                            <tr>
                                <th class="sorting" data-sorting_type="desc" data-column_name="id">ID<span
                                        id="id_icon"></span><span class="fa fa-angle-down"></span></th>
                                <th class="th-sm">NAME</th>
                                <th class="sorting" data-sorting_type="desc" data-column_name="email">EMAIL <span
                                        id="email_icon"></span><span class="fa fa-angle-down"></span></th>
                                <th class="th-sm">Mobile</th>
                                <th class="th-sm">JOINED</th>
                                <th class="th-sm">ORDERS</th>
                                <th class="th-sm">ORDER VALUE</th>
                                <th class="th-sm">STATUS</th>
                                <th class="th-sm">Class</th>
                                <th class="th-sm">Rep Name</th>
                                <th class="th-sm">retail store name</th>
                                <th class="th-sm">Rep Mobile</th>
                                <th class="th-sm">ACTION</th>
                            </tr>
                            </thead>

                            <tbody>
                            @include('admin::buyer.data')
                            </tbody>
                        </table>
                        <input type="hidden" name="hidden_page" id="hidden_page" value="1"/>
                        <input type="hidden" name="hidden_column_name" id="hidden_column_name" value="id"/>
                        <input type="hidden" name="hidden_sort_type" id="hidden_sort_type" value="desc"/>
                        <input type="hidden" name="hidden_status" id="hidden_status" value="2"/>
                        <input type="hidden" name="hidden_type" id="hidden_type" value="Buyer"/>
                        <input type="hidden" name="hidden_eshop_status" id="hidden_eshop_status" value="2"/>
                    </div>


                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="{{ asset('admin/js/filter.js') }}"></script>
    <script>
        $(document).on('change', '.toggle-status', function (event) {
            // Your function here
            event.preventDefault();
            var status = $(this).prop('checked') == true ? 1 : 0;
            var status_name = status ? 'Active' : 'Inactive';
            let check = confirm('Are you sure you want to ' + status_name);
            if (check == false) {
                status ? $(this).prop("checked", false) : $(this).prop("checked", true)
                return;
            }
            // $(this).parent().next('.category-item-content').toggle();
            var user_id = $(this).data('id');

            $.ajax({
                type: "GET",
                dataType: "json",
                url: "{{route('admin.buyers.toggleActiveStatus')}}",
                data: {'status': status, 'buyer_id': user_id},
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
        });
    </script>
@endsection
