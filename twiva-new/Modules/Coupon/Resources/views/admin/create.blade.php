@extends('admin::layouts.app')
@section('title', 'Twiva Coupon Create')
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
                        <li><a href="{{ route('admin.coupons.index') }}" class="back-link">Coupons</a></li>
                        <li><img src="{{ asset('admin/images/icons/gray-right-arrow.svg') }}" alt=""></li>
                        <li><a href="#">Add Coupon</a></li>
                    </ul>
                </div>
                <!--**********  Breadcrumb End ***********-->
                <div class="title-wrapper">
                    <h4>Add Coupon</h4>
                </div>
                @include('admin::layouts.message')

                <form method="POST" action="{{ route('admin.coupons.store') }}">
                    @csrf
                    <div class="catalog-form">

                        @if (count($errors) > 0)
                            @foreach ($errors->all() as $error)
                                <div class="title-wrapper">
                                    <h4>
                                        {{ $error }}
                                    </h4>
                                </div>
                            @endforeach
                        @endif

                        <div class="form-field">
                            <label>Name</label>
                            <input type="text" name="name" placeholder="name" required
                                class="@error('name') is-invalid @enderror" value="{{ old('name') ?? '' }}">
                            @error('name')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-field">
                            <label>Code</label>
                            <input type="text" name="code" placeholder="code" required
                                class="@error('code') is-invalid @enderror" value="{{ old('code') ?? $code }}">
                            @error('code')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>



                        <div class="form-field">
                            <label>Amount</label>
                            <input type="text" name="amount" placeholder="amount" required
                                class="@error('amount') is-invalid @enderror" value="{{ old('amount') ?? '' }}">
                            @error('amount')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>


                        <div class="form-wrapper-list" style="width:  50% !important; flex: 0 0 46%;">
                            <div class="form-wrapper-item-2">
                                <div class="form-field radio-input">
                                    <label>Type</label>
                                    <ul class="input-list">
                                        <li>
                                            <input type="radio" name="type" id="fixed" value="fixed"
                                                {{ old('type') == 'fixed' ? 'checked' : '' }} required="required">
                                            <label for="fixed">Fixed</label>
                                        </li>
                                        <li>
                                            <input type="radio" name="type" id="percentage" value="percentage"
                                                {{ old('type') == 'percentage' ? 'checked' : '' }} required>
                                            <label for="percentage">Percentage</label>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>


                        <div class="form-field">
                            <label>Max Usage</label>
                            <input type="text" name="max_usage" placeholder="max usage"
                                class="@error('max_usage') is-invalid @enderror" value="{{ old('max_usage') ?? '' }}">
                            @error('max_usage')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <!--Form Wrapper Item 2-->
                        <div class="form-field">
                            <div class="form-field radio-input">
                                Activation
                                <label class="switch">
                                    <input type="checkbox" checked="checked" name="is_active">
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>


                        <div class="form-field">
                            <label>Expiry At</label>
                            <div id="expiry_at" class="input-group date" data-date-format="dd-mm-yyyy">
                                <input class="form-control" type="date" name="expiry_at"
                                    class="@error('expiry_at') is-invalid @enderror"
                                    value="{{ old('expiry_at') ?? '' }}" />
                                <span class="input-group-addon"></span>
                                @error('expiry_at')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>


                        <div class="form-field-full">
                            <div class="common-button">
                                {{-- <button class="white-bttn">Preview</button> --}}
                                <button class="purple-btn">Save</button>
                            </div>
                        </div>


                    </div>
                </form>
            </div>
            <!-- /page content -->
        </div>
    </div>
@endsection
@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {

            $(".mul-select-interest").select2({
                placeholder: "Select Interest", //placeholder
                tags: true,
                tokenSeparators: ['/', ',', ';', " "]
            });
        })
    </script>
    <script src="{{ asset('admin/assets/js/api.js') }}"></script>
    <script src="{{ asset('admin/assets/js/business_accountsetup.js') }}"></script>
    <script>
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    $('#blah')
                        .attr('src', e.target.result)
                    /*.width(150)
                    .height(200);*/
                };
                logoUpload1(input);
                reader.readAsDataURL(input.files[0]);
                $("#user").addClass("hide");
                $("#blah").removeClass("hide");
                setInterval(() => {
                    $("#logo").val(window.localStorage.getItem('image'));
                }, 1000);

            }
        }
    </script>
@endsection
