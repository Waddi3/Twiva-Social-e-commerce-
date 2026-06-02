@php
    $url = $data['account']
        ? route('buyer.reset-password', ['token' => $data['token'], 'account' => $data['account']])
        : route('e-commerce.buyer.reset-password', ['token' => $data['token']]);
@endphp

@component('mail::message')
    Hi {{ $data['name'] }},
    You recently requested to reset the password for your account. Click the button below to proceed.
    @component('mail::button', ['url' => $url])
        Reset Password
    @endcomponent
    If you did not request a password reset, please ignore this email .
    This password reset link is only valid for the next 30 minutes.

    Thanks, Twiva team
@endcomponent
