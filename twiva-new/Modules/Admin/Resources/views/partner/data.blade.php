@foreach ($users as $user)
    <tr>
        <td>{{ $user->id }}</td>
        <td>{{ $user->name ?? "--"}}</td>
        <td>{{ $user->businessDetails->contact_name ?? "--"}}</td>
        <td>{{ $user->mobile ?? $user->businessDetails->contact_number}}</td>
        <td>{{ $user->email ?? "--"}}</td>
        <td>
        {{$user->created_at?->format('d/m/Y') ??'-'}}

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
            <div class="d-flex align-items-center">
                <a>
                    <img class="upload-shipping-rate" src="{{ asset('admin/images/icons/upload-cloud.svg') }}"
                         alt="Upload shipping rate"
                         title="Upload shipping rate"
                    />
                </a>

                <a href="{{ route('admin.partner.edit', $user->id) }}"><img
                            src="{{ asset('admin/images/icons/edit-icon.svg') }}" alt=""></a>
                <i data-id="{{$user->id}}" class="fa fa-user btn-login"
                   style="cursor: pointer; margin-right:10px;color: #7b1d42;"></i>
                <label class="switch">
                    <input data-id="{{$user->id}}" class="toggle-status"
                           type="checkbox" {{ !$user->deleted_at ? 'checked' : '' }}>
                    <span class="slider round"></span>
                </label>
                <input type="hidden" name="user_id" value="{{$user->id}}" >
            </div>
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
