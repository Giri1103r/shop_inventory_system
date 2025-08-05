@extends('admin.layouts.admin')
@section('title', 'Medicine')
@section('pageurl', admin_url('ohc/medicine/list'))


@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">

                <div class="card">
                    <h4 class="card-title"></h4>
                    <div class="d-flex justify-content-end p-2">

                        <x-button-filter dataId="" class="search me-1" href=""></x-button-filter>

                        {{-- @if (CheckUserPermission('import')) --}}
                            <x-button-import href="{{ admin_url('ohc/medicine/import') }}"></x-button-import>
                        {{-- @endif --}}

                        {{-- @if (CheckUserPermission('add')) --}}
                        <x-button-add dataId="" class="add btn btn-primary ms-1"
                            href="{{ admin_url('ohc/medicine/add') }}">Add</x-button-add>
                        {{-- @endif --}}
                        {{-- @if (CheckUserPermission('import')) --}}
                        {{-- <x-button-import href="{{ admin_url('ohc/medicine/import') }}"></x-button-import> --}}
                        {{-- @endif --}}

                    </div>

                    <div id="search" class="collapse">
                        <form action="" id="formsearch">
                            <div class="card-body">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-3 mb-3 form-input">
                                            <label for="medicine"
                                                class="form-label">{{ __('ohc_management.medicine_name') }}</label>
                                            <input type="text" name="medicine" id="medicine" class="form-control"
                                                placeholder="Medicine Name">
                                        </div>

                                        <div class="col-md-3 mb-3 form-input">
                                            <label for="emp_name" class="form-label ">{{ __('common.from_date') }}</label>
                                            <div class="input-group date form-input custom-height">
                                                <input type="text" class="form-control " name="from_date" id="from_date"
                                                    autocomplete="off">
                                                <div class="input-group-addon input-group-text">
                                                    <span class="fa fa-calendar"></span>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="col-md-3 mb-3 form-input">
                                            <label for="emp_name" class="form-label ">{{ __('common.to_date') }}</label>
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
                                            <label for="status"
                                                class="form-label">{{ __('ohc_management.approve_status') }}</label>
                                            <select name="approve_status" id="approve_status" style="width: 100%"
                                                class="form-control single-select">
                                                <option value="">Select Status</option>
                                                <option value="{{ encryptId(2) }}">EHS Head Approval Pending</option>
                                                <option value="{{ encryptId(3) }}">EHS Head Approved</option>
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
                                        <th>{{ __('ohc_management.medicine_name') }}</th>
                                        <th>{{ __('ohc_management.pack') }}</th>
                                        <th>{{ __('ohc_management.threshold_limit') }}</th>
                                        <th>{{ __('common.status') }}</th>
                                        <th>{{ __('ohc_management.remarks') }}</th>
                                        <th>{{ __('ohc_management.approve_status') }}</th>
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
            $('#expire_date').flatpickr({
                dateFormat: "d-m-Y",
            })
        });


        $(function() {
            /* Datatable */
            var table = $('.datatable-list').DataTable({
                autoWidth: true,
                responsive: false,
                processing: false,
                serverSide: true,
                searching: true,
                ordering: true,
                scrollX: true,
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
                    url: "{{ admin_url('ohc/medicine/list') }}",
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]')
                            .attr('content')
                    },
                    data: function(d) {
                        d.medicine = $('#medicine').val();
                        d.unit = $('#unit').val();
                        d.from_date = $('#from_date').val();
                        d.to_date = $('#to_date').val();
                        d.status = $('#status').val();
                        d.approve_status = $('#approve_status').val();
                        d.expire_date = $('#expire_date').val();

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
                        searchable: true,
                    },
                    {
                        data: 'medicine',
                        name: 'medicine'
                    },
                    {
                        data: 'pack',
                        name: 'pack'
                    },

                    {
                        data: 'threshold_limit',
                        name: 'threshold_limit'
                    },

                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'remarks',
                        name: 'remarks'
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
                                    var medicine = $('#medicine').val();
                                    var unit = $('#unit').val();
                                    var from_date = $('#from_date').val();
                                    var to_date = $('#to_date').val();
                                    var status = $('#status').val();
                                    var approve_status = $('#approve_status').val();
                                    var expire_date = $('#expire_date').val();

                                    $(".dt-button").removeClass('processing');
                                    $('body').click();
                                    window.location.href =
                                        "{{ admin_url('ohc/medicine/export/pdf') }}" +
                                        '?search=' + searchValue +
                                        '&medicine=' + medicine +
                                        '&unit=' + unit +
                                        '&expire_date=' + expire_date +
                                        '&from_date=' + from_date +
                                        '&to_date=' + to_date +
                                        '&approve_status=' + approve_status +
                                        '&status=' + status
                                }
                            },
                            {
                                extend: 'excel',
                                text: '{{ __('common.excel') }}',
                                action: function(e, dt, button, config) {
                                    var searchValue = $('#datatable-list_filter input').val();
                                    var medicine = $('#medicine').val();
                                    var unit = $('#unit').val();
                                    var expire_date = $('#expire_date').val();
                                    var from_date = $('#from_date').val();
                                    var to_date = $('#to_date').val();
                                    var approve_status = $('#approve_status').val();

                                    var status = $('#status').val();
                                    $(".dt-button").removeClass('processing');
                                    $('body').click();
                                    window.location.href =
                                        "{{ admin_url('ohc/medicine/export/excel') }}" +
                                        '?search=' + searchValue +
                                        '&medicine=' + medicine +
                                        '&unit=' + unit +
                                        '&expire_date=' + expire_date +
                                        '&from_date=' + from_date +
                                        '&approve_status=' + approve_status +
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
            // $(document).on('click', '.statusChange', function() {
            //     var id = $(this).data('id');
            //     var types = $(this).data('type');
            //     if (types == 1) {
            //         var title = '{{ __('Do You want to In-Activate Medicine Details') }}';
            //         var text = '{{ __('common.inactive') }}';
            //         var btncolor = '#dc3545'

            //     } else {
            //         var title = '{{ __('Do You want to Activate Medicine Details') }}';
            //         var text = '{{ __('common.active') }}';
            //         var btncolor = '#7ddc35'
            //     }

            //     Swal.fire({
            //         title: title,
            //         icon: 'warning',
            //         showCancelButton: true,
            //         confirmButtonText: text,
            //         confirmButtonColor: btncolor,
            //         customClass: {
            //             confirmButton: 'btn-skew',
            //             cancelButton: 'btn-skew'
            //         },
            //     }).then((result) => {


            //         if (result.value) {
            //             $.ajax({
            //                 url: "{{ admin_url('ohc/medicine/status') }}",
            //                 type: 'post',

            //                 data: {
            //                     id: id,
            //                     types: types
            //                 },
            //                 success: function(response) {
            //                     const Toast = Swal.mixin({
            //                         toast: true,
            //                         position: 'top-right',
            //                         showConfirmButton: false,
            //                         timer: 3000,
            //                         timerProgressBar: true,
            //                         didOpen: (toast) => {
            //                             toast.addEventListener(
            //                                 'mouseenter',
            //                                 Swal.stopTimer)
            //                             toast.addEventListener(
            //                                 'mouseleave',
            //                                 Swal.resumeTimer
            //                             )
            //                         }
            //                     });
            //                     Toast.fire({
            //                         icon: 'success',
            //                         title: response.msg
            //                     });
            //                     table.draw();
            //                 },
            //                 error: function(data) {
            //                     $.notify(data.responseJSON.msg, "error");
            //                 }
            //             });
            //         } else if (result.isDenied) {
            //             Swal.fire('Something went wrong', '', 'info');
            //         }
            //     })

            // });


            /* Delete Record */
            $(document).on('click', '.recordDelete', function() {

                var id = $(this).data('id');
                var login_id = $(this).data('login_id');

                var title = '{{ __('Do You want to Delete Company Details') }}';
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
                            url: "{{ admin_url('company/delete') }}",
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
                                        text: 'Company Deletion Failed: Module Dependencies Exist.',
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
