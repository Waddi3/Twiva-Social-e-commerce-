<section {{ $attributes }}>
    <h2 class="text-primary-900 text-[30px] text-center font-normal leading-[38px] mb-[30px] px-4 md:text-5xl md:font-normal md:leading-[60px] md:px-0">
        Some of our clients
    </h2>
    <div x-data="carousel()" class="carousel-container flex justify-center md:mx-[-60px] before:w-[60px] md:before:w-[175px] after:w-[60px] md:after:w-[175px]">
        <div class="carousel w-full h-max" x-ref="carousel">
            <img class="carousel-cell ml-10 md:ml-[70px] self-center" src="{{ asset('front/images/clients/Ajira.png') }}" alt="Ajira" />
            <img class="carousel-cell ml-10 md:ml-[70px] self-center" src="{{ asset('front/images/clients/ASU.png') }}" alt="ASU" />
            <img class="carousel-cell ml-10 md:ml-[70px] self-center" src="{{ asset('front/images/clients/Decathlon.png') }}" alt="Decathlon" />
            <img class="carousel-cell ml-10 md:ml-[70px] self-center" src="{{ asset('front/images/clients/Happybox.png') }}" alt="Happybox" />
            <img class="carousel-cell ml-10 md:ml-[70px] self-center" src="{{ asset('front/images/clients/Mastercard Baobab.png') }}" alt="Mastercard-Baobab" />
            <img class="carousel-cell ml-10 md:ml-[70px] self-center" src="{{ asset('front/images/clients/Mediamax.png') }}" alt="Mediamax" />
            <img class="carousel-cell ml-10 md:ml-[70px] self-center" src="{{ asset('front/images/clients/Mipay.png') }}" alt="Mipay" />
            <img class="carousel-cell ml-10 md:ml-[70px] self-center" src="{{ asset('front/images/clients/Novartis.png') }}" alt="Novartis" />
            <img class="carousel-cell ml-10 md:ml-[70px] self-center" src="{{ asset('front/images/clients/SMELink.png') }}" alt="SMELink" />
            <img class="carousel-cell ml-10 md:ml-[70px] self-center" src="{{ asset('front/images/clients/Sollatek.png') }}" alt="Sollatek" />
            <img class="carousel-cell ml-10 md:ml-[70px] self-center" src="{{ asset('front/images/clients/SSC.png') }}" alt="SSC" />
            <img class="carousel-cell ml-10 md:ml-[70px] self-center" src="{{ asset('front/images/clients/Vision Plus.png') }}" alt="Vision-Plus" />
        </div>
    </div>
</section>
