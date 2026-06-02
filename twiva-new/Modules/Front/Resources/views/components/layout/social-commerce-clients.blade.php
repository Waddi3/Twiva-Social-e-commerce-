<section {{ $attributes }}>
    <h2 class="text-primary-900 text-[30px] text-center font-normal leading-[38px] mb-5">
        Some of our social commerce clients
    </h2>
    <div x-data="carousel()" class="carousel-container flex justify-center md:mx-[-60px] before:w-[60px] md:before:w-[175px] after:w-[60px] md:after:w-[175px]">
        <div class="carousel w-full h-max py-9" x-ref="carousel">
            <img class="carousel-cell ml-10 md:ml-[70px] self-center" src="{{ asset('front/images/social-commerce-clients/brand.svg') }}" alt="brand" />
            <img class="carousel-cell ml-10 md:ml-[70px] self-center" src="{{ asset('front/images/social-commerce-clients/cellularpluske.svg') }}" alt="cellularpluske" />
            <img class="carousel-cell ml-10 md:ml-[70px] self-center" src="{{ asset('front/images/social-commerce-clients/decathlon.svg') }}" alt="decathlon" />
            <img class="carousel-cell ml-10 md:ml-[70px] self-center" src="{{ asset('front/images/social-commerce-clients/kilimal.svg') }}" alt="kilimal" />
            <img class="carousel-cell ml-10 md:ml-[70px] self-center" src="{{ asset('front/images/social-commerce-clients/onliine-empire-technologies.svg') }}" alt="onliine-empire-technologies" />
            <img class="carousel-cell ml-10 md:ml-[70px] self-center" src="{{ asset('front/images/social-commerce-clients/ourhub.svg') }}" alt="ourhub" />
            <img class="carousel-cell ml-10 md:ml-[70px] self-center" src="{{ asset('front/images/social-commerce-clients/sollatek.svg') }}" alt="sollatek" />
            <img class="carousel-cell ml-10 md:ml-[70px] self-center" src="{{ asset('front/images/social-commerce-clients/the-little-jewellers.svg') }}" alt="the-little-jewellers" />
            <img class="carousel-cell ml-10 md:ml-[70px] self-center" src="{{ asset('front/images/social-commerce-clients/toy-planet.svg') }}" alt="toy-planet" />
        </div>
    </div>
</section>
