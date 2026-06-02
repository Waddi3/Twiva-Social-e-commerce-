@php use Modules\User\Entities\User; @endphp
@php use Modules\System\Entities\MobileVersion; @endphp
@extends('admin::layouts.app')
@section('title')
    Twiva Mobile Version Create
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
                        <li><a href="{{ route('admin.mobile.version.index') }}" class="back-link">Mobile Versions</a>
                        </li>
                        <li><img src="{{ asset('admin/images/icons/gray-right-arrow.svg') }}" alt=""></li>
                        <li><a href="#">Create New</a></li>
                    </ul>
                </div>
                <!--**********  Breadcrumb End ***********-->

                <div class="title-wrapper">
                    <h4>
                        Add Mobile Version
                    </h4>
                </div>
                @include('admin::layouts.message')

                <form method="POST" action="{{ route('admin.mobile.version.store') }}">
                    @csrf
                    <input type="hidden" name="type" value="{{ User::BusinessType }}">
                    <div class="catalog-form">
                        <div class="title-wrapper address-heading">
                            <h4>Mobile Version</h4>
                        </div>
                        <div class="form-field">
                            <select class="" name="platform">
                                <option
                                        value="{{ MobileVersion::ANDROID_PLATFORM }}">{{ MobileVersion::ANDROID_PLATFORM }}</option>
                                <option
                                        value="{{ MobileVersion::IOS_PLATFORM }}">{{ MobileVersion::IOS_PLATFORM }}</option>
                            </select>
                        </div>
                        <div class="form-field d-flex align-items-center">
                            <span class="mr-4">
                            Forced
                            </span>
                            <label class="switch">
                                <input class="toggle-status" name="is_forced"
                                       type="checkbox" checked>
                                <span class="slider round"></span>
                            </label>
                        </div>
                        <div class="form-field">
                            <label>App Name</label>
                            <input type="text" name="app_name" placeholder="App Name" required
                                   class="@error('app_name') is-invalid @enderror" value="{{ old('app_name') ?? '' }}">
                            @error('app_name')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-field">
                            <label>Version</label>
                            <input type="text" name="version" placeholder="Version" required
                                   class="@error('version') is-invalid @enderror" value="{{ old('version') ?? "" }}">
                            @error('version')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-field-full">
                            <label>Link</label>
                            <input type="text" name="link" placeholder="Link" required
                                   class="@error('link') is-invalid @enderror" value="{{ old('link') ?? '' }}">
                            @error('link')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-field-full">
                            <label>Store Link</label>
                            <input type="text" name="store_link" placeholder="Store Link" required
                                   class="@error('store_link') is-invalid @enderror"
                                   value="{{ old('store_link') ?? '' }}">
                            @error('store_link')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-field-full">
                            <label>Features</label>
                            <textarea name="features" maxlength="400">{{ old('features') ?? '' }}</textarea>
                        </div>
                    </div>

                    <div class="form-field-full save-btn">
                        <div class="common-button">
                            <!-- <button class=" white-bttn">Preview</button> -->
                            <button class="purple-btn">Save</button>
                        </div>
                    </div>
                </form>
                <!-- Right Column End --> <!-- /Page Content -->
            </div>
        </div>
        @endsection
        @section('scripts')
            <script src="{{ asset('admin/assets/js/api.js') }}"></script>
@endsection
