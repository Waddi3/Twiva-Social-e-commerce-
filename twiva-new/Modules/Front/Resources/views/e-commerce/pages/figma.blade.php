<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ $content_discription ?? 'Twiva - Influencer Marketing - Social Commerce
We are an award-winning Influencer marketing company and social commerce platform connecting Businesses with social media
influencers. Market and sell through influencers.' }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <link href="{{ mix('front/css/app.css') }}" rel="stylesheet">
    <link rel="shortcut icon" href="{{asset('front/images/fav.png')}}" type="image/x-icon">


    @stack('meta')

    <title>{{ isset($page_title) ? config('app.name') . " - $page_title" : config('app.name') }}</title>

    @stack('styles')
    @stack('pre-scripts')

    <script async src="https://www.googletagmanager.com/gtag/js?id=AW-298982560"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }

        gtag('js', new Date());

        gtag('config', 'AW-298982560');
    </script>
</head>
<body>
{{--<x-front::e-commerce.layout.navbar/>--}}
{{--<x-front::layout.drawer/>--}}

<div class="w-[1440px] h-[7330px] bg-white flex-col justify-start items-start inline-flex">
    <div class="h-[210px] flex-col justify-start items-end flex">
        <div class="self-stretch px-20 bg-pink-800 shadow justify-between items-center inline-flex">
            <div class="h-[60px] justify-center items-center gap-5 flex">
                <div class="px-4 py-2.5 justify-start items-center gap-1.5 flex">
                    <div class="p-2 bg-amber-500 rounded-[50px] justify-center items-center gap-2.5 flex">
                        <div class="w-6 h-6 px-[5px] justify-center items-center flex"></div>
                    </div>
                    <div class="text-white text-xl font-bold font-['Source Sans Pro'] leading-[30px]">Cash on Delivery</div>
                </div>
                <div class="px-4 py-2.5 justify-start items-center gap-1.5 flex">
                    <div class="p-2 bg-amber-500 rounded-[50px] justify-center items-center gap-2.5 flex">
                        <div class="w-6 h-6 py-0.5 justify-center items-center flex"></div>
                    </div>
                    <div class="text-white text-xl font-bold font-['Source Sans Pro'] leading-[30px]">Swift Delivery</div>
                </div>
                <div class="px-4 py-2.5 justify-start items-center gap-1.5 flex">
                    <div class="p-2 bg-amber-500 rounded-[50px] justify-center items-center gap-2.5 flex">
                        <div class="w-6 h-6 py-0.5 justify-center items-center flex"></div>
                    </div>
                    <div class="text-white text-xl font-bold font-['Source Sans Pro'] leading-[30px]">Easy Returns</div>
                </div>
            </div>
        </div>
        <div class="self-stretch h-20 px-20 py-[15px] bg-white shadow justify-between items-center inline-flex">
            <div class="justify-start items-center gap-[30px] flex">
                <div class="w-[154.44px] h-[50px] pr-[0.53px] justify-center items-center gap-[12.78px] flex">
                    <div class="w-[50px] h-[50px] relative flex-col justify-start items-start flex">
                        <div class="w-[50px] h-[50px] relative rounded-lg"></div>
                    </div>
                    <img class="w-[91.13px] h-[14.49px]" src="https://via.placeholder.com/91x14" />
                </div>
                <div class="w-[493px] flex-col justify-start items-start inline-flex">
                    <div class="self-stretch h-11 flex-col justify-start items-start gap-1.5 flex">
                        <div class="self-stretch h-11 flex-col justify-start items-start gap-1.5 flex">
                            <div class="self-stretch px-3.5 py-2.5 bg-white rounded-lg shadow border border-neutral-200 justify-start items-center gap-2 inline-flex">
                                <div class="grow shrink basis-0 h-6 justify-start items-center gap-2 flex">
                                    <div class="w-5 h-5 p-[1.67px] justify-center items-center flex"></div>
                                    <div class="grow shrink basis-0 text-neutral-400 text-base font-normal font-['Source Sans Pro'] leading-normal">Search for a product, influencer, brand, category</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="justify-end items-center gap-5 flex">
                <div class="p-0.5 justify-end items-center gap-[5px] flex">
                    <div class="w-[30px] h-[30px] relative"></div>
                    <div class="text-center text-black text-xs font-semibold font-['Source Sans Pro'] leading-[18px]">Need help?</div>
                </div>
                <div class="w-[30px] h-[30px] pl-[3.85px] pr-[3.08px] pt-[3.08px] pb-[3.85px] justify-center items-center flex">
                    <div class="w-[23.08px] h-[23.08px] pb-px justify-center items-center inline-flex"></div>
                </div>
                <div class="rounded-lg justify-start items-start flex">
                    <div class="px-4 py-2.5 bg-rose-100 rounded-lg shadow border border-rose-100 justify-center items-center gap-2 flex">
                        <div class="w-5 h-5 p-[1.67px] justify-center items-center flex"></div>
                        <div class="text-primary-700 text-sm font-semibold font-['Source Sans Pro'] leading-tight">Login</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="self-stretch justify-start items-start inline-flex">
            <div class="h-[70px] px-20 py-[15px] bg-gray-50 shadow justify-between items-center flex">
                <div class="grow shrink basis-0 h-10 justify-start items-center gap-5 flex">
                    <div class="rounded-lg justify-start items-start flex">
                        <div class="px-4 py-2.5 bg-white rounded-lg shadow border border-neutral-200 justify-center items-center gap-2 flex">
                            <div class="w-5 h-5 px-[1.67px] py-[4.17px] justify-center items-center flex"></div>
                            <div class="text-zinc-600 text-sm font-semibold font-['Source Sans Pro'] leading-tight">Categories</div>
                        </div>
                    </div>
                    <div class="justify-start items-center flex">
                        <div class="px-4 py-2.5 justify-start items-center flex">
                            <div class="text-zinc-600 text-sm font-normal font-['Source Sans Pro'] leading-tight">Fashion & Accessories</div>
                        </div>
                        <div class="px-4 py-2.5 justify-start items-center flex">
                            <div class="text-zinc-600 text-sm font-normal font-['Source Sans Pro'] leading-tight">Electronics and Accessories</div>
                        </div>
                        <div class="px-4 py-2.5 justify-start items-center flex">
                            <div class="text-zinc-600 text-sm font-normal font-['Source Sans Pro'] leading-tight">Beauty & Health</div>
                        </div>
                        <div class="px-4 py-2.5 justify-start items-center flex">
                            <div class="text-zinc-600 text-sm font-normal font-['Source Sans Pro'] leading-tight">Home & Living</div>
                        </div>
                        <div class="px-4 py-2.5 justify-start items-center flex">
                            <div class="text-zinc-600 text-sm font-normal font-['Source Sans Pro'] leading-tight">Household appliances</div>
                        </div>
                        <div class="px-4 py-2.5 justify-start items-center flex">
                            <div class="text-zinc-600 text-sm font-normal font-['Source Sans Pro'] leading-tight">Tickets</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="h-[550px] px-[90px] py-[30px] bg-white flex-col justify-center items-center gap-[30px] flex">
        <div class="self-stretch text-center"><span style="text-pink-800 text-5xl font-bold font-['Noto Serif'] underline leading-[60px]">Market</span><span style="text-rose-800 text-5xl font-semibold font-['Noto Serif'] leading-[60px]"> </span><span style="text-zinc-700 text-5xl font-normal font-['Noto Serif'] leading-[60px]">and</span><span style="text-rose-800 text-5xl font-semibold font-['Noto Serif'] leading-[60px]"> </span><span style="text-amber-500 text-5xl font-bold font-['Noto Serif'] underline leading-[60px]">Sell</span><span style="text-rose-800 text-5xl font-semibold font-['Noto Serif'] leading-[60px]"> </span><span style="text-zinc-700 text-5xl font-normal font-['Noto Serif'] leading-[60px]">through Influencers</span></div>
        <div class="self-stretch justify-center items-center gap-2.5 inline-flex">
            <div class="w-[969px] h-[400px] relative bg-pink-900 rounded-lg">
                <div class="left-[408.50px] top-[445px] absolute rounded-lg justify-start items-start inline-flex">
                    <div class="h-10 px-4 py-2.5 bg-rose-100 rounded-lg shadow border border-rose-100 justify-center items-center gap-2 flex">
                        <div class="text-pink-700 text-sm font-semibold font-['Source Sans Pro'] leading-tight">Shop Now</div>
                    </div>
                </div>
                <img class="w-[1121.27px] h-[683.42px] left-[-96.90px] top-[-77.39px] absolute" src="https://via.placeholder.com/1121x683" />
            </div>
            <div class="w-[281px] p-5 bg-pink-800 rounded-lg flex-col justify-center items-center gap-2.5 inline-flex">
                <div class="opacity-30 w-[429px] h-[429px] relative">
                    <div class="w-[429px] h-[429px] left-0 top-0 absolute opacity-30 bg-yellow-300 rounded-full"></div>
                    <div class="w-[345.97px] h-[345.97px] left-[41.52px] top-[41.52px] absolute opacity-10 bg-amber-600 rounded-full"></div>
                </div>
                <div class="w-[310.09px] h-[169px] relative origin-top-left rotate-90">
                    <div class="origin-top-left rotate-90 w-[423.59px] h-[423.60px] left-[366.84px] top-[-56.75px] absolute">
                        <div class="w-[310.09px] h-[310.09px] left-[-366.84px] top-[366.84px] absolute origin-top-left -rotate-90 justify-center items-center inline-flex">
                            <div class="w-[310.09px] h-[310.09px] justify-center items-center inline-flex">
                                <div class="w-[310.09px] h-[310.09px] bg-rose-800 rounded-full"></div>
                            </div>
                        </div>
                        <div class="w-[310.09px] h-[310.09px] left-[-155.05px] top-[423.59px] absolute origin-top-left rotate-[-150deg] justify-center items-center inline-flex">
                            <div class="w-[310.09px] h-[310.09px] justify-center items-center inline-flex">
                                <div class="w-[310.09px] h-[310.09px] bg-orange-200 rounded-full"></div>
                            </div>
                        </div>
                        <div class="w-[310.09px] h-[310.09px] left-[-0px] top-[268.55px] absolute origin-top-left rotate-[150deg] justify-center items-center inline-flex">
                            <div class="w-[310.09px] h-[310.09px] justify-center items-center inline-flex">
                                <div class="w-[310.09px] h-[310.09px] bg-cyan-600 rounded-full"></div>
                            </div>
                        </div>
                        <div class="origin-top-left -rotate-90 w-[106.07px] h-[22.75px] left-[-221.10px] top-[179.87px] absolute">
                        </div>
                    </div>
                    <div class="w-[27.94px] h-[27.94px] left-[141.08px] top-[140.38px] absolute bg-white rounded-full"></div>
                    <div class="w-[45.72px] h-[53.02px] left-[227.99px] top-[75.30px] absolute">
                    </div>
                    <div class="w-[57.38px] h-[73.21px] left-[35.04px] top-[69.92px] absolute">
                    </div>
                </div>
                <div class="self-stretch rounded-lg justify-start items-start inline-flex">
                    <div class="grow shrink basis-0 h-9 px-3.5 py-2 bg-white rounded-lg shadow border border-neutral-200 justify-center items-center gap-2 flex">
                        <div class="text-zinc-600 text-sm font-semibold font-['Source Sans Pro'] leading-tight">CLICK TO SPIN</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="h-[736px] px-[90px] py-[60px] bg-white flex-col justify-center items-center gap-[30px] flex">
        <div class="self-stretch text-center text-pink-800 text-3xl font-semibold font-['Noto Serif'] leading-[38px]">Hot Deals</div>
        <div class="self-stretch justify-center items-center gap-5 inline-flex">
            <div class="grow shrink basis-0 rounded-[15px] shadow flex-col justify-start items-start inline-flex">
                <div class="w-[300px] h-[300px] relative">
                    <div class="w-[300px] h-[300px] left-0 top-0 absolute bg-white justify-center items-center inline-flex">
                        <img class="w-[300px] h-[375px]" src="https://via.placeholder.com/300x375" />
                    </div>
                    <div class="w-[50px] h-[50px] p-2.5 left-[243px] top-[10px] absolute bg-orange-500 rounded-[100px] shadow justify-center items-center gap-2.5 inline-flex">
                        <div class="w-8 text-stone-50 text-sm font-bold font-['Source Sans Pro'] leading-tight">-10%</div>
                    </div>
                </div>
                <div class="self-stretch h-[174px] p-4 bg-white rounded-bl-[15px] rounded-br-[15px] flex-col justify-start items-start gap-2.5 flex">
                    <div class="self-stretch justify-start items-start inline-flex">
                        <div class="h-[30px] bg-white justify-start items-center gap-1 flex">
                            <img class="w-[30px] h-[30px] rounded-[200px] border border-white" src="https://via.placeholder.com/30x30" />
                            <div class="grow shrink basis-0 text-black text-xs font-semibold font-['Source Sans Pro'] underline leading-[18px]">Demi Wikinson</div>
                        </div>
                    </div>
                    <div class="self-stretch text-black text-base font-semibold font-['Source Sans Pro'] leading-normal">Women’s Sports Tee, Red</div>
                    <div class="self-stretch justify-start items-center gap-1 inline-flex">
                        <div class="text-neutral-500 text-xs font-semibold font-['Source Sans Pro'] line-through leading-[18px]">KSH 2000</div>
                        <div class="text-pink-900 text-xs font-bold font-['Source Sans Pro'] leading-[18px]">KSH 2000</div>
                        <div class="text-orange-500 text-xs font-semibold font-['Source Sans Pro'] leading-[18px]">-10%</div>
                    </div>
                    <div class="self-stretch justify-start items-start gap-2.5 inline-flex">
                        <div class="justify-start items-start flex">
                            <div class="w-10 h-10 rounded-lg justify-start items-start flex">
                                <div class="grow shrink basis-0 self-stretch p-2.5 bg-yellow-50 rounded-lg shadow border border-yellow-50 justify-center items-center gap-2 flex">
                                    <div class="w-5 h-5 pb-[0.83px] justify-center items-center flex"></div>
                                </div>
                            </div>
                        </div>
                        <div class="grow shrink basis-0 h-10 rounded-lg justify-start items-start flex">
                            <div class="grow shrink basis-0 h-10 px-4 py-2.5 bg-pink-900 rounded-lg shadow border border-pink-900 justify-center items-center gap-2 flex">
                                <div class="text-white text-sm font-semibold font-['Source Sans Pro'] leading-tight">Buy Now</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="rounded-[15px] shadow flex-col justify-start items-start inline-flex">
                <div class="w-[300px] h-[300px] relative">
                    <div class="w-[300px] h-[300px] left-0 top-0 absolute bg-white justify-center items-center inline-flex">
                        <img class="w-[300px] h-[375px]" src="https://via.placeholder.com/300x375" />
                    </div>
                    <div class="w-[50px] h-[50px] p-2.5 left-[243px] top-[10px] absolute bg-orange-500 rounded-[100px] shadow justify-center items-center gap-2.5 inline-flex">
                        <div class="w-8 text-stone-50 text-sm font-bold font-['Source Sans Pro'] leading-tight">-10%</div>
                    </div>
                </div>
                <div class="self-stretch h-[174px] p-4 bg-white rounded-bl-[15px] rounded-br-[15px] flex-col justify-start items-start gap-2.5 flex">
                    <div class="self-stretch justify-start items-start inline-flex">
                        <div class="h-[30px] bg-white justify-start items-center gap-1 flex">
                            <img class="w-[30px] h-[30px] rounded-[200px] border border-white" src="https://via.placeholder.com/30x30" />
                            <div class="grow shrink basis-0 text-black text-xs font-semibold font-['Source Sans Pro'] underline leading-[18px]">Demi Wikinson</div>
                        </div>
                    </div>
                    <div class="self-stretch text-black text-base font-semibold font-['Source Sans Pro'] leading-normal">Women’s Sports Tee, Red</div>
                    <div class="self-stretch justify-start items-center gap-1 inline-flex">
                        <div class="text-neutral-500 text-xs font-semibold font-['Source Sans Pro'] line-through leading-[18px]">KSH 2000</div>
                        <div class="text-pink-900 text-xs font-bold font-['Source Sans Pro'] leading-[18px]">KSH 2000</div>
                        <div class="text-orange-500 text-xs font-semibold font-['Source Sans Pro'] leading-[18px]">-10%</div>
                    </div>
                    <div class="self-stretch justify-start items-start gap-2.5 inline-flex">
                        <div class="justify-start items-start flex">
                            <div class="w-10 h-10 rounded-lg justify-start items-start flex">
                                <div class="grow shrink basis-0 self-stretch p-2.5 bg-yellow-50 rounded-lg shadow border border-yellow-50 justify-center items-center gap-2 flex">
                                    <div class="w-5 h-5 pb-[0.83px] justify-center items-center flex"></div>
                                </div>
                            </div>
                        </div>
                        <div class="grow shrink basis-0 h-10 rounded-lg justify-start items-start flex">
                            <div class="grow shrink basis-0 h-10 px-4 py-2.5 bg-pink-900 rounded-lg shadow border border-pink-900 justify-center items-center gap-2 flex">
                                <div class="text-white text-sm font-semibold font-['Source Sans Pro'] leading-tight">Buy Now</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="rounded-[15px] shadow flex-col justify-start items-start inline-flex">
                <div class="w-[300px] h-[300px] relative">
                    <div class="w-[300px] h-[300px] left-0 top-0 absolute bg-white justify-center items-center inline-flex">
                        <img class="w-[300px] h-[375px]" src="https://via.placeholder.com/300x375" />
                    </div>
                    <div class="w-[50px] h-[50px] p-2.5 left-[243px] top-[10px] absolute bg-orange-500 rounded-[100px] shadow justify-center items-center gap-2.5 inline-flex">
                        <div class="w-8 text-stone-50 text-sm font-bold font-['Source Sans Pro'] leading-tight">-10%</div>
                    </div>
                </div>
                <div class="self-stretch h-[174px] p-4 bg-white rounded-bl-[15px] rounded-br-[15px] flex-col justify-start items-start gap-2.5 flex">
                    <div class="self-stretch justify-start items-start inline-flex">
                        <div class="h-[30px] bg-white justify-start items-center gap-1 flex">
                            <img class="w-[30px] h-[30px] rounded-[200px] border border-white" src="https://via.placeholder.com/30x30" />
                            <div class="grow shrink basis-0 text-black text-xs font-semibold font-['Source Sans Pro'] underline leading-[18px]">Demi Wikinson</div>
                        </div>
                    </div>
                    <div class="self-stretch text-black text-base font-semibold font-['Source Sans Pro'] leading-normal">Women’s Sports Tee, Red</div>
                    <div class="self-stretch justify-start items-center gap-1 inline-flex">
                        <div class="text-neutral-500 text-xs font-semibold font-['Source Sans Pro'] line-through leading-[18px]">KSH 2000</div>
                        <div class="text-pink-900 text-xs font-bold font-['Source Sans Pro'] leading-[18px]">KSH 2000</div>
                        <div class="text-orange-500 text-xs font-semibold font-['Source Sans Pro'] leading-[18px]">-10%</div>
                    </div>
                    <div class="self-stretch justify-start items-start gap-2.5 inline-flex">
                        <div class="justify-start items-start flex">
                            <div class="w-10 h-10 rounded-lg justify-start items-start flex">
                                <div class="grow shrink basis-0 self-stretch p-2.5 bg-yellow-50 rounded-lg shadow border border-yellow-50 justify-center items-center gap-2 flex">
                                    <div class="w-5 h-5 pb-[0.83px] justify-center items-center flex"></div>
                                </div>
                            </div>
                        </div>
                        <div class="grow shrink basis-0 h-10 rounded-lg justify-start items-start flex">
                            <div class="grow shrink basis-0 h-10 px-4 py-2.5 bg-pink-900 rounded-lg shadow border border-pink-900 justify-center items-center gap-2 flex">
                                <div class="text-white text-sm font-semibold font-['Source Sans Pro'] leading-tight">Buy Now</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="rounded-[15px] shadow flex-col justify-start items-start inline-flex">
                <div class="w-[300px] h-[300px] relative">
                    <div class="w-[300px] h-[300px] left-0 top-0 absolute bg-white justify-center items-center inline-flex">
                        <img class="w-[300px] h-[375px]" src="https://via.placeholder.com/300x375" />
                    </div>
                    <div class="w-[50px] h-[50px] p-2.5 left-[243px] top-[10px] absolute bg-orange-500 rounded-[100px] shadow justify-center items-center gap-2.5 inline-flex">
                        <div class="w-8 text-stone-50 text-sm font-bold font-['Source Sans Pro'] leading-tight">-10%</div>
                    </div>
                </div>
                <div class="self-stretch h-[174px] p-4 bg-white rounded-bl-[15px] rounded-br-[15px] flex-col justify-start items-start gap-2.5 flex">
                    <div class="self-stretch justify-start items-start inline-flex">
                        <div class="h-[30px] bg-white justify-start items-center gap-1 flex">
                            <img class="w-[30px] h-[30px] rounded-[200px] border border-white" src="https://via.placeholder.com/30x30" />
                            <div class="grow shrink basis-0 text-black text-xs font-semibold font-['Source Sans Pro'] underline leading-[18px]">Demi Wikinson</div>
                        </div>
                    </div>
                    <div class="self-stretch text-black text-base font-semibold font-['Source Sans Pro'] leading-normal">Women’s Sports Tee, Red</div>
                    <div class="self-stretch justify-start items-center gap-1 inline-flex">
                        <div class="text-neutral-500 text-xs font-semibold font-['Source Sans Pro'] line-through leading-[18px]">KSH 2000</div>
                        <div class="text-pink-900 text-xs font-bold font-['Source Sans Pro'] leading-[18px]">KSH 2000</div>
                        <div class="text-orange-500 text-xs font-semibold font-['Source Sans Pro'] leading-[18px]">-10%</div>
                    </div>
                    <div class="self-stretch justify-start items-start gap-2.5 inline-flex">
                        <div class="justify-start items-start flex">
                            <div class="w-10 h-10 rounded-lg justify-start items-start flex">
                                <div class="grow shrink basis-0 self-stretch p-2.5 bg-yellow-50 rounded-lg shadow border border-yellow-50 justify-center items-center gap-2 flex">
                                    <div class="w-5 h-5 pb-[0.83px] justify-center items-center flex"></div>
                                </div>
                            </div>
                        </div>
                        <div class="grow shrink basis-0 h-10 rounded-lg justify-start items-start flex">
                            <div class="grow shrink basis-0 h-10 px-4 py-2.5 bg-pink-900 rounded-lg shadow border border-pink-900 justify-center items-center gap-2 flex">
                                <div class="text-white text-sm font-semibold font-['Source Sans Pro'] leading-tight">Buy Now</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="rounded-lg justify-start items-start inline-flex">
            <div class="px-[18px] py-2.5 bg-white rounded-lg shadow border border-neutral-200 justify-center items-center gap-2 flex">
                <div class="text-pink-700 text-base font-semibold font-['Source Sans Pro'] leading-normal">View More</div>
                <div class="w-5 h-5 p-[3.33px] justify-center items-center flex"></div>
            </div>
        </div>
    </div>
    <div class="h-[736px] px-[90px] py-[60px] bg-white flex-col justify-center items-center gap-[30px] flex">
        <div class="self-stretch text-center text-pink-800 text-3xl font-semibold font-['Noto Serif'] leading-[38px]">Trending Products</div>
        <div class="self-stretch justify-center items-center gap-5 inline-flex">
            <div class="rounded-[15px] shadow flex-col justify-start items-start inline-flex">
                <div class="w-[300px] h-[300px] justify-center items-center inline-flex">
                    <div class="grow shrink basis-0 self-stretch bg-white justify-center items-center inline-flex">
                        <img class="w-[300px] h-[375px]" src="https://via.placeholder.com/300x375" />
                    </div>
                </div>
                <div class="self-stretch h-[174px] p-4 bg-white rounded-bl-[15px] rounded-br-[15px] flex-col justify-start items-start gap-2.5 flex">
                    <div class="self-stretch justify-start items-start inline-flex">
                        <div class="h-[30px] bg-white justify-start items-center gap-1 flex">
                            <img class="w-[30px] h-[30px] rounded-[200px] border border-white" src="https://via.placeholder.com/30x30" />
                            <div class="grow shrink basis-0 text-black text-xs font-semibold font-['Source Sans Pro'] underline leading-[18px]">Demi Wikinson</div>
                        </div>
                    </div>
                    <div class="self-stretch text-black text-base font-semibold font-['Source Sans Pro'] leading-normal">Women’s Sports Tee, Red</div>
                    <div class="self-stretch justify-start items-center gap-2.5 inline-flex">
                        <div class="text-pink-900 text-xs font-bold font-['Source Sans Pro'] leading-[18px]">KSH 2000</div>
                    </div>
                    <div class="self-stretch justify-start items-start gap-2.5 inline-flex">
                        <div class="justify-start items-start flex">
                            <div class="w-10 h-10 rounded-lg justify-start items-start flex">
                                <div class="grow shrink basis-0 self-stretch p-2.5 bg-yellow-50 rounded-lg shadow border border-yellow-50 justify-center items-center gap-2 flex">
                                    <div class="w-5 h-5 pb-[0.83px] justify-center items-center flex"></div>
                                </div>
                            </div>
                        </div>
                        <div class="grow shrink basis-0 h-10 rounded-lg justify-start items-start flex">
                            <div class="grow shrink basis-0 h-10 px-4 py-2.5 bg-pink-900 rounded-lg shadow border border-pink-900 justify-center items-center gap-2 flex">
                                <div class="text-white text-sm font-semibold font-['Source Sans Pro'] leading-tight">Buy Now</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="rounded-[15px] shadow flex-col justify-start items-start inline-flex">
                <div class="w-[300px] h-[300px] justify-center items-center inline-flex">
                    <div class="grow shrink basis-0 self-stretch bg-white justify-center items-center inline-flex">
                        <img class="w-[300px] h-[375px]" src="https://via.placeholder.com/300x375" />
                    </div>
                </div>
                <div class="self-stretch h-[174px] p-4 bg-white rounded-bl-[15px] rounded-br-[15px] flex-col justify-start items-start gap-2.5 flex">
                    <div class="self-stretch justify-start items-start inline-flex">
                        <div class="h-[30px] bg-white justify-start items-center gap-1 flex">
                            <img class="w-[30px] h-[30px] rounded-[200px] border border-white" src="https://via.placeholder.com/30x30" />
                            <div class="grow shrink basis-0 text-black text-xs font-semibold font-['Source Sans Pro'] underline leading-[18px]">Demi Wikinson</div>
                        </div>
                    </div>
                    <div class="self-stretch text-black text-base font-semibold font-['Source Sans Pro'] leading-normal">Women’s Sports Tee, Red</div>
                    <div class="self-stretch justify-start items-center gap-2.5 inline-flex">
                        <div class="text-pink-900 text-xs font-bold font-['Source Sans Pro'] leading-[18px]">KSH 2000</div>
                    </div>
                    <div class="self-stretch justify-start items-start gap-2.5 inline-flex">
                        <div class="justify-start items-start flex">
                            <div class="w-10 h-10 rounded-lg justify-start items-start flex">
                                <div class="grow shrink basis-0 self-stretch p-2.5 bg-yellow-50 rounded-lg shadow border border-yellow-50 justify-center items-center gap-2 flex">
                                    <div class="w-5 h-5 pb-[0.83px] justify-center items-center flex"></div>
                                </div>
                            </div>
                        </div>
                        <div class="grow shrink basis-0 h-10 rounded-lg justify-start items-start flex">
                            <div class="grow shrink basis-0 h-10 px-4 py-2.5 bg-pink-900 rounded-lg shadow border border-pink-900 justify-center items-center gap-2 flex">
                                <div class="text-white text-sm font-semibold font-['Source Sans Pro'] leading-tight">Buy Now</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="rounded-[15px] shadow flex-col justify-start items-start inline-flex">
                <div class="w-[300px] h-[300px] justify-center items-center inline-flex">
                    <div class="grow shrink basis-0 self-stretch bg-white justify-center items-center inline-flex">
                        <img class="w-[300px] h-[375px]" src="https://via.placeholder.com/300x375" />
                    </div>
                </div>
                <div class="self-stretch h-[174px] p-4 bg-white rounded-bl-[15px] rounded-br-[15px] flex-col justify-start items-start gap-2.5 flex">
                    <div class="self-stretch justify-start items-start inline-flex">
                        <div class="h-[30px] bg-white justify-start items-center gap-1 flex">
                            <img class="w-[30px] h-[30px] rounded-[200px] border border-white" src="https://via.placeholder.com/30x30" />
                            <div class="grow shrink basis-0 text-black text-xs font-semibold font-['Source Sans Pro'] underline leading-[18px]">Demi Wikinson</div>
                        </div>
                    </div>
                    <div class="self-stretch text-black text-base font-semibold font-['Source Sans Pro'] leading-normal">Business Event Conference</div>
                    <div class="self-stretch justify-start items-center gap-2.5 inline-flex">
                        <div class="text-pink-900 text-xs font-bold font-['Source Sans Pro'] leading-[18px]">KSH 2000</div>
                    </div>
                    <div class="self-stretch justify-start items-start gap-2.5 inline-flex">
                        <div class="justify-start items-start flex">
                            <div class="w-10 h-10 rounded-lg justify-start items-start flex">
                                <div class="grow shrink basis-0 self-stretch p-2.5 bg-yellow-50 rounded-lg shadow border border-yellow-50 justify-center items-center gap-2 flex">
                                    <div class="w-5 h-5 pb-[0.83px] justify-center items-center flex"></div>
                                </div>
                            </div>
                        </div>
                        <div class="grow shrink basis-0 h-10 rounded-lg justify-start items-start flex">
                            <div class="grow shrink basis-0 h-10 px-4 py-2.5 bg-pink-900 rounded-lg shadow border border-pink-900 justify-center items-center gap-2 flex">
                                <div class="text-white text-sm font-semibold font-['Source Sans Pro'] leading-tight">Buy Now</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="rounded-[15px] shadow flex-col justify-start items-start inline-flex">
                <div class="w-[300px] h-[300px] justify-center items-center inline-flex">
                    <div class="grow shrink basis-0 self-stretch bg-white justify-center items-center inline-flex">
                        <img class="w-[300px] h-[375px]" src="https://via.placeholder.com/300x375" />
                    </div>
                </div>
                <div class="self-stretch h-[174px] p-4 bg-white rounded-bl-[15px] rounded-br-[15px] flex-col justify-start items-start gap-2.5 flex">
                    <div class="self-stretch justify-start items-start inline-flex">
                        <div class="h-[30px] bg-white justify-start items-center gap-1 flex">
                            <img class="w-[30px] h-[30px] rounded-[200px] border border-white" src="https://via.placeholder.com/30x30" />
                            <div class="grow shrink basis-0 text-black text-xs font-semibold font-['Source Sans Pro'] underline leading-[18px]">Demi Wikinson</div>
                        </div>
                    </div>
                    <div class="self-stretch text-black text-base font-semibold font-['Source Sans Pro'] leading-normal">Business Event Conference</div>
                    <div class="self-stretch justify-start items-center gap-2.5 inline-flex">
                        <div class="text-pink-900 text-xs font-bold font-['Source Sans Pro'] leading-[18px]">KSH 2000</div>
                    </div>
                    <div class="self-stretch justify-start items-start gap-2.5 inline-flex">
                        <div class="justify-start items-start flex">
                            <div class="w-10 h-10 rounded-lg justify-start items-start flex">
                                <div class="grow shrink basis-0 self-stretch p-2.5 bg-yellow-50 rounded-lg shadow border border-yellow-50 justify-center items-center gap-2 flex">
                                    <div class="w-5 h-5 pb-[0.83px] justify-center items-center flex"></div>
                                </div>
                            </div>
                        </div>
                        <div class="grow shrink basis-0 h-10 rounded-lg justify-start items-start flex">
                            <div class="grow shrink basis-0 h-10 px-4 py-2.5 bg-pink-900 rounded-lg shadow border border-pink-900 justify-center items-center gap-2 flex">
                                <div class="text-white text-sm font-semibold font-['Source Sans Pro'] leading-tight">Buy Now</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="rounded-lg justify-start items-start inline-flex">
            <div class="px-[18px] py-2.5 bg-white rounded-lg shadow border border-neutral-200 justify-center items-center gap-2 flex">
                <div class="text-pink-700 text-base font-semibold font-['Source Sans Pro'] leading-normal">View More</div>
                <div class="w-5 h-5 p-[3.33px] justify-center items-center flex"></div>
            </div>
        </div>
    </div>
    <div class="h-[578px] px-[90px] py-[60px] bg-white flex-col justify-center items-center gap-[30px] flex">
        <div class="self-stretch text-center text-pink-800 text-3xl font-semibold font-['Noto Serif'] leading-[38px]">10K+ Trusted Influencers</div>
        <div class="w-[700px] justify-center items-center gap-5 inline-flex">
            <div class="w-[100px] h-[100px] rounded-full justify-center items-center flex">
                <img class="w-[115px] h-[139px]" src="https://via.placeholder.com/115x139" />
            </div>
            <div class="w-[100px] h-[100px] rounded-full justify-center items-center flex">
                <img class="w-[118px] h-[139px]" src="https://via.placeholder.com/118x139" />
            </div>
            <div class="w-[100px] h-[100px] rounded-full justify-end items-center flex">
                <img class="w-[127px] h-[139px]" src="https://via.placeholder.com/127x139" />
            </div>
            <div class="w-[100px] h-[100px] rounded-full justify-center items-center flex">
                <img class="w-[122px] h-[139px]" src="https://via.placeholder.com/122x139" />
            </div>
            <div class="w-[100px] h-[100px] rounded-full justify-center items-center flex">
                <img class="w-[139px] h-[121px]" src="https://via.placeholder.com/139x121" />
            </div>
            <div class="w-[100px] h-[100px] rounded-full justify-center items-center flex">
                <img class="w-[138px] h-[113px]" src="https://via.placeholder.com/138x113" />
            </div>
            <div class="w-[100px] h-[100px] rounded-full justify-center items-center flex">
                <img class="w-[138px] h-[139px]" src="https://via.placeholder.com/138x139" />
            </div>
            <div class="w-[100px] h-[100px] rounded-full justify-center items-center flex">
                <img class="w-[138px] h-[138px]" src="https://via.placeholder.com/138x138" />
            </div>
            <div class="w-[100px] h-[100px] rounded-full justify-center items-center flex">
                <img class="w-[113px] h-[139px]" src="https://via.placeholder.com/113x139" />
            </div>
            <div class="w-[100px] h-[100px] rounded-full justify-center items-center flex">
                <img class="w-[139px] h-[131px]" src="https://via.placeholder.com/139x131" />
            </div>
            <div class="w-[100px] h-[100px] rounded-full justify-center items-center flex">
                <img class="w-[139px] h-[135px]" src="https://via.placeholder.com/139x135" />
            </div>
            <div class="w-[100px] h-[100px] rounded-full justify-center items-center flex">
                <img class="w-[139px] h-[118px]" src="https://via.placeholder.com/139x118" />
            </div>
            <div class="w-[100px] h-[100px] rounded-full justify-center items-center flex">
                <img class="w-[114px] h-[139px]" src="https://via.placeholder.com/114x139" />
            </div>
            <div class="w-[100px] h-[100px] rounded-full justify-center items-center flex">
                <img class="w-[139px] h-[132px]" src="https://via.placeholder.com/139x132" />
            </div>
            <div class="w-[100px] h-[100px] rounded-full justify-center items-center flex">
                <img class="w-[127px] h-[139px]" src="https://via.placeholder.com/127x139" />
            </div>
            <div class="w-[100px] h-[100px] rounded-full justify-center items-center flex">
                <img class="w-[137px] h-[139px]" src="https://via.placeholder.com/137x139" />
            </div>
            <div class="w-[100px] h-[100px] rounded-full justify-center items-center flex">
                <img class="w-[116px] h-[139px]" src="https://via.placeholder.com/116x139" />
            </div>
            <div class="w-[100px] h-[100px] rounded-full justify-center items-center flex">
                <img class="w-[129px] h-[139px]" src="https://via.placeholder.com/129x139" />
            </div>
        </div>
        <div class="h-10 rounded-lg justify-start items-start inline-flex">
            <div class="px-4 py-2.5 bg-pink-900 rounded-lg shadow border border-pink-900 justify-center items-center gap-2 flex">
                <div class="text-white text-sm font-semibold font-['Source Sans Pro'] leading-tight">Learn more about Influencer Marketing</div>
            </div>
        </div>
    </div>
    <div class="h-[519px] px-[90px] py-[60px] bg-pink-900 flex-col justify-center items-center gap-[30px] flex">
        <div class="self-stretch h-[399px] flex-col justify-center items-center gap-[30px] flex">
            <div class="self-stretch text-center text-white text-3xl font-semibold font-['Noto Serif'] leading-[38px]">Boost Your Sales with:</div>
            <div class="self-stretch justify-center items-center gap-2.5 inline-flex">
                <div class="flex-col justify-start items-start inline-flex">
                    <div class="w-[244px] h-[331px] p-5 bg-white rounded-lg shadow flex-col justify-start items-center gap-1.5 flex">
                        <div class="w-[231px] h-[229px] justify-center items-center inline-flex">
                            <img class="w-[231px] h-[229px]" src="https://via.placeholder.com/231x229" />
                        </div>
                        <div class="self-stretch h-14 flex-col justify-start items-center gap-1.5 flex">
                            <div class="self-stretch text-pink-800 text-xl font-bold font-['Source Sans Pro'] leading-[30px]">Influencers Marketing</div>
                            <div class="self-stretch justify-start items-start inline-flex">
                                <div class="justify-center items-center gap-2 flex">
                                    <div class="text-amber-500 text-sm font-semibold font-['Source Sans Pro'] leading-tight">Learn More</div>
                                    <div class="w-5 h-5 p-[3.33px] justify-center items-center flex"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex-col justify-start items-start inline-flex">
                    <div class="w-[244px] h-[331px] p-5 bg-white rounded-lg shadow flex-col justify-start items-center gap-1.5 flex">
                        <div class="w-[231px] h-[229px] justify-center items-center inline-flex">
                            <img class="w-[231px] h-[229px]" src="https://via.placeholder.com/231x229" />
                        </div>
                        <div class="self-stretch h-14 flex-col justify-start items-center gap-1.5 flex">
                            <div class="self-stretch text-pink-800 text-xl font-bold font-['Source Sans Pro'] leading-[30px]">WhatsApp Seeding</div>
                            <div class="self-stretch justify-start items-start inline-flex">
                                <div class="justify-center items-center gap-2 flex">
                                    <div class="text-amber-500 text-sm font-semibold font-['Source Sans Pro'] leading-tight">Learn More</div>
                                    <div class="w-5 h-5 p-[3.33px] justify-center items-center flex"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex-col justify-start items-start inline-flex">
                    <div class="w-[244px] h-[331px] p-5 bg-white rounded-lg shadow flex-col justify-start items-center gap-1.5 flex">
                        <div class="w-[231px] h-[229px] justify-center items-center inline-flex">
                            <img class="w-[231px] h-[229px]" src="https://via.placeholder.com/231x229" />
                        </div>
                        <div class="self-stretch h-14 flex-col justify-start items-center gap-1.5 flex">
                            <div class="self-stretch text-pink-800 text-xl font-bold font-['Source Sans Pro'] leading-[30px]">Twitter Trends</div>
                            <div class="self-stretch justify-start items-start inline-flex">
                                <div class="justify-center items-center gap-2 flex">
                                    <div class="text-amber-500 text-sm font-semibold font-['Source Sans Pro'] leading-tight">Learn More</div>
                                    <div class="w-5 h-5 p-[3.33px] justify-center items-center flex"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex-col justify-start items-start inline-flex">
                    <div class="w-[244px] h-[331px] p-5 bg-white rounded-lg shadow flex-col justify-start items-center gap-1.5 flex">
                        <div class="w-[231px] h-[229px] justify-center items-center inline-flex">
                            <img class="w-[231px] h-[231px]" src="https://via.placeholder.com/231x231" />
                        </div>
                        <div class="self-stretch h-14 flex-col justify-start items-center gap-1.5 flex">
                            <div class="self-stretch text-pink-800 text-xl font-bold font-['Source Sans Pro'] leading-[30px]">Content creation</div>
                            <div class="self-stretch justify-start items-start inline-flex">
                                <div class="justify-center items-center gap-2 flex">
                                    <div class="text-amber-500 text-sm font-semibold font-['Source Sans Pro'] leading-tight">Learn More</div>
                                    <div class="w-5 h-5 p-[3.33px] justify-center items-center flex"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex-col justify-start items-start inline-flex">
                    <div class="w-[244px] h-[331px] p-5 bg-white rounded-lg shadow flex-col justify-start items-center gap-1.5 flex">
                        <div class="w-[231px] h-[229px] justify-center items-center inline-flex">
                            <img class="w-[231px] h-[229px]" src="https://via.placeholder.com/231x229" />
                        </div>
                        <div class="self-stretch h-14 flex-col justify-start items-center gap-1.5 flex">
                            <div class="self-stretch text-pink-800 text-xl font-bold font-['Source Sans Pro'] leading-[30px]">Social Listening</div>
                            <div class="self-stretch justify-start items-start inline-flex">
                                <div class="justify-center items-center gap-2 flex">
                                    <div class="text-amber-500 text-sm font-semibold font-['Source Sans Pro'] leading-tight">Learn More</div>
                                    <div class="w-5 h-5 p-[3.33px] justify-center items-center flex"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="w-[1440px] p-[90px] bg-rose-100 justify-center items-center gap-[30px] inline-flex">
        <div class="grow shrink basis-0 h-[300px] justify-center items-center gap-[30px] flex">
            <div class="grow shrink basis-0 flex-col justify-start items-start gap-2.5 inline-flex">
                <div class="self-stretch h-[72px] p-5 bg-white rounded-lg shadow flex-col justify-center items-center gap-2.5 flex">
                    <div class="self-stretch justify-center items-center gap-2.5 inline-flex">
                        <div class="w-[30px] h-[30px] py-[3px] bg-yellow-50 rounded-[200px] border-4 border-white justify-center items-center flex">
                            <div class="w-24 text-center text-rose-800 text-base font-bold font-['Source Sans Pro'] leading-normal">1</div>
                        </div>
                        <div class="grow shrink basis-0 text-neutral-800 text-2xl font-semibold font-['Source Sans Pro'] leading-loose">Find products and gigs on Twiva</div>
                    </div>
                </div>
                <div class="self-stretch h-[72px] p-5 bg-white rounded-lg shadow flex-col justify-center items-center gap-2.5 flex">
                    <div class="self-stretch justify-center items-center gap-2.5 inline-flex">
                        <div class="w-[30px] h-[30px] py-[3px] bg-yellow-50 rounded-[200px] border-4 border-white justify-center items-center flex">
                            <div class="w-24 text-center text-rose-800 text-base font-bold font-['Source Sans Pro'] leading-normal">2</div>
                        </div>
                        <div class="grow shrink basis-0 text-neutral-800 text-2xl font-semibold font-['Source Sans Pro'] leading-loose">Promote them online.</div>
                    </div>
                </div>
                <div class="self-stretch h-[72px] p-5 bg-white rounded-lg shadow flex-col justify-center items-center gap-2.5 flex">
                    <div class="self-stretch justify-center items-center gap-2.5 inline-flex">
                        <div class="w-[30px] h-[30px] py-[3px] bg-yellow-50 rounded-[200px] border-4 border-white justify-center items-center flex">
                            <div class="w-24 text-center text-rose-800 text-base font-bold font-['Source Sans Pro'] leading-normal">3</div>
                        </div>
                        <div class="grow shrink basis-0 text-neutral-800 text-2xl font-semibold font-['Source Sans Pro'] leading-loose">Earn!</div>
                    </div>
                </div>
            </div>
            <div class="grow shrink basis-0 p-[30px] bg-amber-500/opacity-60 flex-col justify-center items-start gap-5 inline-flex">
                <div class="w-[50px] h-[50px] relative">
                    <div class="w-[50px] h-[50px] left-0 top-0 absolute rounded-lg"></div>
                </div>
                <div class="self-stretch h-[76px] flex-col justify-center items-start gap-2.5 flex">
                    <div class="self-stretch text-white text-3xl font-semibold font-['Noto Serif'] leading-[38px]">Become an Influencer!</div>
                    <div class="self-stretch text-white text-lg font-normal font-['Source Sans Pro'] leading-7">Anzisha biz na 0 investment!</div>
                </div>
                <div class="flex-col justify-center items-start gap-2.5 flex">
                    <div class="w-44 h-11 rounded-lg justify-start items-start inline-flex">
                        <div class="grow shrink basis-0 h-11 px-[18px] py-2.5 bg-rose-100 rounded-lg shadow border border-rose-100 justify-center items-center gap-2 flex">
                            <div class="text-pink-700 text-base font-semibold font-['Source Sans Pro'] leading-normal">Register today</div>
                        </div>
                    </div>
                    <div class="justify-start items-start inline-flex">
                        <div class="justify-center items-center gap-2 flex">
                            <div class="text-gray-50 text-sm font-semibold font-['Source Sans Pro'] leading-tight">Learn More</div>
                            <div class="w-5 h-5 p-[3.33px] justify-center items-center flex"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="w-[819.51px] h-[420px] bg-black/opacity-40"></div>
    </div>
    <div class="w-[1440px] p-[90px] bg-yellow-50 justify-center items-center gap-[30px] inline-flex">
        <div class="w-[859.31px] h-[420px] bg-black/opacity-40"></div>
        <div class="grow shrink basis-0 p-[30px] bg-pink-900/opacity-70 flex-col justify-center items-start gap-5 inline-flex">
            <div class="w-[50px] h-[50px] relative">
                <div class="w-[50px] h-[50px] left-0 top-0 absolute rounded-lg"></div>
            </div>
            <div class="self-stretch h-[76px] flex-col justify-center items-start gap-2.5 flex">
                <div class="self-stretch text-gray-50 text-3xl font-semibold font-['Noto Serif'] leading-[38px]">Become a merchant! </div>
                <div class="self-stretch text-gray-50 text-lg font-normal font-['Source Sans Pro'] leading-7">Gain access to thousands of influencers and millions of buyers!</div>
            </div>
            <div class="flex-col justify-center items-start gap-2.5 flex">
                <div class="w-44 h-11 rounded-lg justify-start items-start inline-flex">
                    <div class="grow shrink basis-0 h-11 px-[18px] py-2.5 bg-rose-100 rounded-lg shadow border border-rose-100 justify-center items-center gap-2 flex">
                        <div class="text-pink-700 text-base font-semibold font-['Source Sans Pro'] leading-normal">Register today</div>
                    </div>
                </div>
                <div class="justify-start items-start inline-flex">
                    <div class="justify-center items-center gap-2 flex">
                        <div class="text-gray-50 text-sm font-semibold font-['Source Sans Pro'] leading-tight">Learn More</div>
                        <div class="w-5 h-5 p-[3.33px] justify-center items-center flex"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="grow shrink basis-0 flex-col justify-start items-start gap-2.5 inline-flex">
            <div class="self-stretch h-[72px] p-5 bg-white rounded-lg shadow flex-col justify-center items-center gap-2.5 flex">
                <div class="self-stretch justify-center items-center gap-2.5 inline-flex">
                    <div class="w-[30px] h-[30px] py-[3px] bg-yellow-50 rounded-[200px] border-4 border-white justify-center items-center flex">
                        <div class="w-24 text-center text-rose-800 text-base font-bold font-['Source Sans Pro'] leading-normal">1</div>
                    </div>
                    <div class="grow shrink basis-0 text-neutral-800 text-2xl font-semibold font-['Source Sans Pro'] leading-loose">List your product and services</div>
                </div>
            </div>
            <div class="self-stretch h-[72px] p-5 bg-white rounded-lg shadow flex-col justify-center items-center gap-2.5 flex">
                <div class="self-stretch justify-center items-center gap-2.5 inline-flex">
                    <div class="w-[30px] h-[30px] py-[3px] bg-yellow-50 rounded-[200px] border-4 border-white justify-center items-center flex">
                        <div class="w-24 text-center text-rose-800 text-base font-bold font-['Source Sans Pro'] leading-normal">2</div>
                    </div>
                    <div class="grow shrink basis-0 text-neutral-800 text-2xl font-semibold font-['Source Sans Pro'] leading-loose">Influencers market and sell</div>
                </div>
            </div>
            <div class="self-stretch h-[72px] p-5 bg-white rounded-lg shadow flex-col justify-center items-center gap-2.5 flex">
                <div class="self-stretch justify-center items-center gap-2.5 inline-flex">
                    <div class="w-[30px] h-[30px] py-[3px] bg-yellow-50 rounded-[200px] border-4 border-white justify-center items-center flex">
                        <div class="w-24 text-center text-rose-800 text-base font-bold font-['Source Sans Pro'] leading-normal">3</div>
                    </div>
                    <div class="grow shrink basis-0 text-neutral-800 text-2xl font-semibold font-['Source Sans Pro'] leading-loose">Increase awareness and sales</div>
                </div>
            </div>
        </div>
    </div>
    <div class="h-[378px] px-[90px] py-[60px] bg-pink-900 flex-col justify-center items-center gap-[30px] flex">
        <div class="self-stretch text-center text-gray-50 text-3xl font-semibold font-['Noto Serif'] leading-[38px]">Brands on Twiva</div>
        <div class="justify-center items-center gap-[30px] inline-flex">
            <div class="p-2.5 bg-white rounded-lg justify-start items-center gap-2.5 flex">
                <img class="w-[60px] h-[60px]" src="https://via.placeholder.com/60x60" />
            </div>
            <div class="p-2.5 bg-white rounded-lg flex-col justify-start items-center gap-2.5 inline-flex">
                <img class="w-[139.53px] h-[60px]" src="https://via.placeholder.com/140x60" />
            </div>
            <div class="p-2.5 bg-white rounded-lg flex-col justify-start items-center gap-2.5 inline-flex">
                <img class="w-[106.67px] h-[60px]" src="https://via.placeholder.com/107x60" />
            </div>
            <div class="bg-white rounded-lg justify-start items-center gap-2.5 flex">
                <img class="w-20 h-20" src="https://via.placeholder.com/80x80" />
            </div>
            <div class="bg-white rounded-lg justify-start items-center gap-2.5 flex">
                <img class="w-20 h-20" src="https://via.placeholder.com/80x80" />
            </div>
            <div class="p-2.5 bg-white rounded-lg flex-col justify-start items-center gap-2.5 inline-flex">
                <img class="w-[141.09px] h-[60px]" src="https://via.placeholder.com/141x60" />
            </div>
            <div class="bg-white rounded-lg justify-start items-center gap-2.5 flex">
                <img class="w-20 h-20" src="https://via.placeholder.com/80x80" />
            </div>
            <div class="bg-white rounded-lg flex-col justify-start items-center gap-2.5 inline-flex">
                <img class="w-[173.87px] h-20" src="https://via.placeholder.com/174x80" />
            </div>
        </div>
        <div class="justify-center items-center gap-[68px] inline-flex">
            <div class="p-2.5 bg-white rounded-lg flex-col justify-start items-center gap-2.5 inline-flex">
                <img class="w-[136.24px] h-[60px]" src="https://via.placeholder.com/136x60" />
            </div>
            <div class="bg-white rounded-lg flex-col justify-start items-center gap-2.5 inline-flex">
                <img class="w-[321.51px] h-20" src="https://via.placeholder.com/322x80" />
            </div>
            <div class="p-2.5 bg-white rounded-lg flex-col justify-start items-center gap-2.5 inline-flex">
                <img class="w-[134.50px] h-[60px]" src="https://via.placeholder.com/134x60" />
            </div>
            <div class="p-2.5 bg-white rounded-lg flex-col justify-start items-center gap-2.5 inline-flex">
                <img class="w-[84.93px] h-[60px]" src="https://via.placeholder.com/85x60" />
            </div>
            <div class="p-2.5 bg-white rounded-lg flex-col justify-start items-center gap-2.5 inline-flex">
                <img class="w-[108px] h-[60px]" src="https://via.placeholder.com/108x60" />
            </div>
        </div>
    </div>
    <div class="h-[1778px] p-[90px] bg-white flex-col justify-start items-start gap-[30px] flex">
        <div class="self-stretch text-center text-pink-800 text-3xl font-semibold font-['Noto Serif'] leading-[38px]">Products you might like</div>
        <div class="self-stretch justify-between items-center inline-flex">
            <div class="w-[493px] flex-col justify-start items-start inline-flex">
                <div class="self-stretch h-11 flex-col justify-start items-start gap-1.5 flex">
                    <div class="self-stretch h-11 flex-col justify-start items-start gap-1.5 flex">
                        <div class="self-stretch px-3.5 py-2.5 bg-white rounded-lg shadow border border-neutral-200 justify-start items-center gap-2 inline-flex">
                            <div class="grow shrink basis-0 h-6 justify-start items-center gap-2 flex">
                                <div class="w-5 h-5 p-[1.67px] justify-center items-center flex"></div>
                                <div class="grow shrink basis-0 text-neutral-400 text-base font-normal font-['Source Sans Pro'] leading-normal">Search for a product, re-seller, merchant, category</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="h-11 p-2.5 justify-between items-center flex">
                <div class="w-5 h-5 px-[1.67px] py-[4.17px] justify-center items-center flex"></div>
                <div><span style="text-zinc-700 text-base font-normal font-['Source Sans Pro'] leading-normal">Sort by: </span><span style="text-zinc-700 text-base font-semibold font-['Source Sans Pro'] leading-normal">Newest</span></div>
            </div>
        </div>
        <div class="self-stretch h-[1386px] flex-col justify-start items-start gap-[30px] flex">
            <div class="self-stretch justify-start items-start gap-5 inline-flex">
                <div class="h-[442px] rounded-[15px] shadow flex-col justify-start items-start inline-flex">
                    <div class="w-[301px] h-[300px] bg-white justify-center items-center inline-flex">
                        <img class="w-[301px] h-[375px]" src="https://via.placeholder.com/301x375" />
                    </div>
                    <div class="self-stretch h-[142px] p-5 bg-white rounded-bl-[15px] rounded-br-[15px] flex-col justify-start items-start gap-2.5 flex">
                        <div class="w-[261px] text-black text-base font-semibold font-['Source Sans Pro'] leading-normal">Women’s Sports Tee, Red</div>
                        <div class="w-[261px] text-pink-900 text-xs font-semibold font-['Source Sans Pro'] leading-[18px]">KSH 2000</div>
                        <div class="self-stretch justify-start items-start gap-2.5 inline-flex">
                            <div class="justify-start items-start flex">
                                <div class="w-10 h-10 rounded-lg justify-start items-start flex">
                                    <div class="grow shrink basis-0 self-stretch p-2.5 bg-yellow-50 rounded-lg shadow border border-yellow-50 justify-center items-center gap-2 flex">
                                        <div class="w-5 h-5 pb-[0.83px] justify-center items-center flex"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="grow shrink basis-0 h-10 rounded-lg justify-start items-start flex">
                                <div class="grow shrink basis-0 h-10 px-4 py-2.5 bg-pink-900 rounded-lg shadow border border-pink-900 justify-center items-center gap-2 flex">
                                    <div class="text-white text-sm font-semibold font-['Source Sans Pro'] leading-tight">Buy Now</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="h-[442px] rounded-[15px] shadow flex-col justify-start items-start inline-flex">
                    <div class="w-[301px] h-[300px] bg-white justify-center items-center inline-flex">
                        <img class="w-[301px] h-[375px]" src="https://via.placeholder.com/301x375" />
                    </div>
                    <div class="self-stretch h-[142px] p-5 bg-white rounded-bl-[15px] rounded-br-[15px] flex-col justify-start items-start gap-2.5 flex">
                        <div class="w-[261px] text-black text-base font-semibold font-['Source Sans Pro'] leading-normal">Women’s Sports Tee, Red</div>
                        <div class="w-[261px] text-pink-900 text-xs font-semibold font-['Source Sans Pro'] leading-[18px]">KSH 2000</div>
                        <div class="self-stretch justify-start items-start gap-2.5 inline-flex">
                            <div class="justify-start items-start flex">
                                <div class="w-10 h-10 rounded-lg justify-start items-start flex">
                                    <div class="grow shrink basis-0 self-stretch p-2.5 bg-yellow-50 rounded-lg shadow border border-yellow-50 justify-center items-center gap-2 flex">
                                        <div class="w-5 h-5 pb-[0.83px] justify-center items-center flex"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="grow shrink basis-0 h-10 rounded-lg justify-start items-start flex">
                                <div class="grow shrink basis-0 h-10 px-4 py-2.5 bg-pink-900 rounded-lg shadow border border-pink-900 justify-center items-center gap-2 flex">
                                    <div class="text-white text-sm font-semibold font-['Source Sans Pro'] leading-tight">Buy Now</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="h-[442px] rounded-[15px] shadow flex-col justify-start items-start inline-flex">
                    <div class="w-[301px] h-[300px] bg-white justify-center items-center inline-flex">
                        <img class="w-[301px] h-[375px]" src="https://via.placeholder.com/301x375" />
                    </div>
                    <div class="self-stretch h-[142px] p-5 bg-white rounded-bl-[15px] rounded-br-[15px] flex-col justify-start items-start gap-2.5 flex">
                        <div class="w-[261px] text-black text-base font-semibold font-['Source Sans Pro'] leading-normal">Women’s Sports Tee, Red</div>
                        <div class="w-[261px] text-pink-900 text-xs font-semibold font-['Source Sans Pro'] leading-[18px]">KSH 2000</div>
                        <div class="self-stretch justify-start items-start gap-2.5 inline-flex">
                            <div class="justify-start items-start flex">
                                <div class="w-10 h-10 rounded-lg justify-start items-start flex">
                                    <div class="grow shrink basis-0 self-stretch p-2.5 bg-yellow-50 rounded-lg shadow border border-yellow-50 justify-center items-center gap-2 flex">
                                        <div class="w-5 h-5 pb-[0.83px] justify-center items-center flex"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="grow shrink basis-0 h-10 rounded-lg justify-start items-start flex">
                                <div class="grow shrink basis-0 h-10 px-4 py-2.5 bg-pink-900 rounded-lg shadow border border-pink-900 justify-center items-center gap-2 flex">
                                    <div class="text-white text-sm font-semibold font-['Source Sans Pro'] leading-tight">Buy Now</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="h-[442px] rounded-[15px] shadow flex-col justify-start items-start inline-flex">
                    <div class="w-[301px] h-[300px] bg-white justify-center items-center inline-flex">
                        <img class="w-[301px] h-[375px]" src="https://via.placeholder.com/301x375" />
                    </div>
                    <div class="self-stretch h-[142px] p-5 bg-white rounded-bl-[15px] rounded-br-[15px] flex-col justify-start items-start gap-2.5 flex">
                        <div class="w-[261px] text-black text-base font-semibold font-['Source Sans Pro'] leading-normal">Women’s Sports Tee, Red</div>
                        <div class="w-[261px] text-pink-900 text-xs font-semibold font-['Source Sans Pro'] leading-[18px]">KSH 2000</div>
                        <div class="self-stretch justify-start items-start gap-2.5 inline-flex">
                            <div class="justify-start items-start flex">
                                <div class="w-10 h-10 rounded-lg justify-start items-start flex">
                                    <div class="grow shrink basis-0 self-stretch p-2.5 bg-yellow-50 rounded-lg shadow border border-yellow-50 justify-center items-center gap-2 flex">
                                        <div class="w-5 h-5 pb-[0.83px] justify-center items-center flex"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="grow shrink basis-0 h-10 rounded-lg justify-start items-start flex">
                                <div class="grow shrink basis-0 h-10 px-4 py-2.5 bg-pink-900 rounded-lg shadow border border-pink-900 justify-center items-center gap-2 flex">
                                    <div class="text-white text-sm font-semibold font-['Source Sans Pro'] leading-tight">Buy Now</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="self-stretch justify-start items-start gap-5 inline-flex">
                <div class="h-[442px] rounded-[15px] shadow flex-col justify-start items-start inline-flex">
                    <div class="w-[301px] h-[300px] bg-white justify-center items-center inline-flex">
                        <img class="w-[301px] h-[375px]" src="https://via.placeholder.com/301x375" />
                    </div>
                    <div class="self-stretch h-[142px] p-5 bg-white rounded-bl-[15px] rounded-br-[15px] flex-col justify-start items-start gap-2.5 flex">
                        <div class="w-[261px] text-black text-base font-semibold font-['Source Sans Pro'] leading-normal">Women’s Sports Tee, Red</div>
                        <div class="w-[261px] text-pink-900 text-xs font-semibold font-['Source Sans Pro'] leading-[18px]">KSH 2000</div>
                        <div class="self-stretch justify-start items-start gap-2.5 inline-flex">
                            <div class="justify-start items-start flex">
                                <div class="w-10 h-10 rounded-lg justify-start items-start flex">
                                    <div class="grow shrink basis-0 self-stretch p-2.5 bg-yellow-50 rounded-lg shadow border border-yellow-50 justify-center items-center gap-2 flex">
                                        <div class="w-5 h-5 pb-[0.83px] justify-center items-center flex"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="grow shrink basis-0 h-10 rounded-lg justify-start items-start flex">
                                <div class="grow shrink basis-0 h-10 px-4 py-2.5 bg-pink-900 rounded-lg shadow border border-pink-900 justify-center items-center gap-2 flex">
                                    <div class="text-white text-sm font-semibold font-['Source Sans Pro'] leading-tight">Buy Now</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="h-[442px] rounded-[15px] shadow flex-col justify-start items-start inline-flex">
                    <div class="w-[301px] h-[300px] bg-white justify-center items-center inline-flex">
                        <img class="w-[301px] h-[375px]" src="https://via.placeholder.com/301x375" />
                    </div>
                    <div class="self-stretch h-[142px] p-5 bg-white rounded-bl-[15px] rounded-br-[15px] flex-col justify-start items-start gap-2.5 flex">
                        <div class="w-[261px] text-black text-base font-semibold font-['Source Sans Pro'] leading-normal">Women’s Sports Tee, Red</div>
                        <div class="w-[261px] text-pink-900 text-xs font-semibold font-['Source Sans Pro'] leading-[18px]">KSH 2000</div>
                        <div class="self-stretch justify-start items-start gap-2.5 inline-flex">
                            <div class="justify-start items-start flex">
                                <div class="w-10 h-10 rounded-lg justify-start items-start flex">
                                    <div class="grow shrink basis-0 self-stretch p-2.5 bg-yellow-50 rounded-lg shadow border border-yellow-50 justify-center items-center gap-2 flex">
                                        <div class="w-5 h-5 pb-[0.83px] justify-center items-center flex"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="grow shrink basis-0 h-10 rounded-lg justify-start items-start flex">
                                <div class="grow shrink basis-0 h-10 px-4 py-2.5 bg-pink-900 rounded-lg shadow border border-pink-900 justify-center items-center gap-2 flex">
                                    <div class="text-white text-sm font-semibold font-['Source Sans Pro'] leading-tight">Buy Now</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="h-[442px] rounded-[15px] shadow flex-col justify-start items-start inline-flex">
                    <div class="w-[301px] h-[300px] bg-white justify-center items-center inline-flex">
                        <img class="w-[301px] h-[375px]" src="https://via.placeholder.com/301x375" />
                    </div>
                    <div class="self-stretch h-[142px] p-5 bg-white rounded-bl-[15px] rounded-br-[15px] flex-col justify-start items-start gap-2.5 flex">
                        <div class="w-[261px] text-black text-base font-semibold font-['Source Sans Pro'] leading-normal">Women’s Sports Tee, Red</div>
                        <div class="w-[261px] text-pink-900 text-xs font-semibold font-['Source Sans Pro'] leading-[18px]">KSH 2000</div>
                        <div class="self-stretch justify-start items-start gap-2.5 inline-flex">
                            <div class="justify-start items-start flex">
                                <div class="w-10 h-10 rounded-lg justify-start items-start flex">
                                    <div class="grow shrink basis-0 self-stretch p-2.5 bg-yellow-50 rounded-lg shadow border border-yellow-50 justify-center items-center gap-2 flex">
                                        <div class="w-5 h-5 pb-[0.83px] justify-center items-center flex"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="grow shrink basis-0 h-10 rounded-lg justify-start items-start flex">
                                <div class="grow shrink basis-0 h-10 px-4 py-2.5 bg-pink-900 rounded-lg shadow border border-pink-900 justify-center items-center gap-2 flex">
                                    <div class="text-white text-sm font-semibold font-['Source Sans Pro'] leading-tight">Buy Now</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="h-[442px] rounded-[15px] shadow flex-col justify-start items-start inline-flex">
                    <div class="w-[301px] h-[300px] bg-white justify-center items-center inline-flex">
                        <img class="w-[301px] h-[375px]" src="https://via.placeholder.com/301x375" />
                    </div>
                    <div class="self-stretch h-[142px] p-5 bg-white rounded-bl-[15px] rounded-br-[15px] flex-col justify-start items-start gap-2.5 flex">
                        <div class="w-[261px] text-black text-base font-semibold font-['Source Sans Pro'] leading-normal">Women’s Sports Tee, Red</div>
                        <div class="w-[261px] text-pink-900 text-xs font-semibold font-['Source Sans Pro'] leading-[18px]">KSH 2000</div>
                        <div class="self-stretch justify-start items-start gap-2.5 inline-flex">
                            <div class="justify-start items-start flex">
                                <div class="w-10 h-10 rounded-lg justify-start items-start flex">
                                    <div class="grow shrink basis-0 self-stretch p-2.5 bg-yellow-50 rounded-lg shadow border border-yellow-50 justify-center items-center gap-2 flex">
                                        <div class="w-5 h-5 pb-[0.83px] justify-center items-center flex"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="grow shrink basis-0 h-10 rounded-lg justify-start items-start flex">
                                <div class="grow shrink basis-0 h-10 px-4 py-2.5 bg-pink-900 rounded-lg shadow border border-pink-900 justify-center items-center gap-2 flex">
                                    <div class="text-white text-sm font-semibold font-['Source Sans Pro'] leading-tight">Buy Now</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="self-stretch justify-start items-start gap-5 inline-flex">
                <div class="h-[442px] rounded-[15px] shadow flex-col justify-start items-start inline-flex">
                    <div class="w-[301px] h-[300px] bg-white justify-center items-center inline-flex">
                        <img class="w-[301px] h-[375px]" src="https://via.placeholder.com/301x375" />
                    </div>
                    <div class="self-stretch h-[142px] p-5 bg-white rounded-bl-[15px] rounded-br-[15px] flex-col justify-start items-start gap-2.5 flex">
                        <div class="w-[261px] text-black text-base font-semibold font-['Source Sans Pro'] leading-normal">Women’s Sports Tee, Red</div>
                        <div class="w-[261px] text-pink-900 text-xs font-semibold font-['Source Sans Pro'] leading-[18px]">KSH 2000</div>
                        <div class="self-stretch justify-start items-start gap-2.5 inline-flex">
                            <div class="justify-start items-start flex">
                                <div class="w-10 h-10 rounded-lg justify-start items-start flex">
                                    <div class="grow shrink basis-0 self-stretch p-2.5 bg-yellow-50 rounded-lg shadow border border-yellow-50 justify-center items-center gap-2 flex">
                                        <div class="w-5 h-5 pb-[0.83px] justify-center items-center flex"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="grow shrink basis-0 h-10 rounded-lg justify-start items-start flex">
                                <div class="grow shrink basis-0 h-10 px-4 py-2.5 bg-pink-900 rounded-lg shadow border border-pink-900 justify-center items-center gap-2 flex">
                                    <div class="text-white text-sm font-semibold font-['Source Sans Pro'] leading-tight">Buy Now</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="h-[442px] rounded-[15px] shadow flex-col justify-start items-start inline-flex">
                    <div class="w-[301px] h-[300px] bg-white justify-center items-center inline-flex">
                        <img class="w-[301px] h-[375px]" src="https://via.placeholder.com/301x375" />
                    </div>
                    <div class="self-stretch h-[142px] p-5 bg-white rounded-bl-[15px] rounded-br-[15px] flex-col justify-start items-start gap-2.5 flex">
                        <div class="w-[261px] text-black text-base font-semibold font-['Source Sans Pro'] leading-normal">Women’s Sports Tee, Red</div>
                        <div class="w-[261px] text-pink-900 text-xs font-semibold font-['Source Sans Pro'] leading-[18px]">KSH 2000</div>
                        <div class="self-stretch justify-start items-start gap-2.5 inline-flex">
                            <div class="justify-start items-start flex">
                                <div class="w-10 h-10 rounded-lg justify-start items-start flex">
                                    <div class="grow shrink basis-0 self-stretch p-2.5 bg-yellow-50 rounded-lg shadow border border-yellow-50 justify-center items-center gap-2 flex">
                                        <div class="w-5 h-5 pb-[0.83px] justify-center items-center flex"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="grow shrink basis-0 h-10 rounded-lg justify-start items-start flex">
                                <div class="grow shrink basis-0 h-10 px-4 py-2.5 bg-pink-900 rounded-lg shadow border border-pink-900 justify-center items-center gap-2 flex">
                                    <div class="text-white text-sm font-semibold font-['Source Sans Pro'] leading-tight">Buy Now</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="h-[442px] rounded-[15px] shadow flex-col justify-start items-start inline-flex">
                    <div class="w-[301px] h-[300px] bg-white justify-center items-center inline-flex">
                        <img class="w-[301px] h-[375px]" src="https://via.placeholder.com/301x375" />
                    </div>
                    <div class="self-stretch h-[142px] p-5 bg-white rounded-bl-[15px] rounded-br-[15px] flex-col justify-start items-start gap-2.5 flex">
                        <div class="w-[261px] text-black text-base font-semibold font-['Source Sans Pro'] leading-normal">Women’s Sports Tee, Red</div>
                        <div class="w-[261px] text-pink-900 text-xs font-semibold font-['Source Sans Pro'] leading-[18px]">KSH 2000</div>
                        <div class="self-stretch justify-start items-start gap-2.5 inline-flex">
                            <div class="justify-start items-start flex">
                                <div class="w-10 h-10 rounded-lg justify-start items-start flex">
                                    <div class="grow shrink basis-0 self-stretch p-2.5 bg-yellow-50 rounded-lg shadow border border-yellow-50 justify-center items-center gap-2 flex">
                                        <div class="w-5 h-5 pb-[0.83px] justify-center items-center flex"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="grow shrink basis-0 h-10 rounded-lg justify-start items-start flex">
                                <div class="grow shrink basis-0 h-10 px-4 py-2.5 bg-pink-900 rounded-lg shadow border border-pink-900 justify-center items-center gap-2 flex">
                                    <div class="text-white text-sm font-semibold font-['Source Sans Pro'] leading-tight">Buy Now</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="h-[442px] rounded-[15px] shadow flex-col justify-start items-start inline-flex">
                    <div class="w-[301px] h-[300px] bg-white justify-center items-center inline-flex">
                        <img class="w-[301px] h-[375px]" src="https://via.placeholder.com/301x375" />
                    </div>
                    <div class="self-stretch h-[142px] p-5 bg-white rounded-bl-[15px] rounded-br-[15px] flex-col justify-start items-start gap-2.5 flex">
                        <div class="w-[261px] text-black text-base font-semibold font-['Source Sans Pro'] leading-normal">Women’s Sports Tee, Red</div>
                        <div class="w-[261px] text-pink-900 text-xs font-semibold font-['Source Sans Pro'] leading-[18px]">KSH 2000</div>
                        <div class="self-stretch justify-start items-start gap-2.5 inline-flex">
                            <div class="justify-start items-start flex">
                                <div class="w-10 h-10 rounded-lg justify-start items-start flex">
                                    <div class="grow shrink basis-0 self-stretch p-2.5 bg-yellow-50 rounded-lg shadow border border-yellow-50 justify-center items-center gap-2 flex">
                                        <div class="w-5 h-5 pb-[0.83px] justify-center items-center flex"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="grow shrink basis-0 h-10 rounded-lg justify-start items-start flex">
                                <div class="grow shrink basis-0 h-10 px-4 py-2.5 bg-pink-900 rounded-lg shadow border border-pink-900 justify-center items-center gap-2 flex">
                                    <div class="text-white text-sm font-semibold font-['Source Sans Pro'] leading-tight">Buy Now</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="self-stretch p-2.5 bg-rose-50 rounded-[10px] justify-center items-center gap-2.5 inline-flex">
            <div class="text-pink-800 text-sm font-semibold font-['Source Sans Pro'] leading-tight">Load More</div>
        </div>
    </div>
    <div class="w-[1440px] h-[331px] px-[90px] bg-pink-900 justify-center items-center gap-[30px] inline-flex">
        <div class="grow shrink basis-0 py-[30px] flex-col justify-center items-start gap-5 inline-flex">
            <div class="w-[30px] h-[30px] relative">
                <div class="w-[30px] h-[30px] left-0 top-0 absolute rounded-lg"></div>
            </div>
            <div class="flex-col justify-end items-center gap-2.5 flex">
                <div class="w-[508px]"><span style="text-white text-3xl font-medium font-['Noto Serif'] leading-[38px]">Are you an </span><span style="text-amber-500 text-3xl font-bold font-['Noto Serif'] leading-[38px]">influencer</span><span style="text-white text-3xl font-medium font-['Noto Serif'] leading-[38px]"> or a </span><span style="text-amber-500 text-3xl font-bold font-['Noto Serif'] leading-[38px]">merchant</span><span style="text-white text-3xl font-medium font-['Noto Serif'] leading-[38px]">?</span></div>
                <div class="w-[508px] text-white text-sm font-normal font-['Source Sans Pro'] leading-tight">TWIVA has an app especially for you! Download the Twiva mobile app today:</div>
            </div>
            <div class="justify-start items-start gap-[15px] inline-flex">
                <img class="w-[153px] h-[46px]" src="https://via.placeholder.com/153x46" />
                <div class="w-[138px] h-[46px] justify-center items-center flex">
                    <div class="w-[138px] h-[46px] relative">
                        <div class="w-[138px] h-[46px] left-0 top-0 absolute">
                            <div class="w-[138px] h-[46px] left-0 top-0 absolute">
                            </div>
                            <div class="w-[114.76px] h-[28.49px] left-[11.50px] top-[10.03px] absolute">
                                <div class="w-[20.43px] h-[25.04px] left-0 top-0 absolute">
                                </div>
                                <div class="w-[86.56px] h-[17.96px] left-[28.20px] top-[10.52px] absolute">
                                </div>
                            </div>
                        </div>
                        <div class="w-[79.74px] h-[7.31px] left-[41.14px] top-[9.70px] absolute">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <img class="w-[476px] h-[331px]" src="https://via.placeholder.com/476x331" />
        <img class="w-[593px] h-[395px]" src="https://via.placeholder.com/593x395" />
    </div>
    <div class="w-20 h-20 relative shadow">
        <div class="w-20 h-20 left-0 top-0 absolute bg-rose-100 rounded-full border-8 border-stone-300/opacity-50"></div>
        <div class="w-[38.29px] h-[38.29px] left-[21.14px] top-[10.86px] absolute"></div>
    </div>
    <div class="h-[554px] px-[90px] pt-16 pb-12 bg-neutral-50 flex-col justify-start items-center gap-[30px] flex">
        <div class="self-stretch h-[394px] flex-col justify-start items-start gap-5 flex">
            <div class="self-stretch justify-between items-center inline-flex">
                <div class="w-[123.55px] h-10 pr-[0.24px] justify-center items-center gap-[10.12px] flex">
                    <div class="w-10 h-10 relative flex-col justify-start items-start flex">
                        <div class="w-10 h-10 relative rounded-lg"></div>
                    </div>
                    <img class="w-[73.19px] h-[11.79px]" src="https://via.placeholder.com/73x12" />
                </div>
                <div class="p-0.5 justify-end items-center gap-[5px] flex">
                    <div class="w-[30px] h-[30px] relative"></div>
                    <div class="text-center text-pink-900 text-xl font-semibold font-['Source Sans Pro'] leading-[30px]">Contact Us</div>
                </div>
            </div>
            <div class="self-stretch justify-start items-start gap-8 inline-flex">
                <div class="grow shrink basis-0 flex-col justify-start items-start gap-3 inline-flex">
                    <div class="self-stretch justify-start items-center gap-2 inline-flex">
                        <div class="justify-start items-start flex">
                            <div class="justify-center items-center gap-2 flex">
                                <div class="text-pink-700 text-base font-semibold font-['Source Sans Pro'] leading-normal">Our Services</div>
                            </div>
                        </div>
                    </div>
                    <div class="self-stretch justify-start items-center gap-2 inline-flex">
                        <div class="justify-start items-start flex">
                            <div class="justify-center items-center gap-2 flex">
                                <div class="text-neutral-400 text-base font-semibold font-['Source Sans Pro'] leading-normal">Social commerce</div>
                            </div>
                        </div>
                    </div>
                    <div class="self-stretch justify-start items-center gap-2 inline-flex">
                        <div class="justify-start items-start flex">
                            <div class="justify-center items-center gap-2 flex">
                                <div class="text-neutral-400 text-base font-semibold font-['Source Sans Pro'] leading-normal">Influencer campaign management
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="self-stretch justify-start items-center gap-2 inline-flex">
                        <div class="grow shrink basis-0 h-6 justify-start items-start flex">
                            <div class="justify-center items-center gap-2 flex">
                                <div class="text-neutral-400 text-base font-semibold font-['Source Sans Pro'] leading-normal">Twitter Trends</div>
                            </div>
                        </div>
                    </div>
                    <div class="self-stretch justify-start items-center gap-2 inline-flex">
                        <div class="grow shrink basis-0 h-6 justify-start items-start flex">
                            <div class="justify-center items-center gap-2 flex">
                                <div class="text-neutral-400 text-base font-semibold font-['Source Sans Pro'] leading-normal">WhatsApp Seeding</div>
                            </div>
                        </div>
                    </div>
                    <div class="self-stretch justify-start items-center gap-2 inline-flex">
                        <div class="grow shrink basis-0 h-6 justify-start items-start flex">
                            <div class="justify-center items-center gap-2 flex">
                                <div class="text-neutral-400 text-base font-semibold font-['Source Sans Pro'] leading-normal">Social Listening</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="grow shrink basis-0 flex-col justify-start items-start gap-8 inline-flex">
                    <div class="h-[172px] flex-col justify-start items-start gap-4 flex">
                        <div class="self-stretch justify-start items-center gap-2 inline-flex">
                            <div class="justify-start items-start flex">
                                <div class="justify-center items-center gap-2 flex">
                                    <div class="text-pink-700 text-base font-semibold font-['Source Sans Pro'] leading-normal">About Us</div>
                                </div>
                            </div>
                        </div>
                        <div class="self-stretch h-[132px] flex-col justify-start items-start gap-3 flex">
                            <div class="self-stretch justify-start items-center gap-2 inline-flex">
                                <div class="justify-start items-start flex">
                                    <div class="justify-center items-center gap-2 flex">
                                        <div class="text-neutral-400 text-base font-semibold font-['Source Sans Pro'] leading-normal">About Twiva</div>
                                    </div>
                                </div>
                            </div>
                            <div class="self-stretch justify-start items-center gap-2 inline-flex">
                                <div class="justify-start items-start flex">
                                    <div class="justify-center items-center gap-2 flex">
                                        <div class="text-neutral-400 text-base font-semibold font-['Source Sans Pro'] leading-normal">Join as a Brand</div>
                                    </div>
                                </div>
                            </div>
                            <div class="self-stretch justify-start items-center gap-2 inline-flex">
                                <div class="justify-start items-start flex">
                                    <div class="justify-center items-center gap-2 flex">
                                        <div class="text-neutral-400 text-base font-semibold font-['Source Sans Pro'] leading-normal">Join as an influencer</div>
                                    </div>
                                </div>
                            </div>
                            <div class="self-stretch justify-start items-center gap-2 inline-flex">
                                <div class="grow shrink basis-0 h-6 justify-start items-start flex">
                                    <div class="justify-center items-center gap-2 flex">
                                        <div class="text-neutral-400 text-base font-semibold font-['Source Sans Pro'] leading-normal">Blog</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="grow shrink basis-0 flex-col justify-start items-start gap-4 inline-flex">
                    <div class="self-stretch justify-start items-center gap-2 inline-flex">
                        <div class="justify-start items-start flex">
                            <div class="justify-center items-center gap-2 flex">
                                <div class="text-pink-700 text-base font-semibold font-['Source Sans Pro'] leading-normal">Support and Help</div>
                            </div>
                        </div>
                    </div>
                    <div class="self-stretch h-[168px] flex-col justify-start items-start gap-3 flex">
                        <div class="self-stretch justify-start items-center gap-2 inline-flex">
                            <div class="grow shrink basis-0 h-6 justify-start items-start flex">
                                <div class="justify-center items-center gap-2 flex">
                                    <div class="text-neutral-400 text-base font-semibold font-['Source Sans Pro'] leading-normal">FAQs</div>
                                </div>
                            </div>
                        </div>
                        <div class="self-stretch justify-start items-center gap-2 inline-flex">
                            <div class="justify-start items-start flex">
                                <div class="justify-center items-center gap-2 flex">
                                    <div class="text-neutral-400 text-base font-semibold font-['Source Sans Pro'] leading-normal">Privacy Policy</div>
                                </div>
                            </div>
                        </div>
                        <div class="self-stretch justify-start items-center gap-2 inline-flex">
                            <div class="justify-start items-start flex">
                                <div class="justify-center items-center gap-2 flex">
                                    <div class="text-neutral-400 text-base font-semibold font-['Source Sans Pro'] leading-normal">Terms & Conditions</div>
                                </div>
                            </div>
                        </div>
                        <div class="self-stretch justify-start items-center gap-2 inline-flex">
                            <div class="justify-start items-start flex">
                                <div class="justify-center items-center gap-2 flex">
                                    <div class="text-neutral-400 text-base font-semibold font-['Source Sans Pro'] leading-normal">Banned Products</div>
                                </div>
                            </div>
                        </div>
                        <div class="self-stretch justify-start items-center gap-2 inline-flex">
                            <div class="grow shrink basis-0 h-6 justify-start items-start flex">
                                <div class="justify-center items-center gap-2 flex">
                                    <div class="text-neutral-400 text-base font-semibold font-['Source Sans Pro'] leading-normal">Training Material</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="grow shrink basis-0 flex-col justify-start items-start gap-4 inline-flex">
                    <div class="self-stretch justify-start items-center gap-2 inline-flex">
                        <div class="justify-start items-start flex">
                            <div class="justify-center items-center gap-2 flex">
                                <div class="text-pink-700 text-base font-semibold font-['Source Sans Pro'] leading-normal">Contact Us</div>
                            </div>
                        </div>
                    </div>
                    <div class="self-stretch h-36 flex-col justify-start items-start gap-3 flex">
                        <div class="self-stretch justify-start items-center gap-2 inline-flex">
                            <div class="justify-start items-start flex">
                                <div class="justify-center items-center gap-2 flex">
                                    <div class="text-neutral-400 text-base font-semibold font-['Source Sans Pro'] leading-normal">+254708088114</div>
                                </div>
                            </div>
                        </div>
                        <div class="self-stretch justify-start items-center gap-2 inline-flex">
                            <div class="justify-start items-start flex">
                                <div class="justify-center items-center gap-2 flex">
                                    <div class="text-neutral-400 text-base font-semibold font-['Source Sans Pro'] leading-normal">info@twiva.co.ke</div>
                                </div>
                            </div>
                        </div>
                        <div class="self-stretch justify-start items-center gap-2 inline-flex">
                            <div class="grow shrink basis-0 h-[72px] justify-start items-start flex">
                                <div class="grow shrink basis-0 h-[72px] justify-center items-center gap-2 flex">
                                    <div class="grow shrink basis-0 text-neutral-400 text-base font-semibold font-['Source Sans Pro'] leading-normal">Kose Heights, Argwings Kodhek Road, Hurlingham, Nairobi, Kenya Office No. W.3.2B on 3rd Floor</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="self-stretch h-[134px] flex-col justify-start items-start gap-2.5 flex">
                        <div class="text-pink-700 text-base font-semibold font-['Source Sans Pro'] leading-normal">Follow us</div>
                        <div class="self-stretch justify-start items-start gap-5 inline-flex">
                            <div class="w-10 h-10 relative">
                                <div class="w-[22px] h-[22px] pl-[6.42px] pr-[5.50px] py-[1.83px] left-[9px] top-[9px] absolute justify-center items-center inline-flex"></div>
                            </div>
                            <div class="w-10 h-10 relative">
                                <div class="w-[22px] h-[22px] left-[9px] top-[9px] absolute"></div>
                            </div>
                            <div class="w-10 h-10 relative">
                                <div class="w-[22px] h-[22px] p-[2.20px] left-[9px] top-[9px] absolute justify-center items-center inline-flex">
                                    <div class="w-[17.60px] h-[17.60px] justify-center items-center inline-flex"></div>
                                </div>
                            </div>
                            <div class="w-10 h-10 relative">
                                <div class="w-[22px] h-[22px] pl-[2.06px] pr-[0.69px] pt-[3.44px] pb-[2.75px] left-[9px] top-[9px] absolute justify-center items-center inline-flex"></div>
                            </div>
                            <div class="w-10 h-10 relative">
                                <div class="w-[22px] h-[22px] px-[0.92px] pt-[3.67px] pb-[4.16px] left-[9px] top-[9px] absolute justify-center items-center inline-flex">
                                    <img class="w-[20.17px] h-[14.17px]" src="https://via.placeholder.com/20x14" />
                                </div>
                            </div>
                            <div class="w-10 h-10 relative">
                                <div class="w-[22px] h-[22px] left-[9px] top-[9px] absolute"></div>
                            </div>
                            <div class="w-10 h-10 relative">
                                <div class="w-[22px] h-[22px] pl-[2.75px] pr-[3.44px] pt-[0.69px] pb-[1.38px] left-[9px] top-[9px] absolute justify-center items-center inline-flex"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="self-stretch justify-start items-center gap-[30px] inline-flex">
            <div class="grow shrink basis-0 text-neutral-400 text-xs font-semibold font-['Source Sans Pro'] leading-[18px]">© 2024 Twiva. All rights reserved.</div>
        </div>
    </div>
</div>
<script type="text/javascript" src="{{ mix('front/js/app.js') }}"></script>
@stack('scripts')
</body>
</html>
