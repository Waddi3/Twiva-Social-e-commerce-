@extends('admin::layouts.app')
@section('title')
    Edit {{$subcategory->name}} Sub Category
@endsection
@section('head')
    <!-- multi select -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/css/select2.min.css">
    <!-- multi select -->
@endsection
@section('content')

    <div>
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
                        <li><a href="{{ route('admin.subcategory.index') }}" class="back-link">Sub Category</a></li>
                        <li><img src="{{ asset('admin/images/icons/gray-right-arrow.svg') }}" alt=""></li>
                        <li><a href="#">Edit</a></li>
                    </ul>
                </div>
                <!--**********  Breadcrumb End ***********-->

                <div class="title-wrapper">
                    <h4>
                        Edit {{$subcategory->name}} Sub Category
                    </h4>
                </div>
                @include('admin::layouts.message')
                <form method="POST" action="{{ route('admin.subcategory.update', ['id' => $subcategory->id]) }}" enctype="multipart/form-data">
                    @csrf
                    <div class="catalog-form">

                        <div class="title-wrapper address-heading">
                            <h4>Edit Sub category</h4>
                        </div>

                        <div class="form-wrapper-list">
                            <div class="form-wrapper-item-1">
                                <div class="form-field-full">
                                    <label for="name">Name</label>
                                    <input type="text" id="name" name="name" required value="{{$subcategory->name}}"
                                              class="@error('name') is-invalid @enderror">
                                    @error('name')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-field multi-select-wrapper">
                            <label>Category</label>
                            <select name="category" id="category" required>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" @if($category->id === $subcategory->category_id) selected @endif>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('category')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-wrapper-list">
                            <div class="form-field">
                                <label>Is Active</label>
                                <label class="switch">
                                    <input name="is_active" class="toggle-featured" type="checkbox" @if($subcategory->is_active) checked @endif>
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>

                    </div>
                    <div class="form-field-full save-btn">
                        <div class="common-button">
                            <button class="purple-btn">Update</button>
                        </div>
                    </div>
                </form>
            </div>
            <!-- Right Column End --> <!-- /Page Content -->
        </div>
    </div>

@endsection
@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/select2.min.js"></script>
    <script src="{{ asset('admin/assets/js/api.js') }}"></script>
    <script src="{{ asset('admin/assets/js/business_accountsetup.js') }}"></script>

@endsection
