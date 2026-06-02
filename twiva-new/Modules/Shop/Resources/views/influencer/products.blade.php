@extends('shop::layouts.app')
@section('title', ($shop->shop_name ?? $user->name) ."'s Webstore")

@section('content')

    <x-shop::core.container class="mt-10">

        <x-shop::core.section>
            <x-shop::partials.profile :platforms="$user->UserPlatforms" :userName="($shop->shop_name ?? $user->name)"
                                      :userDescription="($shop->shop_description)"
                                      :user-image="$shop->logoImage ?? asset('storage/images/products/1632146490.jpg')"
                                      :tags="$tags" class="mb-[30px] md:mb-4 "/>
        </x-shop::core.section>
        <x-shop::core.section class="grid grid-cols-12 gap-6"
        >
            <div class="col-span-12 md:col-span-12">
                <x-shop::partials.filter
                        :products="$products"
                        :subCategories="$subCategories"
                        :selectedCategory="$selectedCategory"
                        :selectedSubCategory="$selectedSubCategory"
                        :categories="$categories"
                />
            </div>
            {{-- <div class="col-span-12 md:col-span-9 id="productSection">
                <div class="grid grid-cols-12 gap-6">
                    @include('shop::components.views._products')
                </div>
            </div> --}}
        </x-shop::core.section>
    </x-shop::core.container>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.3/themes/base/jquery-ui.css">
@endpush
@push('scripts_before')
    <script type="text/javascript" src="{{ mix('front/js/shop.js') }}"></script>
@endpush
