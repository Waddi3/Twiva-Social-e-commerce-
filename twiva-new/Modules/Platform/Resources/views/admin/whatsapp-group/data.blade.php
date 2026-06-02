@foreach ($whats_app_groups as $whats_app_group)
    <tr>
        {{-- <td>{{ $whats_app_group->id }}</td> --}}
        <td>{{ $whats_app_group->name }}</td>
        <td>{{ $whats_app_group->number_of_people }}</td>
        <td>{{ $whats_app_group->user?->name }}</td>
        <td>{{ $whats_app_group->addedOn() }}</td>
        <td><a href="{{ $whats_app_group->invite_link }}" target="_black">Invite Link</a></td>
        <td class="{{ $whats_app_group->statusesCssClasses() }}"><p>{{ $whats_app_group->status }}</p></td>
        <td class="t-action">
            <a href="{{ route('admin.whatsapp.show', [$whats_app_group->id]) }}"><img
                src="{{ asset('admin/images/icons/Frame7158.svg') }}" alt=""></a>
    <!-- Checked switch -->

        </td>
    </tr>
@endforeach
<tr>
    <td colspan="100">
        <div class="pagination">
            <div class="col-sm-5 p-0">
                <div class="col-sm-7 p-0">
                    <div class="paging_simple_numbers" id="example_paginate">
                        {{ $whats_app_groups->links('admin::layouts.pagination') }}
                    </div>
                </div>
            </div>
        </div>
    </td>
</tr>
