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
                        <li><a href="{{route('admin.products.index')}}" class="back-link">Products</a></li>
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
                                <!-- <div class="carousel-item active">
                            @if ($product->main_image_path)
                                    <img class="d-block" src="{{ $product->productMainImage }}">


                                @endif
                                </div> -->
                                @foreach ($product->productImages as $key=>$image)
                                    @if($image->image && $image->image!='null')
                                        <div class="carousel-item @if($key==0) active @endif">
                                            <img class="d-block" src="{{ $image->image_url }}">
                                        </div>
                                    @endif
                                @endforeach

                            </div>
                        </div>

                        <div class="browse-items carousel-indicators">
                            <a href="#product-carousel" class="carousel-control-prev" role="button" data-slide="prev">
                                <img src="{{ asset('admin/images/icons/Icon-White-left.svg') }}" alt="">
                            </a>

                            <!-- <div class="browse-ss active" data-target="#product-carousel" data-slide-to="0">


                            </div> -->
                            @foreach ($product->productImages as $key=>$image)
                                @if($image->image && $image->image!='null')
                                    <div class="browse-ss @if($key==0) active @endif" data-target="#product-carousel"
                                         data-slide-to="{{$key}}">
                                        <img src="{{ $image->image_url }}">
                                    </div>
                                @endif
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
                        <h2>{{ $product->name }}</h2>
                        <h3><label>Price: </label><b>KSH {{ number_format($product->price) }}</b></h3>
                        <div class="spec-row">
                            <h3><label>Retail Price: </label><b>KSH {{ number_format($product->retail_price) }}</b></h3>
                        </div>

                        <div class="spec-row">
                            <label>Qty:</label>
                            <div><h3>{{ $product->stock }} piece</h3>
                                <button>In Stock</button>
                            </div>
                        </div>

                        <div class="spec-row">
                            <label>Size:</label>
                            <div>
                                @foreach ($product->getSizeDetailsAttribute() as $size)
                                    <span>{{ $size->name }}</span>
                                @endforeach
                            </div>
                        </div>

                        <div class="spec-row">
                            <label>Color:</label>
                            <div class="color-lists">
                                @foreach ($product->getColorDetailsAttribute() as $color)
                                    <div style="background-color:#{{ $color->hexaval }};"></div>
                                @endforeach
                                @if($product->getColorDetailsAttribute()==null)
                                    NA
                                @endif
                            </div>
                        </div>

                        <div class="prod-desc">
                            <label>Description</label>
                            <p>{{ $product->description ?? NA }}</p>
                        </div>

                        <div class="prod-spec">
                            <label>Specification</label>
                            <ul>
                                @foreach (explode(',', $product->specification) as $specification)
                                    <li>{{ $specification ?? "NA" }}</li>
                                @endforeach
                            </ul>
                        </div>

                        <div class="prod-spec">
                            <label>Review & Rating</label>
                            @foreach ($product->productReviewsAndRatings as $review)
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
