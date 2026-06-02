@php
    $isInCard = false;
    if( isset(session()->get('cart')[$id]) ) {
        $isInCard = true;
    }
@endphp

{{--@props([
   'isInCard' => 'false',
])--}}
<div class="bg-white rounded-lg shadow overflow-hidden mx-auto product-item min-w-0">
    <div class="relative">
        <a href="{{route('e-commerce.shop.product.show', ['product' => $id, 'user_type' => $userType])}}">
            <img class="w-full h-80 object-cover" src="{{ $image }}" alt="Product Image">
        </a>
        @if(!is_null($discount) && $discount != 0)
        <div class="absolute top-2 right-2 w-14 h-14 bg-orange-500 text-white text-center leading-[2.7rem] font-lg font-semibold text-base px-2 py-1 shadow rounded-full">
            -{{ round($discount, 0) }}%
        </div>
        @endif
    </div>
    <div class="p-4">
        <div class="flex items-center">
            <img class="w-8 h-8 rounded-full object-cover mr-2" src="{{ $userImage }}" alt="User Image">
            <a href="{{route('ecommerce.shop', ['query' => $userName])}}">
                <h2 class="text-gray-800 text-sm underline decoration-1 font-semibold">{{ $userName }}</h2>
            </a>
        </div>
        <a href="{{route('e-commerce.shop.product.show', ['product' => $id, 'user_type' => $userType])}}">
            <p class="text-slate-600 text-lg font-semibold mt-2 line-clamp-1 w-[250px]">{{ $productName }}</p>
        </a>
        <div class="flex items-center mt-2">
            @if( !is_null($retailPrice) && $retailPrice != $productPrice )
                <span class="text-gray-500 text-base font-semibold line-through">KSH {{ number_format($retailPrice) }}</span>
                <span class="text-primary-900 text-base font-semibold ml-2">KSH {{ number_format($productPrice) }}</span>
            @else
                <span class="text-gray-500 text-base font-semibold">KSH {{ number_format($productPrice) }}</span>
            @endif
            @if(!is_null($discount) && $discount != 0)<span class="text-orange-500 text-base ml-2">-{{ round($discount, 0) }}%</span>@endif
        </div>
        <div class="grid grid-cols-5 gap-4 items-center justify-between mt-4">
            <button class="bg-warning-50 text-center text-warning-800 w-11 h-11 rounded-lg add-to-cart"
                    @click="toggle"
                    x-data="toggling({{$isInCard ? 'true' : 'false'}},{{$id}},'{{$userType}}')"
                    data-product="{{$id}}"
            >
                <svg x-show="!isInCard"
                     :is-in-card="{{$isInCard ? 'true' : 'false'}}"
                     class="bg-warning-50 text-warning-800 inline" width="45" height="45" viewBox="0 0 45 45" fill="none" xmlns="http://www.w3.org/2000/svg"
                >
                    <g filter="url(#filter0_d_6643_932)">
                        <rect x="2.5" y="1.0918" width="40" height="40" rx="8" fill="#FDFCE6"/>
                        <rect x="3" y="1.5918" width="39" height="39" rx="7.5" stroke="#FDFCE6"/>
                        <g clip-path="url(#clip0_6643_932)">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M12.5 11.9251C12.5 11.4649 12.8731 11.0918 13.3333 11.0918H16.6667C17.0639 11.0918 17.4058 11.3721 17.4838 11.7616L18.1837 15.2585H31.6667C31.915 15.2585 32.1504 15.3692 32.3087 15.5606C32.4671 15.7519 32.5318 16.0039 32.4852 16.2479L31.1507 23.2458C31.0364 23.8213 30.7233 24.3383 30.2663 24.7062C29.8115 25.0723 29.243 25.2676 28.6595 25.2585H20.5739C19.9903 25.2676 19.4218 25.0723 18.9671 24.7062C18.5102 24.3384 18.1972 23.8217 18.0828 23.2465C18.0827 23.2463 18.0828 23.2468 18.0828 23.2465L16.6907 16.2911C16.685 16.2681 16.6803 16.2447 16.6766 16.221L15.9836 12.7585H13.3333C12.8731 12.7585 12.5 12.3854 12.5 11.9251ZM18.5173 16.9251L19.7174 22.9211C19.7555 23.1129 19.8598 23.2853 20.0122 23.4079C20.1645 23.5305 20.3552 23.5957 20.5507 23.5919L20.5667 23.5918H28.6667L28.6826 23.5919C28.8782 23.5957 29.0688 23.5305 29.2212 23.4079C29.3728 23.2858 29.4769 23.1146 29.5154 22.9238L30.6594 16.9251H18.5173ZM18.3333 28.5918C18.3333 27.6713 19.0795 26.9251 20 26.9251C20.9205 26.9251 21.6667 27.6713 21.6667 28.5918C21.6667 29.5123 20.9205 30.2585 20 30.2585C19.0795 30.2585 18.3333 29.5123 18.3333 28.5918ZM27.5 28.5918C27.5 27.6713 28.2462 26.9251 29.1667 26.9251C30.0871 26.9251 30.8333 27.6713 30.8333 28.5918C30.8333 29.5123 30.0871 30.2585 29.1667 30.2585C28.2462 30.2585 27.5 29.5123 27.5 28.5918Z" fill="#ECA01B"/>
                        </g>
                    </g>
                    <defs>
                        <filter id="filter0_d_6643_932" x="0.5" y="0.0917969" width="44" height="44" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                            <feFlood flood-opacity="0" result="BackgroundImageFix"/>
                            <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/>
                            <feOffset dy="1"/>
                            <feGaussianBlur stdDeviation="1"/>
                            <feColorMatrix type="matrix" values="0 0 0 0 0.0627451 0 0 0 0 0.0941176 0 0 0 0 0.156863 0 0 0 0.05 0"/>
                            <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_6643_932"/>
                            <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_6643_932" result="shape"/>
                        </filter>
                        <clipPath id="clip0_6643_932">
                            <rect width="20" height="20" fill="white" transform="translate(12.5 11.0918)"/>
                        </clipPath>
                    </defs>
                </svg>
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check w-5 h-5 bg-warning-50 text-warning-800 inline" x-show="isInCard"><polyline points="20 6 9 17 4 12"></polyline></svg>
                {{--<i x-show="!isInCard" data-feather="shopping-cart" class="w-5 h-5 bg-warning-50 text-warning-800 inline"></i>--}}
                {{--<i x-show="isInCard" data-feather="check" class="w-5 h-5 bg-warning-50 text-warning-800 inline"></i>--}}

            </button>
            <a href="{{route('e-commerce.shop.product.buyNow', ['product_id' => $id, 'user_type'=> $userType])}}" class="bg-primary-1000 col-span-4 text-white font-semibold px-4 py-2 rounded-lg h-11 text-center">Buy Now</a>
        </div>
        <div class="mt-2">
            <span style="color: {{ $shipmentTagColor }}; background-color: {{ $shipmentTagBackgroundColor }}; padding: 2px 4px; border-radius: 5px;">{{ $shipmentTagText }}</span>
        </div>
    </div>
</div>
