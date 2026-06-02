@php use Modules\System\Entities\MobileVersion; @endphp
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
                        <li><a href="#">Update system settings</a></li>
                    </ul>
                </div>
                @include('admin::layouts.message')
                <div class="title-wrapper">
                    <p>Update system settings</p>
                </div>
                <form method="post" action="{{ route('admin.system.update') }}">
                    @csrf
                    <div class="catalog-form">
                        <div class="bg-white mx-5 p-5 border ">
                            <section class="">
                                <label class="d-flex cursor-pointer">
                                    <input class="mr-2 "
                                           type="checkbox"
                                           {{$system->shouldSendSMSNotificationsToInfluencersAndBusinesses ? 'checked' : ''}}
                                           name="shouldSendSMSNotificationsToInfluencersAndBusinesses"
                                    >
                                    Send SMS Notifications To Influencers And Businesses
                                </label>
                                <label class="d-flex cursor-pointer mt-2">
                                    <input class="mr-2 "
                                           type="checkbox"
                                           {{$system->shouldSendSMSNotificationsToBuyers ? 'checked' : ''}}
                                           name="shouldSendSMSNotificationsToBuyers"
                                    >
                                    Send SMS Notifications To Buyers
                                </label>

                            </section>
                            <div class="d-block mx-auto text-center ">
                                <button id="updatePassword" class="purple-btn " type="submit">Update</button>
                            </div>
                        </div>
                    </div>
                </form>


                <div class="title-wrapper">
                    <p>Android Mobile Version</p>
                </div>
                <form method="POST" action="{{ route('admin.mobile.version.update.index.db') }}">
                    @method('PUT')
                    @csrf
                    <div class="catalog-form">
                        <div class="title-wrapper address-heading">
                            <h4>Android Mobile Version</h4>
                        </div>


                        <input type="hidden" name="android_id"
                               value="{{ $versions->where('platform', MobileVersion::ANDROID_PLATFORM)->first()->id }}">

                        <div class="form-field">
                            <label>Version</label>
                            <input type="text" name="android_version" placeholder="Version" required
                                   value="{{ $versions->where('platform', MobileVersion::ANDROID_PLATFORM)->first()->version }}">
                        </div>

                        <div class="form-field d-flex align-items-center">
                            <span class="mr-4">
                            Forced
                            </span>
                            <label class="switch">
                                <input class="toggle-status" name="android_is_forced"
                                       type="checkbox"
                                       @if($versions->where('platform', MobileVersion::ANDROID_PLATFORM)->first()->is_forced)
                                           checked
                                        @endif
                                >
                                <span class="slider round"></span>
                            </label>
                        </div>

                        {{--                        <div class="form-field-full">--}}
                        {{--                            <label>Store Link</label>--}}
                        {{--                            <input type="text" name="android_store_link" placeholder="Store Link" required--}}
                        {{--                                   value="{{ $versions->where('platform', \Modules\System\Entities\MobileVersion::ANDROID_PLATFORM)->first()->store_link }}">--}}
                        {{--                        </div>--}}
                        {{--                    </div>--}}

                        {{--                    <div class="catalog-form">--}}
                        <div class="title-wrapper address-heading">
                            <h4>Ios Mobile Version</h4>
                        </div>

                        <input type="hidden" name="ios_id"
                               value="{{ $versions->where('platform', MobileVersion::IOS_PLATFORM)->first()->id }}">

                        <div class="form-field">
                            <label>Version</label>
                            <input type="text" name="ios_version" placeholder="Version" required
                                   value="{{ $versions->where('platform', MobileVersion::IOS_PLATFORM)->first()->version }}">
                        </div>

                        <div class="form-field d-flex align-items-center">
                            <span class="mr-4">
                            Forced
                            </span>
                            <label class="switch">
                                <input class="toggle-status" name="ios_is_forced"
                                       type="checkbox"
                                       @if($versions->where('platform', MobileVersion::IOS_PLATFORM)->first()->is_forced)
                                           checked
                                        @endif
                                >
                                <span class="slider round"></span>
                            </label>
                        </div>

                        {{--                        <div class="form-field-full">--}}
                        {{--                            <label>Store Link</label>--}}
                        {{--                            <input type="text" name="ios_store_link" placeholder="Store Link" required--}}
                        {{--                                   value="{{ $versions->where('platform', \Modules\System\Entities\MobileVersion::IOS_PLATFORM)->first()->store_link }}">--}}
                        {{--                        </div>--}}
                        <div class="form-field-full save-btn">
                            <div class="common-button">
                                <!-- <button class=" white-bttn">Preview</button> -->
                                <button class="purple-btn">update</button>
                            </div>
                        </div>
                    </div>

                </form>


                <div class="title-wrapper">
                    <p>Update Banner</p>
                </div>
                <form method="post" action="{{ route('admin.update.banner.e-commerce') }}"
                      enctype="multipart/form-data">
                    @csrf
                    <div class="catalog-form">
                        <div class="text-start d-block mb-3">
                            <button class="purple-btn my-2" type="submit">Update</button>
                        </div>
                        <div class="form-field-full d-flex align-items-center">
                            <input type="file" name="banner" accept="image/*">
                        </div>
                        <div class="form-field-full d-flex align-items-center">
                            <input type="url" name="url_banner" placeholder="Url banner" value="{{ $url_banner }}">
                        </div>
                    </div>
                </form>

                <div class="title-wrapper">
                    <p>Update Titles Notifications</p>
                </div>
                <form method="post" action="{{ route('admin.notification.list.titles.update') }}">
                    @csrf
                    <div class="catalog-form">
                        <div class="text-start d-block mb-3">
                            <button class="purple-btn my-2" type="submit">Update</button>
                        </div>
                        @foreach($notifications_list_titles as $notifications_list_title)
                            <div class="form-field-full d-flex align-items-center">
                                <input type="text"
                                       name="{{ $notifications_list_title->slug }}"
                                       value="{{ $notifications_list_title->title }}"
                                >
                                <label class="px-2">{{ $notifications_list_title->description }}</label>
                            </div>
                        @endforeach
                    </div>
                </form>

            </div>
            <!-- /page content -->
        </div>
    </div>
@endsection
