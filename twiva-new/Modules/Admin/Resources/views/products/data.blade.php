@foreach ($products as $product)
    <tr>
        <td><img src="{{ config('app.media_url') . $product->main_image_path }}"></td>
        <td>{{ $product->id }}</td>
        <td>{{ \Illuminate\Support\Str::limit($product->name, 30) }}</td>
        <td>{{ $product->brand }}</td>
        <td>
            <a
                href="{{ route('admin.business.show', ['business' => $product->user?->id ?? 0]) }}">{{ ucwords($product->user?->name) ?? '' }}</a>
        </td>
        <td>KSH {{ number_format($product->price) }} </td>
        <td>KSH {{ number_format($product->retail_price) }} </td>
        <td>{{ $product->total_sale }} QTY</td>
        <td>{{ $product->updated_at->format('d/m/Y') }}</td>
        <td class="t-rating">{{ $product->productReviewsAndRatings->avg('rating') ?? 0 }} <img
                src="{{ asset('admin/images/icons/star.svg') }}"></td>
        <?php if ($product->stock >= 1) { ?>
        <td class="eshop-active">
            <p>In Stock</p>
        </td>
        <?php } else { ?>
        <td class="eshop-failed">
            <p>Out Of Stock</p>
        </td>
        <?php } ?>
        @if ($product->status == 1)
            <td class="eshop-status-{{ $product->id }} eshop-active">
                <p>Active</p>
            </td>
        @else
            <td class="eshop-status-{{ $product->id }} eshop-failed">
                <p>InActive</p>
            </td>
        @endif
        <td>
            <label class="switch">
                <input data-id="{{ $product->id }}" class="toggle-trending" type="checkbox"
                    @checked($product->is_trending)>
                <span class="slider round"></span>
            </label>
        </td>
        <td class="t-action">
            <a href="{{ route('admin.products.show', $product->id) }}"><img
                    src="{{ asset('admin/images/icons/Frame7158.svg') }}" alt=""></a>
            <!-- Checked switch -->
            <label class="switch">
                <input data-id="{{ $product->id }}" data-user_id="{{ $product->user->id ?? '0' }}"
                    class="toggle-status" type="checkbox" <?php if ($product->status == 1) { ?> checked <?php } ?>>
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
                        {{ $products->links('admin::layouts.pagination') }}
                    </div>
                </div>
            </div>
        </div>
    </td>
</tr>
