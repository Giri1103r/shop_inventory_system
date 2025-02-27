@extends('admin.layouts.admin')
@section('title', 'Prescribe To Patient')
@section('pageurl', admin_url('ohc/prescribe-to-patient/list'))


@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">

                <div class="card">
                    <h4 class="card-title"></h4>
                    <div class="d-flex justify-content-end p-2">

                        <x-button-filter dataId="" class="search me-1" href=""></x-button-filter>



                        {{-- @if (CheckUserPermission('add')) --}}
                        <x-button-add dataId="" class="add btn btn-primary ms-1"
                            href="{{ admin_url('ohc/prescribe-to-patient/add') }}">Add</x-button-add>
                        {{-- @endif --}}

                    </div>

                    <div id="search" class="collapse">
                        <form action="" id="formsearch">
                            <div class="card-body">
                                <div class="col-md-12">
                                    <div class="row">

                                        <div class="col-md-3 mb-3 form-input">
                                            <label for="emp_name" class="form-label ">Emp Name</label>
                                            <input type="text" name="emp_name" id="emp_name" class="form-control form-control-sm">


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
                                            <label for="status" class="form-label">{{ __('Patient Status') }}</label>
                                            <select name="status" id="status" style="width: 100%"
                                                class="form-control single-select">
                                                <option value="">Select Status</option>
                                                @foreach ($patientstatus as $list)
                                                    <option value="{{ $list->id }}">
                                                        {{ $list->patient_status }}
                                                    </option>
                                                @endforeach
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

                                        <th>Employee Name</th>
                                        <th>Problem</th>
                                        <th>Gender</th>
                                        <th>Unit</th>
                                        <th>Department</th>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Suggested By</th>
                                        <th>Treatment</th>
                                        <th>Checkup</th>
                                        <th>Status</th>
                                        <th>Fitness Certificate</th>
                                        <th>Created By</th>
                                        <th>Cancel Remarks</th>
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
    </div>


@stop

