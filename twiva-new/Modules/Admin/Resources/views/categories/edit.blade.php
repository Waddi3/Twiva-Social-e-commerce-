@extends('admin::layouts.app')
@section('title')
    Edit {{$category->name}} category
@endsection
@section('head')
    <!-- multi select -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/css/select2.min.css">
    <!-- multi select -->
    <style>
        .catalog-form {
            display: block;
        }
        .table-responsive th {
            padding: 16px;
            font-family: inter;
            font-style: normal;
            font-weight: 600;
            font-size: 12px;
            line-height: 15px;
            text-transform: uppercase;
            color: rgba(19, 50, 86, 0.7);
            border-top: 1px solid rgba(29, 59, 168, 0.1);
            border-bottom: 1px solid rgba(19, 50, 86, 0.5);
            border-right: 1px solid rgba(19, 50, 86, 0.1);
            white-space: nowrap;
        }
        .table-responsive td {
            vertical-align: middle;
            padding: 16px;
            font-family: Source Sans Pro;
            font-style: normal;
            font-weight: normal;
            font-size: 14px;
            line-height: 18px;
            color: #4B5563;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
            white-space: nowrap;
        }
    </style>
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
                        <li><a href="{{ route('admin.category.index') }}" class="back-link">Category</a></li>
                        <li><img src="{{ asset('admin/images/icons/gray-right-arrow.svg') }}" alt=""></li>
                        <li><a href="#">Edit</a></li>
                    </ul>
                </div>
                <!--**********  Breadcrumb End ***********-->

                <div class="title-wrapper">
                    <h4>
                        Edit {{$category->name}} Category
                    </h4>
                </div>
                @include('admin::layouts.message')
                <form method="POST" action="{{ route('admin.category.update', ['id' => $category->id]) }}" enctype="multipart/form-data">
                    @csrf
                    <div class="catalog-form">

                        <div class="title-wrapper address-heading">
                            <h4>Categories</h4>
                        </div>

                        <div class="form-wrapper-list">
                            <div class="form-wrapper-item-1">

                                <div class="form-field">
                                    <label>Name</label>
                                    <input type="text" name="name" placeholder="" required
                                           value="{{ $category->name ?? ''}}"
                                           class="@error('name') is-invalid @enderror" maxlength="15">
                                    @error('name')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-wrapper-list">
                            <div class="form-field">
                                <label>Is Active</label>
                                <label class="switch">
                                    <input name="is_active" class="toggle-featured" type="checkbox"
                                        {{ $category->is_active ? 'checked' : '' }}>
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>
                        <div class="form-wrapper-list">
                            <div class="form-field">
                                <label>Is featured</label>
                                <label class="switch">
                                    <input name="is_featured" class="toggle-featured" type="checkbox"
                                        {{ $category->is_featured ? 'checked' : '' }}>
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>

                        <div class="title-wrapper address-heading">
                            <h4>Sub categories of {{$category->name}}</h4>
                        </div>

                        <div id="business-dashboard">
                            <div class="table-responsive">
                                <table id="admin-table" class="table table-striped table-bordered table-sm" cellspacing="0"
                                       width="100%">
                                    <thead>
                                    <tr>
                                        <th class="sorting" data-sorting_type="desc" data-column_name="id">ID<span
                                                id="id_icon"></span><span class="fa fa-angle-down"></span></th>
                                        <th class="th-sm">NAME</th>
                                        <th>Active</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    @foreach( $category->subcategories as $subcategory )
                                        <tr>
                                            <td>{{ $subcategory->id }}</td>
                                            <td>
                                                {{$subcategory->name}}
                                            </td>
                                            <td class="t-action">
                                                <label class="switch">
                                                    <input data-id="{{ $subcategory->id }}" class="toggle-activation" type="checkbox"
                                                        {{ $subcategory->is_active ? 'checked' : '' }}>
                                                    <span class="slider round"></span>
                                                </label>
                                            </td>
                                            <td class="t-action">
                                                <div class="d-flex align-items-center">
                                                    <a href="{{ route('admin.subcategory.edit', ['id' => $subcategory->id]) }}"><img
                                                            src="{{ asset('admin/images/icons/edit-icon.svg') }}" alt=""></a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tbody>
                                    </tbody>
                                </table>
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

    @section('scripts')
        <script>
            $(document).on('change', '.toggle-activation', function(event) {
                event.preventDefault();
                var status = $(this).prop('checked') == true ? 1 : 0;
                var status_name = status ? 'Active' : 'Inactive';
                let check = confirm('Are you sure you want to ' + status_name);
                if (check == false) {
                    status ? $(this).prop("checked", false) : $(this).prop("checked", true)
                    return;
                }
                var category_id = $(this).data('id');

                $.ajax({
                    type: "GET",
                    dataType: "json",
                    url: "{{ route('admin.subcategory.activation') }}",
                    data: {
                        'status': status,
                        'category_id': category_id
                    },
                    success: function(data) {
                        console.log(data.success)
                        if (status == 1) {
                            $('.eshop-status-' + user_id).removeClass('eshop-failed');
                            $('.eshop-status-' + user_id).addClass('eshop-active').html('<p>Active</p>')
                        } else {
                            $('.eshop-status-' + user_id).removeClass('eshop-active');
                            $('.eshop-status-' + user_id).addClass('eshop-failed').html('<p>Inactive</p>')
                        }
                    }
                });
            });
        </script>
    @endsection

@endsection
