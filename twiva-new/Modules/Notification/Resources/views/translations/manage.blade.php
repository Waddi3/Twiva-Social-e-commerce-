@extends('admin::layouts.app')
@section('title')
    Update system settings
@endsection
@section('content')
    <div class="">
        <div class="dashboard_container">

            <!-- Left Column Start -->
            <div class="left_col">
                <div class="scroll-view">
                    <div class="clearfix"></div>
                    @include('admin::layouts.sidebar')
                </div>
            </div>
            <div class="right_col add-product-page" role="main">

                <div class="page-title breadcrumb-wrapper">
                    <ul class="breadcrumb breadcrumb-link">
                        <li><img src="{{ asset('admin/images/icons/gray-right-arrow.svg') }}" alt=""></li>
                        <li><a href="#">Update Titles Notifications</a></li>
                    </ul>
                </div>
                <div class="title-wrapper">
                    <p>Update Titles Notifications</p>
                </div>
                @include('admin::layouts.message')
                <form method="post" action="{{ route('admin.notification.list.titles.update') }}">
                    @csrf
                    <div class="text-start d-block mb-3">
                        <button class="purple-btn my-2" type="submit">Update</button>
                    </div>
                    <div class="catalog-form">
                        @foreach($notifications_list_titles as $notifications_list_title)
                            <div class="form-field-full d-flex align-items-center">
                                <input type="text"
                                       name="{{ $notifications_list_title->slug }}"
                                       value="{{ $notifications_list_title->title }}"
                                >
                            </div>
                        @endforeach
                    </div>
                </form>
            </div>
            <!-- /page content -->
        </div>
    </div>
@endsection
