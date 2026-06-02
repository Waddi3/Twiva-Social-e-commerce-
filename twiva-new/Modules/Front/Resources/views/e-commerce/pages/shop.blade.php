@extends('front::e-commerce.layouts.app', [
    'page_title' => 'Social Commerce shop',
])

@section('content')
    @php
        $products = [];
    @endphp

    <x-front::e-commerce.layout.shop-products-container
            :products="$products"
            :categories="$categories"
            :selectedCategory="$selectedCategory"
            :selectedSubCategory="$selectedSubCategory"
    />
    <x-front::layout.footer/>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.3/themes/base/jquery-ui.css">
@endpush
@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/select2.min.js"></script>
    <script type="text/javascript" src="{{ mix('front/js/shop.js') }}"></script>
    <script>
        feather.replace()
    </script>
@endpush
