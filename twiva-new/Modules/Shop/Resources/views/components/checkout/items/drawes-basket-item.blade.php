<div class="flex justify-start items-center py-[5px]">
    <div class="md:py-1.5">
        <div class="w-[50px] h-[50px] bg-[#f5f5f5] bg-center bg-no-repeat bg-contain" style="background-image: url('{{ asset('images/product.png') }}')"></div>
    </div>
    <div class="sm-down:flex justify-between items-center sm-down:w-full ml-2.5">
        <h5 class="text-base text-black-200 font-semibold md:mb-2.5">Women’s Sports Tee, Red</h5>
        <div class="flex justify-start items-center sm-down:hidden">
            <x-partials.counter-basket />
            <button class="ml-2.5">
                <i class="w-5 h-5 text-error-800" data-feather="trash-2"></i>
            </button>
        </div>
        <button class="md:hidden">
            <i class="w-5 h-5 text-gray-900 md:text-error-800" data-feather="trash-2"></i>
        </button>
    </div>
</div>
