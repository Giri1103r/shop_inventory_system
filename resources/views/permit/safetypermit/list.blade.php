@extends('admin.layouts.admin')
@section('title', 'Safety Permit')
@section('pageurl', admin_url('safetypermit/list'))
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">

                <div class="card">
                    <h4 class="card-title"></h4>
                    <div class="d-flex justify-content-end p-2 me-2">
                        <x-button-filter dataId="" class="search me-2" href=""></x-button-filter>
                        @if (CheckUserPermission('add'))
                            <x-button-add dataId="" class="add btn btn-primary"
                                href="{{ admin_url('safetypermit/add') }}">Add</x-button-add>
                        @endif
                    </div>


                    <div id="search" class="collapse">
                        <form action="" id="formsearch">
                            <div class="card-body">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-3 mb-3 form-input">
                                            <label for="permit_id" class="form-label ">Work Permit No</label>
                                            <input type="text" name="permit_id" id="permit_id" class="form-control"
                                                placeholder="Work Permit No">
                                        </div>
                                        <div class="col-md-3 mb-3 form-input">
                                            <label for="inspectiontype" class="form-label ">Unit</label>
                                            <select name="unit_id" id="unit_id" class=" form-control single-select"
                                                style="width: 100%">
                                                <option value="">Select Unit</option>
                                                @foreach ($unitList as $unit)
                                                    <option value="{{ encryptId($unit->id) }}">
                                                        {{ $unit->unit_name }}</option>
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
                                            <label for="inspectiontype" class="form-label ">Status</label>
                                            <select name="status" id="status" class=" form-control single-select"
                                                style="width: 100%">
                                                <option value="">Select Status</option>
                                                @foreach ($status as $status)
                                                    <option value="{{ encryptId($status->id) }}">
                                                        {{ $status->status_name }}</option>
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
                                        <th>Work Permit No</th>
                                        <th>Unit</th>
                                        <th>Date</th>
                                        <th>Exact Job Location</th>
                                        <th>Approve Status</th>
                                        <th>Verified By</th>
                                        <th>Approved By</th>
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
        $(document).ready(function() {
            var firstTh = $('.datatable-list thead th:first');
            firstTh.removeClass('sorting_asc');
        });
        $(document).ready(function() {
            var fromDatepicker = flatpickr("#from_date", {
                dateFormat: "Y-m-d",
                onChange: function(selectedDates) {
                    if (selectedDates.length > 0) {
                        var startDate = selectedDates[0];
                        toDatepicker.set('minDate', startDate);
                        toDatepicker.clear();
                    }
                }
            });

            var toDatepicker = flatpickr("#to_date", {
                dateFormat: "Y-m-d",
                minDate: "today"
            });
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
                    url: "{{ admin_url('safetypermit/list') }}",
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]')
                            .attr('content')
                    },
                    data: function(d) {
                        d.permit_id = $('#permit_id').val();
                        d.unit_id = $('#unit_id').val();
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
                        data: 'permit_id',
                        name: 'permit_id'
                    },
                    {
                        data: 'unit_name',
                        name: 'unit_name'
                    },
                    {
                        data: 'date',
                        name: 'date'
                    },
                    {
                        data: 'exact_location_job',
                        name: 'exact_location_job'
                    },
                    {
                        data: 'status_batch',
                        name: 'status_batch'
                    },
                    {
                        data: 'verified_by',
                        name: 'verified_by'
                    },
                    {
                        data: 'approved_by',
                        name: 'approved_by'
                    },

                    {
                        data: 'created_by',
                        name: 'created_by'
                    },
                    {
                        data: 'created_date',
                        name: 'created_date'
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
                                    permit_id = $('#permit_id').val();
                                    unit_id = $('#unit_id').val();
                                    from_date = $('#from_date').val();
                                    to_date = $('#to_date').val();
                                    status = $('#status').val();

                                    $(".dt-button").removeClass('processing');
                                    $('body').click();
                                    window.location.href =
                                        "{{ admin_url('safetypermit/export/pdf') }}" +
                                        '?search=' + searchValue +
                                        '&permit_id=' + permit_id +
                                        '&unit_id=' + unit_id +
                                        '&from_date=' + from_date +
                                        '&to_date=' + to_date +
                                        '&status=' + status

                                }
                            },
                            {
                                extend: 'excel',
                                text: '{{ __('common.excel') }}',
                                action: function(e, dt, button, config) {
                                    var searchValue = $('#datatable-list_filter input').val();
                                    permit_id = $('#permit_id').val();
                                    unit_id = $('#unit_id').val();
                                    from_date = $('#from_date').val();
                                    to_date = $('#to_date').val();
                                    status = $('#status').val();

                                    $(".dt-button").removeClass('processing');
                                    $('body').click();
                                    window.location.href =
                                        "{{ admin_url('safetypermit/export/excel') }}" +
                                        '?search=' + searchValue +
                                        '&permit_id=' + permit_id +
                                        '&unit_id=' + unit_id +
                                        '&from_date=' + from_date +
                                        '&to_date=' + to_date +
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
                var permit_id = $('#permit_id').val();
                var unit_id = $('#unit_id').val();
                var ppeStatus = $('#ppe_status').val();
                table.draw();
            });

            $(document).on('click', '#resetform', function() {
                $('#permit_id').val('');
                $('#unit_id').val('');
                $('#formsearch .single-select').trigger('change');
                setTimeout(function() {
                    table.draw();
                }, 150);
            });


            /* Status Change */
            $(document).on('click', '.statusChange', function() {
                var id = $(this).data('id');
                var types = $(this).data('type');
                var title, text, btncolor;

                if (types == 1) {
                    title = '{{ __('Do You want to In-Activate  Safety Permit ') }}';
                    text = '{{ __('common.inactive') }}';
                    btncolor = '#dc3545';
                } else {
                    title = '{{ __('Do You want to Activate Safety Permit') }}';
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
                            url: "{{ admin_url('safetypermit/status') }}",
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

            /* Delete Record */
            $(document).on('click', '.recordDelete', function() {
                var id = $(this).data('id');
                var login_id = $(this).data('login_id');

                var title = '{{ __('Do You want to Cancel Safety Permit?') }}';
                var text = '{{ __('Cancel') }}';
                var btncolor = '#28a745';

                Swal.fire({
                    title: title,
                    icon: 'warning',
                    input: 'textarea', // Add a textarea for remarks
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
                        return remarks; // Return the input value
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        var remarks = result.value;

                        // Proceed with AJAX request
                        $.ajax({
                            url: "{{ admin_url('safetypermit/delete') }}",
                            type: 'post',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data: {
                                id: id,
                                login_id: login_id,
                                remarks: remarks // Pass remarks to the server
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
                        Swal.fire('{{ __('Action Cancelled') }}', '', 'info');
                    }
                });
            });


            $(document).on('click', '.permitClose', function() {
                var id = $(this).data('id');
                var login_id = $(this).data('login_id');

                var title = '{{ __('Do You want to Close Safety Permit') }}';
                var text = '{{ __('Close') }}';
                var btncolor = '#28a745';

                Swal.fire({
                    title: title,
                    icon: 'warning',
                    input: 'textarea', // Add a textarea for remarks
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
                        return remarks; // Return the input value
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        var remarks = result.value;

                        // Proceed with AJAX request
                        $.ajax({
                            url: "{{ admin_url('safetypermit/close') }}",
                            type: 'post',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data: {
                                id: id,
                                login_id: login_id,
                                remarks: remarks // Pass remarks to the server
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
