@extends('admin::layouts.app')
@section('title')

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
                    @if($user->type == 1)
                        <li><a href="{{ route('admin.business.index') }}" class="back-link">User</a></li>
                    @endif
                    @if($user->type == 2)
                        <li><a href="{{ route('admin.influencers.index') }}" class="back-link">User</a></li>
                    @endif
                    <li><img src="{{ asset('admin/images/icons/gray-right-arrow.svg') }}" alt=""></li>
                    <li><a href="#">Change Password</a></li>
                </ul>
            </div>
            <!--**********  Breadcrumb End ***********-->
           <div class="title-wrapper">
                <h4>Change Password of {{ $user->email }}</h4>
            </div>

            @include('admin::layouts.message')

            <form method="post" action="{{ route('admin.change.password.post') }}">
                @csrf
            <div class="catalog-form">
                <input type="hidden" name="id" value="{{ $user->id }}">
                <div class="form-field">
                    <label>New Password</label>
                    <input type="password" id="password" name="password" placeholder="*******" onkeyup="checkPasswordLength()" required class="@error('password') is-invalid @enderror">
                    <span id="password_check"></span>
                    @error('password')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>



                <div class="form-field">
                    <label>Confirm Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" placeholder="*******"  onkeyup="checkPasswordMatch()"  required class="@error('password_confirmation') is-invalid @enderror">
                    <span id="password_confirmation_check"></span>
                    @error('password_confirmation')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-field-full">
                    <div class="common-button">
                        <button id="updatePassword" class="purple-btn" type="submit">Update</button>
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
<script>
function checkPasswordLength() {
    var password = $("#password").val();
    if (password.length <= 7) {
        $("#password_check").html("Password must be at least 8 characters").css("color", "red");
        $("#updatePassword").attr("disabled", true);
    } else {
        $("#password_check").html("");
        $("#updatePassword").attr("disabled", false);
    }
}


function checkPasswordMatch() {
    var password = $("#password").val();
    var password_confirmation = $("#password_confirmation").val();
    if (password != password_confirmation) {
        $("#password_confirmation_check").html("Passwords does not match!").css("color", "red");
        $("#updatePassword").attr("disabled", true);
    } else {
        $("#password_confirmation_check").html("Passwords match.").css("color", "green");
        $("#updatePassword").attr("disabled", false);
    }
}

function validatePassword() {
    let current_password = $("#current_password").val();
    let password = $("#password").val();
    let password_confirmation = $("#password_confirmation").val();
    if(current_password.replace(/\s+/g, '') == '') {
        $("#current_password_check").html("Please Enter the current Password.").css("color", "red");
        return;
    }
    if(password.replace(/\s+/g, '') == '') {
        $("#password_check").html("Please Enter the New Password.").css("color", "red");
        return;
    }
    if(password_confirmation.replace(/\s+/g, '') == '') {
        $("#password_confirmation_check").html("Please Enter the Current Password.").css("color", "red");
        return;
    }
    if (password != password_confirmation) {
        $("#password_confirmation_check").html("Passwords does not match!").css("color", "red");
        $("#updatePassword").attr("disabled", true);
        return
    }
    if (password.length <= 7) {
        $("#password_check").html("Password must be at least 8 characters").css("color", "red");
        $("#updatePassword").attr("disabled", true);
        return
    }

    let data = {
        "current_password": current_password,
        "password": password,
        "password_confirmation": password_confirmation
    }

    updatePassword(data);

}
</script>
@endsection
