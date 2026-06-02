@extends('admin::layouts.app')
@section('title')
    Twiva Business
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


            <div class="right_col container-fluid">
                <!--********** Breadcrumb Start ***********-->
                <div class="page-title breadcrumb-wrapper">
                    <ul class="breadcrumb breadcrumb-link">
                        <li><a href="{{ route('admin.business.index') }}" class="back-link">Business</a></li>
                        <li><img src="{{ asset('admin/images/icons/gray-right-arrow.svg') }}" alt=""></li>
                        <li><a href="#">Business Detail</a></li>
                    </ul>
                </div>
                <!--**********  Breadcrumb End ***********-->

                <div class="title-wrapper">
                    <h4>
                        Detail
                    </h4>
                </div>
                @if(!$company)
                    <div class="account-info-wrapper">
                        NO Details Found
                    </div>
                @else
                    <div class="account-info-wrapper">
                        <div class="row">
                            <div class="col-md-6 user-info-wrapper">
                                <din class="user-info">
                                    <din class="profile-wrapper">
                                        <img src="{{ $user->profile_image }}" alt="">
                                    </din>
                                    <div class="content-wrapper">
                                        <div class="company-info">
                                            <h4>
                                                {{ $company->busi_name  }}
                                            </h4>
                                            <p>
                                                {{ $company->comp_email }}
                                            </p>
                                        </div>
                                        <div class="contact-wrapper">
                                            <p>
                                                <span>No of Product:</span>
                                                {{ count($user->products) }} @if(count($user->products) >= 1)
                                                    <a href="{{ route('admin.business.products', $user->id ) }}"
                                                       class="details-link">Product </a>
                                                @endif
                                            </p>
                                            <p>
                                                <span>Influencer Connected:</span>
                                                {{ $user->influencerCount ?? 0 }} @if( $user->influencerCount)
                                                    <a href="{{ route('admin.influencer.connected', $company->user_id) }}"
                                                       class="details-link"> Influencer</a>
                                                @endif
                                            </p>
                                            <p>
                                                <span>Contact Person:</span>
                                                <span>{{ $company->contact_name }}</span>
                                            </p>
                                            <p>
                                                <span>Contact Number:</span>
                                                <span>{{ $user->mobile }}</span>
                                            </p>
                                            <p>
                                                <span>Shop Domain</span>
                                                @if ($user->shop)
                                                    <a href="{{route('shop.product.index',['account' => $user->shop->domain])}}"> {{route('shop.product.index',['account' => $user->shop->domain])}}</a>
                                                @endif
                                            </p>
                                            {{--<p>
                                                <span>Date of Birth</span>
                                                <span>23 Jan 1984(Static, Not taking Input)</span>
                                            </p> --}}
                                        </div>
                                        <div class="about-wrapper">
                                            <p>
                                                {{ $company->description }}
                                            </p>
                                        </div>
                                    </div>
                                    <!-- <div class="edit-btn-wrapper">
                                        <a href="#">
                                            Edit Information
                                        </a>
                                    </div> -->
                                </din>
                            </div>
                            <div class="col-md-6 address-wrapper">
                                <div class="address">
                                    <h4>Address</h4>
                                    <div class="addres-content">
                                        <p>
                                            <span>Country</span>
                                            <span>{{ $company->countryDetails->name ?? '' }} </span>
                                        </p>
                                        <p>
                                            <span>City</span> <span>{{ $company->cityDetails->name ?? ''}}</span>
                                        </p>
                                        {{--                                        <p>--}}
                                        {{--                                            <span>Pincode</span> <span>{{ $company->pincode ?? '' }} </span>--}}
                                        {{--                                        </p>--}}
                                        <p>
                                            <span>State</span> <span>{{ $company->stateDetails->name ?? ''}} </span>
                                        </p>
                                        <p>
                                            <span>Locality</span> <span>{{ $company->area }} </span>
                                        </p>
                                        {{--                                        <p>--}}
                                        {{--                                            <span>Building No.</span> <span>{{ $company->building }} </span>--}}
                                        {{--                                        </p>--}}
                                    </div>
                                    <!-- <div class="edit-btn-wrapper">
                                        <a href="#">
                                            Edit Information
                                        </a>
                                    </div> -->
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>


        </div>
    </div>
@endsection
