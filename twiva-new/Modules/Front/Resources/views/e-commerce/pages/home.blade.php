@extends('front::e-commerce.layouts.app', [
    'page_title' => 'Social Commerce',
])

@section('content')
    @php
        $products = [
            [
                'image' => asset('products/aa1a47858e15dad3aa48bc588a56275b.png'),
                'discount' => 10,
                'userImage' => asset('products/user-image.jpg'),
                'userName' => 'Demi Wilkinson',
                'productName' => 'Women’s Sports Tee, Red',
                'originalPrice' => 'KSH 2000',
                'discountedPrice' => 'KSH 2000',
            ],
            [
                'image' => asset('products/725e85bdee505d5c6d201a2a319874db.png'),
                'discount' => 10,
                'userImage' => asset('products/user-image.jpg'),
                'userName' => 'Demi Wilkinson',
                'productName' => 'Women’s Sports Tee, Red',
                'originalPrice' => 'KSH 2000',
                'discountedPrice' => 'KSH 2000',
            ],
            [
                'image' => asset('products/aa1a47858e15dad3aa48bc588a56275b.png'),
                'discount' => 10,
                'userImage' => asset('products/user-image.jpg'),
                'userName' => 'Demi Wilkinson',
                'productName' => 'Women’s Sports Tee, Red',
                'originalPrice' => 'KSH 2000',
                'discountedPrice' => 'KSH 2000',
            ],
            [
                'image' => asset('products/725e85bdee505d5c6d201a2a319874db.png'),
                'discount' => 10,
                'userImage' => asset('products/user-image.jpg'),
                'userName' => 'Demi Wilkinson',
                'productName' => 'Women’s Sports Tee, Red',
                'originalPrice' => 'KSH 2000',
                'discountedPrice' => 'KSH 2000',
            ],
            // Repeat for other products
        ];

        $productsMightLike = [
            [
                'image' => asset('products/aa1a47858e15dad3aa48bc588a56275b.png'),
                'discount' => 10,
                'userImage' => asset('products/user-image.jpg'),
                'userName' => 'Demi Wilkinson',
                'productName' => 'Women’s Sports Tee, Red',
                'originalPrice' => 'KSH 2000',
                'discountedPrice' => 'KSH 2000',
            ],
            [
                'image' => asset('products/725e85bdee505d5c6d201a2a319874db.png'),
                'discount' => 10,
                'userImage' => asset('products/user-image.jpg'),
                'userName' => 'Demi Wilkinson',
                'productName' => 'Women’s Sports Tee, Red',
                'originalPrice' => 'KSH 2000',
                'discountedPrice' => 'KSH 2000',
            ],
            [
                'image' => asset('products/aa1a47858e15dad3aa48bc588a56275b.png'),
                'discount' => 10,
                'userImage' => asset('products/user-image.jpg'),
                'userName' => 'Demi Wilkinson',
                'productName' => 'Women’s Sports Tee, Red',
                'originalPrice' => 'KSH 2000',
                'discountedPrice' => 'KSH 2000',
            ],
            [
                'image' => asset('products/725e85bdee505d5c6d201a2a319874db.png'),
                'discount' => 10,
                'userImage' => asset('products/user-image.jpg'),
                'userName' => 'Demi Wilkinson',
                'productName' => 'Women’s Sports Tee, Red',
                'originalPrice' => 'KSH 2000',
                'discountedPrice' => 'KSH 2000',
            ],
            [
                'image' => asset('products/aa1a47858e15dad3aa48bc588a56275b.png'),
                'discount' => 10,
                'userImage' => asset('products/user-image.jpg'),
                'userName' => 'Demi Wilkinson',
                'productName' => 'Women’s Sports Tee, Red',
                'originalPrice' => 'KSH 2000',
                'discountedPrice' => 'KSH 2000',
            ],
            [
                'image' => asset('products/725e85bdee505d5c6d201a2a319874db.png'),
                'discount' => 10,
                'userImage' => asset('products/user-image.jpg'),
                'userName' => 'Demi Wilkinson',
                'productName' => 'Women’s Sports Tee, Red',
                'originalPrice' => 'KSH 2000',
                'discountedPrice' => 'KSH 2000',
            ],
            [
                'image' => asset('products/aa1a47858e15dad3aa48bc588a56275b.png'),
                'discount' => 10,
                'userImage' => asset('products/user-image.jpg'),
                'userName' => 'Demi Wilkinson',
                'productName' => 'Women’s Sports Tee, Red',
                'originalPrice' => 'KSH 2000',
                'discountedPrice' => 'KSH 2000',
            ],
            [
                'image' => asset('products/725e85bdee505d5c6d201a2a319874db.png'),
                'discount' => 10,
                'userImage' => asset('products/user-image.jpg'),
                'userName' => 'Demi Wilkinson',
                'productName' => 'Women’s Sports Tee, Red',
                'originalPrice' => 'KSH 2000',
                'discountedPrice' => 'KSH 2000',
            ],
            [
                'image' => asset('products/aa1a47858e15dad3aa48bc588a56275b.png'),
                'discount' => 10,
                'userImage' => asset('products/user-image.jpg'),
                'userName' => 'Demi Wilkinson',
                'productName' => 'Women’s Sports Tee, Red',
                'originalPrice' => 'KSH 2000',
                'discountedPrice' => 'KSH 2000',
            ],
            [
                'image' => asset('products/725e85bdee505d5c6d201a2a319874db.png'),
                'discount' => 10,
                'userImage' => asset('products/user-image.jpg'),
                'userName' => 'Demi Wilkinson',
                'productName' => 'Women’s Sports Tee, Red',
                'originalPrice' => 'KSH 2000',
                'discountedPrice' => 'KSH 2000',
            ],
            [
                'image' => asset('products/aa1a47858e15dad3aa48bc588a56275b.png'),
                'discount' => 10,
                'userImage' => asset('products/user-image.jpg'),
                'userName' => 'Demi Wilkinson',
                'productName' => 'Women’s Sports Tee, Red',
                'originalPrice' => 'KSH 2000',
                'discountedPrice' => 'KSH 2000',
            ],
            [
                'image' => asset('products/725e85bdee505d5c6d201a2a319874db.png'),
                'discount' => 10,
                'userImage' => asset('products/user-image.jpg'),
                'userName' => 'Demi Wilkinson',
                'productName' => 'Women’s Sports Tee, Red',
                'originalPrice' => 'KSH 2000',
                'discountedPrice' => 'KSH 2000',
            ],
        ];
    @endphp

    <x-front::e-commerce.layout.products-container :products="$products" :productsMightLike="$productsMightLike" />


    <x-front::layout.footer />
@endsection
@push('scripts')
    <script type="text/javascript" src="{{ mix('front/js/app.js') }}"></script>
    <script>

        function searchProducts(query) {
            axios.post(localSite + 'products/search',
                {
                    keyWord: query,
                }).then((data) => {
                $("#productSection").fadeOut()
                setTimeout(() => {
                    $("#productSection").html(data.data.products).fadeIn()
                    feather.replace()
                    $([document.documentElement, document.body]).animate({
                        scrollTop: $("#searchInput").offset().top
                    }, 500);
                }, 500)
            }).catch((response) => {
            })
        }
    </script>
@endpush
