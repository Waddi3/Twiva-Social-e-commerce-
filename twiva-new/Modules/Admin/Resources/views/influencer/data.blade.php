@foreach ($users as $user)
    <tr>
        <td>{{ $user->id }}</td>
        <td>{{ $user->influencerDetails->name ?? $user->name }}</td>
        <td>{{ $user->mobile }}</td>
        <td>{{ $user->email }}</td>
        <td>{{ $user->created_at ? $user->created_at->format('d/m/y') : '' }}</td>

        <td class="eshop-link">
            <a
                href="{{ $user?->shop?->domain ? route('shop.product.index', ['account' => $user?->shop?->domain]) : '' }}">
                {{ $user->shop->shop_name ?? '--' }}
            </a>
        </td>
        <td class="product-view">
            @if ($user->shop)
                {{ $user->products_count ?? 0 }}  @if($user->products_count >= 1) |
                 <a href="{{ route('admin.influencer.products', $user->id) }}">View Products</a>
                 @endif
            @endif
        </td>

        <td>{{ $user->influencerTotalSales['count'] }} (KSH {{ number_format($user->influencerTotalSales['amount']) }})</td>

        <td>KSH {{ number_format($user->influencerTotalSales['earning']) }}</td>
        <td>KSH {{ number_format($user->redeem_price) }}</td>
        <td>{{ $user->influencerStories->count() ?? 0 }} Stories</td>

        @if ($user->deleted_at)
            <td class="eshop-status-{{ $user->id }} eshop-failed">
                <p>InActive</p>
            </td>
        @else
            <td class="eshop-status-{{ $user->id }} eshop-active">
                <p>Active</p>
            </td>
        @endif
        <td>
            <form action="{{ route('admin.send.credentials') }}" method="post">
                @csrf
                <input type="hidden" name="id" value="{{ $user->id }}">
                <button class="purple-btn m-0">Send Credentials</button>
            </form>
        </td>
        <td><a href="{{ route('admin.change.password', $user->id) }}">
                <button class="purple-btn m-0">Change Password</button>
            </a></td>
        <td class="t-action">
            <a href="{{ route('admin.influencers.edit', $user->id) }}"><img
                    src="{{ asset('admin/images/icons/edit-icon.svg') }}" alt=""></a>
            <a href="{{ route('admin.influencers.show', $user->id) }}"><img
                    src="{{ asset('admin/images/icons/Frame7158.svg') }}" alt=""></a>
            <i data-id="{{ $user->id }}" class="fa fa-user btn-login"
                style="cursor: pointer;margin-right:10px;color: #7b1d42;"></i>

            <!-- Checked switch -->
            <label class="switch">
                <input data-id="{{ $user->id }}" class="toggle-status" type="checkbox"
                    {{ $user->deleted_at ? '' : 'checked' }}>
                <span class="slider round"></span>
            </label>
        </td>
{{--
        <td>
            <span>
                App ID: {{ $user->app_id }}
            </span>
            <br>
            <span>
                App Secret: {{ $user->app_secret }}
            </span>
        </td> --}}
    </tr>
@endforeach
<tr>
    <td colspan="100">
        <div class="pagination">
            <div class="col-sm-5 p-0">
                <!-- <div class="dataTables_info" id="example_info" role="status" aria-live="polite">Showing 1 to 10 of 57 entries</div></div> -->
                <div class="col-sm-7 p-0">
                    <div class="paging_simple_numbers" id="example_paginate">
                        {{ $users->links('admin::layouts.pagination') }}
                    </div>
                </div>
            </div>
        </div>
    </td>
</tr>
