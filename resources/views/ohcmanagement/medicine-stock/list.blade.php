@extends('admin.layouts.admin')
@section('title', 'Employee Master')
@section('pageurl', admin_url('employee/list'))


@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">

                <div class="card">
                    <h4 class="card-title"></h4>
                    <div class="align-add-btc" style="margin-left: 90%;">

                        <x-button-filter dataId="" class="search" href=""></x-button-filter>
                    </div>
                    <div id="search" class="collapse">
                        <form action="" id="formsearch">
                            <div class="card-body">
                                <div class="col-md-12">
                                    <div class="row">

                                        <div class="col-md-3 mb-3 form-input">
                                            <label for="emp_id" class="form-label ">Employee ID</label>
                                            <input type="text" name="emp_id" id="emp_id" class="form-control"
                                                placeholder="Employee ID">
                                        </div>
                                        <div class="col-md-3 mb-3 form-input">
                                            <label for="emp_name" class="form-label ">Employee Name</label>
                                            <input type="text" name="emp_name" id="emp_name" class="form-control"
                                                placeholder="Employee Name">
                                        </div>
                                        <div class="col-md-3 mb-3 form-input">
                                            <label for="email" class="form-label ">Employee Email</label>
                                            <input type="text" name="email" id="email" class="form-control"
                                                placeholder="Employee Email">
                                        </div>
                                        <div class="col-md-3 mb-3 form-input">
                                            <label for="employee_status" class="form-label ">Employee Status</label>
                                            <input type="text" name="employee_status" id="employee_status"
                                                class="form-control" placeholder="Employee Status">
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label ">Company Name</label>
                                                <select name="company_id" id="company_id" class="form-control single-select"
                                                    style="width: 100%">
                                                    <option value="">Select Company Name</option>

                                                    {{-- @foreach ($companyList as $list)
                                                        <option value="{{ encryptId($list->id) }}">
                                                            {{ $list->company_name }}
                                                        </option>
                                                    @endforeach --}}


                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label ">Unit Name</label>

                                                <select name="unit_id" id="unit_id" class="form-control single-select"
                                                    style="width: 100%">
                                                    <option value="">Select the unit</option>
                                                    {{-- @foreach ($unit as $list)
                                                        <option value="{{ $list->id }}">
                                                            {{ $list->unit_name }}</option>
                                                    @endforeach --}}
                                                </select>


                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label ">Department Name</label>
                                                <select name="department_id" id="department_id"
                                                    class=" form-control single-select" style="width: 100%">
                                                    <option value="">Select Department Name</option>

                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-3 form-input">
                                            <label for="status" class="form-label ">{{ __('common.status') }}</label>
                                            <select name="status" id="status" style="width: 100%"
                                                class="form-control single-select">
                                                <option value="">Select Status</option>
                                                <option value="{{ encryptId(1) }}">Active</option>
                                                <option value="{{ encryptId(0) }}">In-Active</option>
                                            </select>
                                        </div>

                                        <div class="col-md-12 d-flex justify-content-end gap-2 mt-3">
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
                                        <th>Medicine Name</th>
                                        <th>Quantity</th>
                                        <th>Unit</th>
                                        <th>Threshold Limit</th>
                                        <th>{{ __('common.status') }}</th>
                                        <th>{{ __('common.created_date') }}</th>
                                        <th>{{ __('common.created_by') }}</th>
                                        <th data-priority='1'>{{ __('common.action') }}</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>


    @stop

    @push('script')
        <script type="text/javascript" nonce="projectcab">
            $(document).ready(function() {
                var firstTh = $('.datatable-list thead th:first');
                firstTh.removeClass('sorting_asc');
            });
            $(document).on('change', '#company_id', function() {
                var company_id = $(this).val();

                if (company_id) {
                    $.ajax({
                        url: "{{ admin_url('employee/company-ajax') }}",
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            company_id: company_id,
                            _token: '{{ csrf_token() }}' // Include CSRF token
                        },
                        success: function(data) {
                            // Populate the unit dropdown
                            var unitOptions = '<option value="">Select Unit Name</option>';
                            $.each(data.unit, function(index, unit) {
                                unitOptions +=
                                    `<option value="${unit.id}">${unit.unit_name}</option>`;
                            });
                            $('#unit_id').html(unitOptions);
                        },
                        error: function(xhr) {
                            alert('Error fetching units. Please try again.');
                        }
                    });
                } else {
                    $('#unit_id').empty().append('<option value="">Select Unit Name</option>');
                }
            });

            $(document).on('change', '#unit_id', function() {
                var unitId = $(this).val();

                if (unitId) {
                    $.ajax({
                        url: "{{ admin_url('employee/ajax-list') }}/" + unitId + "/0",
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            console.log(data);
                            var departmentOptions = '<option value="">Select Department</option>';
                            $.each(data, function(index, department) {
                                departmentOptions +=
                                    `<option value="${department.id}">${department.department_name}</option>`;
                            });
                            $('#department_id').html(departmentOptions);
                        },
                        error: function(xhr) {
                            alert('Error fetching departments. Please try again.');
                        }
                    });
                } else {
                    $('#department_id').empty().append('<option value="">Select Department</option>');
                }
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
                        url: "{{ admin_url('ohc/medicine-stock-inventory/list') }}",
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]')
                                .attr('content')
                        },
                        data: function(d) {
                            d.company_id = $('#company_id').val();
                            d.unit_id = $('#unit_id').val();
                            d.department_id = $('#department_id').val();
                            d.emp_id = $('#emp_id').val();
                            d.emp_name = $('#emp_name').val();
                            d.email = $('#email').val();
                            d.employee_status = $('#employee_status').val();
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
                            data: 'medicine_id',
                            name: 'medicine_id'
                        },
                        {
                            data: 'quantity',
                            name: 'quantity'
                        },
                        {
                            data: 'unit_id',
                            name: 'unit_id'
                        },
                        {
                            data: 'threshold_limit',
                            name: 'threshold_limit'
                        },

                        {
                            data: 'stock_status',
                            name: 'stock_status'
                        },

                        {
                            data: 'created_at',
                            name: 'created_at'
                        },
                        {
                            data: 'created_by',
                            name: 'created_by'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
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
                            buttons: [{
                                    extend: 'pdf',
                                    text: '{{ __('common.pdf') }}',
                                    action: function(e, dt, button, config) {
                                        var searchValue = $('#datatable-list_filter input')
                                            .val();
                                        emp_id = $('#emp_id').val();
                                        emp_name = $('#emp_name').val();
                                        email = $('#email').val();
                                        employee_status = $('#employee_status').val();
                                        unit_id = $('#unit_id').val();
                                        company_id = $('#company_id').val();
                                        department_id = $('#department_id').val();

                                        status = $('#status').val();
                                        $(".dt-button").removeClass('processing');
                                        $('body').click();
                                        window.location.href =
                                            "{{ admin_url('employee/export/pdf') }}" +
                                            '?search=' + searchValue +
                                            '&emp_id=' + emp_id +
                                            '&emp_name=' + emp_name +
                                            '&email=' + email +
                                            '&company_id=' + company_id +
                                            '&department_id=' + department_id +
                                            '&unit_id=' + unit_id +

                                            '&employee_status=' + employee_status +
                                            '&status=' + status
                                    } // Closing brace for action function
                                },
                                {
                                    extend: 'excel',
                                    text: '{{ __('common.excel') }}',
                                    action: function(e, dt, button, config) {
                                        var searchValue = $('#datatable-list_filter input')
                                            .val();
                                        emp_id = $('#emp_id').val();
                                        emp_name = $('#emp_name').val();
                                        email = $('#email').val();
                                        employee_status = $('#employee_status').val();
                                        status = $('#status').val();
                                        unit_id = $('#unit_id').val();
                                        company_id = $('#company_id').val();
                                        department_id = $('#department_id').val();
                                        $(".dt-button").removeClass('processing');
                                        $('body').click();
                                        window.location.href =
                                            "{{ admin_url('employee/export/excel') }}" +
                                            '?search=' + searchValue +
                                            '&emp_id=' + emp_id +
                                            '&emp_name=' + emp_name +
                                            '&email=' + email +
                                            '&employee_status=' + employee_status +
                                            '&company_id=' + company_id +
                                            '&department_id=' + department_id +
                                            '&unit_id=' + unit_id +
                                            '&status=' + status
                                    } // Closing brace for action function
                                }
                            ]
                        },
                        {
                            "extend": 'pageLength',
                            "text": '{{ __('common.show') }} 10 {{ __('common.records') }}'
                        }
                    ]


                });

                table.on('length.dt', function(e, settings, len) {
                    var text = '{{ __('common.show') }} ' + len + ' {{ __('common.records') }}';
                    $('.buttons-page-length').find('span').text(text);
                });

                $(document).on('click', '#searchform', function() {

                    table.draw();
                });

                $(document).on('click', '#resetform', function() {
                    $('#formsearch .single-select').val('');
                    $('#formsearch .single-select').trigger('change');
                    setTimeout(function() {
                        table.draw();
                    }, 150);
                });

                /* Status Change */
                $(document).on('click', '.statusChange', function() {
                    var id = $(this).data('id');
                    var types = $(this).data('type');
                    if (types == 1) {
                    var title = '{{ __('Do You want to In-Activate Medicine Stock Details') }}';
                    var text = '{{ __('common.inactive') }}';
                    var btncolor = '#dc3545'

                } else {
                    var title = '{{ __('Do You want to Activate Medicine Stock Details') }}';
                    var text = '{{ __('common.active') }}';
                    var btncolor = '#7ddc35'
                }

                    Swal.fire({
                        title: title,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: text,
                        confirmButtonColor: btncolor,
                        customClass: {
                            confirmButton: 'btn-skew',
                            cancelButton: 'btn-skew'
                        },
                    }).then((result) => {


                        if (result.value) {
                            $.ajax({
                                url: "{{ admin_url('ohc/medicine-stock-inventory/status') }}",
                                type: 'post',

                                data: {
                                    id: id,
                                    types: types
                                },
                                success: function(response) {
                                    const Toast = Swal.mixin({
                                        toast: true,
                                        position: 'top-right',
                                        showConfirmButton: false,
                                        timer: 3000,
                                        timerProgressBar: true,
                                        didOpen: (toast) => {
                                            toast.addEventListener(
                                                'mouseenter',
                                                Swal.stopTimer)
                                            toast.addEventListener(
                                                'mouseleave',
                                                Swal.resumeTimer
                                            )
                                        }
                                    });
                                    Toast.fire({
                                        icon: 'success',
                                        title: response.msg
                                    });
                                    table.draw();
                                },
                                error: function(data) {
                                    $.notify(data.responseJSON.msg, "error");
                                }
                            });
                        } else if (result.isDenied) {
                            Swal.fire('Something went wrong', '', 'info');
                        }
                    })

                });
            });
        </script>
    @endpush
