@php use Modules\Coupon\Entities\Coupon; @endphp
@foreach ($coupons as $coupon)
    <tr>
        <td>{{ $coupon->id }}</td>
        <td>{{ $coupon->name }}</td>
        <td>{{ $coupon->code }}</td>
        <td>{{ $coupon->max_usage }}</td>
        <td>{{ $coupon->all_use }}</td>
        <td>{{ $coupon->type }}</td>
        <td>{{ $coupon->amount }}</td>
        <td>{{ $coupon->format_expiry_at }}</td>
        <td class="eshop-status-{{ $coupon->id }} eshop-failed">
            <label class="switch">
                <input data-id="{{ $coupon->id }}" class="toggle-activation" type="checkbox"
                        {{ $coupon->is_active ? 'checked' : '' }}>
                <span class="slider round"></span>
            </label>
        </td>
        <td class="eshop-status-{{ $coupon->id }} eshop-failed">
            <label class="switch">
                @if ($coupon->type == Coupon::TYPE_FIXED)
                    <input data-id="{{ $coupon->id }}" class="toggle-bypass-the-minimum" type="checkbox"
                            {{ $coupon->bypass_the_minimum ? 'checked' : '' }}>
                    <span class="slider round"></span>
                @endif
            </label>
        </td>
        <td class="t-action">
            <a href="{{ route('admin.coupons.edit', $coupon->id) }}">
                <img src="{{ asset('admin/images/icons/edit-icon.svg') }}" alt="">
            </a>
            <form method="POST" action="{{ route('admin.coupons.destroy', $coupon->id) }}" style="display: inline">
                @csrf
                @method('DELETE')
                <button type="submit" style="background: none; border: none; padding: 0; cursor: pointer;">
                    <img src="{{ asset('admin/images/icons/Delete.svg') }}" alt="Delete">
                </button>
            </form>
        </td>
    </tr>
@endforeach
<tr>
    <td colspan="100">
        <div class="pagination">
            <div class="col-sm-5 p-0">
                <div class="col-sm-7 p-0">
                    <div class="paging_simple_numbers" id="example_paginate">
                        {{ $coupons->appends(request()->input())->links('admin::layouts.pagination') }}
                    </div>
                </div>
            </div>
        </div>
    </td>
</tr>
