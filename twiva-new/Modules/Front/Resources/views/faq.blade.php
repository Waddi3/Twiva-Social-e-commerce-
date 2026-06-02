@extends('front::layouts.app', [ 'pageTitle' => 'FAQs' ])

@section('content')
    <main>
        <section class="bg-primary-900 p-[30px] md:py-24 md:px-20 bg-no-repeat md:bg-faq-image md:bg-faq-position">
            <h1 class="text-white text-4xl font-semibold text-center leading-[44px] md:text-6xl md:text-left md:leading-[72px]">
                Frequently Asked Questions
            </h1>
        </section>
        <section class="bg-gray-50 px-2.5 py-[30px] md:px-[300px] py-[60px]">
            <div class="flex flex-col gap-5">
                <x-front::layout.faq-item>
                    <x-slot name="title">How Does Twiva Work?</x-slot>

                    <p>Twiva helps businesses market and sell products through social media influencers.</p>
                    <p>Once a business has listed its products on the platform, we match them to thousands of strategic
                        nano and micro-influencers.</p>
                    <p>The influencers list the products on their Twiva online shops and they are automatically pushed
                        to all their social media shops on Facebook, Instagram, WhatsApp, TikTok and Twitter.</p>
                    <p>An influencer makes a commission every time they sell a product and the business gets a
                        notification a product has been sold.</p>
                </x-front::layout.faq-item>
                <x-front::layout.faq-item>
                    <x-slot name="title">Who Can Join Twiva?</x-slot>
                    <ul>
                        <li>Anyone with a social media presence (for influencers at least 100 followers on any
                            platform)
                        </li>
                        <li>Any business with a social media presence</li>
                        <li>Anyone looking to make an income selling on social media</li>
                        <li>Anyone looking to promote products or services through social media</li>
                    </ul>
                </x-front::layout.faq-item>
                <x-front::layout.faq-item>
                    <x-slot name="title">How do I register as a Business on Twiva?</x-slot>
                    <p><strong>Step 1:</strong> Log on to <a href="https://www.twiva.co.ke">www.twiva.co.ke</a> or
                        simply download the TWIVA app on Google Playstore.</p>
                    <p><strong>Step 2:</strong> Click on “I’m a Business” and register</p>
                    <p><strong>Step 3:</strong> Start uploading your products</p>
                    <p>Watch this video: <a href="https://www.youtube.com/watch?v=_itScCGZC98">How to register and list
                            products as a business on Twiva</a></p>
                </x-front::layout.faq-item>
                <x-front::layout.faq-item>
                    <x-slot name="title">How do I sign up as an influencer on Twiva?</x-slot>
                    <p><strong>Step 1:</strong> Log on to <a href="https://www.twiva.co.ke">www.twiva.co.ke</a> or
                        simply download the <a
                                href="https://play.google.com/store/apps/details?id=com.twiva.ke&hl=en&gl=US">TWIVA
                            app</a> on Google Playstore</p>
                    <p><strong>Step 2:</strong> Click on “I’m an Influencer” and register</p>
                    <p><strong>Step 3:</strong> Set up your Twiva Shop</p>
                    <p><strong>Step 4:</strong> Connect your social media pages</p>
                    <p>Watch this video: <a href="https://www.youtube.com/watch?v=PaonlyymeZo">How to sign up as an
                            influencer and set up your Twiva Shop</a></p>
                </x-front::layout.faq-item>
                <x-front::layout.faq-item>
                    <x-slot name="title">How Do Influencers Earn On The Platform?</x-slot>
                    <p><strong>Commissions:</strong> Influencers earn on Twiva by making a commission each time they
                        sell a product. Commissions range between 5-10% depending on the value of the product. On
                        average, an influencer makes KES 17,000 per month.</p>
                    <p><strong>Influencer Marketing Campaigns:</strong> Influencers earn from brand collaborations.
                        Influencers expose their rate cards to businesses. Businesses create campaign briefs, select the
                        influencers they want to work with, set their fee, stipulate the objectives and goals, and
                        timelines of the campaign.</p>
                    <p><strong>Referral Program:</strong> Influencers earn every time they refer someone to the
                        platform.</p>
                </x-front::layout.faq-item>
                <x-front::layout.faq-item>
                    <x-slot name="title">Is The Platform Free For Everyone?</x-slot>
                    <p>Yes, it is free to register on Twiva for both influencers and businesses.</p>
                </x-front::layout.faq-item>
                <x-front::layout.faq-item>
                    <x-slot name="title">Do You Do Delivery?</x-slot>
                    <p>No. Twiva does not offer delivery services. For now, our businesses/merchants cater for the
                        delivery. However, Twiva will be offering country-wide deliveries starting August 2022.</p>
                </x-front::layout.faq-item>
            </div>
        </section>
    </main>
    <x-front::layout.footer/>
@endsection
