@extends('admin.layouts.admin')
@section('title', 'PPE Request')
@section('pageurl', admin_url('ppe_request/list'))
@section('content')
    @push('style')
        <style>
            .table-responsive {
                overflow-x: auto;
                width: 100%
            }
        </style>
    @endpush
    @php

        $dash_unit_id =
            isset($dashboard_search['unit']) && $dashboard_search['unit'] != ''
                ? $dashboard_search['unit']
                : '';


    @endphp
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">

                <div class="card">
                    <h4 class="card-title"></h4>
                    <div class="d-flex justify-content-end p-2 me-2">
                        <x-button-filter dataId="" class="search me-2" href=""></x-button-filter>


                        @if (CheckUserPermission('add'))
                            <a data-id="" class="add btn btn-primary" href="{{ admin_url('ppe_request/add') }}">New
                                Request</a>
                        @endif

                    </div>


                    <div id="search" class="collapse">
                        <form action="" id="formsearch">
                            <div class="card-body">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-3 mb-3 form-input">
                                            <label for="emp_id" class="form-label ">Emp Id</label>
                                            <select name="emp_id" id="emp_id" class="form-control form-control-sm"
                                                style="width: 100%">
                                                <option value="">Select the Employee ID</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label require">Employee Name</label>
                                                <input type="text" name="emp_name" id="emp_name" class="form-control"
                                                    placeholder="Employee Name">
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-3 form-input">
                                            <label for="company_id" class="form-label">Company</label>
                                            <select name="company_id" id="company_id"
                                                class="form-control single-select form-control-sm" style="width: 100%">
                                                <option value="">Select the company</option>
                                                @if (CheckUserRole(ROLE_EHS_OFFICER) || checkUserRole(ROLE_EHS_HEAD) || checkUserRole(ROLE_STORE_MANAGER))
                                                    @foreach ($company as $list)
                                                        @php
                                                            $isEhs = in_array(auth()->user()->role, [
                                                                ROLE_EHS_HEAD,
                                                                ROLE_EHS_OFFICER,
                                                                ROLE_STORE_MANAGER,
                                                            ]);
                                                            $value = encryptId($list->id);
                                                            $requestCompanyId = $loggedInCompanyId;
                                                            $selected = '';

                                                            if ($requestCompanyId) {
                                                                $selected =
                                                                    $requestCompanyId == $value ? 'selected' : '';
                                                            } elseif (isset($loggedInCompanyId)) {
                                                                $selected =
                                                                    $loggedInCompanyId == $value ? 'selected' : '';
                                                            }
                                                        @endphp
                                                        <option value="{{ $value }}" {{ $selected }}>
                                                            {{ $list->company_name }}
                                                        </option>
                                                    @endforeach
                                                @else
                                                    @foreach ($company as $list)
                                                        <option value="{{ encryptId($list->id) }}">
                                                            {{ $list->company_name }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>




                                        <div class="col-md-3 mb-3 form-input">
                                            <label for="location_id" class="form-label ">Location</label>
                                            <select name="location_id" id="location_id"
                                                class="form-control single-select form-control-sm" style="width: 100%">
                                                <option value="">Select the Location</option>

                                            </select>
                                        </div>

                                        @if ($dash_unit_id != '')
                                            <div class="col-md-3 mb-3 form-input">
                                                <label for="inspectiontype" class="form-label ">Unit</label>
                                                <select name="unit_id" id="unit_id" class=" form-control single-select"
                                                    style="width: 100%">
                                                    <option value="">Select Unit</option>
                                                    @foreach ($unit as $units)
                                                        <option @if ($dash_unit_id == $units->id) selected @endif
                                                            value="{{ encryptId($units->id) }}">
                                                            {{ $units->unit_name }}</option>
                                                    @endforeach

                                                </select>
                                            </div>
                                        @else
                                            <div class="col-md-3 mb-3 form-input">
                                                <label for="unit_id" class="form-label">Unit</label>
                                                <select name="unit_id" id="unit_id"
                                                    class="form-control single-select form-control-sm" style="width: 100%">
                                                    <option value="">Select the Unit</option>

                                                </select>
                                            </div>
                                        @endif

                                        <div class="col-md-3 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label require">Department</label>
                                                <select name="department_id" id="department_id"
                                                    class="form-control single-select form-control-sm" style="width: 100%">
                                                    <option value="">Select the department</option>
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
                                            <label for="emp_name" class="form-label ">Approve Status</label>
                                            <select name="approve_status" id="approve_status" style="width: 100%"
                                                class="form-select single-select">
                                                <option value="">Select the approve status</option>
                                                <option value="1">HOD Approval Pending</option>
                                                <option value="2">HOD Approved</option>
                                                <option value="3">HOD Rejected</option>
                                                <option value="4">EHS Officer Approval Pending</option>
                                                <option value="5">Store manager Issue pending</option>
                                                <option value="6">EHS Officer Rejected</option>
                                                <option value="8">Issued</option>

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
                                        <th>Employee / Worker ID</th>
                                        <th>Employee / Worker Name</th>
                                        <th>Item Code</th>
                                        <th>PPE Name</th>
                                        <th>Company</th>
                                        <th>Location</th>
                                        <th>Unit</th>
                                        <th>Department</th>
                                        <th data-priority="2">Approval Status</th>
                                        <th>{{ __('common.created_by') }}</th>
                                        <th>{{ __('common.created_date') }}</th>
                                        <th data-priority="1">{{ __('common.action') }}</th>
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
        $(document).on('change', '#company_id', function() {
            var companyId = $(this).val();
            if (companyId) {
                $.ajax({
                    url: "{{ admin_url('location/ajax-list') }}/" + companyId + "/0",
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        $('#location_id').empty().append(
                            '<option value="">Select Location</option>');
                        $.each(data, function(key, value) {
                            $('#location_id').append('<option value="' + value
                                .id + '">' + value.name + '</option>');
                        });
                        $('#location_id').trigger('change.');
                    },
                    error: function(xhr) {
                        alert('Error fetching location. Please try again.');
                    }
                });
            } else {
                $('#location_id').empty().append('<option value="">Select Location</option>');
                $('#location_id').trigger('change.');
            }
        });
        // location

        $(document).on('change', '#location_id', function() {
            var locationId = $(this).val();
            if (locationId) {
                $.ajax({
                    url: "{{ admin_url('unit/ajax-list') }}/" + locationId + "/0",
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        $('#unit_id').empty().append(
                            '<option value="">Select unit</option>');
                        $.each(data, function(key, value) {
                            $('#unit_id').append('<option value="' + value
                                .id + '">' + value.name + '</option>');
                        });
                        $('#unit_id').trigger('change.');
                    },
                    error: function(xhr) {
                        alert('Error fetching unit. Please try again.');
                    }
                });
            } else {
                $('#unit_id').empty().append('<option value="">Select unit</option>');
                $('#unit_id').trigger('change.');
            }
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
                        alert('Error fetching unit. Please try again.');
                    }
                });
            } else {
                $('#department_id').empty().append('<option value="">Select Department</option>');
                $('#department_id').trigger('change.');
            }
        });
        $(document).ready(function() {

            $('#resetform').on('click', function(e) {
                e.preventDefault();
                location.reload();
            });

            $('#emp_id').select2({
                ajax: {
                    url: '{{ admin_url('ohc/employee-cum-patient/employeeid') }}',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            search: params.term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: $.map(data, function(item) {
                                return {
                                    id: item.id,
                                    text: item.text
                                };
                            })
                        };
                    }
                },
                minimumInputLength: 1,
                dropdownCssClass: 'form-control',
                selectionCssClass: 'form-control'
            });
            $(document).on('change', '#emp_id', function() {
                var empId = $(this).val();
                if (empId) {
                    $.ajax({
                        url: "{{ admin_url('ohc/employee-cum-patient/employeename') }}",
                        type: 'GET',
                        data: {
                            empId: empId
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.employee) {
                                $('#emp_name').val(response.employee.emp_name).prop('readonly',
                                    true);

                            } else {
                                $('#emp_name').val('').prop('readonly', true);
                            }
                        },
                        error: function(xhr) {
                            alert('Error fetching employee name. Please try again.');
                        }
                    });
                } else {
                    $('#emp_name').val('').prop('readonly', true);
                }
            });


            var firstTh = $('.datatable-list thead th:first');
            firstTh.removeClass('sorting_asc');


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
            var dashboard_unit =
                '{{ isset($dashboard_search['unit']) && $dashboard_search['unit'] != '' ? $dashboard_search['unit'] : '' }}';
            // Initialize DataTable
            var table = $('.datatable-list').DataTable({


                serverSide: true,
                searching: true,
                ordering: true,
                bSort: true,
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
                    url: "{{ admin_url('ppe_request/list') }}",
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: function(d) {
                        d.emp_id = $('#emp_id').val();
                        d.emp_name = $('#emp_name').val();
                        d.from_date = $('#from_date').val();
                        d.unit_id = $('#unit_id').val();
                        d.company_id = $('#company_id').val();
                        d.location_id = $('#location_id').val();
                        d.department_id = $('#department_id').val();
                        d.to_date = $('#to_date').val();
                        d.approve_status = $('#approve_status').val();

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
                        data: 'emp_id',
                        name: 'emp_id'
                    },
                    {
                        data: 'emp_name',
                        name: 'emp_name'
                    },
                    {
                        data: 'item_code',
                        name: 'item_code'
                    },
                    {
                        data: 'ppe_name',
                        name: 'ppe_name'
                    },
                    {
                        data: 'company_id',
                        name: 'company_id'
                    },
                    {
                        data: 'location_id',
                        name: 'location_id'
                    },
                    {
                        data: 'unit_id',
                        name: 'unit_id'
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
                                    var unit_id = $('#unit_id').val();
                                    var company_id = $('#company_id').val();
                                    var location_id = $('#location_id').val();
                                    var department_id = $('#department_id').val();
                                    var from_date = $('#from_date').val();
                                    var to_date = $('#to_date').val();
                                    var approve_status = $('#approve_status').val();

                                    $(".dt-button").removeClass('processing');
                                    $('body').click();
                                    window.location.href =
                                        "{{ admin_url('ppe_request/export/pdf') }}" +
                                        '?search=' + searchValue +
                                        '&emp_id=' + emp_id +
                                        '&emp_name=' + emp_name +
                                        '&unit_id=' + unit_id +
                                        '&company_id=' + company_id +
                                        '&location_id=' + location_id +
                                        '&department_id=' + department_id +
                                        '&from_date=' + from_date +

                                        '&to_date=' + to_date +
                                        '&approve_status=' + approve_status;
                                }
                            },
                            {
                                extend: 'excel',
                                text: '{{ __('common.excel') }}',
                                action: function(e, dt, button, config) {
                                    var searchValue = $('#datatable-list_filter input').val();
                                    var emp_id = $('#emp_id').val();
                                    var emp_name = $('#emp_name').val();
                                    var unit_id = $('#unit_id').val();
                                    var company_id = $('#company_id').val();
                                    var location_id = $('#location_id').val();
                                    var department_id = $('#department_id').val();
                                    var from_date = $('#from_date').val();
                                    var to_date = $('#to_date').val();

                                    var approve_status = $('#approve_status').val();

                                    $(".dt-button").removeClass('processing');
                                    $('body').click();
                                    window.location.href =
                                        "{{ admin_url('ppe_request/export/excel') }}" +
                                        '?search=' + searchValue +
                                        '&emp_id=' + emp_id +

                                        '&emp_name=' + emp_name +
                                        '&unit_id=' + unit_id +
                                        '&company_id=' + company_id +
                                        '&location_id=' + location_id +
                                        '&department_id=' + department_id +
                                        '&from_date=' + from_date +
                                        '&to_date=' + to_date +
                                        '&approve_status=' + approve_status;
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
