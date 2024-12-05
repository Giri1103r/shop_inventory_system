@extends('admin.layouts.admin')
@section('title', 'Upload Log View')
@section('pageurl', admin_url('uploadlog/list'))


@section('content')
    <div class="clearfix"></div>
    <div class="container-fluid">
            <!-- row -->
            <div class="row">
              
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="d-flex justify-content-end p-2">
                                    <a href="{{ admin_url('uploadlog/download/' . request()->logid) }}"
                                        data-bs-toggle="tooltip" title="Download" class="btn btn-secondary ms-1">
                                        Download
                                    </a>
                                
                                    <a href="{{ admin_url('uploadlog/list') }}" data-bs-toggle="tooltip" title="Back"
                                        class="btn btn-secondary ms-2">  <!-- Added ms-2 for margin on the left side of the Back button -->
                                        Back
                                    </a>
                                </div>
                                
                            </div>

                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="datatable-list"
                                        class="table primary-table-bordered table-bordered table-striped display responsive nowrap w-100 mt-2 datatable-list">
                                        <thead class="thead-primary">
                                            <tr>
                                                <th>No.</th>
                                                <th>Line No</th>
                                                <th>Error</th>
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
                 dom: 'Bfrtip',
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
                    url: "{{ admin_url('uploadlog/list/' . request()->logid) }}",
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
                        data: 'line_no',
                        name: 'line_no'
                    },
                    {
                        data: 'error',
                        name: 'error'
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
                            // {
                            //     extend: 'pdf',
                            //     text: '{{ __('common.pdf') }}',
                            //     action: function(e, dt, button, config) {
                            //         var searchValue = $('#datatable-list_filter input').val();
                            //         employee_type = $('#employee_type').val();
                            //         status = $('#status').val();

                            //         console.log(employee_type);

                            //         $(".dt-button").removeClass('processing');
                            //         $('body').click();
                            //         window.location.href =
                            //             "{{ admin_url('emp_manage/employee_type/export/pdf') }}" +
                            //             '?search=' + searchValue +
                            //             '&employee_type=' + employee_type +
                            //             '&status=' + status
                            //     }
                            // },
                            {
                                extend: 'excel',
                                text: '{{ __('common.excel') }}',
                                action: function(e, dt, button, config) {
                                    var searchValue = $('#datatable-list_filter input').val();
                                    $(".dt-button").removeClass('processing');
                                    $('body').click();
                                    window.location.href =
                                        "{{ admin_url('uploadlog/export/excel/' . request()->logid) }}" +
                                        '?search=' + searchValue;
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
