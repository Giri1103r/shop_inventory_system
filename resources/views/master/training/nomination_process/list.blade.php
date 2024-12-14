@extends('admin.layouts.admin')
@section('title', 'Nomination Process')
@section('pageurl', admin_url('nomination_process/list'))


@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">

                <div class="card">
                    <h4 class="card-title"></h4>
                    <div class="d-flex justify-content-end p-2">

                        <x-button-filter dataId="" class="search me-1" href=""></x-button-filter>

                    </div>
                    <div id="search" class="collapse">
                        <form action="" id="formsearch">
                            <div class="card-body">
                                <div class="col-md-12">
                                    <div class="row">

                                        <div class="col-md-3 mb-3 form-input">
                                            <label for="emp_id" class="form-label ">Employee ID</label>
                                            <select name="employee_id" id="emp_id" class=" form-control single-select"
                                                style="width: 100%">
                                                <option value="">Select Employee ID</option>
                                                @foreach ($employeeList as $emp)
                                                    <option value="{{ encryptId($emp->id) }}">
                                                        {{ $emp->emp_id }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3 mb-3 form-input">
                                            <label for="emp_name" class="form-label ">Employee Name</label>
                                            <input type="text" name="emp_name" id="emp_name" placeholder="Employee Name"
                                                class="form-control">
                                        </div>
                                        <div class="col-md-3 mb-3 form-input">
                                            <label for="email" class="form-label ">Email ID</label>
                                            <input type="email" name="email" id="email" placeholder="Email ID"
                                                class="form-control">
                                        </div>
                                        <div class="col-md-3 mb-3 form-input">
                                            <label for="email" class="form-label ">Employee Type</label>
                                            <select name="employee_type" id="employee_type"
                                                class=" form-control single-select" style="width: 100%">
                                                <option value="">Select Employee Type</option>
                                                <option value="Permanent">Permanent</option>
                                                <option value="Probationary">Probationary</option>
                                            </select>
                                        </div>

                                        <div class="col-md-3 mb-3 form-input">
                                            <label for="from_date" class="form-label ">Last training
                                                attended on(Date) </label>
                                            <input type="text" name ="last_training_attended_on"
                                                id="last_training_attended_on_datepicker" class="form-control"
                                                placeholder="From Date">
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
                                        <div class="col-md-3 mb-3 d-flex align-items-end gap-2">
                                            <x-button-search class="me-2"></x-button-search>
                                            <x-button-reset class="ms-1"></x-button-reset>
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
                                        <th>Employee ID</th>
                                        <th>Employee Name</th>
                                        <th>Email ID</th>
                                        <th>Department</th>
                                        <th>Employee Type</th>
                                        <th>Last training attended on (Date)</th>
                                        <th>Last Training Attended on (Topic)</th>
                                        <th>{{ __('common.status') }}</th>
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


    @stop

    @push('script')
        <script type="text/javascript">
            $(document).ready(function() {
                var firstTh = $('.datatable-list thead th:first');
                firstTh.removeClass('sorting_asc');
            });
            flatpickr("#last_training_attended_on_datepicker", {
                dateFormat: "d-m-Y",
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
                        url: "{{ admin_url('nomination_process/list') }}",
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]')
                                .attr('content')
                        },
                        data: function(d) {
                            d.emp_id = $('#emp_id').val();
                            d.emp_name = $('#emp_name').val();
                            d.email = $('#email').val();
                            d.department_id = $('#department_id').val();
                            d.employee_type = $('#employee_type').val();
                            d.last_training_attended_on = $('#last_training_attended_on_datepicker').val();
                            d.topic_id = $('#topic_id').val();
                            d.status = $('#status').val();
                        }

                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            orderable: false,
                            searchable: true,
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
                            data: 'email',
                            name: 'email'
                        },
                        {
                            data: 'department_name',
                            name: 'department_name'
                        },
                        {
                            data: 'employee_type',
                            name: 'employee_type'
                        },
                        {
                            data: 'last_training_attended_on',
                            name: 'last_training_attended_on'
                        },
                        {
                            data: 'topic_name',
                            name: 'topic_name'
                        },
                        {
                            data: 'status',
                            name: 'status'
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
                                        var searchValue = $('#datatable-list_filter input').val();
                                        emp_id = $('#emp_id').val();
                                        emp_name = $('#emp_name').val();
                                        email = $('#email').val();
                                        employee_type = $('#employee_type').val();
                                        last_training_attended_on = $(
                                            '#last_training_attended_on_datepicker').val();
                                        status = $('#status').val();

                                        $(".dt-button").removeClass('processing');
                                        $('body').click();
                                        window.location.href =
                                            "{{ admin_url('nomination_process/export/pdf') }}" +
                                            '?search=' + searchValue +
                                            '&emp_id=' + emp_id +
                                            '&emp_name=' + emp_name +
                                            '&email=' + email +
                                            '&employee_type=' + employee_type +
                                            '&last_training_attended_on=' +
                                            last_training_attended_on +
                                            '&status=' + status
                                    }
                                },
                                {
                                    extend: 'excel',
                                    text: '{{ __('common.excel') }}',
                                    action: function(e, dt, button, config) {
                                        var searchValue = $('#datatable-list_filter input').val();
                                        emp_id = $('#emp_id').val();
                                        emp_name = $('#emp_name').val();
                                        email = $('#email').val();
                                        employee_type = $('#employee_type').val();
                                        last_training_attended_on = $(
                                            '#last_training_attended_on_datepicker').val();
                                        status = $('#status').val();
                                        $(".dt-button").removeClass('processing');
                                        $('body').click();
                                        window.location.href =
                                            "{{ admin_url('nomination_process/export/excel') }}" +
                                            '?search=' + searchValue +
                                            '&emp_id=' + emp_id +
                                            '&emp_name=' + emp_name +
                                            '&email=' + email +
                                            '&employee_type=' + employee_type +
                                            '&last_training_attended_on=' +
                                            last_training_attended_on +
                                            '&status=' + status
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
                        var title = '{{ __('Do You want to In-Activate Nomination Process') }}';
                        var text = '{{ __('common.inactive') }}';
                        var btncolor = '#dc3545'

                    } else {
                        var title = '{{ __('Do You want to Activate Nomination Process') }}';
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
                                url: "{{ admin_url('nomination_process/status') }}",
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


                /* Delete Record */
                $(document).on('click', '.recordDelete', function() {

                    var id = $(this).data('id');
                    var login_id = $(this).data('login_id');

                    var title = '{{ __('Do You want to Delete Nomination Process') }}';
                    var text = '{{ __('common.delete') }}';
                    var btncolor = '#dc3545'

                    Swal.fire({
                        title: title,
                        icon: 'warning',
                        showDenyButton: false,
                        showCancelButton: true,
                        confirmButtonText: text,
                        confirmButtonColor: btncolor,
                        denyButtonColor: '#28a745',
                        customClass: {
                            confirmButton: 'btn-skew',
                            cancelButton: 'btn-skew'
                        },
                    }).then((result) => {

                        if (result.value) {
                            $.ajax({
                                url: "{{ admin_url('nomination_process/delete') }}",
                                type: 'post',
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]')
                                        .attr('content')
                                },
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
                                    if (data.status === 406 && data.responseJSON.msg ===
                                        'module_exits') {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Error',
                                            text: 'Nomination Process Deletion Failed: Module Dependencies Exist.',
                                        });
                                    } else {
                                        $.notify(data.responseJSON.msg, "error");
                                    }
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
