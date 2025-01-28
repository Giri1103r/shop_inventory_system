@extends('admin.layouts.admin')
@section('title', 'PPE Shoe Exemption')
@section('pageurl', admin_url('ppe_exemption/list'))
@section('content')


    <div class="container-fluid">
        <div class="row">
            <div class="col-12">

                <div class="card">
                    <h4 class="card-title"></h4>
                    <div class="d-flex justify-content-end p-2 me-2">
                        <x-button-filter dataId="" class="search me-2" href=""></x-button-filter>
                        @if (CheckUserPermission('add'))
                            <a data-id="" class="add btn btn-primary" href="{{ admin_url('ppe_exemption/add') }}">New
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
                                        <div class="col-md-3 mb-3 form-input">
                                            <label for="emp_name" class="form-label ">Emp Name</label>
                                            <select name="emp_name" id="emp_name" class="form-control form-control-sm"
                                                style="width: 100%">
                                                <option value="">Select the Employee Name</option>
                                            </select>
                                        </div>


                                        <div class="col-md-3 mb-3 form-input">
                                            <label for="emp_name" class="form-label ">Unit</label>
                                            <select name="unit" id="unit" style="width: 100%"
                                                class="form-select single-select">

                                                <option value="">Select the unit name</option>
                                                @foreach ($unit as $name)
                                                    <option value="{{ $name->id }}">{{ $name->unit_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3 mb-3 form-input">
                                            <label for="emp_name" class="form-label ">Department</label>
                                            <select name="department" id="department" style="width: 100%"
                                                class="form-select single-select">
                                                <option value="">Select the department name</option>

                                            </select>
                                        </div>
                                        <div class="col-md-3 mb-3 form-input">
                                            <label for="emp_name" class="form-label ">Company</label>
                                            <select name="company" id="company" style="width: 100%"
                                                class="form-select single-select">
                                                <option value="">Select the company name</option>
                                                @foreach ($company as $name)
                                                    <option value="{{ $name->id }}">{{ $name->company_name }}</option>
                                                @endforeach
                                            </select>
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
                                        <div class="col-md-3 mb-3 form-input">
                                            <label for="emp_name" class="form-label ">Approve Status</label>
                                            <select name="approve_status" id="approve_status" style="width: 100%"
                                                class="form-select single-select">
                                                <option value="">Select the approve status</option>
                                                <option value="4">EHS Head Approval Pending</option>
                                                <option value="5">EHS Head Approved</option>
                                                <option value="6">EHS Head Rejected</option>
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
                                        <th>Employee ID</th>
                                        <th>Employee Name</th>
                                        <th>Department</th>
                                        <th>Unit</th>
                                        <th>From Date</th>
                                        <th>To Date</th>
                                        <th>Reason</th>
                                        <th>Status</th>
                                        <th data-priority="2">Approve Status</th>
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
        });

        $('#emp_id').select2({
            ajax: {
                url: '{{ admin_url('safetypermit/employeeid') }}',
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
                                id: item.text,
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

        $('#emp_name').select2({
            ajax: {
                url: '{{ admin_url('safetypermit/employeename') }}',
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

                            var cleanedText = item.text.replace(/ - .*/, '').trim();
                            return {
                                id: cleanedText,
                                text: cleanedText
                            };
                        })
                    };
                }
            },
            minimumInputLength: 1,
            dropdownCssClass: 'form-control',
            selectionCssClass: 'form-control'
        });


        $(document).ready(function() {
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
        });

        $(document).on('change', '#unit', function() {
            var unitId = $(this).val();
            if (unitId) {
                $.ajax({
                    url: "{{ url('ppe_exemption/ajax-list') }}",
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        unitId: unitId,
                    },
                    success: function(data) {
                        $('#department').empty().append(
                            '<option value="">Select Target Department</option>'
                        );
                        $.each(data, function(key, value) {
                            $('#department').append('<option value="' + value
                                .id + '">' + value.department_name + '</option>');
                        });
                        $('#department').trigger('change');
                    },
                    error: function(xhr) {
                        alert('Error fetching department. Please try again.');
                    }
                });
            } else {
                $('#department').empty().append(
                    '<option value="">Select Target Department</option>');
                $('#department').trigger('change');
            }
        });

        $(document).ready(function() {
            /* Datatable */
            var table = $('#datatable-list').DataTable({
                serverSide: true,
                searching: true,
                ordering: true,
                bSort: true,
                scrollX: true,
                autoWidth: true,
                responsive: false,
                dom: 'Bfrtip',
                ajax: {
                    url: "{{ admin_url('ppe_exemption/list') }}",
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: function(d) {
                        d.emp_id = $('#emp_id').val();
                        d.emp_name = $('#emp_name').val();
                        d.department = $('#department').val();
                        d.unit = $('#unit').val();
                        d.company = $('#company').val();
                        d.from_date = $('#from_date').val();
                        d.to_date = $('#to_date').val();
                        d.status = $('#status').val();
                        d.approve_status = $('#approve_status').val();
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
                        data: 'emp_id',
                        name: 'emp_id'
                    },
                    {
                        data: 'emp_name',
                        name: 'emp_name'
                    },
                    {
                        data: 'department',
                        name: 'department'
                    },
                    {
                        data: 'unit',
                        name: 'unit'
                    },
                    {
                        data: 'from_date',
                        name: 'from_date'
                    },
                    {
                        data: 'to_date',
                        name: 'to_date'
                    },
                    {
                        data: 'reason',
                        name: 'reason'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'approve_status',
                        name: 'approve_status'
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
                lengthMenu: [
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
                                    var department = $('#department').val();
                                    var unit = $('#unit').val();
                                    var company = $('#company').val();
                                    var from_date = $('#from_date').val();
                                    var to_date = $('#to_date').val();
                                    status = $('#status').val();
                                    var approve_status = $('#approve_status').val();

                                    $(".dt-button").removeClass('processing');
                                    $('body').click();
                                    window.location.href =
                                        "{{ admin_url('ppe_exemption/export/pdf') }}" +
                                        '?search=' + searchValue +
                                        '&emp_id=' + emp_id +
                                        '&emp_name=' + emp_name +
                                        '&department=' + department +
                                        '&unit=' + unit +
                                        '&company=' + company +
                                        '&from_date=' + from_date +
                                        '&to_date=' + to_date +
                                        '&status=' + status +
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
                                    var department = $('#department').val();
                                    var unit = $('#unit').val();
                                    var company = $('#company').val();
                                    var from_date = $('#from_date').val();
                                    var to_date = $('#to_date').val();
                                    status = $('#status').val();
                                    var approve_status = $('#approve_status').val();

                                    $(".dt-button").removeClass('processing');
                                    $('body').click();
                                    window.location.href =
                                        "{{ admin_url('ppe_exemption/export/excel') }}" +
                                        '?search=' + searchValue +
                                        '&emp_id=' + emp_id +
                                        '&emp_name=' + emp_name +
                                        '&department=' + department +
                                        '&unit=' + unit +
                                        '&company=' + company +
                                        '&from_date=' + from_date +
                                        '&to_date=' + to_date +
                                        '&status=' + status +
                                        '&approve_status=' + approve_status;
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

            table.on('length.dt', function(e, settings, len) {
                var text = '{{ __('common.show') }} ' + len + ' {{ __('common.records') }}';
                $('.buttons-page-length').find('span').text(text);
            });

            $('#searchform').on('click', function() {
                table.draw();
            });

            $('#resetform').on('click', function() {
                $('#emp_id').val('');
                $('#emp_name').val('');
                $('#department').val('');
                $('#unit').val('');
                $('#company').val('');
                $('#from_date').val('');
                $('#to_date').val('');

                $('.single-select').trigger('change');
                table.draw();
            });

            /* Status Change */
            $(document).on('click', '.statusChange', function() {
                var id = $(this).data('id');
                var types = $(this).data('type');
                var title, text, btncolor;

                if (types == 1) {
                    title = '{{ __('Do You want to In-Activate  PPE Exemption ') }}';
                    text = '{{ __('common.inactive') }}';
                    btncolor = '#dc3545';
                } else {
                    title = '{{ __('Do You want to Activate PPE Exemption') }}';
                    text = '{{ __('common.active') }}';
                    btncolor = '#7ddc35';
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
                            url: "{{ admin_url('ppe_exemption/status') }}",
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
                                        toast.addEventListener('mouseenter',
                                            Swal.stopTimer);
                                        toast.addEventListener('mouseleave',
                                            Swal.resumeTimer);
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
                });
            });

            // /* Delete Record */
            $(document).on('click', '.recordDelete', function() {
                var id = $(this).data('id');
                var login_id = $(this).data('login_id');

                var title = '{{ __('Do You want to Delete PPE Exemption') }}';
                var text = '{{ __('common.delete') }}';
                var btncolor = '#dc3545';

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
                            url: "{{ admin_url('ppe_exemption/delete') }}",
                            type: 'post',
                            data: {
                                id: id,
                                login_id: login_id
                            },
                            success: function(response) {
                                const Toast = Swal.mixin({
                                    toast: true,
                                    position: 'top-right',
                                    showConfirmButton: false,
                                    timer: 3000,
                                    timerProgressBar: true,
                                    didOpen: (toast) => {
                                        toast.addEventListener('mouseenter',
                                            Swal.stopTimer);
                                        toast.addEventListener('mouseleave',
                                            Swal.resumeTimer);
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
                });
            });
        });
    </script>
@endpush
