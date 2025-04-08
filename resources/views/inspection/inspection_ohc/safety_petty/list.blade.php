@extends('admin.layouts.admin')
@section('title', 'Safety Petty Logbook List')
@section('pageurl', admin_url('ohc/safety-petty-logbook/list'))

@section('content')

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">

                <div class="card">
                    <h4 class="card-title"></h4>
                    <div class="d-flex justify-content-end p-2">

                        <x-button-filter dataId="" class="search me-1" href=""></x-button-filter>

                        <x-button-add dataId="" class="add btn btn-primary ms-1"
                            href="{{ admin_url('ohc/safety-petty-logbook/add') }}">Add</x-button-add>

                    </div>
                    <div id="search" class="collapse">
                        <form action="" id="formsearch">
                            <div class="card-body">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <div class="form-group form-input">
                                                <label class="form-label require">Unit</label>
                                                <select name="unit_id" id="unit_id"
                                                    class="form-control single-select" style="width: 100%">
                                                    <option value="">Select Unit</option>
                                                    @foreach ($units as $list)
                                                        <option value="{{ encryptId($list->id) }}">
                                                            {{ $list->unit_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <div class="form-group form-input">
                                                <label class="form-label require">Department</label>
                                                <select name="department_id" id="department_id"
                                                    class="form-control single-select" style="width: 100%">
                                                    <option value="">Select Department</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <div class="form-group form-input">
                                                <label class="form-label require">Employee Name </label>
                                                <select name="emp_id" id="emp_id"
                                                    class="form-control single-select" style="width: 100%">
                                                    <option value="">Select Employee Name</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <div class="form-group form-input">
                                                <label class="form-label require">Employee Code</label>
                                                <input type="text" name="employee_code"
                                                    id="employee_code" class="form-control"
                                                    placeholder="Employee Code" value="">
                                            </div>
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
                                        <th>Unit</th>
                                        <th>Department</th>
                                        <th>Employee Name</th>
                                        <th>Employee Code</th>
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
                        alert('Error fetching department. Please try again.');
                    }
                });
            } else {
                $('#department_id').empty().append('<option value="">Select Department</option>');
                $('#department_id').trigger('change.');
            }
        });

        $('#emp_id').select2({
            ajax: {
                url: '{{ admin_url('ohc/safety-petty-logbook/employeeid') }}',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        search: params.term
                    };
                },
                processResults: function(data) {
                    return {
                        results: $.map(data, function(item) {
                            return {
                                id: item.id,
                                text: item.text
                            };
                        })
                    };
                }
            },
            minimumInputLength: 1,
            dropdownCssClass: 'form-control',
            selectionCssClass: 'form-control'
        });

    });

    $(function() {
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
                url: "{{ admin_url('ohc/safety-petty-logbook/list') }}",
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]')
                        .attr('content')
                },
                data: function(d) {
                    d.unit_id = $('#unit_id').val();
                    d.department_id = $('#department_id').val();
                    d.emp_id = $('#emp_id').val();
                    d.employee_code = $('#employee_code').val();

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
                    data: 'unit_name',
                    name: 'unit_name'
                },
                {
                    data: 'department_name',
                    name: 'department_name'
                },
                {
                    data: 'emp_name',
                    name: 'emp_name'
                },
                {
                    data: 'employee_code',
                    name: 'employee_code'
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
                                unit_id = $('#unit_id').val();
                                department_id = $('#department_id').val();
                                emp_id = $('#emp_id').val();
                                employee_code = $('#employee_code').val();

                                $(".dt-button").removeClass('processing');
                                $('body').click();
                                window.location.href =
                                    "{{ admin_url('ohc/safety-petty-logbook/export/pdf') }}" +
                                    '?search=' + searchValue +
                                    '&unit_id=' + unit_id +
                                    '&department_id=' + department_id +
                                    '&emp_id=' + emp_id +
                                    '&employee_code=' + employee_code
                            }
                        },
                        {
                            extend: 'excel',
                            text: '{{ __('common.excel') }}',
                            action: function(e, dt, button, config) {
                                var searchValue = $('#datatable-list_filter input').val();
                                unit_id = $('#unit_id').val();
                                department_id = $('#department_id').val();
                                emp_id = $('#emp_id').val();
                                employee_code = $('#employee_code').val();

                                $(".dt-button").removeClass('processing');
                                $('body').click();
                                window.location.href =
                                    "{{ admin_url('ohc/safety-petty-logbook/export/excel') }}" +
                                    '?search=' + searchValue +
                                   '&unit_id=' + unit_id +
                                    '&department_id=' + department_id +
                                    '&emp_id=' + emp_id +
                                    '&employee_code=' + employee_code
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


    });
</script>
@endpush
