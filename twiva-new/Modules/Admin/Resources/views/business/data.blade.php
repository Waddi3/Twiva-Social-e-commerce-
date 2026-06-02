@foreach ($users as $user)
    <tr>
        <td>{{ $user->id }}</td>
        <td>{{ $user->name ?? "--"}}</td>
        <td>{{ $user->businessDetails->contact_name ?? "--"}}</td>
        <td>{{ $user->mobile ?? $user->businessDetails->contact_number}}</td>
        <td>{{ $user->email ?? "--"}}</td>
        <td>
        {{$user->created_at?->format('d/m/Y') ??'-'}}

        <td class="eshop-link">
            <a
                href="{{ $user?->shop?->domain ? route('shop.product.index', ['account' => $user?->shop?->domain]) : '' }}">
                {{ $user->shop->shop_name ?? '--' }}
            </a>
        </td>
        <td class="product-view">{{ count($user->products) }} @if(count($user->products) >= 1)
                | <a href="{{ route('admin.business.products', $user->id) }}">View Products</a>
            @endif </td>
        <td>{{ $user->businessTotalSales['count'] }} (KSH {{ number_format($user->businessTotalSales['amount']) }})</td>
        <td>KSH {{ number_format($user->businessTotalSales['earning']) }}</td>
        <td>KSH {{ number_format($user->redeem_price) }}</td>

        @if($user->deleted_at)
            <td class="eshop-status-{{$user->id}} eshop-failed">
                <p>InActive</p>
            </td>
        @else
            <td class="eshop-status-{{$user->id}} eshop-active">
                <p>Active</p>
            </td>
            {{--@endif--}}
        @endif
        <td class="doc-link">
            <a href="{{ route('admin.influencer.connected', $user->id) }}">{{ $user->influencerCount ?? 0 }}
                Influencer</a>
        </td>
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
        <td>
            <button class="bulk-upload purple-btn m-0" data-id="{{$user->id}}">Bulk upload</button>
        </td>
        <td>
            <label class="switch">
                <input data-id="{{$user->id}}" class="toggle-waive-prompt"
                       type="checkbox" {{ $user->waive_prompt ? 'checked' : '' }}>
                <span class="slider round"></span>
            </label>
        </td>
        <td>
            <label class="switch">
                <input data-id="{{$user->id}}" class="toggle-accessing-earnings-prior-to-delivery"
                       type="checkbox" {{ $user->accessing_earnings_prior_to_delivery ? 'checked' : '' }}>
                <span class="slider round"></span>
            </label>
        </td>
        <td class="t-action">
            <div class="d-flex align-items-center">
                <a href="{{ route('admin.business.edit', $user->id) }}"><img
                            src="{{ asset('admin/images/icons/edit-icon.svg') }}" alt=""></a>
                <a href="{{ route('admin.business.show', $user->id) }}"><img
                            src="{{ asset('admin/images/icons/Frame7158.svg') }}" alt=""></a>
                <i data-id="{{$user->id}}" class="fa fa-user btn-login"
                   style="cursor: pointer; margin-right:10px;color: #7b1d42;"></i>
                <label class="switch">
                    <input data-id="{{$user->id}}" class="toggle-status"
                           type="checkbox" {{ !$user->deleted_at ? 'checked' : '' }}>
                    <span class="slider round"></span>
                </label>
            </div>
        </td>

        {{-- <td>
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
                <div class="col-sm-7 p-0">
                    <div class="paging_simple_numbers" id="example_paginate">

                        {{ $users->links('admin::layouts.pagination')}}
                    </div>
                </div>
            </div>
        </div>
    </td>
</tr>
