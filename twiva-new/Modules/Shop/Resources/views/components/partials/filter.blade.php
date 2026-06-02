<section class="container mx-auto py-14" x-data="{ openFilters: false }">
    <div id="back-to-category" class="text-center text-primary-800 mb-4 h-8">
        <a class="flex flex-row justify-center content-center cursor-pointer text-lg font-semibold text-primary-800 mb-4 hidden" >
            <svg width="14" height="14" class="my-2 mx-4" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M7.58923 0.577085C7.91467 0.902522 7.91467 1.43016 7.58923 1.7556L3.17849 6.16634H12.8333C13.2936 6.16634 13.6666 6.53944 13.6666 6.99967C13.6666 7.45991 13.2936 7.83301 12.8333 7.83301H3.17849L7.58923 12.2438C7.91467 12.5692 7.91467 13.0968 7.58923 13.4223C7.2638 13.7477 6.73616 13.7477 6.41072 13.4223L0.577391 7.58893C0.251954 7.26349 0.251954 6.73586 0.577391 6.41042L6.41072 0.577085C6.73616 0.251649 7.2638 0.251649 7.58923 0.577085Z" fill="#AD1650"/>
            </svg>
            Back to ‘<span></span>’
        </a>
    </div>
    <h2 id="category-title" class="text-3xl font-bold text-center text-primary-800 mb-4">Shop</h2>
    <h3 id="sub-title" class="text-xl font-semibold text-center text-primary-600 mb-4"></h3>
    <h4 class="text-normal text-center text-gray-500 mb-6"><span id="products-count"></span> products</h4>
    <div class="flex lg:hidden items-center justify-center gap-4 px-4 mb-4">
        <select id='category-select' class="select2 flex-1" name="state">
            <option value="" @if(!$selectedCategory) selected @endif>Shop All</option>
            @foreach($categories['categories'] as $category)
                <option value="{{$category->id}}" @if($category->id == $selectedCategory) selected @endif>{{$category->name}}</option>
            @endforeach
        </select>
        <button @click="openFilters = true">
            <i data-feather="filter"></i>
        </button>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <aside :class="openFilters ? '' : 'hidden'" class="fixed top-0 left-0 lg:top-[initial] lg:left-[initial] z-[100] lg:z-[0] bg-white w-screen h-[calc(100vh-50px)] overflow-y-auto lg:w-auto lg:h-auto lg:static lg:flex flex-col rounded-lg border border-gray-300 p-4">
            <header class="flex flex-row justify-between items-center">
                <div class="text-3xl font-semibold text-primary-700">Filter</div>
                <button id="reset" class="text-primary-700 hidden lg:block">Reset</button>
                <div class="lg:hidden px-0.5 py-1 justify-center items-center flex text-gray-700" @click="openFilters = false">
                    <i class="w-10 h-10" data-feather="x"></i>
                </div>
            </header>

            <div class="invisible absolute left-[-9999px] lg:static lg:left-[initial] lg:visible lg:flex mt-12 lg:flex-col">
                <header id="categories-title" class="text-xl font-bold text-primary-700 mb-4">Categories</header>
                <div id='categories-container' class="overflow-y-auto max-h-[500px]" data-category="{{$selectedCategory ? $selectedCategory : 0}}">
                    <ul class="shadow mt-6">
                        <li>
                            <a class="category p-3 flex flex-row items-center {{!$selectedCategory ? 'text-primary-600 bg-primary-50 selected' : 'text-gray-600'}} cursor-pointer" data-id="0">
                                <svg
                                    class="mr-3"
                                    width="14" height="16"
                                    viewBox="0 0 14 16"
                                    fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M2.46683 0.933659C2.59273 0.765788 2.79032 0.666992 3.00016 0.666992H11.0002C11.21 0.666992 11.4076 0.765788 11.5335 0.933659L13.5335 3.60033C13.62 3.71572 13.6668 3.85608 13.6668 4.00033V13.3337C13.6668 13.8641 13.4561 14.3728 13.081 14.7479C12.706 15.1229 12.1973 15.3337 11.6668 15.3337H2.3335C1.80306 15.3337 1.29436 15.1229 0.919282 14.7479C0.54421 14.3728 0.333496 13.8641 0.333496 13.3337V4.00033C0.333496 3.85608 0.380281 3.71572 0.466829 3.60033L2.46683 0.933659ZM3.3335 2.00033L2.3335 3.33366H11.6668L10.6668 2.00033H3.3335ZM12.3335 4.66699H1.66683V13.3337C1.66683 13.5105 1.73707 13.68 1.86209 13.8051C1.98712 13.9301 2.15669 14.0003 2.3335 14.0003H11.6668C11.8436 14.0003 12.0132 13.9301 12.1382 13.8051C12.2633 13.68 12.3335 13.5105 12.3335 13.3337V4.66699ZM4.3335 6.00033C4.70169 6.00033 5.00016 6.2988 5.00016 6.66699C5.00016 7.19742 5.21088 7.70613 5.58595 8.08121C5.96102 8.45628 6.46973 8.66699 7.00016 8.66699C7.5306 8.66699 8.0393 8.45628 8.41438 8.08121C8.78945 7.70613 9.00016 7.19742 9.00016 6.66699C9.00016 6.2988 9.29864 6.00033 9.66683 6.00033C10.035 6.00033 10.3335 6.2988 10.3335 6.66699C10.3335 7.55105 9.98231 8.39889 9.35719 9.02401C8.73206 9.64914 7.88422 10.0003 7.00016 10.0003C6.11611 10.0003 5.26826 9.64914 4.64314 9.02401C4.01802 8.39889 3.66683 7.55105 3.66683 6.66699C3.66683 6.2988 3.96531 6.00033 4.3335 6.00033Z" fill="#222123"/>
                                </svg>
                                Shop All
                            </a>
                        </li>
                        @foreach($categories['categories'] as $category)
                        <li>
                            <a id='category-{{$category->id}}' class="category relative py-2 px-3 flex flex-row items-center cursor-pointer {{$category->id == $selectedCategory ? 'text-primary-600 bg-primary-50 selected' : 'text-gray-600'}}"
                               data-id="{{$category->id}}"
                               data-min="{{$category->min_price ? round($category->min_price) : 0}}"
                               data-max="{{$category->min_price ? round($category->max_price) : 999999}}"
                            >
                                {{$category->name}}
                                <i data-feather="chevron-right" class=" absolute top-3 right-2 w-4 h-4"></i>
                            </a>
                            @if( $category->subcategories->count() )
                                <ul class="sub-category-list hidden">
                                    @foreach($category->subcategories as $subCategory)
                                        <li>
                                            <a id='sub-category-{{$subCategory->id}}' class="sub-category py-2 px-3 flex flex-row items-center cursor-pointer {{$subCategory->id == $selectedSubCategory ? 'text-primary-600 bg-primary-50 selected' : 'text-gray-600'}}" data-id="{{$subCategory->id}}">
                                                {{$subCategory->name}}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div id="deals-container" class="mt-12 flex flex-col">
                <header class="text-xl font-bold text-primary-700">Deals</header>
                <ul class="mt-6">
                    <li class="mb-2">
                        <a class="cursor-pointer cursor-pointer flex flex-row items-center text-gray-600 filter-item" data-value="all">
                            <div class="cursor-pointer flex rounded-md border border-primary-600 bg-primary-50 p-1 h-5 w-5 text-center justify-center items-center mr-2">
                                <svg width="10" height="7" viewBox="0 0 10 7" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M9.35355 0.646447C9.54882 0.841709 9.54882 1.15829 9.35355 1.35355L3.85355 6.85355C3.65829 7.04882 3.34171 7.04882 3.14645 6.85355L0.646447 4.35355C0.451184 4.15829 0.451184 3.84171 0.646447 3.64645C0.841709 3.45118 1.15829 3.45118 1.35355 3.64645L3.5 5.79289L8.64645 0.646447C8.84171 0.451184 9.15829 0.451184 9.35355 0.646447Z" fill="#222123"/>
                                </svg>
                            </div>
                            All
                        </a>
                    </li>
                    <li class="mb-2">
                        <a class="cursor-pointer flex flex-row items-center text-gray-500 filter-item" data-value="discounted">
                            <div class="flex rounded-md border border-gray-300 bg-gray-50 p-1 h-5 w-5 text-center justify-center items-center mr-2">
                                <svg class="hidden" width="10" height="7" viewBox="0 0 10 7" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M9.35355 0.646447C9.54882 0.841709 9.54882 1.15829 9.35355 1.35355L3.85355 6.85355C3.65829 7.04882 3.34171 7.04882 3.14645 6.85355L0.646447 4.35355C0.451184 4.15829 0.451184 3.84171 0.646447 3.64645C0.841709 3.45118 1.15829 3.45118 1.35355 3.64645L3.5 5.79289L8.64645 0.646447C8.84171 0.451184 9.15829 0.451184 9.35355 0.646447Z" fill="#222123"/>
                                </svg>
                            </div>
                            Discounted
                        </a>
                    </li>
                    <li class="mb-2">
                        <a class="cursor-pointer flex flex-row items-center text-gray-500 filter-item" data-value="full-price">
                            <div class="flex rounded-md border border-gray-300 bg-gray-50 p-1 h-5 w-5 text-center justify-center items-center mr-2">
                                <svg class="hidden" width="10" height="7" viewBox="0 0 10 7" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M9.35355 0.646447C9.54882 0.841709 9.54882 1.15829 9.35355 1.35355L3.85355 6.85355C3.65829 7.04882 3.34171 7.04882 3.14645 6.85355L0.646447 4.35355C0.451184 4.15829 0.451184 3.84171 0.646447 3.64645C0.841709 3.45118 1.15829 3.45118 1.35355 3.64645L3.5 5.79289L8.64645 0.646447C8.84171 0.451184 9.15829 0.451184 9.35355 0.646447Z" fill="#222123"/>
                                </svg>
                            </div>
                            Full price
                        </a>
                    </li>
                </ul>
                <input id="deals" class="filter-data" type="hidden" value="all" />
            </div>

            <div id="rating-container" class="mt-12 flex flex-col">
                <header class="text-xl font-bold text-primary-700">Rating</header>
                <ul class="mt-6">
                    <li class="mb-2">
                        <a class="cursor-pointer flex flex-row items-center text-gray-600 filter-item" data-value="all">
                            <div class="flex rounded-md border border-primary-600 bg-primary-50 p-1 h-5 w-5 text-center justify-center items-center mr-2">
                                <svg width="10" height="7" viewBox="0 0 10 7" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M9.35355 0.646447C9.54882 0.841709 9.54882 1.15829 9.35355 1.35355L3.85355 6.85355C3.65829 7.04882 3.34171 7.04882 3.14645 6.85355L0.646447 4.35355C0.451184 4.15829 0.451184 3.84171 0.646447 3.64645C0.841709 3.45118 1.15829 3.45118 1.35355 3.64645L3.5 5.79289L8.64645 0.646447C8.84171 0.451184 9.15829 0.451184 9.35355 0.646447Z" fill="#222123"/>
                                </svg>
                            </div>
                            All
                        </a>
                    </li>
                    <li class="mb-2">
                        <a class="cursor-pointer flex flex-row items-center text-gray-500 filter-item" data-value="2">
                            <div class="flex rounded-md border border-gray-300 bg-gray-50 p-1 h-5 w-5 text-center justify-center items-center mr-2">
                                <svg class="hidden" width="10" height="7" viewBox="0 0 10 7" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M9.35355 0.646447C9.54882 0.841709 9.54882 1.15829 9.35355 1.35355L3.85355 6.85355C3.65829 7.04882 3.34171 7.04882 3.14645 6.85355L0.646447 4.35355C0.451184 4.15829 0.451184 3.84171 0.646447 3.64645C0.841709 3.45118 1.15829 3.45118 1.35355 3.64645L3.5 5.79289L8.64645 0.646447C8.84171 0.451184 9.15829 0.451184 9.35355 0.646447Z" fill="#222123"/>
                                </svg>
                            </div>
                            2.0 and above
                        </a>
                    </li>
                    <li class="mb-2">
                        <a class="cursor-pointer flex flex-row items-center text-gray-500 filter-item" data-value="3">
                            <div class="flex rounded-md border border-gray-300 bg-gray-50 p-1 h-5 w-5 text-center justify-center items-center mr-2">
                                <svg class="hidden" width="10" height="7" viewBox="0 0 10 7" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M9.35355 0.646447C9.54882 0.841709 9.54882 1.15829 9.35355 1.35355L3.85355 6.85355C3.65829 7.04882 3.34171 7.04882 3.14645 6.85355L0.646447 4.35355C0.451184 4.15829 0.451184 3.84171 0.646447 3.64645C0.841709 3.45118 1.15829 3.45118 1.35355 3.64645L3.5 5.79289L8.64645 0.646447C8.84171 0.451184 9.15829 0.451184 9.35355 0.646447Z" fill="#222123"/>
                                </svg>
                            </div>
                            3.0 and above
                        </a>
                    </li>
                    <li class="mb-2">
                        <a class="cursor-pointer flex flex-row items-center text-gray-500 filter-item" data-value="4">
                            <div class="flex rounded-md border border-gray-300 bg-gray-50 p-1 h-5 w-5 text-center justify-center items-center mr-2">
                                <svg class="hidden" width="10" height="7" viewBox="0 0 10 7" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M9.35355 0.646447C9.54882 0.841709 9.54882 1.15829 9.35355 1.35355L3.85355 6.85355C3.65829 7.04882 3.34171 7.04882 3.14645 6.85355L0.646447 4.35355C0.451184 4.15829 0.451184 3.84171 0.646447 3.64645C0.841709 3.45118 1.15829 3.45118 1.35355 3.64645L3.5 5.79289L8.64645 0.646447C8.84171 0.451184 9.15829 0.451184 9.35355 0.646447Z" fill="#222123"/>
                                </svg>
                            </div>
                            4.0 and above
                        </a>
                    </li>
                    <li class="mb-2">
                        <a class="cursor-pointer flex flex-row items-center text-gray-500 filter-item" data-value="5">
                            <div class="flex rounded-md border border-gray-300 bg-gray-50 p-1 h-5 w-5 text-center justify-center items-center mr-2">
                                <svg class="hidden" width="10" height="7" viewBox="0 0 10 7" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M9.35355 0.646447C9.54882 0.841709 9.54882 1.15829 9.35355 1.35355L3.85355 6.85355C3.65829 7.04882 3.34171 7.04882 3.14645 6.85355L0.646447 4.35355C0.451184 4.15829 0.451184 3.84171 0.646447 3.64645C0.841709 3.45118 1.15829 3.45118 1.35355 3.64645L3.5 5.79289L8.64645 0.646447C8.84171 0.451184 9.15829 0.451184 9.35355 0.646447Z" fill="#222123"/>
                                </svg>
                            </div>
                            5.0
                        </a>
                    </li>
                </ul>
                <input id="rating" class="filter-data" type="hidden" value="all" />
            </div>

            <div id="price-container" class="mt-12 flex flex-col">
                <header class="text-xl font-bold text-primary-700">Price Range</header>
                <div class="mt-6">
                    <div class="flex flex-row justify-between mb-4">
                        <span id="min-range" class="text-gray-700">- KSH</span>
                        <span id="max-range" class="text-gray-700">- KSH</span>
                    </div>
                    <div id="price-range"></div>
                    <input id="min-range-input" class="filter-data" type="hidden" value="{{!is_null($categories['min']) ? round($categories['min']) : 0 }}" />
                    <input id="max-range-input" class="filter-data" type="hidden" value="{{!is_null($categories['max']) ? round($categories['max']) : 999999 }}" />
                </div>
            </div>
        </aside>
        <div class="lg:col-span-3">
            <article id="shop-products" class="grid grid-cols-1 lg:grid-cols-3 w-full gap-6">
            </article>
            <div id="loading" class="px-4 mt-6 col-span-3 w-full text-center">
                <div class="loading"></div>
            </div>
            <div class="px-4 mt-6 w-full">
                <button id="products-load-more" class="w-full text-primary-700 bg-primary-50 px-6 py-2 rounded-lg font-semibold hidden">
                    Load more
                </button>
            </div>
        </div>
        <input type="hidden" id="products-count-storage" value="" />
    </div>
    <input id="query" type="hidden" @if(app('request')->has('keyWord')) value="{{app('request')->input('keyWord')}}" @endif />
