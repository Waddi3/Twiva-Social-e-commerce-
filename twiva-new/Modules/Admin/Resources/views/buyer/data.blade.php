@foreach ($users as $user)
    <tr>
        <td>{{ $user->id }}</td>
        <td>{{ $user->name ?? "Buyer" }}</td>
        <td>{{ $user->email }}</td>
        <td>{{ $user->mobile }}</td>
        <td>{{ $user->created_at->format('d/m/y') }}</td>
        <td>{{ $user->ordersCount }}</td>
        <td>KSH {{ number_format($user->ordersAmount) }}</td>
        @if(!$user->trashed())
            <td class="eshop-status-{{$user->id}} eshop-active">
                <p>Active</p>
            </td>
        @else
            <td class="eshop-status-{{$user->id}} eshop-failed">
                <p>InActive</p>
            </td>
        @endif
        <td>{{ $user->class }}</td>
        <td>{{ $user->rep_name }}</td>
        <td>{{ $user->retail_store_name }}</td>
        <td>{{ $user->rep_mobile }}</td>
        <td class="t-action">
{{--            <a href="https://www.google.com/maps?q={{ $address->latitude }},{{ $address->longitude }}"--}}
{{--               target="_blank">--}}
{{--                <span class="font-thin text-primary-600" data-feather="map-pin"></span>--}}
{{--            </a>--}}
            <!-- <img src="{{ asset('admin/images/icons/Frame7158.svg') }}" alt=""> -->
            <!-- Checked switch -->
            <label class="switch">
                <input data-id="{{$user->id}}" class="toggle-status"
                       type="checkbox" {{ !$user->trashed() ? 'checked' : '' }}>
                <span class="slider round"></span>
            </label>
        </td>
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
