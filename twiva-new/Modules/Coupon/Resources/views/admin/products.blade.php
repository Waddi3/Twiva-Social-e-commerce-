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
                    <li><a href="{{ url()->previous() }}" class="back-link">Influencer ({{$user->shop->shop_name}})</a></li>
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
                            <input type="hidden" name="hidden_user_id" id="hidden_user_id" value="{{ $user->id }}">
                        </div>
                    </div>

                    <div class="button-catalog">
{{--                        <a href="{{ route('exportInfluencerProducts', $user->id) }}"><button class="white-bttn">Download CSV</button></a>--}}
                        {{-- <button class="purple-btn"> <img src="{{ asset('images/icons/plus.svg') }}">Add Product</button> --}}
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="admin-table" class="table table-striped table-bordered table-sm" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>IMAGE</th>
                                <th>PRODUCT ID</th>
                                <th>PRODUCT NAME</th>
                                <th>PRICE</th>
                                <th>SALES</th>
                                <th>UPDATED DATE</th>
                                {{-- <th>CONNECTED ESHOP</th> --}}
                                <th>RATING</th>
                                <th>STOCK</th>
                                <th>STATUS</th>
                                <th>ACTIONS</th>
                            </tr>
                        </thead>

                        <tbody>

                            @include('admin::influencer.productsData')
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
<script>

$(document).ready(function() {
    function fetchDataProducts(query, user_id) {
        $.ajax({
            url : "/influencer/product/fetch_data?query="+query+"&id="+user_id,
            success : function(data) {
                console.log(data);
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

        var product_id = $(this).data('id');

        $.ajax({
            type: "GET",
            dataType: "json",
            url: '/changeInfluencerProductStatus',
            data: {'status': status, 'product_id': product_id},
            success: function(data){
                console.log(data.success)
                if(status == 1) {
                    $('.eshop-status-'+product_id).removeClass('eshop-failed');
                    $('.eshop-status-'+product_id).addClass('eshop-active').html('<p>Active</p>')
                } else {
                    $('.eshop-status-'+product_id).removeClass('eshop-active');
                    $('.eshop-status-'+product_id).addClass('eshop-failed').html('<p>Inactive</p>')
                }
            }
        });
    });
</script>
@endsection