@push('script')
    <script type="text/javascript">
        $(document).ready(function() {
            var firstTh = $('.datatable-list thead th:first');
            firstTh.removeClass('sorting_asc');
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

            });
        });

        // $('#emp_name').select2({
        //     ajax: {
        //         url: '{{ admin_url('ohc/prescribe-to-patient/employeename') }}',
        //         dataType: 'json',
        //         delay: 250,
        //         data: function(params) {
        //             return {
        //                 search: params.term
        //             };
        //         },
        //         processResults: function(data) {
        //             return {
        //                 results: $.map(data, function(item) {
        //                     return {
        //                         id: item.id,
        //                         text: item.text
        //                     };
        //                 })
        //             };
        //         }
        //     },
        //     minimumInputLength: 1,
        //     dropdownCssClass: 'form-control',
        //     selectionCssClass: 'form-control'
        // });
        $(function() {
            /* Initialize DataTable */
            var table = $('.datatable-list').DataTable({
                autoWidth: true,
                responsive: false,
                processing: true,
                serverSide: true,
                searching: true,
                ordering: true,
                scrollX: true,

                dom: 'Bfrtip',
                ajax: {
                    url: "{{ admin_url('ohc/prescribe-to-patient/list') }}",
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: function(d) {
                        d.emp_name = $('#emp_name').val();
                        d.from_date = $('#from_date').val();
                        d.to_date = $('#to_date').val();
                        d.status = $('#status').val();

                    },
                    error: function(xhr) {
                        if (xhr.status === 419) {
                            alert('Session has expired. Redirecting to login.');
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
                        data: 'emp_name',
                        name: 'emp_name'
                    },
                    {
                        data: 'cheif_complaint',
                        name: 'cheif_complaint'
                    },
                    {
                        data: 'gender',
                        name: 'gender'
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
                        data: 'date',
                        name: 'date'
                    },
                    {
                        data: 'time',
                        name: 'time'
                    },
                    {
                        data: 'suggested_by',
                        name: 'suggested_by'
                    },
                    {
                        data: 'treatment',
                        name: 'treatment'
                    },
                    {
                        data: 'vital_checkup',
                        name: 'vital_checkup'
                    },

                    {
                        data: 'patient_status',
                        name: 'patient_status'
                    },
                    {
                        data: 'fitness_certificate',
                        name: 'fitness_certificate'
                    },
                    {
                        data: 'created_by',
                        name: 'created_by'
                    },
                    {
                        data: 'cancel_remarks',
                        name: 'cancel_remarks'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false
                    }
                ],
                language: {
                    paginate: {
                        first: '<i class="fa fa-angle-double-left"></i>',
                        last: '<i class="fa fa-angle-double-right"></i>',
                        next: '<i class="fa fa-angle-right"></i>',
                        previous: '<i class="fa fa-angle-left"></i>'
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
                                action: function() {
                                    var searchValue = $('.dataTables_filter input').val();

                                    var emp_name = $('#emp_name').val();
                                    var from_date = $('#from_date').val();
                                    var to_date = $('#to_date').val();
                                    var status = $('#status').val();
                                    window.location.href =
                                        "{{ admin_url('ohc/prescribe-to-patient/export/pdf') }}?search=" +
                                        searchValue +
                                        '&emp_name=' + emp_name +
                                        '&from_date=' + from_date +
                                        '&to_date=' + to_date +
                                        '&status=' + status
                                }
                            },
                            {
                                extend: 'excel',
                                text: '{{ __('common.excel') }}',
                                action: function() {
                                    var searchValue = $('.dataTables_filter input').val();
                                    var emp_name = $('#emp_name').val();
                                    var from_date = $('#from_date').val();
                                    var to_date = $('#to_date').val();
                                    var status = $('#status').val();
                                    window.location.href =
                                        "{{ admin_url('ohc/prescribe-to-patient/export/excel') }}?search=" +
                                        searchValue +
                                        '&emp_name=' + emp_name +
                                        '&from_date=' + from_date +
                                        '&to_date=' + to_date +
                                        '&status=' + status
                                }
                            }
                        ]
                    },
                    {
                        extend: 'pageLength',
                        text: '{{ __('common.show') }}'
                    }
                ]
            });

            function showToast(icon, message) {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-right',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
                Toast.fire({
                    icon: icon,
                    title: message
                });
            }
            /* Custom Search */
            $(document).on('click', '#searchform', function() {
                table.draw();
            });

            $(document).on('click', '#resetform', function() {
                $('#formsearch input, #formsearch select').val('').trigger('change');
                table.draw();
            });

            $(document).on('click', '.Close', function() {
                var id = $(this).data('id');
                var login_id = $(this).data('login_id');

                var title = '{{ __('Do You want to Cancel the OPD Patient list') }}';
                var text = '{{ __('Submit') }}';
                var btncolor = '#28a745';

                Swal.fire({
                    title: title,
                    icon: 'warning',
                    input: 'textarea',
                    inputPlaceholder: '{{ __('Enter your remarks here...') }}',
                    showCloseButton: true,
                    confirmButtonText: text,
                    confirmButtonColor: btncolor,
                    customClass: {
                        confirmButton: 'btn-skew'
                    },
                    preConfirm: (remarks) => {
                        if (!remarks) {
                            Swal.showValidationMessage('{{ __('Remarks are required!') }}');
                        }
                        return remarks;
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        var remarks = result.value;


                        $.ajax({
                            url: "{{ admin_url('ohc/prescribe-to-patient/close') }}",
                            type: 'post',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data: {
                                id: id,
                                login_id: login_id,
                                remarks: remarks
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
                                if (data.status === 406 && data.responseJSON.msg ===
                                    'module_exits') {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: 'Location Deletion Failed: Module Dependencies Exist.',
                                    });
                                } else {
                                    $.notify(data.responseJSON.msg, "error");
                                }
                            }
                        });
                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                        Swal.fire('{{ __('Action Closed') }}', '', 'info');
                    }
                });
            });

        });
    </script>
@endpush
