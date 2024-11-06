@extends('admin.layouts.admin')
@section('title', 'User List')
@section('pageurl', admin_url('dashboard'))

@push('style')
    <style>


    </style>
@endpush

@section('content')
    <div class="content-wrapper">
        <div class="container-full">
            <div class="content-header">
                <div class="d-flex align-items-center">
                    <div class="mr-auto">

                        <div class="d-inline-block align-items-center">
                            <nav>
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ url('') }}"><i class="mdi mdi-home-outline"></i></a> </li>
                                    <li class="breadcrumb-item" aria-current="page">Tables</li>
                                    <li class="breadcrumb-item active" aria-current="page">Data Tables</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>

            <section class="content">
                <div class="row">

                    <div class="col-12">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">{{ __('administration.employee_list') }}</h4>
                                    <div>
                                        <x-button-filter dataId="" class="search" href=""></x-button-filter>
                                        <x-button-import href="{{ admin_url('employee/import') }}"></x-button-import>
                                        <x-button-add dataId="" class="add"
                                            href="{{ admin_url('admin/master/user/add') }}"></x-button-add>
                                    </div>
                                </div>

                                <div id="search" class="collapse">
                                    <form action="" id="formsearch">
                                        <div class="card-body">
                                            <div class="col-md-12">
                                                <div class="row">
                                                    <div class="col-md-3 mb-3 form-input">
                                                        <label for="inspectiontype" class="form-label ">User Name</label>
                                                        <input type="text" name="emp_name" id="emp_name"
                                                            class="form-control">
                                                    </div>
                                                    <div class="col-md-3 mb-3 form-input">
                                                        <label for="status"
                                                            class="form-label ">{{ __('common.status') }}</label>
                                                        <select name="status" id="status" style="width: 100%"
                                                            class="form-control single-select">
                                                            <option value="">Select Status</option>
                                                            <option value="{{ encryptId(1) }}">Active</option>
                                                            <option value="{{ encryptId(0) }}">In-Active</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <x-button-search></x-button-search>
                                                        <x-button-reset></x-button-reset>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                    <hr>
                                </div>

                                <div class="card-body ">
                                    <div class="table-responsive">
                                        <table id="datatable-list"
                                            class="table primary-table-bordered table-bordered table-striped display responsive nowrap w-100 mt-2 datatable-list">
                                            <thead class="thead-primary">
                                                <tr>
                                                    <th>{{ __('common.sno') }}</th>
                                                    <th>User ID</th>
                                                    <th>User Name</th>
                                                    <th>User Role</th>
                                                    <th>{{ __('common.status') }}</th>
                                                    <th>{{ __('common.created_by') }}</th>
                                                    <th>{{ __('common.created_date') }}</th>
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
                </div>
            </section>

        </div>
    </div>


@endsection


@push('scripts')
@endpush

@push('script')
    <script type="text/javascript" nonce="projectcab">
        $(document).ready(function() {
            var firstTh = $('.datatable-list thead th:first');
            firstTh.removeClass('sorting_asc');
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
                    url: "{{ admin_url('admin/master/user/list') }}",
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]')
                            .attr('content')
                    },
                    data: function(d) {
                        d.emp_no = $('#emp_no').val();
                        d.emp_name = $('#emp_name').val();
                        d.emp_type = $('#emp_type').val();
                        d.factory = $('#factory').val();
                        d.designation = $('#designation').val();
                        d.department = $('#department').val();
                        d.status = $('#status').val();

                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'user_id',
                        name: 'user_id'
                    },
                    {
                        data: 'user_name',
                        name: 'user_name'
                    },
                    {
                        data: 'user_role',
                        name: 'user_role'
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
                        buttons: [
                            {
                                extend: 'pdf',
                                text: '{{ __('common.pdf') }}',
                                action: function(e, dt, button, config) {
                                    var searchValue = $('#datatable-list_filter input').val();
                                    emp_no = $('#emp_no').val();
                                    emp_name = $('#emp_name').val();
                                    emp_type = $('#emp_type').val();
                                    factory = $('#factory').val();
                                    designation = $('#designation').val();
                                    department = $('#department').val();
                                    status = $('#status').val();

                                    $(".dt-button").removeClass('processing');
                                    $('body').click();
                                    window.location.href =
                                        "{{ admin_url('employee/export/pdf') }}" +
                                        '?search=' + searchValue +
                                        '&emp_no=' + emp_no +
                                        '&emp_name=' + emp_name +
                                        '&emp_type=' + emp_type +
                                        '&factory=' + factory +
                                        '&designation=' + designation +
                                        '&department=' + department +
                                        '&status=' + status
                                }
                            },
                            {
                                extend: 'excel',
                                text: '{{ __('common.excel') }}',
                                action: function(e, dt, button, config) {
                                    var searchValue = $('#datatable-list_filter input').val();
                                    emp_no = $('#emp_no').val();
                                    emp_name = $('#emp_name').val();
                                    emp_type = $('#emp_type').val();
                                    factory = $('#factory').val();
                                    designation = $('#designation').val();
                                    department = $('#department').val();
                                    status = $('#status').val();
                                    $(".dt-button").removeClass('processing');
                                    $('body').click();
                                    window.location.href =
                                        "{{ admin_url('employee/export/excel') }}" +
                                        '?search=' + searchValue +
                                        '&emp_no=' + emp_no +
                                        '&emp_name=' + emp_name +
                                        '&emp_type=' + emp_type +
                                        '&factory=' + factory +
                                        '&designation=' + designation +
                                        '&department=' + department +
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
                console.log('test');
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
                    var title = '{{ __('administration.employee_inactive_msg') }}';
                    var text = '{{ __('common.inactive') }}';
                    var btncolor = '#dc3545'

                } else {
                    var title = '{{ __('administration.employee_active_msg') }}';
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
                            url: "{{ admin_url('employee/status') }}",
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

                var title = '{{ __('administration.employee_delete_msg') }}';
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
                            url: "{{ admin_url('employee/delete') }}",
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
                                        text: 'Employee Deletion Failed: Module Dependencies Exist.',
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
