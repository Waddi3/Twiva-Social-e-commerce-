<nav x-data="{ fixed: false, openCategories: false, activeCategory: null }"
     class="bg-white lg:shadow-navbar z-[100] flex flex-col justify-between items-center sticky"
     :class="{ 'top-0': fixed, 'right-0': fixed, 'left-0': fixed }" @scroll.window="fixed = (window.pageYOffset > 0)">


    <!-- Top section with icons and messages -->
    <div class="w-full flex flex-wrap justify-center items-center bg-primary-700 text-white text-center p-2 sticky">
        <div class="flex gap-6 justify-center justify-items-center w-full xl:w-auto">
            <div class="flex items-center gap-1">
                <div class="bg-warning-800 p-2 rounded-full">
                    <i data-feather="dollar-sign" class="w-2 h-2 lg:w-6 lg:h-6"></i>
                </div>
                <span class="font-bold text-xs lg:text-base">Cash on Delivery</span>
            </div>
            <div class="flex items-center gap-1">
                <div class="bg-warning-800 p-2 rounded-full">
                    <i data-feather="truck" class="w-2 h-2 lg:w-6 lg:h-6"></i>
                </div>
                <span class="font-bold text-xs lg:text-base">Swift Delivery</span>
            </div>
            <div class="flex items-center gap-1">
                <div class="bg-warning-800 p-2 rounded-full">
                    <i data-feather="refresh-ccw" class="w-2 h-2 lg:w-6 lg:h-6"></i>
                </div>
                <span class="font-bold text-xs lg:text-base">Easy Returns</span>
            </div>
        </div>
    </div>


    <!-- Bottom section with logo and search bar -->
    <div class="block lg:flex w-full lg:justify-between lg:items-center px-4 md:px-[80px] md:py[15px] lg:h-[80px]">
        <div class="lg:hidden flex w-full justify-between items-center mb-2">
            <a href="{{route('ecommerce.index')}}">
                <img src="{{ asset('front/images/logos/Logo.png') }}" alt="Twiva Logo">
            </a>
            <div class="flex w-full justify-end items-center gap-4">
                <button class="block hidden border-primary-900 p-2" @click="$dispatch('drawers-menu-toggle')">
                    <i data-feather="menu" class="w-6 h-6 text-primary-900"></i>
                    <span class="badge absolute top-[-8px] right-[-8px]">0</span>
                </button>
                <button @click="$dispatch('drawers-basket-toggle')" class="relative">
                    <i data-feather="shopping-cart" class="w-6 h-6 gap-5"></i>
                    <span id="basketCount"
                          class="basketCount basketCount badge absolute top-[-8px] right-[-8px]">0</span>
                </button>
                @auth('buyer')
                    <a class="relative" href="{{route('e-commerce.buyer.order.index')}}">
                        <i data-feather="user" class="w-[30px] h-[30px]"></i>
                    </a>
                @endauth
                <div class="Menu2 w-5 h-5 px-0.5 py-1 justify-center items-center flex text-primary-700"
                     @click="openCategories = !openCategories">
                    <i x-show="!openCategories" data-feather="align-justify"></i>
                    <i x-show="openCategories" data-feather="x"></i>
                </div>
            </div>
        </div>
        <a class="hidden lg:flex items-center gap-3" href="{{route('ecommerce.index')}}">
            <img src="{{ asset('front/images/logos/Logo.png') }}" alt="Twiva Logo">
        </a>
        <div class="flex items-center grow gap-6 w-full">
            <div class="relative w-full">
                <form id="search-form" method="GET"
                      @if(request('account')) action="{{route('shop.product.products',['account' => request('account')])}}"
                      @else action="{{route('ecommerce.shop')}}" @endif
                      class="relative w-full lg:max-w-lg md:mx-[30px] flex items-center justify-between border rounded-lg overflow-hidden">
                    {{-- <i data-feather="search" class="absolute top-1/2 left-2.5 transform -translate-y-1/2"></i> --}}
                    @if(request('account'))
                        <input name="keyWord" type="text"
                               placeholder="search for a product, brand, category."
                               class="flex-1 md:w-[493px] px-4 py-2 focus:outline-none gap-3"
                               value="{{request('keyWord')}}"/>
                    @else
                        <input id="searchInput" type="text" placeholder="Search for a product, brand, category"
                               class="flex-1 md:w-[493px] px-4 py-2 focus:outline-none gap-3"
                               value="{{request('keyWord')}}"/>
                    @endif
                    <button type='submit' class="p-2 bg-warning-800">
                        <i data-feather="search" class=""></i>
                    </button>
                </form>
            </div>
        </div>
        <button class="block hidden border-primary-900 p-2" @click="$dispatch('drawers-menu-toggle')">
            <i data-feather="menu" class="w-6 h-6 text-primary-900"></i>
            <span class="badge absolute top-[-8px] right-[-8px]">0</span>
        </button>
        <div class="hidden lg:flex gap-4 items-center justify-center xl:justify-end w-[261px] xl:w-auto mt-2 xl:mt-0">
            <button @click="$dispatch('drawers-basket-toggle')" class="relative">
                <i data-feather="shopping-cart" class="w-6 h-6 gap-5"></i>
                <span id="basketCount" class="basketCount badge absolute top-[-8px] right-[-8px]">0</span>
            </button>
            @auth('buyer')
                {{--<a class="relative" href="{{route('e-commerce.buyer.order.index')}}">
                    <i data-feather="user" class="w-[30px] h-[30px]"></i>
                </a>--}}
                <button class="relative" @click="$dispatch('drawers-user-toggle')">
                    <i data-feather="user" class="w-[30px] h-[30px]"></i>
                </button>
            @endauth
            @guest('buyer')
                <a href="{{route('ecommerce.login')}}"
                   class="px-4 py-2.5 bg-primary-50 rounded-lg text-primary-700 text-sm font-semibold font-['Source Sans Pro'] leading-tight">
                    <div class="rounded-lg justify-start items-start flex cursor-pointer ">
                        Login
                        <div
                                class=" shadow border border-rose-100 justify-center items-center gap-2 flex">
                            <div class="w-5 h-5 p-[1.67px] justify-center items-center flex">
                                <i data-feather="log-in" class="text-primary-700"></i>
                            </div>
                        </div>
                    </div>
                </a>
            @endguest
        </div>

        <div x-show="openCategories" x-cloak x-transition
             class="absolute top-[100px] left-0 w-screen h-[calc(100vh-140px)] bg-white"
        " style="z-index: 10;">
        <div class="relative w-full h-[calc(100%-120px)]">
            <div class="w-full overflow-y-auto bg-white py-4 px-4 h-full">
                <ul>
                    <li class="py-2 left-5">
                        <a class="sub-category flex items-center text-gray-700 cursor-pointer">
                            <svg
                                    class="mr-3"
                                    width="14" height="16"
                                    viewBox="0 0 14 16"
                                    fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                      d="M2.46683 0.933659C2.59273 0.765788 2.79032 0.666992 3.00016 0.666992H11.0002C11.21 0.666992 11.4076 0.765788 11.5335 0.933659L13.5335 3.60033C13.62 3.71572 13.6668 3.85608 13.6668 4.00033V13.3337C13.6668 13.8641 13.4561 14.3728 13.081 14.7479C12.706 15.1229 12.1973 15.3337 11.6668 15.3337H2.3335C1.80306 15.3337 1.29436 15.1229 0.919282 14.7479C0.54421 14.3728 0.333496 13.8641 0.333496 13.3337V4.00033C0.333496 3.85608 0.380281 3.71572 0.466829 3.60033L2.46683 0.933659ZM3.3335 2.00033L2.3335 3.33366H11.6668L10.6668 2.00033H3.3335ZM12.3335 4.66699H1.66683V13.3337C1.66683 13.5105 1.73707 13.68 1.86209 13.8051C1.98712 13.9301 2.15669 14.0003 2.3335 14.0003H11.6668C11.8436 14.0003 12.0132 13.9301 12.1382 13.8051C12.2633 13.68 12.3335 13.5105 12.3335 13.3337V4.66699ZM4.3335 6.00033C4.70169 6.00033 5.00016 6.2988 5.00016 6.66699C5.00016 7.19742 5.21088 7.70613 5.58595 8.08121C5.96102 8.45628 6.46973 8.66699 7.00016 8.66699C7.5306 8.66699 8.0393 8.45628 8.41438 8.08121C8.78945 7.70613 9.00016 7.19742 9.00016 6.66699C9.00016 6.2988 9.29864 6.00033 9.66683 6.00033C10.035 6.00033 10.3335 6.2988 10.3335 6.66699C10.3335 7.55105 9.98231 8.39889 9.35719 9.02401C8.73206 9.64914 7.88422 10.0003 7.00016 10.0003C6.11611 10.0003 5.26826 9.64914 4.64314 9.02401C4.01802 8.39889 3.66683 7.55105 3.66683 6.66699C3.66683 6.2988 3.96531 6.00033 4.3335 6.00033Z"
                                      fill="#222123"/>
                            </svg>
                            Shop All
                        </a>
                    </li>
                    @foreach ($categories['categories'] as $category)
                        <li class="py-2 left-5">
                            <a href="{{route('ecommerce.shop', ['category' => $category->id])}}"
                               class="text-gray-700 flex justify-between items-center"
                               @click.prevent="activeCategory === {{ $category->id }} ? activeCategory = null : activeCategory = {{ $category->id }}">
                                {{ $category->name }}
                                @if ($category->subcategories->count() > 0)
                                    <i x-show="activeCategory !== {{ $category->id }}" data-feather="chevron-right"
                                       class="w-4 h-4"></i>
                                    <i x-show="activeCategory === {{ $category->id }}" data-feather="chevron-left"
                                       class="w-4 h-4"></i>
                                @endif
                            </a>
                            @if ($category->subcategories->count() > 0)
                                <div x-show="activeCategory === {{ $category->id }}"
                                     x-transition.opacity.duration.200
                                     class="absolute top-0 left-0 w-full h-full bg-white py-4 px-4 overflow-x-visible overflow-y-auto"
                                        {{-- sm:relative --}} {{-- style="margin-left: 488px; margin-top: -25px;" --}}>
                                    <ul>
                                        <li class="py-1">
                                            <a @click="activeCategory = false" class="text-gray-700">
                                                Back to main menu
                                            </a>
                                        </li>
                                        <li class="py-1">
                                            <a class="text-primary-700">
                                                {{ $category->name }}
                                            </a>
                                        </li>
                                        @foreach ($category->subcategories as $subcategory)
                                            <li class="py-1">
                                                <a href="{{route('ecommerce.shop', ['sub_category' => $subcategory->id])}}"
                                                   class="text-gray-700">
                                                    {{ $subcategory->name }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
            @guest('buyer')
                <div class="flex flex-col items-center justify-center gap-4 mt-2 pt-4 border-t border-gray-300">
                    <p class="text-gray-700">
                        Do you have a TWIVA account? Login here:
                    </p>
                    <div class="rounded-lg justify-start items-start flex cursor-pointer">
                        <div
                                class="px-4 py-2.5 bg-primary-50 rounded-lg shadow border border-rose-100 justify-center items-center gap-2 flex">
                            <div class="w-5 h-5 p-[1.67px] justify-center items-center flex">
                                <i data-feather="log-in" class="text-primary-700"></i>
                            </div>
                            <a href="{{route('ecommerce.login')}}"
                               class="text-primary-700 text-sm font-semibold font-['Source Sans Pro'] leading-tight">Login
                            </a>
                        </div>
                    </div>
                </div>
            @endguest
        </div>
    </div>
    </div>
