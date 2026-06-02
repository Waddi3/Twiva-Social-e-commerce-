@extends('admin::layouts.app')
@section('title')
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
                        <li><a href="#">Products</a></li>
                    </ul>
                </div>
                <!--**********  Breadcrumb End ***********-->
                <div class="catalog-page">

                    <div class="select-top select-search">
                        <div class="search-wrap">
                            <div class="search-panel">
                                <img src="{{ asset('admin/images/icons/search.svg') }}">
                                <input type="text" placeholder="Search by name, email, or ID, or brand..."
                                    id="keywords">
                            </div>

                            <select name="status" id="status" style="width: 250px" class="dropdown-toggle">
                                <option value="2">All Status</option>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>

                            <select name="rating" id="rating" style="width: 250px" class="dropdown-toggle">
                                <option value="0">All Rating</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                            </select>

                            <select name="category" id="category" style="width: 250px" class="dropdown-toggle">
                                <option value="0">All Categories</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>


                        </div>



                    </div>
                    <div class="alert alert-danger alert-block" style="display:none">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        <span class="error"></span>
                    </div>

                    <div class="table-responsive">
                        <table id="admin-table" class="table table-striped table-bordered table-sm" cellspacing="0"
                            width="100%">
                            <thead>
                                <tr>
                                    <th class="th-sm">IMAGE</th>
                                    <th class="th-sm">PRODUCT ID</th>
                                    <th class="th-sm">PRODUCT NAME</th>
                                    <th class="th-sm">BRAND NAME</th>
                                    <th class="th-sm">BUSINESS</th>
                                    <th class="th-sm">PRICE</th>
                                    <th class="th-sm">RETAIL PRICE</th>
                                    <th class="th-sm">SALES</th>
                                    <th class="th-sm">UPDATED DATE</th>
                                    <th class="th-sm">RATING</th>
                                    <th class="th-sm">STOCK</th>
                                    <th class="th-sm">STATUS</th>
                                    <th class="th-sm">Is Trending</th>
                                    <th class="th-sm">ACTIONS</th>

                                </tr>
                            </thead>

                            <tbody>
                                @include('admin::products.data')
                            </tbody>
                        </table>

                        <input type="hidden" name="hidden_page" id="hidden_page" value="1" />
                        <input type="hidden" name="hidden_column_name" id="hidden_column_name" value="id" />
                        <input type="hidden" name="hidden_sort_type" id="hidden_sort_type" value="asc" />
                        <input type="hidden" name="hidden_status" id="hidden_status" value="2" />
                        <input type="hidden" name="hidden_page_name" id="hidden_page_name" value="products" />
                        <input type="hidden" name="hidden_rating" id="hidden_rating" value="0" />
                        <input type="hidden" name="hidden_category" id="hidden_category" value="0" />


                    </div>

                </div>
            </div>
            <!-- /page content -->
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).on('change', '.toggle-status', function(event) {
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
            var status = $(this).prop('checked') == true ? 1 : 0;
            var product_id = $(this).data('id');
            var user_id = $(this).data('user_id')

            $.ajax({
                type: "GET",
                dataType: "json",
                url: '/changeBusinessProductStatus',
                data: {
                    'status': status,
                    'user_id': user_id,
                    'product_id': product_id
                },
                success: function(data) {
                    console.log(data.success)
                    if (status == 1) {
                        $('.eshop-status-' + product_id).removeClass('eshop-failed');
                        $('.eshop-status-' + product_id).addClass('eshop-active').html('<p>Active</p>')
                    } else {
                        $('.eshop-status-' + product_id).removeClass('eshop-active');
                        $('.eshop-status-' + product_id).addClass('eshop-failed').html(
                            '<p>Inactive</p>')
                    }
                },
                error: function(data) {
                    $(".error").html(data.responseJSON.message);
                    $(".alert-block").show();
                    // console.log($(this).is( ":checked" ));
                    ($(this).is(":checked") ? $(this).prop("checked", false) : $(this).prop("checked",
                        true));
                }
            });
        });
    </script>
    <script>
        $(document).on('change', '.toggle-trending', function(event) {
            // Your function here
            event.preventDefault();
            // var trending = $(this).prop('checked') == true ? 1 : 0;
            // let check = confirm('Are you sure?');
            // if (check == false)
            // {
            //     trending ? $(this).prop("checked", false) : $(this).prop("checked", true)
            //     return;
            // }
            var trending = $(this).prop('checked') == true ? 1 : 0;
            var product_id = $(this).data('id');

            $.ajax({
                type: "GET",
                dataType: "json",
                url: '/changeTrendingProductStatus',
                data: {
                    'trending': trending,
                    'product_id': product_id
                },
                success: function(data) {
                    // console.log(data.success)
                    // alert('Done');
                },
                error: function(data) {
                    // alert('Fail');
                    // $(".error").html(data.responseJSON.message);
                    // $(".alert-block").show();
                    // // console.log($(this).is( ":checked" ));
                    // ($(this).is(":checked") ? $(this).prop("checked", false) : $(this).prop("checked", true));
                }
            });
        });
    </script>
    <script src="{{ asset('admin/js/productfilter.js') }}"></script>
@endsection
