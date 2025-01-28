@extends('admin.layouts.admin')
@section('title', 'Medicine Issuance')
@section('pageurl', admin_url('ohc/medicine-issuance/list'))
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
                        <x-button-filter dataId="" class="search me-2" href=""></x-button-filter>

                        {{-- @if (CheckUserPermission('add')) --}}
                        <x-button-add dataId="" class="add btn btn-primary"
                            href="{{ admin_url('ohc/prescribe-to-patient/add') }}">Add</x-button-add>
                        {{-- @endif --}}

                    </div>

                    <div id="search" class="collapse">
                        <form action="" id="formsearch">
                            <div class="card-body">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-3 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label ">Unit</label>
                                                <select name="unit_id" id="unit_id" class="form-control single-select"
                                                    style="width: 100%">
                                                    <option value="">Select the unit</option>
                                                    @foreach ($unit as $list)
                                                        <option value="{{ encryptId($list->id) }}">
                                                            {{ $list->unit_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label ">Department</label>
                                                <select name="department_id" id="department_id"
                                                    class=" form-control single-select" style="width: 100%">
                                                    <option value="">Select Department </option>

                                                </select>
                                            </div>
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
                                        <div class="col-md-3 mb-3 form-input">
                                            <label for="status" class="form-label">{{ __('common.status') }}</label>
                                            <select name="status" id="status" style="width: 100%"
                                                class="form-control single-select">
                                                <option value="">Select Status</option>
                                                <option value="{{ encryptId(1) }}">Active</option>
                                                <option value="{{ encryptId(0) }}">In-Active</option>
                                            </select>
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
                            class="table primary-table-bordered table-bordered table-striped  nowrap w-100 mt-2 datatable-list">
                                <thead class="thead-primary">
                                    <tr>
                                        <th>{{ __('common.sno') }}</th>
                                        <th>Unit Name</th>
                                        <th>Department Name</th>
                                        <th>Issue Date</th>
                                        <th data-priority="1">Action</th>
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

            $('#resetform').on('click', function(e) {
                e.preventDefault();
                location.reload();
            });

            $(document).on('change', '#unit_id', function() {
                var unitId = $(this).val();
                if (unitId) {
                    $.ajax({
                        url: "{{ admin_url('department/ajax-list') }}/" + unitId + "/0",
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            $('#department_id').empty().append(
                                '<option value="">Select Department</option>');
                            $.each(data, function(key, value) {
                                $('#department_id').append('<option value="' + value
                                    .id + '">' + value.name + '</option>');
                            });
                            $('#department_id').trigger('change.');
                        },
                        error: function(xhr) {
                            alert('Error fetching department. Please try again.');
                        }
                    });
                } else {
                    $('#department_id').empty().append('<option value="">Select Department</option>');
                    $('#department_id').trigger('change.');
                }
            });

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
            $('#resetform').on('click', function(e) {
                e.preventDefault();
                $('#unit_id').val('');
                $('#department_id').val('');
                $('#from_date').val('');
                $('#to_date').val('');
                $('#datatable-list').DataTable().draw();
            });
        });

        var table = $('.datatable-list').DataTable({
            serverSide: true,
            searching: true,
            ordering: true,
            bSort: false,
            scrollX: true,
            autoWidth: false,
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
                url: "{{ admin_url('ohc/medicine-issuance/list') }}",
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: function(d) {
                    d.unit_id = $('#unit_id').val();
                    d.department_id = $('#department_id').val();
                    d.from_date = $('#from_date').val();
                    d.to_date = $('#to_date').val();
                    d.status = $('#status').val();
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
                    data: 'unit_id',
                    name: 'unit_id'
                },
                {
                    data: 'department_id',
                    name: 'department_id'
                },
                {
                    data: 'issue_date',
                    name: 'issue_date'
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
                                var department_id = $('#department_id').val();
                                var unit_id = $('#unit_id').val();
                                var from_date = $('#from_date').val();
                                var to_date = $('#to_date').val();
                                var status = $('#status').val();
                                $(".dt-button").removeClass('processing');
                                $('body').click();
                                window.location.href =
                                    "{{ admin_url('ohc/medicine-issuance/export/pdf') }}" +
                                    '?search=' + searchValue +
                                    '&department_id=' + department_id +
                                    '&unit_id=' + unit_id +
                                    '&from_date=' + from_date +
                                    '&status=' + status +
                                    '&to_date=' + to_date;
                            }
                        },
                        {
                            extend: 'excel',
                            text: '{{ __('common.excel') }}',
                            action: function(e, dt, button, config) {
                                var searchValue = $('#datatable-list_filter input').val();
                                var department_id = $('#department_id').val();
                                var unit_id = $('#unit_id').val();
                                var from_date = $('#from_date').val();
                                var to_date = $('#to_date').val();
                                var status = $('#status').val();

                                $(".dt-button").removeClass('processing');
                                $('body').click();
                                window.location.href =
                                    "{{ admin_url('ohc/medicine-issuance/export/excel') }}" +
                                    '?search=' + searchValue +
                                    '&department_id=' + department_id +
                                    '&unit_id=' + unit_id +
                                    '&from_date=' + from_date +
                                    '&status=' + status +
                                    '&to_date=' + to_date;
                            }
                        }
                    ]
                },
                {
                    extend: 'pageLength',
                    text: '{{ __('common.show') }} 10 {{ __('common.records') }}'
                }
            ],
        });
    </script>
@endpush
