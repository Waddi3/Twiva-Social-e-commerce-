@extends('admin::layouts.app')
@section('title')
    Twiva Product
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


            <!-- page content -->
            <div class="right_col add-product-page" role="main">
                <!--********** Breadcrumb Start ***********-->
                <div class="page-title breadcrumb-wrapper">
                    <ul class="breadcrumb breadcrumb-link">
                        <li><a href="{{ route('admin.influencers.index') }}" class="back-link">Influencers</a></li>
                        <li><img src="{{ asset('admin/images/icons/gray-right-arrow.svg') }}" alt=""></li>
                        <li><a href="{{ route('admin.influencers.show',['influencer' => $influencer->id]) }}" class="back-link">Influencer ({{$influencer->name}})</a></li>
                        <li><img src="{{ asset('admin/images/icons/gray-right-arrow.svg') }}" alt=""></li>
                        <li><a href="{{ url()->previous() }}" class="back-link">products</a></li>
                        <li><img src="{{ asset('admin/images/icons/gray-right-arrow.svg') }}" alt=""></li>
                        <li><a href="#">Product Detail</a></li>
                    </ul>
                </div>
                <!--**********  Breadcrumb End ***********-->
                <div class="product-main">


                    <div class="product-detail-cont">
                        <!-- <div class="browse-box">
                    <img src="{{ asset('admin/images/card-images/product.jpg') }}">
                </div> -->

                        <div id="product-carousel" class="carousel slide browse-box" data-ride="carousel">
                            <div class="carousel-inner">
                                <div class="carousel-item active">
                                    @if ($product->productMainImage)
                                        <img class="d-block" src="{{ $product->productMainImage->image_url }}">
                                    @endif
                                </div>
                                @foreach ($product->influencerProductImages as $image)
                                    <div class="carousel-item">
                                        <img class="d-block" src="{{ $image->image_url }}">
                                    </div>
                                @endforeach

                            </div>
                        </div>

                        <div class="browse-items">
                            <a href="#product-carousel" class="carousel-control-prev" role="button" data-slide="prev">
                                <img src="{{ asset('admin/images/icons/Icon-White-left.svg') }}" alt="">
                            </a>

                            <div class="browse-ss">

                                @if ($product->productMainImage)
                                    <img src="{{ $product->productMainImage->image_url }}">
                                @endif
                            </div>
                            @foreach ($product->influencerProductImages as $image)
                                <div class="browse-ss">

                                    <img src="{{ $image->image_url }}">

                                </div>
                            @endforeach
                            <a href="#product-carousel" class="carousel-control-next" role="button" data-slide="next">
                                <img src="{{ asset('admin/images/icons/Icon-White-right.svg') }}" alt="">
                            </a>
                        </div>
                        <!-- <div class="switch-check-box-wrapper">
                            <span>Published</span>
                            <input type="checkbox" checked="checked " id="switch" />
                            <label class="label-switch" for="switch">Toggle</label>
                        </div> -->
                    </div>

                    <div class="product-specification">
                        <h2>{{ $product->product_title }}</h2>
                        <h3><label>Price: </label><b>KSH {{ number_format($product->product_price) }}</b></h3>

                        <div class="spec-row">
                            <label>Qty:</label>
                            <div><h3>{{ $product->productDetails->stock }} piece</h3>
                                <button>In Stock</button>
                            </div>
                        </div>

                        <div class="spec-row">
                            <label>Size:</label>
                            <div>
                                @foreach ($product->productDetails->getSizeDetailsAttribute() as $size)
                                    <span>{{ $size->name }}</span>
                                @endforeach
                            </div>
                        </div>

                        <div class="spec-row">
                            <label>Color:</label>
                            <div class="color-lists">
                                @foreach ($product->productDetails->getColorDetailsAttribute() as $color)
                                    <div style="background-color:#{{ $color->hexaval }};"></div>
                                @endforeach
                            </div>
                        </div>

                        <div class="prod-desc">
                            <label>Description</label>
                            <p>{{ $product->product_description }}</p>
                        </div>

                        <div class="prod-spec">
                            <label>Specification</label>
                            <ul>
                                @foreach (explode(',', $product->productDetails->specification) as $specification)
                                    <li>{{ $specification }}</li>
                                @endforeach
                            </ul>
                        </div>

                        <div class="prod-spec">
                            <label>Review & Rating</label>
                            @foreach ($product->productDetails->productReviewsAndRatings as $review)
                                <div class="review-author">
                                    <span><img
                                            src="{{ asset('admin/images/icons/verified.svg') }}"></span> {{ $review->buyer->name ?? 'Buyer' }}
                                </div>
                                <div class="date"><img
                                        src="{{ asset('admin/images/icons/star-rate.svg') }}">{{ $review->updated_at->format('M j, Y') }}
                                </div>
                                <p>{{ $review->review }} </p>
                                <hr/>
                            @endforeach
                        </div>

                        <div class="form-field-full">
                            <div class="common-button">
                                {{-- <a href="{{ route('product.delete', $product->id) }}"><button class="white-bttn">Delete</button></a> --}}
                                {{-- <a href="{{ route('product.edit', $product->id) }}"><button class="purple-btn">Edit</button></a> --}}
                            </div>
                        </div>
                    </div>


                </div>
            </div>
            <!-- /page content -->
        </div>
    </div>
@endsection
