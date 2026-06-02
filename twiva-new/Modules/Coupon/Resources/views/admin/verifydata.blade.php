@foreach ($users as $user)
    <tr>
        <td>{{ $user->id }}</td>
        <td>{{ $user->influencerDetails->name ?? $user->name }}</td>
        <td>{{ $user->mobile }}</td>
        <td>{{ $user->email }}</td>
        <td>{{ date('d/m/Y',strtotime($user->created_at))}}</td>

        <td>
            <button type="button" class="btn btn-primary idget py-0 px-1"
                    data-name="{{ $user->name }}" data-id="{{$user->id}}"
                    data-toggle="modal" data-target="#myModal">
                Open
            </button>
        </td>

        @if($user->influencerDetails)
            <td class="eshop-active"><p>Created</p></td>
        @else
            <td class="eshop-failed"><p>Not Created</p></td>
        @endif
        <td class="eshop-link">
            <p>{{ $user->shop->shop_name  ?? '--'}}</p>
            {{-- <a href="">http://www.konmatfix.com</a> --}}
        </td>
        <td class="product-view">{{ $user->shop ? count($user->shop->influencerProducts) : 0 }} @if( $user->shop)
                @if(count($user->shop->influencerProducts) >=1 )
                    | <a href="{{ route('admin.influencer.products', $user->id) }}">View Products</a>
                @endif
            @endif</td>
        <td>{{ $user->influencerTotalSales['count'] }} (KSH {{ number_format($user->influencerTotalSales['amount']) }})</td>
        <td>KSH {{ number_format($user->influencerTotalSales['amount']) }}</td>
        <td>{{ count($user->influencerStories) ?? 0 }} Stories</td>



        <td class="t-action">
            <a href="{{ route('admin.influencers.edit', $user->id) }}"><img
                        src="{{ asset('admin/images/icons/edit-icon.svg') }}" alt=""></a>
            <a href="{{ route('admin.influencers.show', $user->id) }}"><img
                        src="{{ asset('admin/images/icons/Frame7158.svg') }}" alt=""></a>
            <i data-id="{{$user->id}}" class="fa fa-user btn-login"
               style="cursor: pointer;margin-right:10px;color: #7b1d42;"></i>

            <!-- Checked switch -->
            <label class="switch">
                <input data-id="{{$user->id}}" class="toggle-status"
                       type="checkbox" {{ $user->status ? 'checked' : '' }}>
                <span class="slider round"></span>
            </label>
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

