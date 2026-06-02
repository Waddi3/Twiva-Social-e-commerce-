@foreach ($products as $product)
    <tr>

        <td><img src="{{ $product->productMainImage?->image_url }}"></td>
        <td>{{ $product->id }}</td>
        <td>{{ $product->product_title }}</td>
        <td>KSH {{ number_format($product->product_price) }} </td>
        <td>{{ count($product->productSales) }} QTY</td>
        <td>{{ $product->updated_at->format('d/m/Y') }}</td>
        {{-- <td>40 eshop</td> --}}
        <td class="t-rating">{{ $product->productReviewsAndRatings->avg('rating') ?? 0}} <img
                src="{{ asset('admin/images/icons/star.svg') }}"></td>
        @if ($product?->productDetails?->stock >= 1)
            <td class="eshop-active">
                <p>In Stock</p>
            </td>
        @else
            <td class="eshop-failed">
                <p>Out Of Stock</p>
            </td>
        @endif
        @if($product->status == 1)
            <td class="eshop-status-{{$product->id}} eshop-active">
                <p>Active</p>
            </td>
        @endif
        @if($product->status == 0)
            <td class="eshop-status-{{$product->id}} eshop-failed">
                <p>InActive</p>
            </td>
        @endif
        <td class="t-action">
            <a href="{{ route('admin.influencerProduct.show', $product->id) }}"><img
                    src="{{ asset('admin/images/icons/Frame7158.svg') }}" alt=""></a>
            <!-- Checked switch -->
            <label class="switch">
                <input data-id="{{$product->id}}" class="toggle-status" type="checkbox"
                       @if($product->status == 1) checked @endif >
                <span class="slider round"></span>
            </label>
        </td>
    </tr>
@endforeach
<tr>
    <td colspan="100">
        <div class="pagination">
            <div class="col-sm-5 p-0">
                <!-- <div class="dataTables_info" id="example_info" role="status" aria-live="polite">Showing 1 to 10 of 57 entries</div></div> -->
                <div class="col-sm-7 p-0">
                    <div class="paging_simple_numbers" id="example_paginate">
                        {{ $products->links('admin::layouts.pagination')}}
                    </div>
                </div>
            </div>
        </div>
    </td>
</tr>
