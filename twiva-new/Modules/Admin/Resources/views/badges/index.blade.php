@extends('admin::layouts.app')
@section('title')
    Twiva - Influencer Reseller Verification
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
                        <li><a href="#">Influencer Reseller Verification</a></li>
                    </ul>
                </div>
                <!--**********  Breadcrumb End ***********-->


                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="catalog-page">
                    <div class="select-top select-search">
                        <div class="search-wrap">
                            <div class="search-panel">
                                <img src="{{ asset('admin/images/icons/search.svg') }}">
                                <input type="text" placeholder="Search..." id="keywords">
                            </div>
                        </div>

                    </div>

                    <div class="table-responsive">
                        <table id="admin-table" class="table table-striped table-bordered table-sm" cellspacing="0"
                            width="100%">
                            <thead>
                                <tr>
                                    <th> ID</th>
                                    <th class="th-sm">NAME</th>
                                    <th class="th-sm">Phone Number</th>
                                    <th class="th-sm">Email Address</th>
                                    <th class="th-sm">Created At</th>
                                    <th class="th-sm">Status</th>
                                    <th class="th-sm">ACTION</th>
                                </tr>
                            </thead>

                            <tbody>
                                @include('admin::badges.data')
                            </tbody>
                        </table>
                    </div>


                </div>
            </div>
        </div>
    </div>

    <!-- The Modal -->
    <div class="modal" id="myModal">
        <div class="modal-dialog">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title"></h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <!-- Modal body -->
                <div class="modal-body mt-3">
                    <form method="post">
                        @csrf
                        <input type="hidden" name="id" id="verifyid">
                        <div class="form-group">
                            <select class="form-control" name="status">
                                <option disabled selected>-Select-</option>
                                <option value="approved">Approve</option>
                                <option value="declined">Disapprove</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <textarea name="comments" class="form-control" rows="5"></textarea>
                        </div>

                        <button type="submit" class="purple-btn">Submit</button>
                    </form>
                </div>


            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).on('keyup', '#keywords', function() {
            let query = $('#keywords').val();
            let additionalParams = {
                'page': 1,
                'query': query
            }
            let route = `{{ route('admin.badges.index') }}`
            let url = route + '?' + $.param(additionalParams);
            history.pushState(null, '', url);
            $.ajax({
                url: url,
                success: function(data) {
                    $('tbody').html('');
                    $('tbody').html(data);
                }
            })
        })
    </script>

    <script>
        $('body').on('click', '.idget', function() {
            var id = $(this).attr("data-id");
            var name = $(this).attr("data-name");
            $('.modal-title').html(name);
            $("#verifyid").val(id);
        });
    </script>
@endsection
