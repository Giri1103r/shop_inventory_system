@extends('admin.layouts.admin')
@section('title', 'Upload Log List')
@section('pageurl', admin_url('uploadlog/list'))


@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
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
