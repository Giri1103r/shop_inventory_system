@extends('admin.layouts.admin')
@section('title', ' Inventory Tabular')
@section('pageurl', admin_url('ohc/inventory-tabular-view/list'))
@section('content')
    @push('style')
        <style>
            .table-responsive {
                overflow-x: auto;
                width: 100%
            }
        </style>
    @endpush
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">

                <div class="card">
                    <h4 class="card-title"></h4>
                    <div class="d-flex justify-content-end p-2 me-2">


                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="datatable-list"
                            class="table primary-table-bordered table-bordered table-striped  nowrap w-100 mt-2 datatable-list">
                                <thead class="thead-primary">
                                    <tr>
                                        <th>{{ __('common.sno') }}</th>
                                        <th>Medicine Name</th>
                                        <th>Total Purchase</th>
                                        <th>Total Issue</th>
                                        <th>Total First Aid</th>
                                        <th>Total Prescribe</th>
                                        <th>Balance</th>
                                        <th>Threshold</th>
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
    <script type="text/javascript" nonce="projectcab">
        $(document).ready(function() {



            table.on('length.dt', function(e, settings, len) {
                var text = '{{ __('common.show') }} ' + len + ' {{ __('common.records') }}';
                $('.buttons-page-length').find('span').text(text);
            });


            $('#searchform').on('click', function() {
                table.draw();
            });



        });

        var table = $('.datatable-list').DataTable({
            serverSide: true,
            searching: true,
            ordering: true,
            bSort: false,
            scrollX: true,
            autoWidth: true,
            responsive: false,
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
                url: "{{ admin_url('ohc/inventory-tabular-view/list') }}",
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },

                error: function(xhr, error, code) {
                    if (xhr.status === 419) {
                        alert('Session has expired. You will be redirected to the login page.');
                        window.location.href = "{{ url('') }}";
                    }
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },

                {
                    data: 'medicine_id',
                    name: 'medicine_id'
                },
                {
                    data: 'total_purchase',
                    name: 'total_purchase'
                },
                {
                    data: 'total_issue',
                    name: 'total_issue'
                },
                {
                    data: 'total_first_aid',
                    name: 'total_first_aid'
                },
                {
                    data: 'total_prescribe',
                    name: 'total_prescribe'
                },
                {
                    data: 'balance',
                    name: 'balance'
                },
                {
                    data: 'threshold_limit',
                    name: 'threshold_limit'
                },

            ],
            language: {
                paginate: {
                    first: '<i title="{{ __('common.first') }}" class="fa fa-angle-double-left" aria-hidden="true"></i>',
                    last: '<i title="{{ __('common.last') }}" class="fa fa-angle-double-right" aria-hidden="true"></i>',
                    next: '<i title="{{ __('common.next') }}" class="fa fa-angle-right" aria-hidden="true"></i>',
                    previous: '<i title="{{ __('common.previous') }}" class="fa fa-angle-left" aria-hidden="true"></i>'
                },
                info: "{{ __('common.dt_info') }}",
                infoEmpty: "{{ __('common.dt_infoEmpty') }}",
                infoFiltered: "{{ __('common.dt_infoFiltered') }}"
            },
            aLengthMenu: [
                [10, 25, 50, 100],
                [10, 25, 50, 100]
            ],
            buttons: [
            // {
            //         // extend: 'collection',
            //         // text: '{{ __('common.export') }}',
            //         // buttons: [{
            //         //         extend: 'pdf',
            //         //         text: '{{ __('common.pdf') }}',
            //         //         action: function(e, dt, button, config) {
            //         //             var searchValue = $('#datatable-list_filter input').val();
            //         //             var department_id = $('#department_id').val();
            //         //             var unit_id = $('#unit_id').val();
            //         //             var from_date = $('#from_date').val();
            //         //             var to_date = $('#to_date').val();
            //         //             var status = $('#status').val();
            //         //             $(".dt-button").removeClass('processing');
            //         //             $('body').click();
            //         //             window.location.href =
            //         //                 "{{ admin_url('ohc/inventory-tabular-view/export/pdf') }}" +
            //         //                 '?search=' + searchValue +
            //         //                 '&department_id=' + department_id +
            //         //                 '&unit_id=' + unit_id +
            //         //                 '&from_date=' + from_date +
            //         //                 '&status=' + status +
            //         //                 '&to_date=' + to_date;
            //         //         }
            //         //     },
            //         //     {
            //         //         extend: 'excel',
            //         //         text: '{{ __('common.excel') }}',
            //         //         action: function(e, dt, button, config) {
            //         //             var searchValue = $('#datatable-list_filter input').val();
            //         //             var department_id = $('#department_id').val();
            //         //             var unit_id = $('#unit_id').val();
            //         //             var from_date = $('#from_date').val();
            //         //             var to_date = $('#to_date').val();
            //         //             var status = $('#status').val();

            //         //             $(".dt-button").removeClass('processing');
            //         //             $('body').click();
            //         //             window.location.href =
            //         //                 "{{ admin_url('ohc/inventory-tabular-view/export/excel') }}" +
            //         //                 '?search=' + searchValue +
            //         //                 '&department_id=' + department_id +
            //         //                 '&unit_id=' + unit_id +
            //         //                 '&from_date=' + from_date +
            //         //                 '&status=' + status +
            //         //                 '&to_date=' + to_date;
            //         //         }
            //         //     }
            //         // ]
            //     },
                {
                    extend: 'pageLength',
                    text: '{{ __('common.show') }} 10 {{ __('common.records') }}'
                }
            ],
        });
    </script>
@endpush
