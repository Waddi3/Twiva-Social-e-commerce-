@php use Modules\System\Entities\MobileVersion; @endphp
@extends('admin::layouts.app')
@section('title')
    Mobile Versions
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
                        <li><a href="#">Mobile Versions</a></li>
                    </ul>
                </div>
                <!--**********  Breadcrumb End ***********-->
                @include('admin::layouts.message')
                <div class="catalog-page">
                    <div class="select-top select-search">
                        <div class="search-wrap">
                            <div class="search-panel">
                                <img src="{{ asset('admin/images/icons/search.svg') }}">
                                <input type="text" placeholder="Search ..." id="keywords">
                                {{-- <input type="text" placeholder="Search by name, email, or ID..." id="keywords" onkeyup="searchFilter();"> --}}
                            </div>
                            <select name="status" id="platforms" style="width: 250px" class="dropdown-toggle">
                                <option value="all">All Platforms</option>
                                <option
                                        value="{{ MobileVersion::ANDROID_PLATFORM }}">{{ MobileVersion::ANDROID_PLATFORM }}</option>
                                <option
                                        value="{{ MobileVersion::IOS_PLATFORM }}">{{ MobileVersion::IOS_PLATFORM }}</option>
                            </select>
                            <div class="button-catalog">
                            </div>
                        </div>

                        <div class="button-catalog">
                            <a href="{{ route('admin.mobile.version.create') }}">
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
                                <th class="sorting" data-sorting_type="asc" data-column_name="id">ID<span
                                            id="id_icon"></span><span class="fa fa-angle-down"></span></th>
                                <th>app name</th>
                                <th>platform</th>
                                <th>version</th>
                                <th>is forced</th>
                                <th>link</th>
                                <th>store link</th>
                                <th>ACTION</th>
                            </tr>
                            </thead>

                            <tbody id="businessUsers">
                            @include('system::mobile-versions.data')
                            </tbody>

                        </table>
                        <input type="hidden" name="hidden_page" id="hidden_page" value="1"/>
                        <input type="hidden" name="hidden_column_name" id="hidden_column_name" value="id"/>
                        <input type="hidden" name="hidden_sort_type" id="hidden_sort_type" value="asc"/>
                        <input type="hidden" name="hidden_platform" id="hidden_platform" value="all"/>
                        <input type="hidden" name="hidden_subscription" id="hidden_subscription" value="0"/>
                        {{--                        <input type="hidden" name="hidden_type" id="hidden_type"--}}
                        {{--                               value="{{ \Modules\User\Entities\User::BusinessType }}"/>--}}
                        {{--                        <input type="hidden" name="hidden_eshop_status" id="hidden_eshop_status" value="all"/>--}}
                    </div>


                </div>
            </div>
            <!-- /page content -->
        </div>
    </div>

@endsection
@section('scripts')
    <script>
        function fetch_data(page, sort_type, sort_by, platform, subscription, query) {
            let url = `{{ route('admin.mobile.version.index', ['render' => true]) }}`;
            $.ajax({
                url: url + "&page=" + page +
                    "&sortby=" + sort_by +
                    "&sorttype=" + sort_type +
                    "&query=" + query +
                    "&platform=" + platform +
                    "&subscription=" + subscription,
                success: function (data) {
                    $('tbody').html('');
                    $('tbody').html(data);
                }
            })
        }

        $(document).ready(function () {
            $(document).on('keyup', '#keywords', function () {
                let query = $('#keywords').val();
                let column_name = $('#hidden_column_name').val();
                // let type = $('#hidden_type').val();
                // let eshop_status = $('#hidden_eshop_status').val();
                let sort_type = $('#hidden_sort_type').val();
                let page = $('#hidden_page').val();
                let platform = $('#hidden_platform').val();
                let subscription = $('#hidden_subscription').val();
                fetch_data(page, sort_type, column_name, platform, subscription, query);
            })

            $(document).on('change', '#platforms', function (event) {
                event.preventDefault();
                let platform = $("#platforms").val();
                $('#hidden_platform').val(platform);
                let column_name = $('#hidden_column_name').val();
                let sort_type = $('#hidden_sort_type').val();
                // let eshop_status = $('#hidden_eshop_status').val();
                let query = $('#keywords').val();
                if ($('#keywords').val() === '') {
                    query = "";
                }
                let page = $('#hidden_page').val();
                // let type = $('#hidden_type').val();
                let subscription = $('#hidden_subscription').val();
                // console.log(page, sort_type, column_name, status, query)
                fetch_data(page, sort_type, column_name, platform, subscription, query);
            })
        });
    </script>
    <script>
        $(document).on('change', '.toggle-force', function (event) {
            event.preventDefault();

            var forced = $(this).prop('checked') == true ? 1 : 0;
            var forced_name = forced ? 'Force' : 'Unforced';
            let check = confirm('Are you sure you want to ' + forced_name);
            if (check == false) {
                status ? $(this).prop("checked", false) : $(this).prop("checked", true)
                return;
            }
            // Your function here
            // $(this).parent().next('.category-item-content').toggle();
            var status = $(this).prop('checked') == true ? 1 : 0;
            var mobile_version = $(this).data('id');

            $.ajax({
                type: "GET",
                dataType: "json",
                url: `{{ route('admin.mobile.version.change.forced') }}`,
                data: {'is_forced': status, 'mobile_version': mobile_version},
                success: function (data) {
                    console.log(data.success)
                    if (status == 1) {
                        $('.force-status-' + mobile_version).text('Force')
                    } else {
                        $('.force-status-' + mobile_version).text('Not Force')
                    }
                }
            });
        });
    </script>
@endsection
