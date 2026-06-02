@extends('admin::layouts.app')
@section('title')
    Sub Categories
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
                        <li><a href="#">Sub Categories</a></li>
                    </ul>
                </div>
                <!--**********  Breadcrumb End ***********-->


                <div class="catalog-page">
                    <div class="select-top select-search">
                        <div></div>
                        <div class="button-catalog">
                            <a href="{{ route('admin.subcategory.create') }}">
                                <button class="purple-btn">
                                    <img src="{{ asset('admin/images/icons/plus.svg') }}">
                                    Sub Category
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
                                    <th class="th-sm">NAME</th>
                                    <th>Active</th>
                                    <th>ACTION</th>
                                </tr>
                            </thead>

                            <tbody>
                                @include('admin::categories.subcategory-data')
                            </tbody>
                        </table>
                    </div>


                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).on('change', '.toggle-activation', function(event) {
            event.preventDefault();
            var status = $(this).prop('checked') == true ? 1 : 0;
            var status_name = status ? 'Active' : 'Inactive';
            let check = confirm('Are you sure you want to ' + status_name);
            if (check == false) {
                status ? $(this).prop("checked", false) : $(this).prop("checked", true)
                return;
            }
            var category_id = $(this).data('id');

            $.ajax({
                type: "GET",
                dataType: "json",
                url: "{{ route('admin.subcategory.activation') }}",
                data: {
                    'status': status,
                    'subcategory_id': category_id
                },
                success: function(data) {
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