</section>
@if($selectedCategory)
    @push('scripts')
        <script>
            function openOrScrollToCategory() {
                let $category = $('.category.selected');
                $category.addClass('hidden');
                $category.siblings('ul').removeClass('hidden');


                /*const categoriesContainer = document.getElementById('categories-container');
                const categoryElement = document.getElementById('category-{{--{{$selectedCategory}}--}}');
                console.log('categoryElement.offsetTop: ', categoryElement.offsetTop)
                const categoryPositionWithOffset = categoryElement.offsetTop - 520;
                categoriesContainer.scrollTo({ top: categoryPositionWithOffset, behavior: 'smooth' });*/
            }
            openOrScrollToCategory();
        </script>
    @endpush
@endif
{{--@if($selectedSubCategory)
    @push('scripts')
        <script>
            function scrollToSubCategory() {
                const subCategoriesContainer = document.getElementById('sub-categories-container');
                const subCategoryElement = document.getElementById('sub-category-{{$selectedSubCategory}}');
                // element.scrollIntoView({ behavior: 'smooth', block: 'start' });
                const subCategoryPositionWithOffset = subCategoryElement.offsetTop - 520;
                subCategoriesContainer.scrollTo({ top: subCategoryPositionWithOffset, behavior: 'smooth' });
            }
            scrollToSubCategory();
        </script>
    @endpush
@endif--}}
@push('scripts')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/select2.min.js"></script>
    <script>
        $('#category-select').select2().on('change', function() {
            const selectedId = this.value;

            console.log('select: ', selectedId)
            $('#sub-category-' + selectedId).trigger('click');
        });
    </script>
@endpush
@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/css/select2.min.css">

    <style>
        .select2-container {
            flex: 1;
        }
    </style>
@endpush
