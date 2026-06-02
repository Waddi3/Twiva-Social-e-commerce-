@foreach ($badges as $badge)
    <tr>
        <td>{{ $badge->id }}</td>
        <td>{{ $badge->badge->name }}</td>
        <td>{{ $badge->user?->mobile ?? '-' }}</td>
        <td>{{ $badge->user?->email ?? '-' }}</td>
        <td>{{ $badge->addOn }}</td>
        <td class="{{ $badge->statusClassesCss() }}">
            <p>{{ $badge->status }}</p>
        </td>
        <td>
            <button type="button" class="btn btn-outline-primary idget py-0 px-1" data-name="{{ $badge->badge->name }}"
                @disabled($badge->status != 'pending') data-id="{{ $badge->id }}" data-toggle="modal" data-target="#myModal">
                Open
            </button>
        </td>
    </tr>
@endforeach
<tr>
    <td colspan="100">
        <div class="pagination">
            <div class="col-sm-5 p-0">
                <div class="col-sm-7 p-0">
                    <div class="paging_simple_numbers" id="example_paginate">
                        {{ $badges->links('admin::layouts.pagination') }}
                    </div>
                </div>
            </div>
        </div>
    </td>
</tr>
