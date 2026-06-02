<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
        }

        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            padding: 20px;
        }

        .header {
            text-align: center;
            padding: 20px;
        }

        .header img {
            max-width: 80px;
            margin-bottom: 20px;
        }

        .header h1 {
            color: #7A0D41;
            font-size: 20px;
            margin-bottom: 0;
        }

        .text-primary {
            color: #7A0D41;
        }

        .ticket {
            background-color: #fff7d6;
            color: #E87808;
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            padding: 10px;
            border-radius: 8px;
            margin: 20px 0;
        }

        .event-image {
            text-align: center;
            margin: 20px 0;
        }

        .event-image img {
            max-width: 100%;
            border-radius: 8px;
        }

        .event-details {
            text-align: left;
        }

        .event-details h2 {
            color: #333333;
            font-size: 24px;
            margin-top: 0;
        }

        .details {
            display: flex;
            align-items: center;
        }

        .details img {
            width: 20px;
            height: 20px;
            margin-right: 10px;
        }

        .description {
            color: #666;
            margin-top: 10px;
        }

        .qr-code {
            text-align: center;
            margin: 20px 0;
        }

        .qr-code img {
            max-width: 150px;
        }

        .qr-code p {
            color: #666;
            font-size: 14px;
            margin-top: 10px;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }

        .footer a {
            margin: 0 10px;
            text-decoration: none;
        }

        .app-links {
            text-align: center;
            margin: 20px 0;
        }

        .app-links img {
            max-width: 120px;
            margin: 5px;
        }
    </style>
</head>
<body>
<div class="email-container">
    <div class="header">
        <img src="{{ asset('shop/images/logos/Logo.png') }}" alt="Logo">
        <h1>Thank you for registering to this event</h1>
        <p>Below are the event ticket details:</p>
    </div>
    <div class="ticket">
        Ticket #{{$delivery_code}}
    </div>
    <div class="event-image">
        <img src="{{ $product->main_image_path }}" alt="Event Image">
    </div>
    <div class="event-details">
        <h2>{{$product->product_title}}</h2>
        <div class="details" style="margin-bottom: 10px;">
            <img src="{{ asset('shop/images/EventProducts/calendar.svg') }}" alt="Date">
            <span>{{$product->eventTicketProduct->date->format('Y m d')}} &nbsp;   &nbsp; &nbsp;</span>
            <div class="details">
                <img src="{{ asset('shop/images/EventProducts/clock.svg') }}" alt="clock">
                <span>{{$product->eventTicketProduct->start_time}} - {{$product->eventTicketProduct->end_time}}</span>
            </div>
        </div>
        <div class="details">
            <img src="{{ asset('shop/images/EventProducts/map-pin.svg') }}" alt="Location">
            <span>{{$product->eventTicketProduct->location}}</span>
        </div>
        <div class="description">
            <p style="font-weight: 700">Description</p>
            <p>
                {{$product->description}}
            </p>
        </div>
    </div>
    <div class="qr-code">
        <div class="container">
            <img src="data:image/png;base64,{{ $qr_code }}">
        </div>
        <a href="{{$delivery_code_url}}">{{$delivery_code_url}}</a>
    </div>
    <div class="app-links">
        <p class="text-primary"><strong>Get the TWIVA app!</strong></p>
        <p>Get the most of TWIVA by installing the mobile app. You can log in using your existing email address and
            password.</p>
        <a href="{{config('services.social-links.google-play')}}"><img src="{{ asset('front/images/badges/app-store.png') }}" alt="App Store"></a>
        <a href="{{config('services.social-links.apple-store')}}"><img src="{{ asset('front/images/badges/google-play.png') }}" alt="Google Play"></a>
    </div>
    <div class="footer">
        <a href="{{config('services.social-links.twitter')}}"><img
                    src="{{ asset('front/images/social-icons/twitter-gray.png') }}" alt="Twitter"></a>
        <a href="{{config('services.social-links.facebook')}}"><img
                    src="{{ asset('front/images/social-icons/facebook-gray.png') }}" alt="Facebook"></a>
        <a href="{{config('services.social-links.linkedin')}}"><img
                    src="{{ asset('front/images/social-icons/linkedIn-gray.png') }}" alt="LinkedIn"></a>
        <a style="display: block;margin-top: 20px;" href="{{config('app.url')}}">
            <img src="{{asset('front/images/logos/logo-gray.png')}}" alt="">
        </a>
        <p>Copyright © 2024</p>
    </div>
</div>
</body>
</html>