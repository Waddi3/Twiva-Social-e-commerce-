@php use Modules\User\Entities\User; @endphp
@extends('admin::layouts.app')
@section('title')
    Twiva Partner Create
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
                        <li><a href="{{ route('admin.partner.index') }}" class="back-link">Partner</a></li>
                        <li><img src="{{ asset('admin/images/icons/gray-right-arrow.svg') }}" alt=""></li>
                        <li><a href="#">Create New</a></li>
                    </ul>
                </div>
                <!--**********  Breadcrumb End ***********-->

                <div class="title-wrapper">
                    <h4>
                        Add Partner
                    </h4>
                </div>
                @include('admin::layouts.message')

                <form method="POST" action="{{ route('admin.partner.store') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="type" value="{{ User::PartnerType }}">
                    <div class="catalog-form">
                        <div class="title-wrapper address-heading">
                            <h4>User</h4>
                        </div>
                        <div class="form-field">
                            <label>User Email Id</label>
                            <input type="email" name="email" placeholder="Email" required
                                   class="@error('email') is-invalid @enderror" value="{{ old('email') ?? '' }}">
                            @error('email')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-field">
                            <label>Password</label>
                            <input type="password" name="password" placeholder="Password" required
                                   class="@error('password') is-invalid @enderror" value="{{ old('password') ?? "" }}">
                            @error('password')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="title-wrapper address-heading">
                            <h4>Partner</h4>
                        </div>
                        <div class="form-wrapper-list">
                            <div class="form-wrapper-item-1">

                                <div class="form-field">
                                    <label>Partner Name</label>
                                    <input type="text" name="name" placeholder="" required
                                           class="@error('name') is-invalid @enderror"
                                           value="{{ old('name') ?? '' }}" maxlength="15">
                                    @error('name')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-field">
                                    <label>Contact Person Name</label>
                                    <input type="text" name="contact_name" placeholder="" required
                                           class="@error('contact_name') is-invalid @enderror"
                                           value="{{ old('contact_name') ?? '' }}" maxlength="15">
                                    @error('contact_name')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-wrapper-item-2">
                                <div class="form-field-full m-0">
                                    <div class="browse-box">
                                        <div class="browse-box-inner">
                                            <label for="product-featur-img">
                                                <input type="hidden" name="logo" id="logo"
                                                       value="{{ old('logo') ?? '' }}">
                                                <img id="blah" src="{{ asset('admin/images/icons/Frame120.svg') }}">
                                                <input type="file" name="logo" id="product-featur-img"
                                                       onchange="readURL(this);">
                                                <p>Partner Logo</p>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-field">
                                    <label>Partner Phone Number</label>
                                    <select class="country-code" id="country_code" name="phone_code">
                                        @foreach ($countries as  $country)
                                            <option value="{{ $country->phone_code }}"
                                                    @if($country->phone_code == '254') selected @endif>{{ $country->country_code }}
                                                - {{ $country->phone_code }} </option>
                                        @endforeach
                                    </select>
                                    <input type="number" name="phone_number" required
                                           class="@error('phone_number') is-invalid @enderror"
                                           value="{{ old('phone_number') ?? '' }}" min=0>
                                    @error('phone_number')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-field-full">
                            <label>Description</label>
                            <textarea name="description" maxlength="400">{{ old('description') ?? '' }}</textarea>
                        </div>

                        <div class="title-wrapper address-heading">
                            <h4>Address</h4>
                        </div>

                        <div class="form-field multi-select-wrapper">
                            <label>Select Country</label>
                            <select class="mul-select-country" name="country" id="country" onchange="getStates()"
                                    required class="@error('country') is-invalid @enderror">
                                <option selected disabled>Select Country</option>
                                @foreach ($countries as $country)
                                    <option value="{{ $country->id }}">{{ $country->country_name }}</option>
                                @endforeach
                            </select>
                            @error('country')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-field">
                            <label>State</label>
                            <select class="mul-select-state" name="state" id="state" onchange="getCities()" required
                                    class="@error('state') is-invalid @enderror">

                            </select>
                            @error('state')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-field">
                            <label>City</label>
                            <select class="mul-select-city" name="city" id="city">

                            </select>
                        </div>

                        <div class="form-field">
                            <label>Locality / Area / Street</label>
                            <input type="text" name="area" placeholder="" required value="{{ old('area') ?? '' }}"
                                   maxlength="30">
                        </div>

                    </div>

                    <div class="form-field-full save-btn">
                        <div class="common-button">
                            <button class="purple-btn">Save</button>
                        </div>
                    </div>
            </div>
            </form>
            <!-- Right Column End --> <!-- /Page Content -->
        </div>
    </div>
@endsection
@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/select2.min.js"></script>

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
    <script src="{{ asset('admin/assets/js/api.js') }}"></script>
    <script src="{{ asset('admin/assets/js/business_accountsetup.js') }}"></script>
    <script>

        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
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
                }, 2000);

            }
        }

    </script>

@endsection
