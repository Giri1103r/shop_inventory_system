@extends('admin.layouts.admin')
@section('title', 'First Aider Location')
@section('pageurl', admin_url('ohc/first-aid-location/list'))


@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">

                <div class="card">
                    <h4 class="card-title"></h4>
                    <div class="d-flex justify-content-end p-2">

                        <x-button-filter dataId="" class="search me-1" href=""></x-button-filter>

                        @if (CheckUserPermission('import'))
                            <x-button-import href="{{ admin_url('company/import') }}"></x-button-import>
                        @endif
                        <x-button-import href="{{ admin_url('ohc/first-aid-location/import') }}"></x-button-import>
                        {{-- @if (CheckUserPermission('add')) --}}
                        <x-button-add dataId="" class="add btn btn-primary ms-1"
                            href="{{ admin_url('ohc/first-aid-location/add') }}">Add</x-button-add>
                        {{-- @endif --}}
                        {{-- @if (CheckUserPermission('import')) --}}

                        {{-- @endif --}}

                    </div>

                    <div id="search" class="collapse">
                        <form action="" id="formsearch">
                            <div class="card-body">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-3 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label ">{{ __('common.unit') }}</label>
                                                <select name="unit_id" id="unit_id" class="form-control single-select"
                                                    style="width: 100%">
                                                    <option value="">Select the unit</option>
                                                    @foreach ($unit as $list)
                                                        <option value="{{ encryptId($list->id) }}">
                                                            {{ $list->unit_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label ">{{ __('common.department') }}</label>
                                                <select name="department_id" id="department_id"
                                                    class=" form-control single-select" style="width: 100%">
                                                    <option value="">Select Department </option>

                                                </select>
                                            </div>
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
                                        <th>{{ __('common.unit') }}</th>
                                        <th>{{ __('common.department') }}</th>
                                        <th>{{ __('ohc_management.location_name') }}</th>
                                        <th>{{ __('ohc_management.station_master') }}</th>
                                        <th>{{ __('ohc_management.station_number') }}</th>
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
        $(document).ready(function() {
            // Remove default sorting class
            $('.datatable-list thead th:first').removeClass('sorting_asc');

            // Initialize Flatpickr for date fields


            // Initialize DataTable
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
                    url: "{{ admin_url('ohc/first-aid-location/list') }}",
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: function(d) {
                        d.unit_id = $('#unit_id').val();
                        d.department_id = $('#department_id').val();
                        d.from_date = $('#from_date').val();
                        d.to_date = $('#to_date').val();
                        d.status = $('#status').val();

                    },

                    error: function(xhr) {
                        if (xhr.status === 419) {
                            alert('Session has expired. You will be redirected to the login page.');
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
                        data: 'unit_id',
                        name: 'unit_id'
                    },
                    {
                        data: 'department_id',
                        name: 'department_id'
                    },
                    {
                        data: 'location_id',
                        name: 'location_id'
                    },
                    {
                        data: 'station_master',
                        name: 'station_master'
                    },
                    {
                        data: 'station_number',
                        name: 'station_number'
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
                        orderable: false
                    }
                ],
                language: {
                    paginate: {
                        first: '<i title="{{ __('common.first') }}" class="fa fa-angle-double-left"></i>',
                        last: '<i title="{{ __('common.last') }}" class="fa fa-angle-double-right"></i>',
                        next: '<i title="{{ __('common.next') }}" class="fa fa-angle-right"></i>',
                        previous: '<i title="{{ __('common.previous') }}" class="fa fa-angle-left"></i>'
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
                                action: function(e, dt, button, config) {
                                    var searchValue = $('#datatable-list_filter input').val();
                                    var department_id = $('#department_id').val();
                                    var unit_id = $('#unit_id').val();
                                    var from_date = $('#from_date').val();
                                    var to_date = $('#to_date').val();
                                    var status = $('#status').val();

                                    $(".dt-button").removeClass('processing');
                                    $('body').click();
                                    window.location.href =
                                        "{{ admin_url('ohc/first-aid-location/export/pdf') }}" +
                                        '?search=' + searchValue +
                                        '&unit_id=' + unit_id +
                                        '&department_id=' + department_id +
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
                                    var department_id = $('#department_id').val();
                                    var unit_id = $('#unit_id').val();
                                    var from_date = $('#from_date').val();
                                    var to_date = $('#to_date').val();
                                    var status = $('#status').val();
                                    $(".dt-button").removeClass('processing');
                                    $('body').click();
                                    window.location.href =
                                        "{{ admin_url('ohc/first-aid-location/export/excel') }}" +
                                        '?search=' + searchValue +
                                        '&unit_id=' + unit_id +
                                        '&department_id=' + department_id +
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
                table.draw();
            });

            $(document).on('click', '#resetform', function() {
                $('#formsearch .single-select').val('');
                $('#formsearch .single-select').trigger('change');
                setTimeout(function() {
                    table.draw();
                }, 150);
            });


            // Status Change functionality
            $(document).on('click', '.statusChange', function() {
                var id = $(this).data('id');
                var type = $(this).data('type');
                var title = type == 1 ? '{{ __('Do You want to In-Activate First Aider Location') }}' :
                    '{{ __('Do You want to Activate First Aider Location') }}';
                var btnColor = type == 1 ? '#dc3545' : '#7ddc35';

                Swal.fire({
                    title: title,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: type == 1 ? '{{ __('common.inactive') }}' :
                        '{{ __('common.active') }}',
                    confirmButtonColor: btnColor,
                    customClass: {
                        confirmButton: 'btn-skew',
                        cancelButton: 'btn-skew'
                    }
                }).then((result) => {
                    if (result.value) {
                        $.ajax({
                            url: "{{ admin_url('ohc/first-aid-location/status') }}",
                            type: 'POST',
                            data: {
                                id: id,
                                types: type
                            },
                            success: function(response) {
                                showToast('success', response.msg);
                                table.draw();
                            },
                            error: function(xhr) {
                                showToast('error', xhr.responseJSON.msg);
                            }
                        });
                    }
                });
            });

            // Delete Record functionality
            $(document).on('click', '.recordDelete', function() {
                var id = $(this).data('id');
                Swal.fire({
                    title: '{{ __('Do You want to Delete Company Details') }}',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: '{{ __('common.delete') }}',
                    confirmButtonColor: '#dc3545',
                    customClass: {
                        confirmButton: 'btn-skew',
                        cancelButton: 'btn-skew'
                    }
                }).then((result) => {
                    if (result.value) {
                        $.ajax({
                            url: "{{ admin_url('company/delete') }}",
                            type: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data: {
                                id: id
                            },
                            success: function(response) {
                                showToast('success', response.msg);
                                table.draw();
                            },
                            error: function(xhr) {
                                showToast('error', xhr.responseJSON.msg);
                            }
                        });
                    }
                });
            });

            // Helper functions
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

            function exportData(type) {
                var params = {
                    search: $('#datatable-list_filter input').val(),
                    medicine: $('#medicine').val(),
                    unit: $('#unit').val(),
                    from_date: $('#from_date').val(),
                    to_date: $('#to_date').val(),
                    status: $('#status').val()
                };
                var url = `{{ admin_url('ohc/first-aid-location/export/${type}') }}?` + $.param(params);
                window.location.href = url;
            }
        });
    </script>
@endpush
