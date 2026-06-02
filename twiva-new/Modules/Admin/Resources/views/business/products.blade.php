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
                <ul class="breadcrumb breadcrumb-link">
                    <li><a href="{{ url()->previous() }}" class="back-link">Business ({{ $company->busi_name ?? '' }})</a></li>
                    <li><img src="{{ asset('admin/images/icons/gray-right-arrow.svg') }}" alt=""></li>
                    <li><a href="#">Products</a></li>
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
                    </div>

                    <div class="button-catalog">
{{--                        <a href="{{ route('exportBusinessProducts', $company->user_id) }}"><button class="white-bttn">Download CSV</button></a>--}}
                        {{-- <button class="purple-btn"> <img src="{{ asset('images/icons/plus.svg') }}">Add Product</button> --}}
                    </div>
                </div>
                <div class="alert alert-danger alert-block" style="display:none">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <span class="error"></span>
                </div>

                <div class="table-responsive">
                    <table id="admin-table" class="table table-striped table-bordered table-sm" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>IMAGE</th>
                                <th>PRODUCT ID</th>
                                <th>PRODUCT NAME</th>
                                <th>PRICE</th>
                                <th>RETAIL PRICE</th>
                                <th>SALES</th>
                                <th>UPDATED DATE</th>
                                <th>CONNECTED ESHOP</th>
                                <th>RATING</th>
                                <th>STOCK</th>
                                <th>STATUS</th>
                                <th>ACTIONS</th>
                            </tr>
                        </thead>

                        <tbody>
                            {{-- <tr>
                                <td><img src="./images/card-images/rect.jpg"></td>
                                <td>2972</td>
                                <td>Canon EOS 80D 24.2MP Digital SLR Camera</td>
                                <td>$250.00 </td>
                                <td>40 QTY</td>
                                <td>5/27/15</td>
                                <td>40 eshop</td>
                                <td class="t-rating">3.5 <img src="{{ asset('images/icons/star.svg') }}"></td>
                                <td class="eshop-active">
                                    <p>In Stock</p>
                                </td>
                                <td class="t-action">
                                    <img src="{{ asset('images/icons/Frame7158.svg') }}" alt="">
                                    <!-- Checked switch -->
                                    <label class="switch">
                                        <input type="checkbox" checked>
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                            </tr> --}}
                            @include('admin::business.productsData')
                        </tbody>
                    </table>
                            <input type="hidden" name="hidden_user_id" id="hidden_user_id" value="{{ $company->user_id ?? ''}}">

                </div>

                <div class="pagination">
                    <div class="col-sm-5 p-0">
                        <!-- <div class="dataTables_info" id="example_info" role="status" aria-live="polite">Showing 1 to 10 of 57 entries</div></div> -->
                        <div class="col-sm-7 p-0">
                            <div class="dataTables_paginate paging_simple_numbers" id="example_paginate">
                                {{ $products->links('admin::layouts.pagination') }}
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
    function fetchDataProducts(query, user_id) {
        $.ajax({
            url : "/business/product/fetch_data?query="+query+"&id="+user_id,
            success : function(data) {
                // console.log(data);
                $('tbody').html('');
                $('tbody').html(data);
            },
            error : function (data) {
                console.log(data);
            }
        })
    }
    $(document).on('keyup', '#keywords', function() {
        var query = $('#keywords').val();
        var user_id = $('#hidden_user_id').val();
        console.log(query, user_id);
        fetchDataProducts(query, user_id);
    })
});
</script>
<script>
$( document ).ready(function() {

    $(document).on('change', '.toggle-status', function(event) {
        // Your function here
        event.preventDefault();
        var status = $(this).prop('checked') == true ? 1 : 0;
        var status_name = status ? 'Active' : 'Inactive';
        let check = confirm('Are you sure you want to '+ status_name);
        if(check == false) {
            status ? $(this).prop( "checked", false ) :  $(this).prop( "checked", true )
            return;
        }
        // $(this).parent().next('.category-item-content').toggle();
        var status = $(this).prop('checked') == true ? 1 : 0;
        var product_id = $(this).data('id');
        var user_id = $('#hidden_user_id').val();

        $.ajax({
            type: "GET",
            dataType: "json",
            url: '/changeBusinessProductStatus',
            data: {'status': status,
                    'user_id' : user_id,
                    'product_id': product_id},
            success: function(data){
                console.log(data.success)
                if(status == 1) {
                    $('.eshop-status-'+product_id).removeClass('eshop-failed');
                    $('.eshop-status-'+product_id).addClass('eshop-active').html('<p>Active</p>')
                } else {
                    $('.eshop-status-'+product_id).removeClass('eshop-active');
                    $('.eshop-status-'+product_id).addClass('eshop-failed').html('<p>Inactive</p>')
                }
            },
            error: function(data) {
                $(".error").html(data.responseJSON.message);
                $(".alert-block").show();
                // console.log($(this).is( ":checked" ));
                // ($(this).is( ":checked" ) ? $(this).prop("checked", false ) :  $(this).prop("checked", true ));
                $(this).setAttribute("checked", "");
            }
        });
    });
});
</script>
@endsection
