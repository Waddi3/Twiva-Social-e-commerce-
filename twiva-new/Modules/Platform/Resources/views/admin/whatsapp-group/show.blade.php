@php use Modules\Platform\Entities\WhatsAppGroup; @endphp
@extends('admin::layouts.app')
@section('title')
    Twiva - WhatsApp Groups
@endsection
@section('content')
    <div class="">
        <div class="dashboard_container">

            <!-- Left Column Start -->
            <div class="left_col">
                <div class="scroll-view">
                    <div class="clearfix"></div>

                    <!-- Sidebar menu -->
                    @include('admin::layouts.sidebar')
                    <!-- /Sidebar menu -->
                </div>
            </div>
            <!-- Left Column End -->


            <div class="right_col container-fluid">
                <!--********** Breadcrumb Start ***********-->
                <div class="page-title breadcrumb-wrapper">
                    <ul class="breadcrumb breadcrumb-link">
                        <li>
                            <a href="{{ route('admin.whatsapp.index') }}">
                                <img src="{{ asset('admin/images/icons/left-arr.svg') }}" alt="">
                                View WhatsApp Group
                            </a>
                        </li>
                    </ul>
                </div>
                <!--**********  Breadcrumb End ***********-->
                <div class="title-wrapper">
                    <h4>

                    </h4>
                </div>
                {{-- <div class="account-info-wrapper">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="user-info">

                            </div>
                        </div>
                    </div>
                </div> --}}
                <div class="account-info-wrapper">
                    <div class="row">
                        <div class="col-md-9 user-info-wrapper">
                            <div class="user-info">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="user-info">
                                            <div class="content-wrapper">
                                                <div class="company-info">
                                                    <p>
                                                        <img src="{{ asset('admin/images/icons/social/whatsapp.svg') }}"
                                                             width="50" height="50" style="margin-right: 15px">
                                                        <span>
                                                            <strong style="font-size: 3rem;">
                                                                {{ $whats_app_group->name }}
                                                            </strong>
                                                        </span>
                                                    </p>
                                                </div>
                                                <div class="contact-wrapper">
                                                    <p>
                                                        <img src="{{ asset('admin/images/icons/address.svg') }}"
                                                             alt="">

                                                        <span style="font-style: italic; font-size: 1.2rem">
                                                            {{ $whats_app_group?->city?->name }} ,
                                                            {{ $whats_app_group?->city?->country->name }}
                                                        </span>
                                                    </p>
                                                    <p>
                                                        <span>
                                                            <strong>
                                                                {{ $whats_app_group?->CategoryWhatsAppGroups->pluck('categorizable')->pluck('name')->implode(', ') }}
                                                            </strong>
                                                        </span>
                                                    </p>
                                                    <p>
                                                        <span style="display: block">
                                                            Members
                                                        </span>
                                                        <span>
                                                            <strong style="font-size: 2rem; color:#7d0d41">
                                                                {{ $whats_app_group?->number_of_people }}
                                                            </strong>
                                                        </span>
                                                    </p>
                                                    <p>
                                                        <span style="display: block" class="mb-2">Whats App Group
                                                            Link</span>
                                                        <span>
                                                            <a href="{{ $whats_app_group?->invite_link }}"
                                                               style="color: #ED4272;" target="_black">
                                                                Invite Link
                                                            </a>
                                                        </span>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="">
                                @if ($whats_app_group?->ActiveVerified())
                                    <div>
                                        <div class="row">
                                            <div class="col-12 col-md-6">
                                                <button id="" class="btn btn-danger btn-danger-alt w-100 py-3"
                                                        data-toggle="modal" data-target="#myModal">
                                                    Reject
                                                </button>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <button id="verity-button"
                                                        class="btn btn-success btn-success-alt w-100 py-3">
                                                    Verify
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="d-flex justify-content-end" style="font-size: 18px;">
                                        @if ($whats_app_group?->status == 'rejected')
                                            <div class="badge badge-pill badge-danger badge-danger-alt">
                                                {{ $whats_app_group?->status }}
                                            </div>
                                        @elseif($whats_app_group?->status == 'verified')
                                            <div class="badge badge-pill badge-success badge-success-alt">
                                                {{ $whats_app_group?->status }}
                                            </div>
                                        @endif

                                        @if ($whats_app_group?->status != WhatsAppGroup::STATUSES['verified'])
                                            <div>
                                                @foreach ($whats_app_group?->reasonRejections as $reason)
                                                    <label>{{ $reason->reason }}</label>
                                                    <br>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal" id="myModal">
        <div class="modal-dialog">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title"></h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <!-- Modal body -->
                <div class="modal-body">
                    <h2 class="text-center mb-4" style="font-size: 2rem; color:#7d0d41; font-weight: 600">Reason for
                        Rejection</h2>
                    <div class="form-wrapper">

                        <div class="form-field-full">
                            <label>Reason</label>
                            {{-- <input type="text" id="reason_for_rejections" name="reason_for_rejections" placeholder="Enter rejection reason" required> --}}
                            <select name="reason_for_rejections" id="reason_for_rejections" required>
                                <option selected disabled>Select rejection reason</option>
                                @foreach ($reasons as $reason)
                                    <option value="{{ $reason->id }}">{{ $reason->reason }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="d-flex">
                        <div style="flex: 1">
                            <button type="button" class="btn btn-border-gray w-100"
                                    style="padding: 9px 18px !important;"
                                    data-dismiss="modal">Cancel
                            </button>
                        </div>
                        <div class="ml-2" style="flex: 1">
                            <button id="reject-button" class="purple-btn py-3 mt-0">Confirm</button>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {

            function updateStatusWhatsAppGroup(whats_app_group_id, status, reason_for_rejections) {
                console.log(reason_for_rejections);
                $.ajax({
                    type: "GET",
                    dataType: "json",
                    url: "{{ route('admin.whatsapp.verified') }}",
                    data: {
                        'reason_for_rejections': reason_for_rejections,
                        'whats_app_group_id': whats_app_group_id,
                        'status': status
                    },
                    success: function (data) {
                        location.reload();
                    },
                    error: function (data) {
                        alert('Please try later, something went wrong.');
                    }
                });
            }

            $('#verity-button').on('click', function (event) {
                let whats_app_group_id = `{{ $whats_app_group->id }}`;
                let status = 'verified';
                let reason_for_rejections = {};
                updateStatusWhatsAppGroup(whats_app_group_id, status, reason_for_rejections);
            });
            $('#reject-button').on('click', function (event) {
                let whats_app_group_id = `{{ $whats_app_group->id }}`;
                let status = 'rejected';
                let reason_for_rejections = $('#reason_for_rejections').val();
                updateStatusWhatsAppGroup(whats_app_group_id, status, reason_for_rejections);
            });
        });
    </script>
@endsection
