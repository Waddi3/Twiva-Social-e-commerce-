@foreach ($campaigns as $campaign)
    <tr>
        <td>{{ $campaign->id }}</td>
        <td>{{ $campaign->post_name }}</td>
        <td>{{ $campaign->user?->name ?? "--"}}</td>
        <td>{{ $campaign->post_type ?? 'INVITATION' }}</td>
        <td>{{ $campaign->price_per_post }}</td>
        <td>{{ $campaign->expiration_date }}</td>
        <td>
            <select style="width: 90px" class="dropdown-toggle approve-status-drop-down">
                <option value="0" @if($campaign->approve_status === 0) selected @endif disabled>Pending</option>
                <option value="1" @if($campaign->approve_status === 1) selected @endif>Approved</option>
                <option value="2" @if($campaign->approve_status === 2) selected @endif>Rejected</option>
            </select>
        </td>
        <td>{{ $campaign->user_typew }}</td>
        <td>{{ $campaign->influencers_in_post_count }}</td>
        <td>{{ $campaign->applied_count }}</td>
        <td>{{ $campaign->declined_count }}</td>
        <td>{{ $campaign->hired_count }}</td>
        <td>{{ $campaign->pending_count }}</td>
        <td>{{ $campaign->description }}</td>
        <td class="t-action">
            <div class="d-flex align-items-center">
                <i data-id="{{ $campaign->user?->id }}" data-campaign-id="{{ $campaign->id }}"
                   class="fa fa-user btn-login"
                   style="cursor: pointer; margin-right:10px;color: #7b1d42;"></i>
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
                        {{ $campaigns->withQueryString()->links('admin::layouts.pagination') }}
                    </div>
                </div>
            </div>
        </div>
    </td>
</tr>
