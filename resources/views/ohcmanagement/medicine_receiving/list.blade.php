@extends('admin.layouts.admin')
@section('title', 'Medicine Receiving')
@section('pageurl', admin_url('ohc/medicine-receiving-form/list'))
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
                            href="{{ admin_url('ohc/medicine-receiving-form/add') }}">Add</x-button-add>
                        {{-- @endif --}}

                    </div>

                    <div id="search" class="collapse">
                        <form action="" id="formsearch">
                            <div class="card-body">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-3 mb-3 form-input">
                                            <label for="medicine_name" class="form-label ">Medicine Name</label>
                                            <select name="medicine_id" id="medicine_id"
                                                class="form-control single-select form-control-sm" style="width: 100%">
                                                <option value="">Select the Medicine Name</option>
                                                @foreach ($medicine as $list)
                                                    <option value="{{ $list->id }}">{{ $list->medicine }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3 mb-3 form-input">
                                            <label for="vendor_name" class="form-label ">Vendor Name</label>
                                            <select name="vendor_id" id="vendor_id"
                                                class="form-control single-select form-control-sm" style="width: 100%">
                                                <option value="">Select the Vendor Name</option>
                                                @foreach ($vendor as $list)
                                                    <option value="{{ $list->id }}">{{ $list->vendor_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-3 mb-3 form-input">
                                            <label for="emp_name" class="form-label ">Expire Date</label>
                                            <div class="input-group date form-input custom-height">
                                                <input type="text" class="form-control " name="expire_date"
                                                    id="expire_date" autocomplete="off">
                                                <div class="input-group-addon input-group-text">
                                                    <span class="fa fa-calendar"></span>
                                                </div>
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
                                            <label for="vendor_name" class="form-label ">Approve Status</label>
                                            <select name="approve_status" id="approve_status"
                                                class="form-control single-select form-control-sm" style="width: 100%">
                                                <option value="">Select the approve status</option>
                                                <option value="2">EHS Officer verification Pending</option>
                                                <option value="5">L1 EHS verification Pending</option>
                                                <option value="8">EHS Head Approval Pending</option>
                                                <option value="11">Open</option>
                                                <option value="12">Close</option>

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
                                        <th>Medicine Name</th>
                                        <th>Vendor Name</th>
                                        <th>HSN Number</th>
                                        <th>Pack</th>
                                        <th>Quantity</th>
                                        <th>Batch Number</th>
                                        <th>Rate</th>
                                        <th>Expire Date</th>
                                        <th>Approve Status</th>
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
            $('#expire_date').flatpickr({
                dateFormat: "d-m-Y",
            })
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
                $('#medicine_id').val('');
                $('#vendor_id').val('');
                $('#from_date').val('');
                $('#to_date').val('');
                $('#datatable-list').DataTable().draw();
            });
        });

        var table = $('.datatable-list').DataTable({
            autoWidth: false,
            responsive: false,
            processing: false,
            serverSide: true,
            searching: true,
            scrollX: true,
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
                url: "{{ admin_url('ohc/medicine-receiving-form/list') }}",
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: function(d) {
                    d.medicine_id = $('#medicine_id').val();
                    d.vendor_id = $('#vendor_id').val();
                    d.from_date = $('#from_date').val();
                    d.to_date = $('#to_date').val();
                    d.expire_date = $('#expire_date').val();
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
                    data: 'medicine_id',
                    name: 'medicine_id'
                },
                {
                    data: 'vendor_id',
                    name: 'vendor_id'
                },
                {
                    data: 'hsn_id',
                    name: 'hsn_id'
                },
                {
                    data: 'pack_id',
                    name: 'pack_id'
                },
                {
                    data: 'quantity',
                    name: 'quantity'
                },
                {
                    data: 'batch_number',
                    name: 'batch_number'
                },
                {
                    data: 'rate',
                    name: 'rate'
                },
                {
                    data: 'expire_date',
                    name: 'expire_date'
                },
                {
                    data: 'approvedStatus',
                    name: 'approvedStatus'
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
                                var medicine_id = $('#medicine_id').val();
                                var vendor_id = $('#vendor_id').val();
                                var expire_date = $('#expire_date').val();

                                var from_date = $('#from_date').val();
                                var to_date = $('#to_date').val();
                                var approve_status = $('#approve_status').val();


                                $(".dt-button").removeClass('processing');
                                $('body').click();
                                window.location.href =
                                    "{{ admin_url('ohc/medicine-receiving-form/export/pdf') }}" +
                                    '?search=' + searchValue +
                                    '&medicine_id=' + medicine_id +
                                    '&vendor_id=' + vendor_id +
                                    '&expire_date=' + expire_date +
                                    '&from_date=' + from_date +
                                    '&approve_status=' + approve_status +
                                    '&to_date=' + to_date;
                            }
                        },
                        {
                            extend: 'excel',
                            text: '{{ __('common.excel') }}',
                            action: function(e, dt, button, config) {
                                var searchValue = $('#datatable-list_filter input').val();
                                var medicine_id = $('#medicine_id').val();
                                var vendor_id = $('#vendor_id').val();
                                var expire_date = $('#expire_date').val();
                                var from_date = $('#from_date').val();
                                var approve_status = $('#approve_status').val();
                                var to_date = $('#to_date').val();


                                $(".dt-button").removeClass('processing');
                                $('body').click();
                                window.location.href =
                                    "{{ admin_url('ohc/medicine-receiving-form/export/excel') }}" +
                                    '?search=' + searchValue +
                                    '&medicine_id=' + medicine_id +
                                    '&vendor_id=' + vendor_id +
                                    '&expire_date=' + expire_date +
                                    '&from_date=' + from_date +
                                    '&approve_status=' + approve_status +
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

        $(document).on('click', '.stockClose', function() {
            var id = $(this).data('id');
            var login_id = $(this).data('login_id');

            var title = "Do you want to close the stock request?";
            var text = "Close";
            var btncolor = "#28a745";

            Swal.fire({
                title: title,
                icon: "warning",
                input: "textarea",
                inputPlaceholder: "Enter your remarks here...",
                showCloseButton: true,
                confirmButtonText: text,
                confirmButtonColor: btncolor,
                customClass: {
                    confirmButton: "btn-skew"
                },
                preConfirm: (remarks) => {
                    if (!remarks || remarks.trim() === "") {
                        Swal.showValidationMessage("Remarks are required!");
                        return false;
                    }
                    return remarks.trim();
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    var remarks = result.value;

                    // Proceed with AJAX request
                    $.ajax({
                        url: "{{ url('ohc/medicine-receiving-form/close') }}",
                        type: "POST",
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                        },
                        data: {
                            id: id,
                            login_id: login_id,
                            remarks: remarks
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: "success",
                                title: response.msg ||
                                    "Stock request closed successfully!",
                                toast: true,
                                position: "top-right",
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true
                            });
                            table.draw();
                        },
                        error: function(xhr) {
                            let errorMessage = "Something went wrong!";

                            // Check if responseJSON exists and contains a 'msg'
                            if (xhr.responseJSON && xhr.responseJSON.msg) {
                                errorMessage = xhr.responseJSON.msg;
                            } else if (xhr.status === 419) {
                                errorMessage = "Session expired. Please refresh and try again.";
                            } else if (xhr.status === 500) {
                                errorMessage =
                                    "Internal Server Error. Please check the server logs.";
                            }

                            Swal.fire({
                                icon: "error",
                                title: "Error",
                                text: errorMessage
                            });
                        }
                    });
                }
            });
        });
    </script>
@endpush
