<div {{ $attributes->class('container mx-auto  md:max-w-screen-md lg:max-w-screen-lg xl:max-w-screen-xl 2xl:max-w-screen-2xl')->only('class') }}>
    @if(\Illuminate\Support\Facades\Session::has('message'))
        <p id="toastMessage"
           class="text-center p-3 bg-green-200 text-green-700 rounded-md mt-6">{{\Illuminate\Support\Facades\Session::get('message')}}</p>
    @endif
    @if(\Illuminate\Support\Facades\Session::has('errorMessage'))
        <p id="toastMessage"
           class="text-center p-5 bg-primary-50 text-primary-700 rounded-md mt-6">{{\Illuminate\Support\Facades\Session::get('errorMessage')}}</p>
    @endif

    {{ $slot }}
</div>
