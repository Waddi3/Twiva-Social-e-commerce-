@extends('admin::layouts.app')
@section('title')
    Twiva Mobile Versions
@endsection
@section('head')
    <!-- multi select -->
    {{--    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/css/select2.min.css">--}}
    <!-- multi select -->
@endsection
@section('content')
    <div class="">
        <div class="dashboard_container">
            <!-- Left Column Start -->
            <div class="left_col">
                <div class="scroll-view">
                    <div class="clearfix"></div>

                    <!-- Sidebar menu -->
                    @include('admin::layouts.sidebar')
                    <!-- /Sidebar menu -->
                </div>
            </div>
            <!-- Left Column End -->

            <!-- Right Column Start --> <!-- Page Content -->
            <div class="right_col add-product-page" role="main">

                <!--********** Breadcrumb Start ***********-->
                <div class="page-title breadcrumb-wrapper">
                    <ul class="breadcrumb breadcrumb-link">
                        <li><a href="{{ route('admin.mobile.version.update.index') }}" class="back-link">Mobile Versions</a>
                        </li>
                        <li><img src="{{ asset('admin/images/icons/gray-right-arrow.svg') }}" alt=""></li>
                        <li><a href="#">Update</a></li>
                    </ul>
                </div>
                <!--**********  Breadcrumb End ***********-->

                <div class="title-wrapper">
                    <h4>
                        Update Mobile Version
                    </h4>
                </div>
                @include('admin::layouts.message')


                <!-- Right Column End --> <!-- /Page Content -->
            </div>
        </div>
        @endsection
        @section('scripts')
            <script src="{{ asset('admin/assets/js/api.js') }}"></script>
@endsection
