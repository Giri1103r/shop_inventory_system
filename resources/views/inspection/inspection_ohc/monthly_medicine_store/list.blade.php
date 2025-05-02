@extends('admin.layouts.admin')
@section('title', 'Monthly OHC Store Medicine Inspection Checklist')
@section('pageurl', admin_url('ohc/monthly-medicine-store/inspection/list'))
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
                            href="{{ admin_url('ohc/monthly-medicine-store/inspection/add') }}">Add</x-button-add>
                        {{-- @endif --}}
                    </div>
                    <div id="search" class="collapse">
                        <form action="" id="formsearch">
                            <div class="card-body">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label
                                                    class="form-label require">{{ __('inspection.inspection_date') }}</label>
                                                <input type="text" name="inspection_date" id = "inspection_date"
                                                    class="form-control inspection_date">
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label require">{{ __('inspection.next_due') }}</label>
                                                <input type="text" name="next_due" id = "next_due"
                                                    class="form-control next_due">
                                            </div>
                                        </div>

                                        <div class="col-md-3 mb-3 form-input">
                                            <label for="status" class="form-label ">{{ __('common.status') }}</label>
                                            <select name="status" id="status" style="width: 100%"
                                                class="form-control single-select">
                                                <option value="">Select Status</option>
                                                <option value="{{ encryptId(1) }}">OBSERVATION PENDING</option>
                                                <option value="{{ encryptId(3) }}">OBSERVATION APPROVED</option>
                                                <option value="{{ encryptId(2) }}">OBSERVATION REJECTED</option>
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
                                class="table primary-table-bordered table-bordered table-striped display responsive nowrap w-100 mt-2 datatable-list">
                                <thead class="thead-primary">
                                    <tr>
                                        <th>{{ __('common.sno') }}</th>
                                        <th>{{ __('inspection.inspection_date') }}</th>
                                        <th>{{ __('inspection.next_due') }}</th>
                                        <th>{{ __('Inspection Status') }}</th>
                                        <th>{{ __('common.action') }}</th>
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

                flatpickr(".inspection_date", {
                    dateFormat: "d-m-Y",
                });
                flatpickr(".next_due", {
                    dateFormat: "d-m-Y",
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
                        url: "{{ admin_url('ohc/monthly-medicine-store/inspection/list') }}",
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]')
                                .attr('content')
                        },
                        data: function(d) {
                            d.inspection_date = $('#inspection_date').val();
                            d.next_due = $('#next_due').val();
                            d.status = $('#status').val();
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
                            searchable: true,
                        },

                        {
                            data: 'inspection_date',
                            name: 'inspection_date',
                        },
                        {
                            data: 'next_due',
                            name: 'next_due',
                        },
                        {
                            data: 'inspection_status',
                            name: 'inspection_status',
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
                                        inspection_date = $('#inspection_date').val();
                                        next_due = $('#next_due').val();
                                        status = $('#status').val();


                                        $(".dt-button").removeClass('processing');
                                        $('body').click();
                                        window.location.href =
                                            "{{ admin_url('ohc/monthly-medicine-store/inspection/export/pdf') }}" +
                                            '?search=' + searchValue +
                                            '&inspection_date=' + inspection_date +
                                            '&status=' + status +
                                            '&next_due=' + next_due
                                    }
                                },
                                {
                                    extend: 'excel',
                                    text: '{{ __('common.excel') }}',
                                    action: function(e, dt, button, config) {
                                        var searchValue = $('#datatable-list_filter input').val();
                                        inspection_date = $('#inspection_date').val();
                                        next_due = $('#next_due').val();
                                        status = $('#status').val();
                                        $(".dt-button").removeClass('processing');
                                        $('body').click();
                                        window.location.href =
                                            "{{ admin_url('ohc/monthly-medicine-store/inspection/export/excel') }}" +
                                            '?search=' + searchValue +
                                            '&inspection_date=' + inspection_date +
                                            '&status=' + status +
                                            '&next_due=' + next_due
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
                //         var title = '{{ __('Do You want to In-Activate Equipment checklist') }}';
                //         var text = '{{ __('common.inactive') }}';
                //         var btncolor = '#dc3545'

                //     } else {
                //         var title = '{{ __('Do You want to Activate Equipment checklist') }}';
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
                //                 url: "{{ admin_url('ohc/monthly-medicine-store/inspection/list/status') }}",
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

                    var title = '{{ __('Do You want to Delete Equipment checklist') }}';
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
                                url: "{{ admin_url('ohc/monthly-medicine-store/inspection/list/delete') }}",
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
