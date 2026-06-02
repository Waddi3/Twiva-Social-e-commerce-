@extends('admin::layouts.app')
@section('title')
    Dashboard
@endsection
@section('content')
    <div class="" id="admin-dashboard">


        <div class="dashboard_container">

            <div class="left_col">
                <div class="scroll-view">
                    <div class="clearfix"></div>

                    @include('admin::layouts.sidebar')


                </div>
            </div>
            <!-- page content -->
            <!-- Right Column Start --> <!-- Page Content -->
            <div class="right_col dashboard-page" role="main">

                <!--********** Breadcrumb Start ***********-->
                <div class="page-title breadcrumb-wrapper">
                    <ul class="breadcrumb">
                        <li><a href="#">Dashboard</a></li>
                    </ul>
                </div>
                <!--**********  Breadcrumb End ***********-->

                <div class="dashboard-section">
                    <!-- Card Section Start -->
                    <div class="card-section container-fluid">

                        <!-- Card Section 1 -->
                        <div class="card-section-1 row">
                            <div class="cards col-lg-3">
                                <div class="card-content" id="card-1">
                                    <div class="card-heading">
                                        <h2>Business</h2>
                                        <img src="{{ asset('admin/images/icons/suitcase-icon.svg') }}" alt="">
                                    </div>
                                    <div class="card-numbers">
                                        <h1>{{ $business['current'] ?? 0 }}</h1>
                                    </div>
                                    <div class="card-desc">
                                        <img src="{{ asset('admin/images/icons/Vector1.svg') }}" alt="">
                                        <p>{{ round($business['percent'] ?? '', 2) ?? 0 }} %</p>
                                        <p>Since last month</p>
                                    </div>
                                </div>
                            </div>

                            <div class="cards col-lg-3">
                                <div class="card-content" id="card-2">
                                    <div class="card-heading">
                                        <h2>Influencer</h2>
                                        <img src="{{ asset('admin/images/icons/influencer-icon.svg') }}" alt="">
                                    </div>
                                    <div class="card-numbers">
                                        <h1>{{ $influencers['current'] ?? 0 }}</h1>
                                    </div>
                                    <div class="card-desc">
                                        <img src="{{ asset('admin/images/icons/Vector1.svg') }}" alt="">
                                        <p>{{ round($influencers['percent'] ?? '', 2) ?? 0 }} %</p>
                                        <p>Since last month</p>
                                    </div>
                                </div>
                            </div>

                            <div class="cards col-lg-3">
                                <div class="card-content" id="card-3">
                                    <div class="card-heading">
                                        <h2>Buyer</h2>
                                        <img src="{{ asset('admin/images/icons/buyer-icon.svg') }}" alt="">
                                    </div>
                                    <div class="card-numbers">
                                        <h1>{{ $buyers['current'] ?? 0 }}</h1>
                                    </div>
                                    <div class="card-desc">
                                        <img src="{{ asset('admin/images/icons/Vector1.svg') }}" alt="">
                                        <p>{{ round($buyers['percent'], 2) ?? 0}} %</p>
                                        <p>Since last month</p>
                                    </div>
                                </div>
                            </div>

                            <div class="cards col-lg-3">
                                <div class="card-content" id="card-4">
                                    <div class="card-heading">
                                        <h2>Total Products</h2>
                                        <img src="{{ asset('admin/images/icons/doc-icon.svg') }}" alt="">
                                    </div>
                                    <div class="card-numbers">
                                        <h1>{{ !empty($products) ? $products->count() : 0}}</h1>
                                    </div>
                                    <div class="card-desc">
                                        <img src="{{ asset('admin/images/icons/Vector1.svg') }}" alt="">
                                        <p>{{ round($productsPercent ?? '', 2) }} %</p>
                                        <p>Since last month</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card Section 2 -->
                        <div class="card-section-2 row">
                            <div class="cards col-lg-3">
                                <div class="card-content" id="card-5">
                                    <div class="card-heading">
                                        <h2>Ongoing Order</h2>
                                        <img src="{{ asset('admin/images/icons/ongoing-order.svg') }}" alt="">
                                    </div>
                                    <div class="card-numbers">
                                        <h1>{{ $orders['current'] ?? 0}}</h1>
                                    </div>
                                    <div class="card-desc">
                                        <img src="{{ asset('admin/images/icons/Vector1.svg') }}" alt="">
                                        <p>{{ $orders['percent'] ?? 0 }}%</p>
                                        <p>Since last month</p>
                                    </div>
                                </div>
                            </div>

                            <div class="cards col-lg-3">
                                <div class="card-content" id="card-6">
                                    <div class="card-heading">
                                        <h2>Pending Payout</h2>
                                        <img src="{{ asset('admin/images/icons/payout-icon.svg') }}" alt="">
                                    </div>
                                    <div class="card-numbers">
                                        <h1>1400</h1>
                                    </div>
                                    <div class="card-desc">
                                        <img src="{{ asset('admin/images/icons/Vector1.svg') }}" alt="">
                                        <p>10.21%</p>
                                        <p>Since last month</p>
                                    </div>
                                </div>
                            </div>

                            <div class="cards col-lg-3">
                                <div class="card-content" id="card-7">
                                    <div class="card-heading">
                                        <h2>Revenue</h2>
                                        <img src="{{ asset('admin/images/icons/revenue-icon.svg') }}" alt="">
                                    </div>
                                    <div class="card-numbers">
                                        <h1>$1400</h1>
                                    </div>
                                    <div class="card-desc">
                                        <img src="{{ asset('admin/images/icons/Vector1.svg') }}" alt="">
                                        <p>10.21%</p>
                                        <p>Since last month</p>
                                    </div>
                                </div>
                            </div>

                            <div class="cards col-lg-3">
                                <div class="card-content" id="card-8">
                                    <div class="card-heading">
                                        <h2>Growth</h2>
                                        <img src="{{ asset('admin/images/icons/growth-icon.svg') }}" alt="">
                                    </div>
                                    <div class="card-numbers">
                                        <h1>+36.50%</h1>
                                    </div>
                                    <div class="card-desc">
                                        <img src="{{ asset('admin/images/icons/Vector1.svg') }}" alt="">
                                        <p>10.21%</p>
                                        <p>Since last month</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="company-section container-fluid">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="section-1">
                                    <h1>Top 5 Businesses</h1>
                                    @foreach ($top5FiveBusinesses ?? [] as $business)
                                        <div class="content-box-wrapper">
                                            <div class="content">
                                                <div class="image">
                                                    <img src="{{ $business->profile_image }}"
                                                         alt="">

                                                </div>
                                                <div class="desc">
                                                    <h1>{{ $business->name }}</h1>
                                                    <h2>Total Sales: KSH {{ number_format($business->sellAmount) }}</h2>
                                                    <p>Count of orders : {{ $business->orders_count }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="section-2">
                                    <h1>Top 5 Influencer</h1>
                                    @foreach ($top5FiveInfluencers ?? [] as $influencer)
                                        <div class="content-box-wrapper">
                                            <div class="content">
                                                <div class="image">
                                                    <img src="{{ $influencer->profile_image }}"
                                                         alt="">
                                                </div>
                                                <div class="desc">
                                                    <h1>{{ $influencer->name ?? 'Influencer' }}</h1>
                                                    <h2>Total Sales: zz {{ $influencer->sellAmount }}</h2>
                                                    <p>Count of orders : {{ $influencer->orders_count }}</p>

                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
@endsection
