@extends('admin::layouts.app')
@section('title')
    Campaigns
@endsection
@section('content')
    <div class="" id="business-dashboard">
        <div class="dashboard_container">

            <!-- Left Column Start -->
            <div class="left_col">
                <div class="scroll-view">
                    <div class="clearfix"></div>

                    <!-- sidebar menu -->
                    @include('admin::layouts.sidebar')
                    <!-- /sidebar menu -->
                </div>
            </div>
            <!-- Left Column End -->

            <!-- page content -->
            <div class="right_col dashboard-page" role="main">
                <!--********** Breadcrumb Start ***********-->
                <div class="page-title breadcrumb-wrapper">
                    <ul class="breadcrumb">
                        <li><a href="#">Business</a></li>
                    </ul>
                </div>
                <!--**********  Breadcrumb End ***********-->
                @include('admin::layouts.message')
                <div class="catalog-page">
                    <div class="select-top select-search">
                        <div class="search-wrap">
                            <div class="search-panel">
                                <img src="{{ asset('admin/images/icons/search.svg') }}">
                                <input type="text" placeholder="Search by name or id ..." id="keywords"
                                       value="{{ request('query') }}">
                                {{-- <input type="text" placeholder="Search by name, email, or ID..." id="keywords" onkeyup="searchFilter();"> --}}
                            </div>
                            <select name="status" id="status" style="width: 250px" class="dropdown-toggle">
                                <option value="all">All Campaigns</option>
                                <option value="INVITATION">INVITATION</option>
                                <option value="BROADCAST">BROADCAST</option>
                            </select>
                            <div class="button-catalog">
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="admin-table" class="table table-striped table-bordered table-sm" cellspacing="0"
                               width="100%">
                            <thead>
                            <tr>
                                <th class="sorting" data-sorting_type="asc" data-column_name="id">ID<span
                                        id="id_icon"></span><span class="fa fa-angle-down"></span></th>
                                <th>NAME</th>
                                <th>Business Name</th>
                                <th>Type</th>
                                <th>price per post</th>
                                <th>expiration date</th>
                                <th>Status</th>
                                <th># Influencers Type</th>
                                <th># Influencers</th>
                                <th># Applied</th>
                                <th># Declined</th>
                                <th># Hired</th>
                                <th># Pending</th>
                                <th>description</th>
                                <th>Action</th>
                            </tr>
                            </thead>

                            <tbody>
                            @include('campaign::admin.campaigns.data')
                            </tbody>

                        </table>
                    </div>
                </div>
            </div>
            <!-- /page content -->
        </div>
    </div>

@endsection
@section('scripts')
    <script>
        $(document).ready(function () {
            $('#business-table').DataTable({
                "scrollX": true,
                "scrollY": false,
            });
            $('.dataTables_length').addClass('bs-select');
        });
    </script>

    <script>
        function exportBusiness(_this) {
            let _url = $(_this).data('href');
            window.location.href = _url;
        }
    </script>

    <script>
        $(document).ready(function () {

            function filterData(query, type) {
                let url = `{{ route('admin.campaign.index', ['page' => 1]) }}`;
                if (type !== 'all') {
                    url += `&type=${type}`
                }
                if (query) {
                    url += `&query=${query}`
                }
                $.ajax({
                    url: url,
                    success: function (data) {
                        $('tbody').html('');
                        $('tbody').html(data);
                        window.history.replaceState(null, null, url);
                    }
                })
            }

            $(document).on('keyup', '#keywords', function () {
                let query = $('#keywords').val();
                let type = $('#status').val();
                filterData(query, type)
            });

            $(document).on('change', '#status', function () {
                let query = $('#keywords').val();
                let type = $('#status').val();
                filterData(query, type)
            });

            $('.approve-status-drop-down').change(function() {
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: "{{route('admin.campaign.approve_status')}}",
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    data: {
                        "status": $(this).val(),
                        "campaign_id": $(this).closest('tr').find('td:first-child').text()
                    },
                    success: function (data) {
                        console.log(data);
                        if( data.status ) {
                            alert(data.message);
                        }
                    },
                    error: function (data) {
                        console.log(data)
                    }
                });
            });
        })
    </script>
    <script>
        $(document).on('click', '.btn-login', function (e) {
            var user_id = $(this).data('id');
            let campaign_id = $(this).data('campaign-id');
            e.preventDefault();

            $.ajax({
                type: "POST",
                dataType: "json",
                url: "/user/" + user_id + `/login-as?login-as-to-campaign=${campaign_id}`,
                data: {
                    "_token": "{{ csrf_token() }}",
                },
                success: function (data) {
                    console.log(data);
                    window.open(data.url, '_blank');
                },
                error: function (data) {
                    console.log(data)
                }
            });
        });
    </script>
@endsection
