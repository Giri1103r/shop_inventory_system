@extends('admin.layouts.admin')
@section('title', 'PPE Shoe Request')
@section('pageurl', admin_url('ppe_request/list'))
@section('content')

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">

                <div class="card">
                    <h4 class="card-title"></h4>
                    <div class="d-flex justify-content-end p-2 me-2">
                        <x-button-filter dataId="" class="search me-2" href=""></x-button-filter>


                        <x-button-add dataId="" class="add btn btn-primary"
                            href="{{ admin_url('ppe_request/add') }}">Add</x-button-add>


                    </div>


                    <div id="search" class="collapse">
                        <form action="" id="formsearch">
                            <div class="card-body">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-3 mb-3 form-input">
                                            <label for="emp_id" class="form-label ">Employee ID</label>
                                            <input type="text" name="emp_id" id="emp_id" class="form-control ">
                                        </div>
                                        <div class="col-md-3 mb-3 form-input">
                                            <label for="emp_name" class="form-label ">Employee Name</label>
                                            <input type="text" name="emp_name" id="emp_name" class="form-control ">
                                        </div>

                                        <div class="col-md-3 mb-3 form-input">
                                            <label for="emp_name" class="form-label ">From Date</label>
                                            <div class="input-group date form-input custom-height">
                                                <input type="text" class="form-control " name="from_date" id="from_date"
                                                    autocomplete="off">
                                                <div class="input-group-addon input-group-text">
                                                    <span class="fa fa-calendar"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-3 form-input">
                                            <label for="emp_name" class="form-label ">To Date</label>
                                            <div class="input-group date form-input  custom-height">
                                                <input type="text" class="form-control " name="to_date" id="to_date"
                                                    autocomplete="off">
                                                <div class="input-group-addon input-group-text">
                                                    <span class="fa fa-calendar"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 mt-3">
                                            <x-button-search></x-button-search>
                                            <x-button-reset></x-button-reset>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <hr>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="datatable-list"
                                class="table primary-table-bordered table-bordered table-striped display responsive nowrap w-100 mt-2 datatable-list">
                                <thead class="thead-primary">
                                    <tr>
                                        <th>{{ __('common.sno') }}</th>
                                        <th>Emp Id</th>
                                        <th>Emp Name</th>
                                        <th>PPE Name</th>
                                        <th>PPE Type</th>
                                        <th>Department</th>
                                        <th>Approval Status</th>
                                        <th>{{ __('common.created_by') }}</th>
                                        <th>{{ __('common.created_date') }}</th>
                                        <th>{{ __('common.action') }}</th>
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
            // Reset form
            $('#resetform').on('click', function(e) {
                e.preventDefault();
                location.reload();
            });

            // Remove sorting class from first table header
            var firstTh = $('.datatable-list thead th:first');
            firstTh.removeClass('sorting_asc');

            // Initialize flatpickr for date pickers
            var fromDatepicker = flatpickr("#from_date", {
                dateFormat: "d-m-Y",
                onChange: function(selectedDates) {
                    if (selectedDates.length > 0) {
                        var startDate = selectedDates[0];
                        toDatepicker.set('minDate', startDate);
                        toDatepicker.clear();
                    }
                }
            });

            var toDatepicker = flatpickr("#to_date", {
                dateFormat: "d-m-Y",
                minDate: "today"
            });

            // Initialize DataTable
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
                    url: "{{ admin_url('ppe_request/list') }}",
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: function(d) {
                        d.emp_id = $('#emp_id').val();
                        d.emp_name = $('#emp_name').val();
                        d.from_date = $('#from_date').val();
                        d.to_date = $('#to_date').val();
                        d.ppe_status = $('#ppe_status').val();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'emp_id',
                        name: 'emp_id'
                    },
                    {
                        data: 'emp_name',
                        name: 'emp_name'
                    },
                    {
                        data: 'ppe_name',
                        name: 'ppe_name'
                    },
                    {
                        data: 'ppe_type',
                        name: 'ppe_type'
                    },
                    {
                        data: 'department',
                        name: 'department'
                    },
                    {
                        data: 'approve_status',
                        name: 'approve_status'
                    },
                    {
                        data: 'created_by',
                        name: 'created_by'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false
                    }
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
                buttons: [{
                        extend: 'collection',
                        text: '{{ __('common.export') }}',
                        buttons: [{
                                extend: 'pdf',
                                text: '{{ __('common.pdf') }}',
                                action: function(e, dt, button, config) {
                                    var searchValue = $('#datatable-list_filter input').val();
                                    var emp_id = $('#emp_id').val();
                                    var emp_name = $('#emp_name').val();
                                    var from_date = $('#from_date').val();
                                    var to_date = $('#to_date').val();
                                    var ppe_status = $('#ppe_status').val();

                                    $(".dt-button").removeClass('processing');
                                    $('body').click();
                                    window.location.href =
                                        "{{ admin_url('ppe_request/export/pdf') }}" +
                                        '?search=' + searchValue +
                                        '&emp_id=' + emp_id +
                                        '&emp_name=' + emp_name +
                                        '&from_date=' + from_date +
                                        '&to_date=' + to_date +
                                        '&ppe_status=' + ppe_status;
                                }
                            },
                            {
                                extend: 'excel',
                                text: '{{ __('common.excel') }}',
                                action: function(e, dt, button, config) {
                                    var searchValue = $('#datatable-list_filter input').val();
                                    var emp_id = $('#emp_id').val();
                                    var emp_name = $('#emp_name').val();
                                    var from_date = $('#from_date').val();
                                    var to_date = $('#to_date').val();
                                    var ppe_status = $('#ppe_status').val();

                                    $(".dt-button").removeClass('processing');
                                    $('body').click();
                                    window.location.href =
                                        "{{ admin_url('ppe_request/export/excel') }}" +
                                        '?search=' + searchValue +
                                        '&emp_id=' + emp_id +
                                        '&emp_name=' + emp_name +
                                        '&from_date=' + from_date +
                                        '&to_date=' + to_date +
                                        '&ppe_status=' + ppe_status;
                                }
                            }
                        ]
                    },
                    {
                        extend: 'pageLength',
                        text: '{{ __('common.show') }} 10 {{ __('common.records') }}'
                    }
                ]
            });

            // Change event for length selection
            table.on('length.dt', function(e, settings, len) {
                var text = '{{ __('common.show') }} ' + len + ' {{ __('common.records') }}';
                $('.buttons-page-length').find('span').text(text);
            });

            // Search form submit event
            $('#searchform').on('click', function() {
                table.draw();
            });

            // Reset form submit event
            $('#resetform').on('click', function() {
                $('#emp_id').val('');
                $('#emp_name').val('');
                $('#from_date').val('');
                $('#to_date').val('');
                $('#ppe_status').val('');
                table.draw();
            });
        });
    </script>
@endpush
