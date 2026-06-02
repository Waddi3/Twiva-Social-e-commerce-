<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title')</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
          integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="{{ asset('admin/css/animate.min.css') }}" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap.min.css">


    <!-- Custom styling plus plugins -->
    <link href="{{ asset('admin/css/custom.css') }}" rel="stylesheet">
    <link href="{{ asset('admin/css/style.css') }}" rel="stylesheet">

    <link rel="preconnect" href="https://fonts.gstatic.com">

    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@400;600;700;900&display=swap"
          rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('admin/images/favicon/favicon.ico') }}"/>
    <style>
        .cursor-pointer{
            cursor: pointer;
        }
    </style>
    @yield('head')
</head>

<body class="nav-md">

<!-- Top Navbar Start -->
<header>
    <!-- Navbar Logo -->
    <div class="logo d-flex align-items-center">
        <div class="nav toggle">
            <a id="menu_toggle"><img src="{{ asset('admin/images/toggle-icon.svg') }}" alt=""></a>
        </div>

        <img alt="" src="{{ asset('admin/images/logo/logo-white.svg') }}">
    </div>

    <!-- Navbar Menu -->
    <div class="top_nav">
        <div class="nav_menu">
            <nav class="" role="navigation">

                <ul class="nav navbar-nav navbar-right">
                    <li class="">
                        <a href="javascript:" class="user-profile dropdown-toggle" data-toggle="dropdown"
                           aria-expanded="false">
                            {{-- <img src="{{ asset('images/icons/img.jpg') }}" alt=""> --}}
                            <!--  {{ Auth::user()->name ?? 'Admin' }} -->
                            Administrator
                            <img src="{{ asset('admin/images/icons/chevron-down-white.svg') }}" alt=""
                                 class="down-arrow">
                        </a>

                        <ul class="dropdown-menu dropdown-usermenu animated fadeInDown pull-right">
                            <li class="header-company-name-wrapper">
                                <p class="company-name">Administrator</p>
                            </li>
                            <li><a class="nav-link" href="{{ route('admin.logout') }}"
                                   onclick="event.preventDefault(); document.getElementById('frm-logout').submit();">
                                    <button class="purple-btn">Log Out</button>
                                </a>
                            </li>
                            <form id="frm-logout" action="{{ route('admin.logout') }}" method="POST"
                                  style="display: none;">
                                {{ csrf_field() }}
                            </form>
                        </ul>
                    </li>

                    <li role="presentation">
                        <a href="{{ route('admin.notification.index') }}" class="info-number" aria-expanded="false">
                            <img src="{{ asset('admin/images/icons/bell-icon.svg') }}" alt="">
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>

</header>

@yield('content')

<script src="{{asset('admin/assets/lib/jquery-3.2.1.min.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
        integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous">
</script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
        integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous">
</script>

<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap.min.js"></script>
<script src="{{ asset('admin/js/slick.min.js') }}"></script>

<script src="{{ asset('admin/js/custom.js') }}"></script>
<script src="{{ asset('admin/js/chart.min.js') }}"></script>
@yield('scripts')
</body>

</html>

