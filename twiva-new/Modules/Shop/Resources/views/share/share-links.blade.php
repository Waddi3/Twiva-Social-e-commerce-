@push('style')
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/share-buttons.css') }}">
    <script src="{{ asset('js/share-buttons.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"
            integrity="sha256-4+XzXVhsDmqanXGHaHvgh1gMQKX40OUvDEBTu8JcmNs=" crossorigin="anonymous"></script>
    <script src="{{ asset('js/share-buttons.jquery.js') }}"></script>
@endpush

<div>
    <p class="text-black-100 text-lg font-semibold">Share with friends:</p>
    {!! $share_links !!}
</div>
