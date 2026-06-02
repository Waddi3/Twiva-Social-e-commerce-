@extends('layouts.app')
@section('title')

@endsection
@section('head')
    <!-- multi select -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/css/select2.min.css">
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
                @include('layouts.sidebar')
                <!-- /Sidebar menu -->
            </div>
        </div>
        <!-- Left Column End -->

        <!-- page content -->
        <div class="right_col add-product-page" role="main">

           <!--********** Breadcrumb Start ***********-->
           <div class="page-title breadcrumb-wrapper">
                <ul class="breadcrumb breadcrumb-link">
                    <li><a href="{{ route('products.index') }}" class="back-link">Products</a></li>
                    <li><img src="{{ asset('admin/images/icons/gray-right-arrow.svg') }}" alt=""></li>
                    <li><a href="#">Edit Product</a></li>
                </ul>
            </div>
            <!--**********  Breadcrumb End ***********-->

           <div class="title-wrapper">
                <h4>Add Product</h4>
            </div>

            <div class="catalog-form">

                <div class="form-field">
                    <label>Product Title</label>
                    <input type="text" placeholder="Globex Corporation" value="{{ $product->name }}">
                </div>


                <!--Dropdown-->
                <div class="form-field multi-select-wrapper">
                    <label>Select Category</label>
                    <select class="mul-select-category" multiple="true" onchange="fetchSizes()">
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-field">
                    <label>Price</label>
                    <input type="text" placeholder="KSH 400" value="{{ number_format($product->price) }}">
                </div>

                <div class="form-field">
                    <label>Stock</label>
                    <div class="number">
                        <span class="minus">-</span>
                        <input type="text" value="{{ $product->stock }}">
                        <span class="plus">+</span>
                    </div>
                </div>

                <div class="form-field multi-select-wrapper">
                    <label>Select Size</label>
                    <select class="mul-select-size" multiple="true">
                        {{-- <option value="Cambodia" selected>Cambodia</option> --}}
                        {{-- @foreach ($sizes as $size)

                            <option value="{{ $size->id }}" <?php if(isset($product->getSizeDetailsAttribute()[$size->id])) ?> selected>{{ $size->name }}</option>
                        @endforeach --}}
                    </select>
                </div>

                <div class="form-field multi-select-wrapper">
                    <label>Select Color</label>
                    <select class="mul-select-color" multiple="true">
                        @foreach ($colors as $color)
                            <option value="{{ $color->id }}">{{ $color->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-field-full">
                    <label>Description</label>
                    <textarea>{{ $product->description }}</textarea>
                </div>

                <div class="form-field-full specification-sec">
                    <div class="field-inner">
                    <label>Specification</label>
                    <input type="text" placeholder="Globex Corporation">
                    </div>
                    <button>+ Add Specification</button>
                </div>

                <div class="form-field-full">
                    <label>Specification</label>
                    <div class="browse-box">
                        <div class="browse-box-inner">
                            <label for="product-featur-img">
                                <input type="file" id="product-featur-img">
                                <div class="browse-content">
                                    <img src="{{ asset('admin/images/icons/image.svg') }}">
                                    <p>Drag & drop</p>
                                    <p>Your image here or <strong style="color:#7A0D41;">Browse</strong></p>
                                </div>
                                <!-- uploded image preview  -->
                                <div class="featured-image-preview-wrapper">
                                    <!-- <img src="./images/product-img/Rectangle.png" alt=""> -->
                                </div>
                                <!-- uploded image preview  -->
                            </label>
                        </div>
                    </div>

                    <div class="browse-items">
                        <span>+</span>
                        <div class="browse-ss">
                            <label for="media0">
                                <input type="file" id="media0" >

                                <!-- uploded image preview  -->
                                <div class="product-additional-image">
                                    <!-- <img src="./images/card-images/member-1.jpg" alt=""> -->
                                </div>
                                <!-- uploded image preview  -->
                            </label>
                        </div>

                        <div class="browse-ss">
                            <label for="media1">
                                <input type="file" id="media1" >
                                <!-- uploded image preview  -->
                                <div class="product-additional-image">
                                    <!-- <img src="./images/card-images/member-1.jpg" alt=""> -->
                                </div>
                                <!-- uploded image preview  -->
                            </label>
                        </div>

                        <div class="browse-ss">
                            <label for="media2">
                                <input type="file" id="media2" >
                                <!-- uploded image preview  -->
                                <div class="product-additional-image">
                                    <!-- <img src="./images/card-images/member-1.jpg" alt=""> -->
                                </div>
                                <!-- uploded image preview  -->
                            </label>
                        </div>

                        <div class="browse-ss">
                            <label for="media3">
                                <input type="file" id="media3">

                            <!-- uploded image preview  -->
                            <div class="product-additional-image">
                                <!-- <img src="./images/card-images/member-1.jpg" alt=""> -->
                            </div>
                            <!-- uploded image preview  -->
                            </label>
                        </div>

                        <div class="browse-ss">
                            <label for="media4">
                                <input type="file" id="media4" >
                            </label>
                        </div>

                    </div>
                </div>

                <div class="form-field-full">
                    <div class="common-button">
                        <button class=" white-bttn">Preview</button>
                        <button class="purple-btn">Save</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- /page content -->
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/select2.min.js"></script>

<script>
    $(document).ready(function(){
        $(".mul-select-size").select2({
                placeholder: "Select Size", //placeholder
                tags: true,
                tokenSeparators: ['/',',',';'," "]
        });
        $(".mul-select-color").select2({
            placeholder: "Select Color", //placeholder
            tags: true,
            tokenSeparators: ['/',',',';'," "]
        });
        $(".mul-select-category").select2({
            placeholder: "Select Category", //placeholder
            tags: true,
            tokenSeparators: ['/',',',';'," "]
        });

    })

    function fetchSizes() {
        console.log($(".mul-select-category").val());
        $.ajax({
            type: 'GET',
            url:  '/get-sizes',
            data: {
                'ids' : $(".mul-select-category").val()
            },
            success:function(data) {
                $('.mul-select-size').html('');
                if(!data.sizes) return;
                data.sizes.forEach(element => {
                    element.forEach(el => {
                        $('.mul-select-size').append(`
                            <option value="${el.id}">${el.name}</option>`
                        )
                    })
                });

            },
            error : function (data) {
                console.log(data);
                $('.mul-select-size').html('No Size Found');
            }
        });
    }

</script>
@endsection
