@php use Modules\User\Entities\User; @endphp
@extends('admin::layouts.app')
@section('title')
    Business Users
@endsection
@section('head')
    <style>
        .white-bttn {
            cursor: pointer;
        }
    </style>
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
                        <li><a href="#">Business</a></li>
                    </ul>
                </div>
                <!--**********  Breadcrumb End ***********-->
                @include('admin::layouts.message')
                <div class="catalog-page">
                    <div class="select-top select-search">
                        <div class="search-wrap">
                            <div class="search-panel">
                                <img src="{{ asset('admin/images/icons/search.svg') }}">
                                <input type="text" placeholder="Search by name, email, or ID..." id="keywords">
                                {{-- <input type="text" placeholder="Search by name, email, or ID..." id="keywords" onkeyup="searchFilter();"> --}}
                            </div>
                            <select name="status" id="status" style="width: 250px" class="dropdown-toggle">
                                <option value="2">All Status</option>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                            <div class="button-catalog">
                            </div>
                        </div>

                        <div class="button-catalog">
                            <a href="{{asset('admin/assets/template/Twiva_Bulk_Upload_Product_Template.xlsx')}}">
                                <button class="white-bttn">
                                    Bulk template
                                </button>
                            </a>
                            <a href="{{ route('admin.business.create') }}">
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
                                <th>BUSINESS NAME</th>
                                <th>CONTACT PERSON</th>
                                <th>CONTACT NO</th>
                                <th>EMAIL</th>
                                <th>JOINED</th>
                                <th>ESHOP NAME</th>
                                <th>PRODUCTS</th>
                                <th>TOTAL SALES</th>
                                <th>This month sales</th>
                                <th>REDEEM PRICE PAYOUT</th>
                                <th>STATUS</th>
                                <th>INFLUENCER</th>
                                <th>SEND CREDENTIALS</th>
                                <th>Change Password</th>
                                <th>Bulk product upload</th>
                                <th>Waive payment</th>
                                <th>accessing earnings prior to delivery</th>
                                <th>ACTION</th>
                                {{-- <th>Credentials Info</th> --}}
                            </tr>
                            </thead>

                            <tbody id="businessUsers">
                            @include('admin::business.data')
                            </tbody>

                        </table>
                        <input type="hidden" name="hidden_page" id="hidden_page" value="1"/>
                        <input type="hidden" name="hidden_column_name" id="hidden_column_name" value="id"/>
                        <input type="hidden" name="hidden_sort_type" id="hidden_sort_type" value="desc"/>
                        <input type="hidden" name="hidden_status" id="hidden_status" value="2"/>
                        <input type="hidden" name="hidden_subscription" id="hidden_subscription" value="0"/>
                        <input type="hidden" name="hidden_type" id="hidden_type"
                               value="{{ User::BusinessType }}"/>
                        <input type="hidden" name="hidden_eshop_status" id="hidden_eshop_status" value="2"/>
                    </div>


                </div>
            </div>
            <!-- /page content -->
        </div>
        <form id="bulk-upload-form" method="POST" action="{{route('admin.business.product.bulk.upload')}}"
              enctype="multipart/form-data">
            <input type="file" name="file" id="bulk-upload" accept="application/zip"/>
            <input type="hidden" name="business-id" id="business-id" value=""/>
        </form>
    </div>
    <div id="overlay" class="hidden">
        {{--<span></span>--}}
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
    <script>
        function exportBusiness(_this) {
            let _url = $(_this).data('href');
            window.location.href = _url;
        }
    </script>
    <script>
        $(document).on('click', '.btn-login', function (e) {
            var user_id = $(this).data('id');
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
    <script src="{{ asset('admin/js/filter.js') }}"></script>
    <script>
        $(document).on('change', '.toggle-status', function (event) {
            event.preventDefault();

            var status = $(this).prop('checked') == true ? 1 : 0;
            var status_name = status ? 'Active' : 'Inactive';
            let check = confirm('Are you sure you want to ' + status_name);
            if (check == false) {
                status ? $(this).prop("checked", false) : $(this).prop("checked", true)
                return;
            }
            // Your function here
            // $(this).parent().next('.category-item-content').toggle();
            var status = $(this).prop('checked') == true ? 1 : 0;
            var user_id = $(this).data('id');

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
        });
    </script>
    <script>
        $(document).on('change', '.toggle-waive-prompt', function (event) {
            event.preventDefault();

            var status = $(this).prop('checked') == true ? 1 : 0;
            var status_name = status ? 'Active' : 'Inactive';
            let check = confirm('Are you sure');
            if (check == false) {
                status ? $(this).prop("checked", false) : $(this).prop("checked", true)
                return;
            }
            var status = $(this).prop('checked') == true ? 1 : 0;
            var user_id = $(this).data('id');

            $.ajax({
                type: "GET",
                dataType: "json",
                url: `{{ route('admin.business.activation.waive.prompt') }}`,
                data: {'status': status, 'user_id': user_id},
                success: function (data) {
                    alert('Done');
                },
                error: function (data) {
                    $(this).prop(!status);
                    alert('Error, please check your data!');
                }
            });
        });

        $(document).on('click', '.bulk-upload', function (e) {
            e.preventDefault();
            let businessId = $(this).attr('data-id');
            $('#business-id').val(businessId);
            console.log($(this));
            $('#bulk-upload').trigger('click');
        });

        $('#bulk-upload').change(function (e) {
            $('#bulk-upload-form').submit();
        });
    </script>
    <script>
        $(document).on('change', '.toggle-accessing-earnings-prior-to-delivery', function (event) {
            event.preventDefault();

            var status = $(this).prop('checked') == true ? 1 : 0;
            var status_name = status ? 'Active' : 'Inactive';
            let check = confirm('Are you sure');
            if (check == false) {
                status ? $(this).prop("checked", false) : $(this).prop("checked", true)
                return;
            }
            var status = $(this).prop('checked') == true ? 1 : 0;
            var user_id = $(this).data('id');

            $.ajax({
                type: "GET",
                dataType: "json",
                url: `{{ route('admin.business.activation.toggle.accessing.earnings.prior.to.delivery') }}`,
                data: {'status': status, 'user_id': user_id},
                success: function (data) {
                    alert('Done');
                },
                error: function (data) {
                    $(this).prop(!status);
                    alert('Error, please check your data!');
                }
            });
        });
    </script>
@endsection
