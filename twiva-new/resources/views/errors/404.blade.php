@extends('front::layouts.app')
@section('title','404')

@section('content')
    <div class="flex justify-center py-24">
        <img src="{{ asset('front/landing/images/404.png') }}" alt="">
    </div>
    <x-front::layout.footer/>
@endsection

