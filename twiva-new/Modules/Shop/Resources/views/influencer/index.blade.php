@extends('shop::layouts.app')
@section('title', ($shop->shop_name ?? $user->name) ."'s Webstore")
@section('content')
    <x-shop::layout.products-container
            :products="$products"
            :productsMightLike="$products"
            :user="$user"
            :shop="$shop"
            :tags="$tags" :badges="$badges??[]"
            :show-more-badges="$show_more_badges??[]"
            :hasServicesProduct="$hasServicesProduct ?? false"
    />
@endsection
@push('script')
    <script>
        feather.replace();
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
