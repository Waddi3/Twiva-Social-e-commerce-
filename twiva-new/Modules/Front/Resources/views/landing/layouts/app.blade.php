<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css"
          integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous"/>
    <link rel="stylesheet" href="{{ asset('front/landing/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('front/landing/css/style.css') }}">
    <link rel="shortcut icon" href="{{asset('favicon.ico')}}" type="image/x-icon">

</head>

<body>
<!--Main Header-->
<div class="main-header">
    <!--Top Navbar-->
    <header class="header-wrapper">
        <div class="nav-p">
            <nav class="navbar navbar-expand-md bg-dark navbar-dark">

                <!-- Toggler/collapsibe Button -->
                <button class="navbar-toggler" type="button">
                    <span><img src="{{ asset('front/landing/images/toggle-icon.svg') }}" alt=""></span>
                </button>

                <!-- Nav Logo -->
                <a class="navbar-brand" href="/">
                    <img src="{{ asset('front/landing/images/logo.svg') }}" alt="">
                </a>


                <!-- Navbar links -->
                <div class="" id="collapsibleNavbar">
                    <ul class="navbar-nav">

                    </ul>

                    <div class="navbar-btn-wrap">
                        @if(auth()->guard('business')->user())
                            <a href="https://business.twiva.co.ke/business/dashboard-2.php">
                                <button class="login-btn">Dashboard</button>
                            </a>
                        @else
                            <a href="{{config('twiva.bases.app_url')}}">
                                <button class="login-btn">Log In</button>
                            </a>
                            <a href="{{ config('twiva.bases.app_url') }}">
                                <button class="signup-btn">Sign Up</button>
                            </a>
                        @endif
                    </div>
                </div>

            </nav>
        </div>
    </header>

    @yield('content')
    <footer class="footer-wrapper">
        <div class="footer-section f-p">
            <div class="footer-lists">
                <h1 class="footer-list-heading">
                    <img src="{{ asset('front/landing/images/icons/Union.svg') }}" alt=""/>
                    TWIVA
                </h1>
                <a href="{{ route('home.about') }}" class="footer-list-item">About Us</a>
                <a href="{{config('services.blogSite.url')}}" class="footer-list-item">Blog</a>
            </div>

            <div class="footer-lists">
                <h1 class="footer-list-heading">Legal</h1>
                <a href='{{ route('home.policy') }}' class="footer-list-item">Privacy Policy</a>
                <a href='{{ route('home.terms-and-conditions') }}' class="footer-list-item">Terms & Conditions</a>
                <a href='{{ route('home.banned') }}' class="footer-list-item">Banned Products</a>
            </div>

            <div class="footer-lists">
                <h1 class="footer-list-heading">Contact Us</h1>
                <a href="tel:+254708088114" class="footer-list-item"><img
                            src="{{ asset('front/landing/images/icons/tel.svg') }}" alt=""> +254708088114</a>
                <a href="mailto:info@twiva.co.ke" class="footer-list-item">
                    <img src="{{ asset('front/landing/images/icons/mail-icon.svg') }}" alt="">
                    info@twiva.co.ke
                </a>
                <a href="https://goo.gl/maps/Kyy7syGZmyyGCpPD7" target="_blank" class="footer-list-item d-flex"
                   style="max-width: 280px;">
                    <img src="{{ asset('front/landing/images/icons/address-icon.png') }}" alt="">
                    Kose Heights, Argwings Kodhek Road, Hurlingham, Nairobi, Kenya Office No. W.3.2B on 3rd Floor
                </a>
            </div>
        </div>

        <div class="copyright-section">
            <div class="copyright-heading">
                <p>Copyright © 2022 Twiva. All Rights Reserved.</p>
            </div>

            <ul class="social-icons">
                <li class="icon-list-item">
                    <a href="https://www.instagram.com/twiva_influence/" target="_blank"><img
                                src="{{ asset('front/landing/images/icons/instagram.svg') }}" alt=""></a>
                </li>

                <li class="icon-list-item">
                    <a href="https://x.com/Twiva_influence" target="_blank"><img
                                src="{{ asset('front/landing/svg/x.svg') }}" alt=""></a>
                </li>

                <li class="icon-list-item">
                    <a href="https://www.facebook.com/twiva/" target="_blank"><img
                                src="{{ asset('front/landing/images/icons/facebook.svg') }}" alt=""></a>
                </li>

                <li class="icon-list-item" style="width:70px;">
                    <a href="https://wa.me/254708088114" target="_blank">
                    <!-- <img src="{{--{{ asset('front/landing/images/icons/whatsapp-black.svg') }}--}}" alt=""> -->
                        <i class="fab fa-whatsapp"></i>
                        Talk to Us
                    </a>
                </li>

            </ul>

        </div>
    </footer>


    <script src="{{ asset('front/landing/js/jquery-3.5.1.min.js') }}"></script>
    <script src="{{ asset('front/landing/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('front/landing/js/custom.js') }}"></script>
</div>
</body>

</html>
