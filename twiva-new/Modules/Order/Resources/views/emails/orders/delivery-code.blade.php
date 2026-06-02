<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Delivery Confirm</title>

    <style>

        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 50px 0 0 0;
        }

        button {
            padding: 15px 18px !important;
            text-align: center;
            color: #fff;
            font-size: 16px;
            font-weight: 600;
            max-width: 100%;
            width: 30%;
            border: none;
            margin: 30px 0 13px 0;
            background: #7A0D41;
            box-shadow: 0px 4px 8px rgba(138, 14, 73, 0.16);
            border-radius: 4px;

            &:hover {
                opacity: .9;
            }

            &.white-bttn {
                margin: 0 !important;
                border: solid 1px #7A0D41;
                color: #7A0D41;
                background: transparent;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <img src="data:image/png;base64,{{ $qr_code }}">
</div>
<br>
<br>
<div class="container">
    <a href="{{ $delivery_code_url }}"
       style="padding: 15px 18px !important; text-align: center; color: #fff; font-size: 16px; font-weight: 600; max-width: 100%; width: 30%; border: none; margin: 30px 0 13px 0; background: #7A0D41; box-shadow: 0px 4px 8px rgba(138, 14, 73, 0.16); border-radius: 4px;text-decoration:none;"
       target="_blank"
    >Delivery Confirm</a>
</div>
<div class="container">
    <p>
        Your delivery is on its way! Remember to give the code
        {{ $delivery_code }}
        to our delivery personnel to confirm delivery.
    </p>
</div>
<br>
<br>
<div class="container">
    Thanks,<br>
    {{ config('app.name') }}
</div>
</body>
</html>

{{--<x-mail::message>--}}
{{--    # Introduction--}}

{{--    The body of your message.--}}


{{--    --}}{{--    <x-mail::button :url="'{{ $delivery_code_url }}'">--}}
{{--    --}}{{--        Button Text--}}
{{--    --}}{{--    </x-mail::button>--}}

{{--   --}}
{{--</x-mail::message>--}}
