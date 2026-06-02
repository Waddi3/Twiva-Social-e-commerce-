@php use Modules\User\Entities\User; @endphp
@extends('admin::layouts.app')
@section('title')
    Notifications
@endsection
@section('content')
    <div class="" id="business-dashboard">
        <div class="dashboard_container">

            <div class="left_col">
                <div class="scroll-view">
                    <div class="clearfix"></div>

                    <!-- sidebar menu -->
                    @include('admin::layouts.sidebar')
                    <!-- /sidebar menu -->
                </div>
            </div>
            <!-- page content -->
            <div class="right_col dashboard-page" role="main">

                <!--********** Breadcrumb Start ***********-->
                <div class="page-title breadcrumb-wrapper">
                    <ul class="breadcrumb">
                        <li><a href="#">Notifications</a></li>
                    </ul>
                </div>
                <!--**********  Breadcrumb End ***********-->


                <div class="catalog-page">

                    <div class="table-responsive">
                        <table id="admin-table" class="table table-striped table-bordered table-sm" cellspacing="0"
                               width="100%">
                            <thead>
                            <tr>
                                <th>User</th>
                                <th>Notification</th>
                                <th>User Type</th>
                                <th>Type</th>
                                <th>Action</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach ($notifications as $notification)
                                <tr>
                                    <td>
                                        @if ($notification->user?->type == User::InfluencerType)
                                            <a href="{{ route('admin.influencers.show', $notification->user->id) }}">
                                                {{$notification->user->name}}
                                            </a>
                                        @endif
                                        @if ($notification->user?->type == User::BusinessType)
                                            <a href="{{ route('admin.business.show', $notification->user->id) }}">
                                                {{$notification->user->name}}
                                            </a>

                                        @endif
                                        @if ($notification->user?->type == User::BuyerType)
                                            <a href="#">
                                                {{$notification->user->name}}
                                            </a>
                                        @endif

                                    </td>
                                    <td>{{ $notification->data['title'] }}</td>
                                    <td>
                                        {{$notification->user?->type ?? '-'}}
                                    </td>
                                    <td>
                                        {{$notification->notificationType}}
                                    </td>
                                    <td class="t-action">
                                        @if($notification->isOrder)
                                            <a href="{{ route('admin.orders.show', ['id' => $notification->data['reason_id'] ?? 0]) }}">
                                                <img src="{{ asset('admin/images/icons/Frame7158.svg') }}" alt="">
                                            </a>
                                        @else
                                            --
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <input type="hidden" name="hidden_page" id="hidden_page" value="1"/>
                        <input type="hidden" name="hidden_column_name" id="hidden_column_name" value="id"/>
                        <input type="hidden" name="hidden_sort_type" id="hidden_sort_type" value="asc"/>
                        <input type="hidden" name="hidden_status" id="hidden_status" value="9"/>
                        <input type="hidden" name="hidden_page_name" id="hidden_page_name" value="orders"/>
                    </div>

                    <div class="pagination">
                        <div class="col-sm-5">
                            <!-- <div class="dataTables_info" id="example_info" role="status" aria-live="polite">Showing 1 to 10 of 57 entries</div></div> -->
                            <div class="col-sm-7">
                                <div class="dataTables_paginate paging_simple_numbers" id="example_paginate">
                                    {{ $notifications->links('admin::layouts.pagination') }}
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="delete-product" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <div class="title">
                        <h4>
                            Update Subscription?
                        </h4>
                    </div>
                    <p>The subscription will be active immediately and
                        will be charged accordingly in the next cycle. </p>
                </div>

                <div class="modal-footer ">
                    <button type="button" class="c-btn close-btn " data-dismiss="modal">Close</button>
                    <button class="purple-btn c-btn">Update</button>
                </div>
            </div>

        </div>
    </div>

    <!--End Modal -->
@endsection
@section('scripts')
    <!-- <script src="{{ asset('js/orderfilter.js') }}"></script> -->
@endsection
