@extends('admin.layouts.admin')
@section('title', 'EHS Inputs')
@section('pageurl', admin_url('kpi/ehs-inputs/list'))


@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">

                <div class="card">
                    <h4 class="card-title"></h4>
                    <div class="d-flex justify-content-end p-2">

                        <x-button-filter dataId="" class="search me-1" href=""></x-button-filter>
                        @if (CheckUserPermission('import'))
                            <x-button-import href="{{ admin_url('kpi/ehs-inputs/import') }}"></x-button-import>
                        @endif
                        <x-button-add dataId="" class="add btn btn-primary ms-1"
                            href="{{ admin_url('kpi/ehs-inputs/add') }}">Add</x-button-add>
                    </div>
                    <div id="search" class="collapse">
                        <form action="" id="formsearch" autocomplete="off">
                            <div class="card-body">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <div class="form-group form-input">
                                                <label class="form-label">{{ __('common.company') }}</label>
                                                <select name="company_id" id="company_id"
                                                    class=" form-control single-select" style="width: 100%">
                                                    <option value="">Select Company Name</option>
                                                    @foreach ($companies as $company)
                                                        <option value="{{ encryptId($company->id) }}">
                                                            {{ $company->company_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <div class="form-group form-input">
                                                <label class="form-label">{{ __('common.location') }}</label>
                                                <select name="location_id" id="location_id"
                                                    class=" form-control single-select" style="width: 100%">
                                                    <option value="">Select Location</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <div class="form-group form-input">
                                                <label class="form-label">{{ __('common.unit') }}</label>
                                                <select name="unit_id" id="unit_id" class=" form-control single-select"
                                                    style="width: 100%">
                                                    <option value="">Select Unit</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <div class="form-group form-input">
                                                <label class="form-label">{{ __('common.department') }}</label>
                                                <select name="department_id" id="department_id"
                                                    class=" form-control single-select" style="width: 100%">
                                                    <option value="">Select Department</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group form-input">
                                                <label class="form-label">{{ __('common.year') }}</label>
                                                <input type="text" name="year" id="year" class="form-control"
                                                    placeholder="Enter Year">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group form-input">
                                                <label class="form-label ">{{ __('common.month') }}</label>
                                                <input type="text" name="month" id="month" class="form-control"
                                                    placeholder="Enter Month">
                                            </div>
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
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 mb-3 d-flex justify-content-end align-items-start gap-2">
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
                                        <th>{{ __('common.company') }}</th>
                                        <th>{{ __('common.location') }}</th>
                                        <th>{{ __('common.unit') }}</th>
                                        <th>{{ __('common.department') }}</th>
                                        <th>{{ __('common.month') }}</th>
                                        <th>{{ __('common.year') }}</th>
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


    @stop

    @push('script')
        <script type="text/javascript" nonce="projectcab">
            $(document).ready(function() {
                var firstTh = $('.datatable-list thead th:first');
                firstTh.removeClass('sorting_asc');

                $('#year').datepicker({
                    format: 'yyyy',
                    minViewMode: 2,
                    autoclose: true
                });

                $('#month').datepicker({
                    format: 'mm',
                    minViewMode: 1,
                    autoclose: true
                });

                $(document).on('change', '#company_id', function() {
                    var companyId = $(this).val();
                    if (companyId) {
                        $.ajax({
                            url: "{{ admin_url('location/ajax-list') }}/" + companyId + "/0",
                            type: 'GET',
                            dataType: 'json',
                            success: function(data) {
                                $('#location_id').empty().append(
                                    '<option value="">Select Location</option>');
                                $.each(data, function(key, value) {
                                    $('#location_id').append('<option value="' + value.id +
                                        '">' + value
                                        .name + '</option>');
                                });
                                $('#location_id').trigger('change.');
                            },
                            error: function(xhr) {
                                alert('Error fetching locations. Please try again.');
                            }
                        });
                    } else {
                        $('#location_id').empty().append('<option value="">Select Location</option>');
                        $('#location_id').trigger('change.');
                    }
                });

                $(document).on('change', '#location_id', function() {
                    var locationId = $(this).val();
                    if (locationId) {
                        $.ajax({
                            url: "{{ admin_url('unit/ajax-list') }}/" + locationId + "/0",
                            type: 'GET',
                            dataType: 'json',
                            success: function(data) {
                                $('#unit_id').empty().append(
                                    '<option value="">Select Unit</option>');
                                $.each(data, function(key, value) {
                                    $('#unit_id').append('<option value="' + value.id +
                                        '">' + value
                                        .name + '</option>');
                                });
                                $('#unit_id').trigger('change.');
                            },
                            error: function(xhr) {
                                alert('Error fetching unit. Please try again.');
                            }
                        });
                    } else {
                        $('#unit_id').empty().append('<option value="">Select Unit</option>');
                        $('#unit_id').trigger('change.');
                    }
                });

                $(document).on('change', '#unit_id', function() {
                    var unitId = $(this).val();
                    if (unitId) {
                        $.ajax({
                            url: "{{ admin_url('department/ajax-list') }}/" + unitId + "/0",
                            type: 'GET',
                            dataType: 'json',
                            success: function(data) {
                                $('#department_id').empty().append(
                                    '<option value="">Select Department</option>');
                                $.each(data, function(key, value) {
                                    $('#department_id').append('<option value="' + value
                                        .id + '">' + value.name + '</option>');
                                });
                                $('#department_id').trigger('change.');
                            },
                            error: function(xhr) {
                                alert('Error fetching unit. Please try again.');
                            }
                        });
                    } else {
                        $('#department_id').empty().append('<option value="">Select Department</option>');
                        $('#department_id').trigger('change.');
                    }
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
                        url: "{{ admin_url('kpi/ehs-inputs/list') }}",
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]')
                                .attr('content')
                        },
                        data: function(d) {
                            d.company_id = $('#company_id').val();
                            d.location_id = $('#location_id').val();
                            d.unit_id = $('#unit_id').val();
                            d.department_id = $('#department_id').val();
                            d.status = $('#status').val();
                            d.year = $('#year').val();
                            d.month = $('#month').val();

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
                            data: 'company_name',
                            name: 'company_name'
                        },
                        {
                            data: 'location_name',
                            name: 'location_name'
                        },
                        {
                            data: 'unit_name',
                            name: 'unit_name'
                        },
                        {
                            data: 'department_name',
                            name: 'department_name'
                        },
                        {
                            data: 'month',
                            name: 'month'
                        },
                        {
                            data: 'calendar_year',
                            name: 'calendar_year'
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
                                        company_id = $('#company_id').val();
                                        location_id = $('#location_id').val();
                                        unit_id = $('#unit_id').val();
                                        department_id = $('#department_id').val();
                                        status = $('#status').val();
                                        year = $('#year').val();
                                        month = $('#month').val();

                                        $(".dt-button").removeClass('processing');
                                        $('body').click();
                                        window.location.href =
                                            "{{ admin_url('kpi/ehs-inputs/export/pdf') }}" +
                                            '?search=' + searchValue +
                                            '&company_id=' + company_id +
                                            '&location_id=' + location_id +
                                            '&unit_id=' + unit_id +
                                            '&department_id=' + department_id +
                                            '&month=' + month +
                                            '&year=' + year +
                                            '&status=' + status
                                    }
                                },
                                {
                                    extend: 'excel',
                                    text: '{{ __('common.excel') }}',
                                    action: function(e, dt, button, config) {
                                        var searchValue = $('#datatable-list_filter input').val();
                                        company_id = $('#company_id').val();
                                        location_id = $('#location_id').val();
                                        unit_id = $('#unit_id').val();
                                        department_id = $('#department_id').val();
                                        status = $('#status').val();
                                        year = $('#year').val();
                                        month = $('#month').val();


                                        $(".dt-button").removeClass('processing');
                                        $('body').click();
                                        window.location.href =
                                            "{{ admin_url('kpi/ehs-inputs/export/excel') }}" +
                                            '?search=' + searchValue +
                                            '&company_id=' + company_id +
                                            '&location_id=' + location_id +
                                            '&unit_id=' + unit_id +
                                            '&department_id=' + department_id +
                                            '&month=' + month +
                                            '&year=' + year +
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
                        var title = '{{ __('Do You want to In-Activate EHS Inputs Details') }}';
                        var text = '{{ __('common.inactive') }}';
                        var btncolor = '#dc3545'

                    } else {
                        var title = '{{ __('Do You want to Activate EHS Inputs Details') }}';
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
                                url: "{{ admin_url('kpi/ehs-inputs/status') }}",
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

                    var title = '{{ __('Do You want to Delete EHS Inputs Management') }}';
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
                                url: "{{ admin_url('kpi/ehs-inputs/delete') }}",
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
                                            text: 'EHS Inputs Deletion Failed: Module Dependencies Exist.',
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
