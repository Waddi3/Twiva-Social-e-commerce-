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
                              id="productSection">
            @include('shop::components.views._products')
        </x-shop::core.section>
    </x-shop::core.container>
@endsection
