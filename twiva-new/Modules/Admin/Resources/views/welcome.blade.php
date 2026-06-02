<!DOCTYPE html>
<html lang="en">

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <!-- Meta, title, CSS, favicons, etc. -->
        <link rel="icon" type="image/png" href="{{ asset('admin/images/favicon/favicon.ico') }}"/>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Twiva Admin Login</title>

        <!-- Bootstrap core CSS -->

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link href="{{ asset('admin/css/animate.min.css') }}" rel="stylesheet">
        <!-- Custom styling plus plugins -->
        <link rel="stylesheet" href="{{ asset('admin/css/mobile-view.css') }}">
        <link href="{{ asset('admin/css/custom.css') }}" rel="stylesheet">
        <link href="{{ asset('admin/css/style.css') }}" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

        <script src="{{asset('admin/assets/lib/jquery-3.2.1.min.js')}}"></script>
        <link rel="preconnect" href="https://fonts.gstatic.com">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
        <link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@400;600;700;900&display=swap" rel="stylesheet">

    </head>
<body  class="pt-0">
    <div class="login pt-5">
        <div class="container">
            <div class="login-inner">

                <div class="login-left">
                    <!-- <img src="images/banner/login.png"> -->
                </div>
                <div class="login-right">
                    <div class="login-section login-form">
                        @auth

                        <a href="{{ url('/dashboard') }}" class="text-sm text-gray-700 underline">Dashboard</a>

                         @else

                        <form id="login-form" method="post" action="{{ route('admin.login.post') }}">
                            @csrf
                            <div class="logo"><img src="{{ asset('admin/images/logo/logo.svg') }}" /></div>
                            @if(session()->has('error'))
                                <h5 class="login-error alert alert-danger m-0" role="alert">{{ session()->get('error') }}</h5>
                            @endif
                            {{-- @include('layouts.message') --}}
                            <div class="input-field">

                                <!-- <h5 class="login-error alert alert-success" role="alert" id="emailerr">Login Successfully</h5> -->
                                <label>E-mail Address</label>
                                <input id="email" type="text" name="username" placeholder="Email" required />
                                <h5 id="usercheck" class="empty-field-error"></h5>
                            </div>
                            <div class="input-field">
                                <label>Password</label>
                                <input id="password" type="password" name="password" placeholder="*******"  required/>
                                <h5 id="passcheck" class="empty-field-error"></h5>
                            </div>

                            <button type="submit" id="submitbbtn"><i class="fa fa-spinner fa-spin mr-1 text-white d-none"></i> Login</button>

                        </form>
                @endauth

                     </div>
                 </div>
            </div>
        </div>
        <div class="footer-login">
            Copyright © 2021 Twiva. All Rights Reserved.
            <a href="#">Terms & Conditions</a>
        </div>
    </div>


</body>

<script src="{{ asset('admin/js/bootstrap.min.js') }}"></script>
{{-- <script src="{{ asset('admin/js/custom.js') }}"></script> --}}

</html>
