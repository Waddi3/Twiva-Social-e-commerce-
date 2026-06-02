@extends('admin::layouts.app')
@section('title')
E-Shop Connected
@endsection
@section('content')
<div class="" id="business-dashboard">
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
        <div class="right_col dashboard-page" role="main">
            <!--********** Breadcrumb Start ***********-->
            <div class="page-title breadcrumb-wrapper">
                <ul class="breadcrumb breadcrumb-link">
                    <li><a href="{{ url()->previous() }}" class="back-link">Products</a></li>
                    <li><img src="{{ asset('admin/images/icons/gray-right-arrow.svg') }}" alt=""></li>
                    <li><a href="#">Connected eShop</a></li>
                </ul>
            </div>
            <!--**********  Breadcrumb End ***********-->

            <div class="catalog-page">
                <div class="eshop-product">
                    {{-- <img src="{{ asset('images/card-images/rect.jpg') }}"> --}}
                    <img src="{{ config('app.media_url') . $product->main_image_path }}" style="width: 100px;
                    border-radius: 54px;">
                    <p>{{ $product->name }}</p>
                    <p>{{ $product->company->busi_name }}</p>
                    <p>KSH {{ number_format($product->price) }}</p>
                    <p>KSH {{ number_format($product->retail_price) }}</p>
                </div>

                <div class="select-top select-search">
                    <div class="search-wrap">
                        <div class="search-panel">
                            <img src="{{ asset('admin/images/icons/search.svg') }}">
                            <input type="text" placeholder="Search by Email..." id="keywords">
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="admin-table" class="table table-striped table-bordered table-sm" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>ESHOP</th>
                                <th>INFLUENCER</th>
                                <th>EMAIL</th>
                                <th>SALE</th>
                            </tr>
                        </thead>

                        <tbody>
                            {{-- <tr>
                                <td>234235</td>
                                <td class="eshop-link">
                                    <p>Barone LLC.</p>
                                    <a href="">http://www.konmatfix.com</a>
                                </td>
                                <td>Albert Flores</td>
                                <td>dolores.chambers@example.com</td>
                                <td>40 ($250.00)</td>
                            </tr> --}}
                            @include('admin::business.eshopsConnectedData')
                        </tbody>
                    </table>
                    <input type="hidden" name="hidden_page" id="hidden_page" value="1"/>
                    <input type="hidden" name="hidden_column_name" id="hidden_column_name" value="id" />
                    <input type="hidden" name="hidden_sort_type" id="hidden_sort_type" value="asc"/>
                    <input type="hidden" name="hidden_status" id="hidden_status" value="2"/>
                    <input type="hidden" name="hidden_page_name" id="hidden_page_name" value="eshopConnected"/>
                    <input type="hidden" name="hidden_id" id="hidden_id" value="{{ $product->id }}"/>
                </div>

                <div class="pagination">
                    <div class="col-sm-5 p-0">
                        <!-- <div class="dataTables_info" id="example_info" role="status" aria-live="polite">Showing 1 to 10 of 57 entries</div></div> -->
                        <div class="col-sm-7 p-0">
                            <div class="dataTables_paginate paging_simple_numbers" id="example_paginate">
                                {{-- <ul class="pagination">
                                    <li class="paginate_button previous disabled" id="example_previous">
                                        <a href="#" aria-controls="example" data-dt-idx="0" tabindex="0">
                                            <img src="./images/icons/Left.svg" alt="">
                                        </a>
                                    </li>
                                    <li class="paginate_button active">
                                        <a href="#" aria-controls="example" data-dt-idx="1" tabindex="0">1</a>
                                    </li>
                                    <li class="paginate_button ">
                                        <a href="#" aria-controls="example" data-dt-idx="2" tabindex="0">2</a>
                                    </li>
                                    <li class="paginate_button ">
                                        <a href="#" aria-controls="example" data-dt-idx="3" tabindex="0">3</a>
                                    </li>
                                    <li class="paginate_button ">
                                        <a href="#" aria-controls="example" data-dt-idx="4" tabindex="0">4</a>
                                    </li>
                                    <li class="paginate_button ">
                                        <a href="#" aria-controls="example" data-dt-idx="5" tabindex="0">5</a>
                                    </li>
                                    <li class="paginate_button ">
                                        <a href="#" aria-controls="example" data-dt-idx="6" tabindex="0">6</a>
                                    </li>
                                    <li class="paginate_button ">
                                        <a href="#" aria-controls="example" data-dt-idx="6" tabindex="0">7</a>
                                    </li>
                                    <li class="paginate_button ">
                                        <a href="#" aria-controls="example" data-dt-idx="6" tabindex="0">8</a>
                                    </li>
                                    <li class="paginate_button ">
                                        <a href="#" aria-controls="example" data-dt-idx="6" tabindex="0">9</a>
                                    </li>
                                    <li class="paginate_button ">
                                        <a href="#" aria-controls="example" data-dt-idx="6" tabindex="0">10</a>
                                    </li>
                                    <li class="paginate_button next" id="example_next">
                                        <a href="#" aria-controls="example" data-dt-idx="7" tabindex="0">
                                            <img src="./images/icons/Right.svg" alt="">
                                        </a>
                                    </li>
                                </ul> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /page content -->
    </div>
</div>
@endsection
@section('scripts')
<script src="{{ asset('admin/js/basicfilter.js') }}"></script>
@endsection
