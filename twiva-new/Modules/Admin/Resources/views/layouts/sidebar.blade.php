<!-- Sidebar menu -->
<div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
    <div class="menu_section">
        <h4>Menu</h4>
        <ul class="nav side-menu">
            <li>
                <a href="{{ route('admin.dashboard') }}">
                    <img src="{{ asset('admin/images/icons/Dashboard-icon.svg') }}">
                    Dashboard
                    <span class="fa fa-chevron-down"></span>
                </a>

            </li>
            <li>
                <a href="{{ route('admin.business.index') }}">
                    <img src="{{ asset('admin/images/icons/Business.svg') }}">
                    Businesses
                    <span class="fa fa-chevron-down"></span>
                </a>
            </li>

            <li class="">

                <a href="#submenu4" class="collapsed text-capitalizen" data-toggle="collapse" data-target="#submenu4"
                    aria-expanded="false"> <img src="{{ asset('admin/images/icons/Influencer.svg') }}">
                    Influencers
                    @if (Request::segment(2) == 'verify' || Request::segment(1) == 'influencers')
                        <span class="fa fa-chevron-down rotate-active"></span>
                    @else
                        <span class="fa fa-chevron-down"></span>
                    @endif
                </a>


                <div class="collapse @if (Request::segment(2) == 'verify') show @endif @if (Request::segment(1) == 'influencers') show @endif"
                    id="submenu4" aria-expanded="false">
                    <ul class="nav side-menu">
                        <li class="">
                            <a class="pl-5" href="{{ route('admin.influencers.index') }}">
                                Influencers List
                            </a>
                        </li>
                        {{-- <li class="">
                            <a class="pl-5" href="{{ route('admin.influencers.verify') }}">
                                Influencer Verification
                            </a>
                        </li> --}}
                        <li class="">
                            <a class="pl-5" href="{{ route('admin.badges.index') }}">
                                Influencer Reseller Verification
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <li>
                <a href="{{ route('admin.partner.index') }}">
                    <img src="{{ asset('admin/images/icons/Buyer.svg') }}">
                    Partners
                    <span class="fa fa-chevron-down"></span>
                </a>
            </li>

            <li>
                <a href="{{ route('admin.campaign.index') }}">
                    <img width="32" height="20" src="{{ asset('admin/images/icons/Vector-2.svg') }}">
                    Campaigns
                    <span class="fa fa-chevron-down"></span>
                </a>

            </li>

            <li>
                <a href="{{ route('admin.buyers.index') }}">
                    <img src="{{ asset('admin/images/icons/Buyer.svg') }}">
                    Buyers
                    <span class="fa fa-chevron-down"></span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.products.index') }}">
                    <img src="{{ asset('admin/images/icons/report-icon.svg') }}">
                    Products
                    <span class="fa fa-chevron-down"></span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.whatsapp.index') }}">
                    <img src="{{ asset('admin/images/icons/social/whatsapp.svg') }}">
                    WhatsApp Groups
                    <span class="fa fa-chevron-down"></span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.orders.index') }}">
                    <img src="{{ asset('admin/images/icons/Order-icon.svg') }}">
                    Orders
                    <span class="fa fa-chevron-down"></span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.transactions.index') }}">
                    <img src="{{ asset('admin/images/icons/Transaction.svg') }}">
                    Transactions
                    <span class="fa fa-chevron-down"></span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.system.show') }}">
                    <img width="32" height="20" src="{{ asset('admin/images/icons/settings.svg') }}">
                    System
                    <span class="fa fa-chevron-down"></span>
                </a>

            </li>


            <li>
                <a href="#submenu5" class="collapsed text-capitalizen" data-toggle="collapse" data-target="#submenu5"
                    aria-expanded="false" {{-- href="{{ route('admin.category.index') }}" --}}>
                    <img width="32" height="20" src="{{ asset('admin/images/icons/catalog.svg') }}">
                    Categories
                    <span class="fa fa-chevron-down"></span>
                </a>

                <div class="collapse" id="submenu5" aria-expanded="false">
                    <ul class="nav side-menu">
                        <li class=""><a class="pl-5" href="{{ route('admin.category.index') }}">Categories List
                            </a></li>
                        <li class=""><a class="pl-5" href="{{ route('admin.subcategory.index') }}">Sub
                                Categories List</a></li>
                    </ul>
                </div>


            </li>





            <li>
                <a href="{{ route('admin.coupons.index') }}">
                    <img width="48" height="48" src="{{ asset('admin/images/icons/Coupon-color.svg') }}">
                    Coupons
                    <span class="fa fa-chevron-down"></span>
                </a>

            </li>
        </ul>
    </div>
</div>
<!-- /Sidebar menu -->
