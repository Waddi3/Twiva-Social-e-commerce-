@extends('admin::layouts.app')
@section('title')
    Twiva WhatsApp Group
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
                        <li><a href="#">WhatsApp Group</a></li>
                    </ul>
                </div>
                <!--**********  Breadcrumb End ***********-->


                <div class="catalog-page">

                    <div class="select-top select-search">
                        <div class="search-wrap w-100">
                            <div class="search-panel">
                                <img src="{{ asset('admin/images/icons/search.svg') }}">
                                <input type="text" placeholder="Search..." id="keywords">
                            </div>
                        </div>



                    </div>

                    <div class="table-responsive">
                        <table id="admin-table" class="table table-striped table-bordered table-sm" cellspacing="0"
                            width="100%">
                            <thead>
                                <tr>
                                    {{-- <th class="sorting" data-sorting_type="desc" data-column_name="id">ID<span
                                            id="id_icon"></span><span class="fa fa-angle-down"></span></th> --}}
                                    <th class="th-sm">NAME</th>
                                    <th>no. of people</th>
                                    <th>added by</th>
                                    <th>added on</th>
                                    <th>invite link</th>
                                    <th>status</th>
                                    <th>actions</th>
                                    {{-- <th class="th-sm">created at</th> --}}
                                </tr>
                            </thead>

                            <tbody>
                                @include('platform::admin.whatsapp-group.data')
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
        $(document).on('keyup', '#keywords', function() {
            let query = $('#keywords').val();
            let additionalParams = {
                'page': 1,
                'query': query
            }
            let route = `{{ route('admin.whatsapp.index') }}`
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
@endsection
