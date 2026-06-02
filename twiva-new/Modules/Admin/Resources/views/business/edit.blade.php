@php use Modules\User\Entities\User; @endphp
@extends('admin::layouts.app')
@section('title')
    Twiva Business Create
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
                        <li><a href="{{ route('admin.business.index') }}" class="back-link">Business</a></li>
                        <li><img src="{{ asset('admin/images/icons/gray-right-arrow.svg') }}" alt=""></li>
                        <li><a href="#">Edit</a></li>
                    </ul>
                </div>
                <!--**********  Breadcrumb End ***********-->

                <div class="title-wrapper">
                    <h4>
                        Edit Business
                    </h4>
                </div>
                @include('admin::layouts.message')
                <form method="POST" action="{{ route('admin.business.update', $id) }}" enctype="multipart/form-data">
                    @method('PUT')
                    @csrf
                    <input type="hidden" name="type" value="{{ User::BusinessType }}">
                    <div class="catalog-form">

                        <div class="title-wrapper address-heading">
                            <h4>Business</h4>
                        </div>
                        <div class="form-wrapper-list">
                            <div class="form-wrapper-item-1">

                                <div class="form-field">
                                    <label>Business Name</label>
                                    <input type="text" name="name" placeholder="" required
                                           value="{{ $business->name ?? ''}}"
                                           class="@error('name') is-invalid @enderror" maxlength="15">
                                    @error('name')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-field">
                                    <label>Contact Person Name</label>
                                    <input type="text" name="contact_name" placeholder=""
                                           value="{{ $company->contact_name ?? ''}}" required
                                           class="@error('contact_name') is-invalid @enderror" maxlength="15">
                                    @error('contact_name')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-field">
                                    <label>E-mail Address</label>
                                    <input type="email" name="email" placeholder=""
                                           value="{{ $business->email ?? ''}}"
                                           class="@error('email') is-invalid @enderror">
                                    @error('email')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-wrapper-item-2">
                                <div class="form-field-full mb-0">
                                    <div class="browse-box">
                                        <div class="browse-box-inner">
                                            <label for="product-featur-img">
                                                <input type="hidden" name="logo" id="logo"
                                                       value="{{ $business->profile_image ?? '' }}">
                                                @if($business && $business->profile_image)
                                                    <img id="blah" src="{{ $business->profile_image }}">
                                                @else
                                                    <img id="blah" src="{{ asset('admin/images/icons/Frame120.svg') }}">
                                                @endif
                                                <input type="file" name="logo" id="product-featur-img"
                                                       onchange="readURL(this);">
                                                <p>Business Logo</p>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-field">
                                    <label>Business Phone Number</label>
                                    <select class="country-code" id="country_code" name="phone_code">
                                        @foreach ($countries as  $country)
                                            <option value="{{ $country->phone_code }}"
                                                    @if($business->phone_code == $country->phone_code) selected @endif>{{ $country->country_code }}
                                                - {{ $country->phone_code }}</option>
                                        @endforeach
                                    </select>
                                    <input type="number" name="phone_number"
                                           value="{{ $business->phone_number ?? '' }}"
                                           required class="@error('phone_number') is-invalid @enderror" min="0">
                                    @error('phone_number')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-field-full">
                            <label>Description</label>
                            <textarea name="description" maxlength="400">{{ $business->description ?? '' }}</textarea>
                        </div>

                        <div class="title-wrapper address-heading">
                            <h4>Address</h4>
                        </div>

                        <div class="form-field multi-select-wrapper">
                            <label>Select Country</label>
                            <select class="mul-select-country" name="country" id="country" onchange="getStates()"
                                    required>
                                @foreach ($countries as $country)
                                    <option
                                            value="{{ $country->id }}" {{ $country->id == $company->country ? 'selected' : '' }}>{{ $country->id == $company->country ? $company->countryDetails->name: $country->country_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-field">
                            <label>State</label>
                            <select class="mul-select-state" name="state" id="state" onchange="getCities()" required>
                                @if ($states)
                                    @foreach ($states as $state)
                                        <option
                                                value="{{ $state->id }}" {{ $state->id == $company->state ? 'selected' : '' }}>{{ $state->id == $company->state ? $company->stateDetails->name : $state->state_name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <div class="form-field">
                            <label>City</label>
                            <select class="mul-select-city" name="city" id="city">
                                @if ($cities)
                                    @foreach ($cities as $city)
                                        <option
                                                value="{{ $city->id }}" {{ ( $city->id == $company->city) ? 'selected' : '' }}>{{ $city->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <div class="form-field">
                            <label>Locality / Area / Street</label>
                            <input type="text" name="area" placeholder="" required value="{{ $company->area ?? ''}}"
                                   maxlength="30">
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

    <script>


        function getStates() {
            $.ajax({
                type: 'GET',
                url: '/getStates',
                data: {
                    'country_id': $("#country").val()
                },
                success: function (data) {
                    $('.mul-select-state').html('');
                    if (!data.states) return;
                    data.states.forEach(element => {
                        $('.mul-select-state').append(`
                        <option value="${element.id}">${element.name}</option>`
                        )
                    });

                },
                error: function (data) {
                    $('.mul-select-state').html('No state Found');
                }
            });
        }

        function getCities() {
            $.ajax({
                type: 'GET',
                url: '/getCities',
                data: {
                    'state_id': $("#state").val()
                },
                success: function (data) {
                    $('.mul-select-city').html('');
                    if (!data.cities) return;
                    data.cities.forEach(element => {
                        $('.mul-select-city').append(`
                        <option value="${element.id}">${element.name}</option>`
                        )
                    });

                },
                error: function (data) {
                    console.log(data);
                    $('.mul-select-city').html('No City Found');
                }
            });
        }

    </script>
    <script>

        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#blah')
                        .attr('src', e.target.result)
                };
                logoUpload1(input);
                reader.readAsDataURL(input.files[0]);
                $("#user").addClass("hide");
                $("#blah").removeClass("hide");
                setInterval(() => {
                    $("#logo").val(window.localStorage.getItem('image'));
                }, 2000);
            }
        }

    </script>

@endsection
