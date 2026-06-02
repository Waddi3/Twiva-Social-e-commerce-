@extends('admin::layouts.app')
@section('title')
    Twiva Influencer
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
                        <li><a href="{{ route('admin.influencers.index') }}" class="back-link">Influencer</a></li>
                        <li><img src="{{ asset('admin/images/icons/gray-right-arrow.svg') }}" alt=""></li>
                        <li><a href="#">Detail</a></li>
                    </ul>
                </div>
                <!--**********  Breadcrumb End ***********-->
                <div class="title-wrapper">
                    <h4>
                        Detail
                    </h4>
                </div>
                <div class="account-info-wrapper">
                    <div class="row">
                        <div class="col-md-6 user-info-wrapper">
                            <din class="user-info">
                                <din class="profile-wrapper">
                                    @if($influencer->profile_image)
                                        <img src="{{ $influencer->profile_image }}" alt="">
                                    @else
                                        <img src="{{ asset('admin/images/Profile.svg')}}" alt="">
                                    @endif
                                </din>
                                <div class="content-wrapper">
                                    <div class="company-info">
                                        <a target="_blank"
                                           href="{{route('shop.product.index',['account' => $influencer->shop->domain])}}"
                                           class="details-link">
                                            {{ $influencer->shop->shop_name ?? ''}}
                                        </a>
                                        <p>
                                            {{ $influencer->email }}
                                        </p>
                                    </div>
                                    <div class="contact-wrapper">
                                        <p>
                                            <span>Name:</span>
                                            <span>{{ $influencer->name ?? '' }}</span>
                                        </p>
                                        {{-- <p>
                                            <span>Contact Number:</span>
                                            <span>(405) 555-0128</span>
                                        </p> --}}
                                        <p>
                                            <span>Gender</span>
                                            <span>{{  $influencer->influencerDetails->gender ?? ''}}</span>
                                        </p>
                                        <p>
                                            <span>Date of Birth</span>
                                            <span>{{ str_replace('-', '/', $influencer->influencerDetails->dob ?? '') }}</span>
                                        </p>
                                        <p>
                                            <span>Mobile Number</span>
                                            <span>{{ $influencer->mobile}}</span>
                                        </p>
                                    </div>
                                    <div class="about-wrapper">
                                        <p>
                                            {{ $influencer->influencerDetails->description ?? ''}}
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
                                <div class="addres-content">
                                    <p>
                                        <span>Interest:</span>
                                        <span>
                                    @foreach ($interests as $interest)
                                                {{ $interest->name }},
                                            @endforeach
                                    </span>
                                    </p>
                                    @if($influencer->shop)
                                        @if(count($influencer->shop->influencerProducts) >= 1)
                                            <p>

                                                <span>No of Product:</span>
                                                <a href="{{ route('admin.influencer.products', $influencer->id) }}"
                                                   class="details-link">{{$influencer->shop ? count($influencer->shop->influencerProducts) : 0 }}
                                                    Products </a>
                                            </p>
                                        @endif
                                    @endif

                                    <div class="social-connection">
                                        <h2>Social Network connection</h2>
                                        <ul class="network-list">
                                            @foreach($influencer->UserPlatforms as $UserPlatforms)
                                                <li class="network-list-item">
                                                    <div>
                                                        <img src="{{asset($UserPlatforms->platform->icon)}}"
                                                            height="20" width="20"
                                                             alt="">
                                                        <p>{{$UserPlatforms->name}}</p>
                                                    </div>
                                                    <a href="{{ $UserPlatforms->link ?? ''}}">View</a>
                                                </li>
                                            @endforeach

                                        </ul>
                                    </div>
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
            </div>


        </div>
    </div>
@endsection
