<div x-data="{ openCategories: false, activeCategory: null }"
    class="hidden relative lg:flex flex-row w-full justify-between items-center md:px-[80px] md:py[15px] h-[60px]">
    <!-- Navigation links -->
    <div class="w-full">
        <div class="flex justify-start items-center">
            <div class="Button w-32 h-10 rounded-lg justify-start items-start inline-flex">
                <div
                    class="ButtonBase cursor-pointer px-4 py-2.5 bg-white rounded-lg shadow border border-neutral-200 justify-center items-center gap-2 flex" @click="openCategories = !openCategories">
                    <div class="Menu2 w-5 h-5 px-0.5 py-1 justify-center items-center flex text-primary-700">
                        <i x-show="!openCategories" data-feather="list"></i>
                        <i x-show="openCategories" data-feather="x"></i>
                    </div>
                    <div class="Text text-zinc-600 text-sm font-semibold font-['Source Sans Pro'] leading-tight">
                        <span x-text="openCategories ? 'Close' : 'Categories'"></span>
                    </div>
                </div>
            </div>
            <div class="lg:flex hidden flex-wrap gap-4">
                @foreach ($featuredCategories as $category)
                    <a href="{{route('ecommerce.shop', ['category' => $category->id])}}" class="text-gray-700 hover:text-primary-700 px-4 py-2">
                        {{ $category->name }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <div x-show="openCategories" x-cloak x-transition
        class="absolute top-full left-4 w-full w-max"
        @click.away="openCategories = false" style="z-index: 10;">
        <div class="relative w-max">
            <div class="w-max min-w-[350px] overflow-y-auto bg-white py-4 px-6 max-h-[65vh] shadow-lg">
                <ul>
                    @foreach ($categories['categories'] as $category)
                        <li class="grid grid-cols-12 py-1 left-5">
                            <a href="{{route('ecommerce.shop', ['category' => $category->id])}}" class="col-span-11 text-gray-700 flex justify-between items-center"
                                >
                                {{ $category->name }}
                            </a>
                            @if ($category->subcategories->count() > 0)
                                <a @click.prevent="activeCategory === {{ $category->id }} ? activeCategory = null : activeCategory = {{ $category->id }}" class="cursor-pointer">
                                    <i x-show="activeCategory !== {{ $category->id }}" data-feather="chevron-right"
                                       class="w-4 h-4"></i>
                                    <i x-show="activeCategory === {{ $category->id }}" data-feather="chevron-left"
                                       class="w-4 h-4"></i>
                                </a>
                            @endif
                            @if ($category->subcategories->count() > 0)
                                <div x-show="activeCategory === {{ $category->id }}" x-cloak x-transition.opacity.duration.200
                                    class="absolute top-0 left-full bg-white shadow-lg py-2 px-4 min-w-[175px] w-max overflow-x-visible overflow-y-auto"
                                    {{-- sm:relative --}} {{-- style="margin-left: 488px; margin-top: -25px;" --}}>
                                    <ul>
                                        @foreach ($category->subcategories as $subcategory)
                                            <li class="py-1">
                                                <a href="{{route('ecommerce.shop', ['sub_category' => $subcategory->id])}}" class="text-gray-700">
                                                    {{ $subcategory->name }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>




</nav>
