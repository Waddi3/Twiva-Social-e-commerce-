@php use Modules\Influencer\Entities\Product; @endphp
<div class="flex justify-start items-center mb-2.5 md:mb-[30px]">
    <span class="text-black-100 text-lg font-semibold">Service: </span>
    <div class="ml-8">
        <span
                style="color: {{ Product::shipmentTagColor($product) }}; background-color: {{ Product::shipmentTagBackgroundColor($product) }}; padding: 2px 4px; border-radius: 5px;">{{ Product::shipmentTagText($product) }}</span>
    </div>
</div>
