@extends('admin.layouts.admin')
@section('title', 'Road Side First Aid')
@section('pageurl', admin_url('ohc/roadside-first-aid/list'))


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
                            href="{{ admin_url('ohc/roadside-first-aid/add') }}">Add</x-button-add>
                        {{-- @endif --}}

                    </div>

                    <div id="search" class="collapse">
                        <form action="" id="formsearch">
                            <div class="card-body">
                                <div class="col-md-12">
                                    <div class="row">

                                        <div class="col-md-3 mb-3 form-input">
                                            <label for="emp_name" class="form-label ">Injured Person Name</label>
                                            <input type="text" name="emp_name" class="form-control" id="emp_name">
                                        </div>

                                        <div class="col-md-3 mb-3 form-input">
                                            <label for="location_of_incident" class="form-label ">Location of
                                                Incident</label>
                                            <input type="text" name="location_of_incident" class="form-control"
                                                id="location_of_incident">
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

                                        <th>Injured Person Name</th>
                                        <th>Location of Incident</th>
                                        <th>Date of Incident</th>
                                        <th>Time Of Incident</th>
                                        <th>Person Condition</th>
                                        <th>First Aider Name</th>
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
                autoWidth: false,
                responsive: true,
                processing: true,
                serverSide: true,
                searching: true,
                ordering: true,
                dom: 'Bfrtip',
                ajax: {
                    url: "{{ admin_url('ohc/roadside-first-aid/list') }}",
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: function(d) {
                        d.emp_name = $('#emp_name').val();
                        d.from_date = $('#from_date').val();
                        d.to_date = $('#to_date').val();
                        d.status = $('#status').val();
                        d.location_of_incident = $('#location_of_incident').val();

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
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'location_of_incident',
                        name: 'location_of_incident'
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
                        data: 'person_condtion',
                        name: 'person_condtion'
                    },
                    {
                        data: 'first_aider_name',
                        name: 'first_aider_name'
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
                                    var status = $('#status').val();
                                    var location_of_incident = $('#location_of_incident').val();

                                    window.location.href =
                                        "{{ admin_url('ohc/roadside-first-aid/export/pdf') }}?search=" +
                                        searchValue +
                                        '&emp_name=' + emp_name +
                                        '&from_date=' + from_date +
                                        '&to_date=' + to_date +
                                        '&location_of_incident=' + location_of_incident +
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
                                    var location_of_incident = $('#location_of_incident').val();
                                    var status = $('#status').val();
                                    window.location.href =
                                        "{{ admin_url('ohc/roadside-first-aid/export/excel') }}?search=" +
                                        searchValue +
                                        '&emp_name=' + emp_name +
                                        '&from_date=' + from_date +
                                        '&location_of_incident=' + location_of_incident +
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

            /* Status Change */
            $(document).on('click', '.statusChange', function() {
                var id = $(this).data('id');
                var types = $(this).data('type');
                if (types == 1) {
                    var title = '{{ __('Do You want to In-Activate Road Side First Aid') }}';
                    var text = '{{ __('common.inactive') }}';
                    var btncolor = '#dc3545'

                } else {
                    var title = '{{ __('Do You want to Activate Road Side First Aid') }}';
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
                            url: "{{ admin_url('ohc/roadside-first-aid/status') }}",
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
