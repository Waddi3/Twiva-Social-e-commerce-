@if(session()->has('message'))
<div class="alert alert-success alert-block">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <strong>{{ session()->get('message') }}</strong>
</div>
@endif

@if(session()->has('error'))
<div class="alert alert-danger alert-block">
    <button type="button" class="close" data-dismiss="alert">×</button>
    <strong>{{ session()->get('error') }}</strong>
</div>
@endif


{{--@if (count($errors) > 0)--}}
{{--    @foreach ($errors->all() as $error)--}}
{{--        <div class="title-wrapper">--}}
{{--            <h4>--}}
{{--                {{ $error }}--}}
{{--            </h4>--}}
{{--        </div>--}}
{{--    @endforeach--}}
{{--@endif--}}
