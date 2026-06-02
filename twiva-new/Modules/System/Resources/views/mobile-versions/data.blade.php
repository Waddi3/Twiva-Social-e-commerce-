@forelse($versions as $version)
    <tr>
        <td>{{ $version->id }}</td>
        <td>{{ $version->app_name }}</td>
        <td>{{ $version->platform }}</td>
        <td>{{ $version->version }}</td>
        <td class="force-status-{{ $version->id }}">{{ $version->is_forced ? 'Force' : 'Not Force'}}</td>
        <td>
            <a href="{{ $version->link }}" target="_blank">Direct Link</a>
        </td>
        <td>
            <a href="{{ $version->store_link }}" target="_blank">Direct Link</a>
        </td>
        <td class="t-action">
            <div class="d-flex align-items-center">
                <a href="{{ route('admin.mobile.version.edit', $version->id) }}"><img
                        src="{{ asset('admin/images/icons/edit-icon.svg') }}" alt=""></a>
                <label class="switch ml-2">
                    <input data-id="{{$version->id}}" class="toggle-force"
                           type="checkbox" {{ $version->is_forced ? 'checked' : '' }}>
                    <span class="slider round"></span>
                </label>
            </div>
        </td>
    </tr>
@empty

@endforelse

<tr>
    <td colspan="100">
        <div class="pagination">
            <div class="col-sm-5 p-0">
                <div class="col-sm-7 p-0">
                    <div class="paging_simple_numbers" id="example_paginate">
                        {{ $versions->links('admin::layouts.pagination')}}
                    </div>
                </div>
            </div>
        </div>
    </td>
</tr>
