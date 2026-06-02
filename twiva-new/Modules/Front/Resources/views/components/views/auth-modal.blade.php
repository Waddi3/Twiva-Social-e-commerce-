@props([
    'message' => 'You need to sign in for Payment on Delivery',
    'id' => 'auth-modal'
])

<section class="absolute top-0 shadow-lg w-full min-h-screen h-full bg-gray-700 bg-opacity-70" id="{{$id}}"
         style=" display: none;place-items: center;">
    <div class="bg-white rounded-lg relative">
        <p id="close-auth-modal"
           class="text-right absolute right-0 text-2xl text-gray-700 cursor-pointer mr-5 mt-5 py-0.5 px-2 hover:bg-gray-200 duration-200 ease-in rounded-full">
            &times;</p>
        <div class="text-center  px-12 py-5   ">
            <img src="{{asset('front/images/logos/Logo.png')}}" alt="" class="mx-auto">
            <h2 class="text-l py-5">{{$message}}</h2>
            <div class="flex justify-center ">
                <div>
                    <a href="{{route('e-commerce.buyer.login')}}"
                       class="flex justify-center items-center h-[40px] bg-primary-50 text-primary-700 rounded-lg px-5">
                        <span class="">Login</span>
                    </a>
                </div>
                <div>
                    <a class="flex justify-center items-center w-full h-[40px] bg-primary-600 text-white rounded-lg px-5 ml-5"
                       href="{{route('e-commerce.buyer.register')}}">
                        Register
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    $(document).on('click', '#close-auth-modal', () => {
        $("#{{$id}}").hide().css('display', 'none');
    });

</script>
