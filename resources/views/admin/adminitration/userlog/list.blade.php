@extends('admin.layouts.admin')
@section('title', 'User Access Log')
@section('pageurl', admin_url('admin/master/userlog/list'))

@push('style')
    <style>


    </style>
@endpush

@section('content')
    <div class="content-wrapper">
        <div class="container-full">
            <div class="content-header">
                <div class="d-flex align-items-center">
                    <div class="mr-auto">
                        <div class="d-inline-block align-items-center">
                            <nav>
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ url('') }}"><i class="mdi mdi-home-outline"></i></a> </li>
                                    <li class="breadcrumb-item" aria-current="page"> {{ __('Admin Master') }} </li>
                                    <li class="breadcrumb-item active" aria-current="page">{{ __('User Access Log') }}</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>

            <section class="content">
                <div class="row">

                    <div class="col-12">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">{{ __('User Access Log') }}</h4>
                                    <div>
                                        <x-button-filter dataId="" class="search" href=""></x-button-filter>

                                    </div>
                                </div>

                                <div id="search" class="collapse">
                                    <form action="" id="formsearch">
                                        <div class="card-body">
                                            <div class="col-md-12">
                                                <div class="row">

                                                    <div class="col-md-3 mb-3 form-input">
                                                        <label for="user"
                                                            class="form-label ">{{ __('User Name') }}</label>
                                                        <select name="user" id="user" style="width: 100%"
                                                            class="form-control single-select">
                                                            <option value="">Select User</option>
                                                            @foreach ( $userdetails as $user )
                                                            <option value="{{ encryptId($user->id) }}">{{ $user->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <x-button-search></x-button-search>
                                                        <x-button-reset></x-button-reset>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                    <hr>
                                </div>

                                <div class="card-body ">
                                    <div class="table-responsive">
                                        <table id="datatable-list"
                                            class="table primary-table-bordered table-bordered table-striped display responsive nowrap w-100 mt-2 datatable-list">
                                            <thead class="thead-primary">
                                                <tr>
                                                    <th>No.</th>
                                                    <th>User Name</th>
                                                    <th>URL</th>
                                                    <th>Session ID</th>
                                                    <th>IP Address</th>
                                                    <th>Date & Time</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

        </div>
    </div>


@endsection


@push('scripts')
@endpush

@push('script')
    <script type="text/javascript" nonce="projectcab">
        $(document).ready(function() {

            var firstTh = $('.datatable-list thead th:first');
            firstTh.removeClass('sorting_asc');
        });

        $(function() {
            /* Datatable */
            var table = $('.datatable-list').DataTable({
                dom: 'Bfrtip',
                autoWidth: false,
                responsive: true,
                processing: false,
                serverSide: true,
                searching: true,
                ordering: true,
                layout: {
                    top2Start: 'buttons',
                    top2End: {
                        search: {
                            placeholder: ''
                        }
                    },
                    topStart: '',
                    topEnd: '',
                    bottomStart: '',
                    bottomEnd: '',
                    bottom2Start: 'info',
                    bottom2End: 'paging'
                },


                ajax: {
                    url: "{{ admin_url('admin/master/userlog/list') }}",
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]')
                            .attr('content')
                    },
                    data: function(d) {
                        d.user = $('#user').val();
                    },
                    error: function(xhr, error, code) {
                            if (xhr.status === 419) {
                                alert('Session has expired. You will be redirected to the login page.');
                                window.location.href = "{{ url('') }}"; // Redirect to login page
                            }
                        }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'request_uri',
                        name: 'request_uri'
                    },
                    {
                        data: 'session_id',
                        name: 'session_id'
                    },
                    {
                        data: 'client_ip',
                        name: 'client_ip'
                    },
                    {
                        data: 'datetime',
                        name: 'datetime'
                    },
                ],

                language: {
                    paginate: {
                        first: '<i title="{{ __('common.first') }}" class="fa fa-angle-double-left" aria-hidden="true"></i>',
                        last: '<i title="{{ __('common.last') }}" title="Next" class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        next: '<i title="{{ __('common.next') }}" class="fa fa-angle-right" aria-hidden="true"></i>',
                        previous: '<i title="{{ __('common.previous') }}" class="fa fa-angle-left" aria-hidden="true"></i>',
                    },
                    "info": "{{ __('common.dt_info') }}",
                    "infoEmpty": "{{ __('common.dt_infoEmpty') }}",
                    "infoFiltered": "{{ __('common.dt_infoFiltered') }}",
                },
                aLengthMenu: [
                    [10, 25, 50, 100],
                    [10, 25, 50, 100]
                ],
                buttons: [{
                        extend: 'collection',
                        text: '{{ __('common.export') }}',
                        buttons: [
                               {
                                extend: 'excel',
                                text: '{{ __('common.excel') }}',
                                action: function(e, dt, button, config) {
                                    var searchValue = $('#datatable-list_filter input').val();
                                    user = $('#user').val();

                                    $(".dt-button").removeClass('processing');
                                    $('body').click();
                                    window.location.href =
                                        "{{ admin_url('admin/master/userlog/export/excel') }}" +
                                        '?search=' + searchValue +
                                        '&user='+ user

                                }
                            },
                        ]
                    },

                    {
                        "extend": 'pageLength',
                        "text": '{{ __('common.show') }} 10 {{ __('common.records') }}'
                    }
                ],

            });

            table.on('length.dt', function(e, settings, len) {
                var text = '{{ __('common.show') }} ' + len + ' {{ __('common.records') }}';
                $('.buttons-page-length').find('span').text(text);
            });

            $(document).on('click', '#searchform', function() {
                table.draw();
            });

            $(document).on('click', '#resetform', function() {
                setTimeout(function() {
                    table.draw();
                }, 150);
            });


        });
    </script>
@endpush
