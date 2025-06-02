@extends('admin.layouts.admin')
@section('title', 'First Aid')
@section('pageurl', admin_url('ohc/first-aid/list'))


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
                            href="{{ admin_url('ohc/first-aid/add') }}">Add</x-button-add>
                        {{-- @endif --}}

                    </div>

                    <div id="search" class="collapse">
                        <form action="" id="formsearch">
                            <div class="card-body">
                                <div class="col-md-12">
                                    <div class="row">

                                        <div class="col-md-3 mb-3 form-input">
                                            <label for="emp_id" class="form-label ">Emp Id</label>
                                             <input type="text" class="form-control" name="emp_id" id="emp_id">

                                        </div>
                                        <div class="col-md-3 mb-3 form-input">
                                            <label for="emp_id" class="form-label ">Emp Name</label>
  <input type="text" class="form-control" name="emp_name" id="emp_name">
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

                                        <th>Worker/Employee Name</th>
                                        <th>Worker/Employee Code</th>
                                        <th>Date of Incident</th>
                                        <th>Time Of Incident</th>
                                        <th>Treatment Start Time</th>
                                        <th>Treatment End Time</th>
                                        <th>First Aider Name</th>
                                        <th>Medicine Name</th>
                                        <th>Treatment Provided</th>
                                        <th data-priority='3'>{{ __('common.status') }}</th>
                                        <th data-priority='2'>{{ __('common.created_by') }}</th>
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
            $('#resetform').on('click', function(e) {
                e.preventDefault();
                location.reload();
            });
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
                    url: "{{ admin_url('ohc/first-aid/list') }}",
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: function(d) {
                        d.emp_name = $('#emp_name').val();
                        d.from_date = $('#from_date').val();
                        d.to_date = $('#to_date').val();
                        d.emp_id = $('#emp_id').val();
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
                        data: 'emp_id',
                        name: 'emp_id'
                    },
                    {
                        data: 'emp_name',
                        name: 'emp_name'
                    },
                    {
                        data: 'date_of_incident',
                        name: 'date_of_incident'
                    },
                    {
                        data: 'time_of_incident',
                        name: 'time_of_incident'
                    },
                    {
                        data: 'treatment_start_time',
                        name: 'treatment_start_time'
                    },
                    {
                        data: 'treatment_end_time',
                        name: 'treatment_end_time'
                    },
                    {
                        data: 'first_aider_name',
                        name: 'first_aider_name'
                    },
                    {
                        data: 'medicine_id',
                        name: 'medicine_id'
                    },
                    {
                        data: 'treatment_provided',
                        name: 'treatment_provided'
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
                                    var emp_id = $('#emp_id').val();
                                    var status = $('#status').val();
                                    window.location.href =
                                        "{{ admin_url('ohc/first-aid/export/pdf') }}?search=" +
                                        searchValue +
                                        '&emp_name=' + emp_name +
                                        '&from_date=' + from_date +
                                        '&to_date=' + to_date +
                                        '&emp_id=' + emp_id +
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
                                    var emp_id = $('#emp_id').val();
                                    var status = $('#status').val();
                                    window.location.href =
                                        "{{ admin_url('ohc/first-aid/export/excel') }}?search=" +
                                        searchValue +
                                        '&emp_name=' + emp_name +
                                        '&from_date=' + from_date +
                                        '&to_date=' + to_date +
                                        '&emp_id=' + emp_id +
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

            /* Status Change */
            $(document).on('click', '.statusChange', function() {
                var id = $(this).data('id');
                var types = $(this).data('type');
                if (types == 1) {
                    var title = '{{ __('Do You want to In-Activate First Aid') }}';
                    var text = '{{ __('common.inactive') }}';
                    var btncolor = '#dc3545'

                } else {
                    var title = '{{ __('Do You want to Activate First Aid') }}';
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
                            url: "{{ admin_url('ohc/first-aid/status') }}",
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
