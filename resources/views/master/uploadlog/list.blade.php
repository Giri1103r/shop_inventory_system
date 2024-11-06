@extends('admin.layouts.admin')
@section('title', 'Upload Log List')
@section('pageurl', admin_url('uploadlog/list'))


@section('content')
    <div class="clearfix"></div>
    <div class="page-titles">
        <div class="d-flex align-items-center">
            <h2 class="text-black">{{ __('Upload Log') }}</h2>

        </div>
        <ol class="breadcrumb">
            <li class="breadcrumb-item active ms-auto">
                <a class="d-flex align-self-center" href="{{ admin_url('dashboard') }}">
                    <svg class="me-2 svg-main-icon" xmlns="http://www.w3.org/2000/svg"
                        xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24"
                        version="1.1">
                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                            <rect x="0" y="0" width="24" height="24"></rect>
                            <path
                                d="M3.95709826,8.41510662 L11.47855,3.81866389 C11.7986624,3.62303967 12.2013376,3.62303967 12.52145,3.81866389 L20.0429,8.41510557 C20.6374094,8.77841684 21,9.42493654 21,10.1216692 L21,19.0000642 C21,20.1046337 20.1045695,21.0000642 19,21.0000642 L4.99998155,21.0000673 C3.89541205,21.0000673 2.99998155,20.1046368 2.99998155,19.0000673 L2.99999828,10.1216672 C2.99999935,9.42493561 3.36258984,8.77841732 3.95709826,8.41510662 Z M10,13 C9.44771525,13 9,13.4477153 9,14 L9,17 C9,17.5522847 9.44771525,18 10,18 L14,18 C14.5522847,18 15,17.5522847 15,17 L15,14 C15,13.4477153 14.5522847,13 14,13 L10,13 Z"
                                fill="#16A3A4"></path>
                        </g>
                    </svg>

                </a>
            </li>
            <li class="breadcrumb-item"><a href="javascript:void(0)">{{ __('leftmenu.menu_2') }}</a></li>
            <li class="breadcrumb-item"><a href="javascript:void(0)">{{ __('leftmenu.menu_24') }}</a></li>
            <li class="breadcrumb-item"><a href="javascript:void(0)">{{ __('leftmenu.menu_26') }}</a></li>

        </ol>
    </div>

    <div class="content-body  default-height">
        <div class="container-fluid main-content">
            <!-- row -->
            <div class="row">

                <div class="col-12">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">{{ __('Upload Log List') }}</h4>

                            </div>

                            <div class="card-body ">
                                <div class="table-responsive">
                                    <table id="datatable-list"
                                        class="table primary-table-bordered table-bordered table-striped display responsive nowrap w-100 mt-2 datatable-list">
                                        <thead class="thead-primary">
                                            <tr>
                                                <th>No.</th>
                                                <th>Module Name</th>
                                                <th>File Name</th>
                                                <th>Date & Time</th>
                                                <th>Upload By</th>
                                                <th>Status</th>
                                                <th>Action</th>
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
        </div>
    </div>


@stop

@push('script')
    <script type="text/javascript">
        $(document).ready(function() {

            var firstTh = $('.datatable-list thead th:first');
            firstTh.removeClass('sorting_asc');
        });

        $(function() {
            /* Datatable */
            var table = $('.datatable-list').DataTable({
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
                    url: "{{ admin_url('uploadlog/list') }}",
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]')
                            .attr('content')
                    },
                    data: function(d) {

                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'type_name',
                        name: 'type_name'
                    },
                    {
                        data: 'file_orgname',
                        name: 'file_orgname'
                    },
                    {
                        data: 'datetime',
                        name: 'datetime'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'uploadstatus',
                        name: 'uploadstatus'
                    },
                    {
                        data: 'action',
                        name: 'action',
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
                buttons: [
                    // {
                    //     extend: 'collection',
                    //     text: '{{ __('common.export') }}',
                    //     buttons: [{
                    //             extend: 'pdf',
                    //             text: '{{ __('common.pdf') }}',
                    //             action: function(e, dt, button, config) {
                    //                 var searchValue = $('#datatable-list_filter input').val();
                    //                 employee_type = $('#employee_type').val();
                    //                 status = $('#status').val();

                    //                 console.log(employee_type);

                    //                 $(".dt-button").removeClass('processing');
                    //                 $('body').click();
                    //                 window.location.href =
                    //                     "{{ admin_url('emp_manage/employee_type/export/pdf') }}" +
                    //                     '?search=' + searchValue +
                    //                     '&employee_type=' + employee_type +
                    //                     '&status=' + status
                    //             }
                    //         },
                    //         {
                    //             extend: 'excel',
                    //             text: '{{ __('common.excel') }}',
                    //             action: function(e, dt, button, config) {
                    //                 var searchValue = $('#datatable-list_filter input').val();
                    //                 employee_type = $('#employee_type').val();
                    //                 status = $('#status').val();
                    //                 $(".dt-button").removeClass('processing');
                    //                 $('body').click();
                    //                 window.location.href =
                    //                     "{{ admin_url('emp_manage/employee_type/export/excel') }}" +
                    //                     '?search=' + searchValue +
                    //                     '&employee_type=' + employee_type +
                    //                     '&status=' + status
                    //             }
                    //         },
                    //     ]
                    // },

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
