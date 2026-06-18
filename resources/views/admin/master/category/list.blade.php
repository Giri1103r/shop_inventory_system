@extends('admin.layouts.admin')
@section('title', 'Category Master')
@section('pageurl', admin_url('master/category/list'))


@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">

                <div class="card">
                    <h4 class="card-title"></h4>
                    <div class="d-flex flex-wrap justify-content-end gap-2 p-2">

                        <x-button-add dataId="" class="add" href="{{ admin_url('master/category/add') }}">
                            Add
                        </x-button-add>

                        <x-button-filter dataId="" class="search" href="">
                        </x-button-filter>

                        <x-button-import href="{{ admin_url('master/category/import') }}">
                        </x-button-import>

                    </div>

                    <div id="search" class="collapse">
                        <form action="" id="formsearch">
                            <div class="card-body">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-3 mb-3 form-input">
                                            <label for="company_id"
                                                class="form-label">{{ __('common.category_id') }}</label>
                                            <select name="category_id" class="forn-control single-select" id="category_id">
                                                <option value="">Select the option</option>
                                                @foreach ($categoryList as $categoryId)
                                                    <option value="{{ $categoryId->category_id }}">
                                                        {{ $categoryId->category_id }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3 mb-3 form-input">
                                            <label for="company_id"
                                                class="form-label">{{ __('common.category_code') }}</label>
                                            <select name="category_code" class="forn-control single-select" id="category_code">
                                                <option value="">Select the option</option>
                                                @foreach ($categoryList as $catCode)
                                                    <option value="{{ $catCode->category_code }}">
                                                        {{ $catCode->category_code }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3 mb-3 form-input">
                                            <label for="company_name"
                                                class="form-label">{{ __('common.category_name') }}</label>
                                            <select name="category_name" class="forn-control single-select" id="category_name">
                                                <option value="">Select the option</option>
                                                @foreach ($categoryList as $catName)
                                                    <option value="{{ $catName->category_name }}">
                                                        {{ $catName->category_name }}</option>
                                                @endforeach
                                            </select>
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
                                        <th>{{ __('common.category_id') }}</th>
                                        <th>{{ __('common.category_code') }}</th>
                                        <th>{{ __('common.category_name') }}</th>
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


@stop

@push('script')
    <script type="text/javascript">
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
                    url: "{{ admin_url('master/category/list') }}",
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]')
                            .attr('content')
                    },
                    data: function(d) {
                        let formData = $('#formsearch').serialize();
                        let params = new URLSearchParams(formData);
                        params.forEach((value, key) => d[key] = value);
                    },
                    error: function(xhr, error, code) {
                        if (xhr.status === 419) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Session Expired',
                                text: 'Your session has expired. Please login again.',
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#d33'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = "{{ url('') }}";
                                }
                            });
                        }
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'category_id',
                        name: 'category_id'
                    },
                    {
                        data: 'category_code',
                        name: 'category_code'
                    },
                    {
                        data: 'category_name',
                        name: 'category_name'
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
                                    var formData = $('#formsearch').serialize();
                                    var exportUrl =
                                        "{{ admin_url('master/category/export/pdf') }}";
                                    window.location.href = exportUrl + '?search=' +
                                        searchValue + '&' +
                                        formData;
                                }
                            },
                            {
                                extend: 'excel',
                                text: '{{ __('common.excel') }}',
                                action: function(e, dt, button, config) {
                                    var searchValue = $('#datatable-list_filter input').val();
                                    var formData = $('#formsearch').serialize();
                                    var exportUrl =
                                        "{{ admin_url('master/category/export/excel') }}";
                                    window.location.href = exportUrl + '?search=' +
                                        searchValue + '&' +
                                        formData;
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
                    var title = '{{ __('Do You want to In-Activate Company Details') }}';
                    var text = '{{ __('common.inactive') }}';
                    var btncolor = '#00000'

                } else {
                    var title = '{{ __('Do You want to Activate Company Details') }}';
                    var text = '{{ __('common.active') }}';
                    var btncolor = '#00000'
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
                            url: "{{ admin_url('master/category/status') }}",
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
                            url: "{{ admin_url('master/category/delete') }}",
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
