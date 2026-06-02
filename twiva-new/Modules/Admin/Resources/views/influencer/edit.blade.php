@php use Modules\User\Entities\User; @endphp
@extends('admin::layouts.app')
@section('title')
    Twiva Inflencer Edit
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
                        <li><a href="{{ route('admin.influencers.index') }}" class="back-link">Influencers</a></li>
                        <li><img src="{{ asset('admin/images/icons/gray-right-arrow.svg') }}" alt=""></li>
                        <li><a href="#">Edit</a></li>
                    </ul>
                </div>
                <!--**********  Breadcrumb End ***********-->

                <div class="title-wrapper">
                    <h4>
                        Edit Influencer
                    </h4>
                </div>

                @include('admin::layouts.message')

                <form method="POST" action="{{ route('admin.influencers.update', $id) }}" enctype="multipart/form-data">
                    @method('PUT')
                    @csrf
                    <input type="hidden" name="type" value="{{ User::InfluencerType }}">
                    <div class="catalog-form">
                        {{-- <div class="title-wrapper address-heading">
                            <h4>User</h4>
                        </div> --}}
                        {{-- <div class="form-field">
                            <label>User Email Id</label>
                            <input type="email" name="email" placeholder="email" required class="@error('email') is-invalid @enderror"  value="{{ old('email') ?? '' }}">
                            @error('email')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-field">
                            <label>Password</label>
                            <input type="password" name="password" placeholder="Password" required class="@error('password') is-invalid @enderror" value="{{ old('password') ?? "" }}">
                            @error('password')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div> --}}

                        <div class="title-wrapper address-heading">
                            <h4>Influencer Detail</h4>
                        </div>
                        <div class="form-wrapper-list">
                            <!--Form Wrapper Item 1-->
                            <div class="form-wrapper-item-1">

                                <div class="form-field">
                                    <label>Full Name</label>
                                    <input type="text" placeholder="Globex Corporation" name="name" required
                                           class="@error('name') is-invalid @enderror"
                                           value="{{ $influencer->name ?? '' }}">
                                    @error('name')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>


                                <div class="form-field">
                                    <label>Phone Number</label>
                                    <select class="country-code" id="country_code" name="phone_code">
                                        @foreach ($countries as  $country)
                                            <option value="{{ $country->phone_code }}"
                                                    @if($influencer->phone_code == $country->phone_code) selected @endif>{{ $country->country_code }}
                                                - {{ $country->phone_code }}</option>
                                        @endforeach
                                    </select>
                                    <input type="number" name="phone_number"
                                           value="{{ $influencer->phone_number ?? '' }}"
                                           required class="@error('phone_number') is-invalid @enderror" min="0">
                                    @error('phone_number')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- <div class="form-field">
                                    <label>E-mail Address</label>
                                    <input type="email" placeholder="Email">
                                </div> --}}

                                <div class="form-field">
                                    <label>Date of Birth</label>
                                    <div id="dob-btn" class="input-group date" data-date-format="dd-mm-yyyy" required>
                                        <input class="form-control" type="date" name="dob"
                                               class="@error('dob') is-invalid @enderror"
                                               value="{{ $influencer->InfluencerDetails->dob ?? "" }}"/>
                                        <span class="input-group-addon"></span>
                                        @error('name')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                            </div>

                            <!--Form Wrapper Item 2-->
                            <div class="form-wrapper-item-2">

                                <div class="form-field-full">
                                    <div class="browse-box">
                                        <div class="browse-box-inner">
                                            <label for="product-featur-img">
                                                <input type="hidden" name="logo" id="logo"
                                                       value="{{ $influencer->profile_image ?? '' }}">
                                                {{-- <input type="file" id="product-featur-img"> --}}
                                                @if($influencer->profile_image ?? '')
                                                    <img id="blah" src="{{ $influencer->profile_image ?? '' }}">
                                                @else
                                                    <img id="blah" src="{{ asset('admin/images/icons/Frame120.svg') }}">
                                                @endif
                                                {{-- <img  src="../images/card-images/browse.png"> --}}
                                                <input type="file" id="product-featur-img" name="profile_image"
                                                       onchange="readURL(this);">
                                                <!-- <p class="image-size">image size 2000 X 1200</p> -->
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-field radio-input">
                                    <label>Gender</label>
                                    @if(isset($influencer->InfluencerDetails->gender))
                                        <ul class="input-list">
                                            <li>
                                                <input type="radio" name="gender" id="male"
                                                       value="{{ User::MALE }}" required
                                                       @if($influencer->InfluencerDetails->gender == User::MALE)
                                                           checked
                                                        @endif>
                                                <label for="male">Male</label>
                                            </li>
                                            <li>
                                                <input type="radio" name="gender" id="female"
                                                       value="{{ User::FEMALE }}" required
                                                       @if($influencer->InfluencerDetails->gender == User::FEMALE)
                                                           checked
                                                        @endif>
                                                <label for="female">Female</label>
                                            </li>
                                            <li>
                                                <input type="radio" name="gender" id="others"
                                                       value="{{ User::NOT_SPECIFIED }}" required
                                                       @if($influencer->InfluencerDetails->gender == User::NOT_SPECIFIED)
                                                           checked
                                                        @endif>
                                                <label for="others">Other</label>
                                            </li>
                                        </ul>
                                    @endif
                                </div>
                            </div>

                        </div>

                        <div class="form-field-full" style="margin-bottom:36px;">
                            <label>Description</label>
                            <textarea name="description">{{ $influencer->description ?? ''}}</textarea>
                            @error('description')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-field multi-select-wrapper">
                            <label>Interest</label>
                            <select class="mul-select-interest" multiple="multiple" name="interests[]"
                                    class="@error('interests') is-invalid @enderror"
                                    {{--value="{{ old('interests') ?? '' }}"--}}>
                                @if($influencer->interests->pluck('name')->toArray())
                                    {{--
                                            <?php $interests = explode(',', $influencer->interests) ?>
                                    --}}
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                                @if(in_array($category->id, $interests)) selected @endif>{{ $category->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            @error('interests')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>


                        <div class="form-field-full save-btn">
                            <div class="common-button">
                                <!-- <button class=" white-bttn">Preview</button> -->
                                <button class="purple-btn">Update</button>
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
                $(document).ready(function () {

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
