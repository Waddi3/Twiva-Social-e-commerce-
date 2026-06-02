@extends('front::e-commerce.layouts.app', [ 'page_title' => 'Mange Address' ])

@section('content')
    <x-shop::core.container>
        <a href="{{route('e-commerce.buyer.settings-page')}}">
            <div class="border-gray-200 sm-down:border md:mt-2.5 md:mb-7 sm-down:bg-white">
                <button class="flex justify-center items-center p-2.5 text-gray-700">
                    <i class="w-5 h-5 text-gray-700" data-feather="arrow-left"></i>
                    <span class="ml-4">Back to Settings</span>
                </button>
            </div>
        </a>
        <div class="block md:flex md:justify-between">
            <h1 class="h1 font-semibold text-2xl">Manage Address </h1>
            <a href="{{route('e-commerce.buyer.address.create')}}"
               class="flex md:justify-center md:items-center p-3 bg-primary-50 text-primary-700 rounded-md">
                <span class="ml-3 font-semibold ">Add New Address </span>
                <i class="w-6 h-6" data-feather="plus"></i>
            </a>
        </div>
        <section class="mt-5 w-3/4 grid lg:grid-cols-3 md:grid-cols-2 sm:grid-cols-1 gap-3.5">
            @forelse($addresses as $address)
                <div class="flex ml-4 my-3">
                    <div class="w-[300px]">
                        <p class="text-lg font-bold">{{$address->contactName}}</p>
                        <p class="py-2">{{$address->fullAddress}}</p>
                        <p>Contact No. {{$address->phone_number}}</p>
                    </div>
                    <div class="flex  space-x-1.5">
                        <form class="inline-block" method="POST"
                              action="{{route('e-commerce.buyer.address.delete',['shippingAddress' => $address->id])}}">
                            @method('DELETE')
                            @csrf
                            <button type="submit" class="text-error-800 ">
                                <span class="font-thin" data-feather="trash"></span>
                            </button>
                        </form>

                        @if($address->latitude and $address->longitude)
                            <a href="https://www.google.com/maps?q={{ $address->latitude }},{{ $address->longitude }}"
                               target="_blank">
                                <span class="font-thin text-primary-600" data-feather="map-pin"></span>
                            </a>
                        @endif


                        <a href="{{route('e-commerce.buyer.address.edit',['shippingAddress' => $address->id])}}"
                           class="bold">
                            <span class="font-thin" data-feather="edit"></span>
                        </a>


                    </div>
                </div>
            @empty
            @endforelse

        </section>
    </x-shop::core.container>

@endsection
@push('scripts')
    <script type="text/javascript" src="{{ mix('front/js/app.js') }}"></script>
@endpush
