@extends('admin.layouts.admin')
@section('title', 'MSDS List')
@section('pageurl', admin_url('msds/list'))

@section('content')

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">

                <div class="card">
                    <h4 class="card-title"></h4>
                    <div class="d-flex justify-content-end p-2">

                        <x-button-filter dataId="" class="search me-1" href=""></x-button-filter>

                        <x-button-add dataId="" class="add btn btn-primary ms-1"
                            href="{{ admin_url('msds/add') }}">Add</x-button-add>

                    </div>
                    <div id="search" class="collapse">
                        <form action="" id="formsearch">
                            <div class="card-body">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <div class="form-group form-input">
                                                <label for="location_id" class="form-label require">
                                                    Location
                                                </label>
                                                <select name="location_id" id="location_id"
                                                    class=" form-control single-select" style="width: 100%">
                                                    <option value="">Select Location</option>
                                                    @foreach ($locations as $location)
                                                        <option value="{{ encryptId($location->id) }}">
                                                            {{ $location->location_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <div class="form-group form-input">
                                                <label for="unit_id" class="form-label require">
                                                    Unit
                                                </label>
                                                <select name="unit_id" id="unit_id" class=" form-control single-select"
                                                    style="width: 100%">
                                                    <option value="">Select Unit</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <div class="form-group form-input">
                                                <label for="department_id" class="form-label require">
                                                    Department
                                                </label>
                                                <select name="department_id" id="department_id"
                                                    class=" form-control single-select" style="width: 100%">
                                                    <option value="">Select Department</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3 form-input">
                                            <label for="emp_name" class="form-label ">From Date</label>
                                            <div class="input-group date form-input custom-height">
                                                <input type="text" class="form-control " name="from_date" id="from_date"
                                                    autocomplete="off">
                                                <div class="input-group-addon input-group-text">
                                                    <span class="fa fa-calendar"></span>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="col-md-4 mb-3 form-input">
                                            <label for="emp_name" class="form-label ">To Date</label>
                                            <div class="input-group date form-input  custom-height">
                                                <input type="text" class="form-control " name="to_date" id="to_date"
                                                    autocomplete="off">
                                                <div class="input-group-addon input-group-text">
                                                    <span class="fa fa-calendar"></span>
                                                </div>
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
                                        <th>Location</th>
                                        <th>Unit</th>
                                        <th>Department</th>
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
            $('#location_id').on('change', function() {
                var location_id = $(this).val();
                $('#department_id').val("").trigger("change");
                $('#unit_id').val("").trigger("change");

                var csrf_token = $('meta[name="csrf-token"]').attr('content');

                $.ajax({
                    url: '{{ admin_url('msds/getUnit') }}',
                    type: 'POST',
                    data: {
                        location: location_id,
                        _token: csrf_token
                    },
                    success: function(response) {
                        let options = '<option value="">Select Unit</option>';
                        if (response.unit && response.unit.length > 0) {
                            response.unit.forEach(function(unit) {
                                options +=
                                    `<option value="${unit.id}">${unit.unit}</option>`;
                            });
                        } else {
                            options = '<option value="">No Unit available</option>';
                        }
                        $('#unit_id').html(options);
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX Error:", error);
                        $('#unit_id').html('<option value="">Error loading Unit</option>');
                    }
                });
            });

            $('#unit_id').on('change', function() {
                $('#department_id').val("").trigger("change");

                var location_id = $('#location_id').val();
                var unit_id = $(this).val();
                var csrf_token = $('meta[name="csrf-token"]').attr('content');

                $.ajax({
                    url: '{{ admin_url('msds/getDepartment') }}',
                    type: 'POST',
                    data: {
                        unit: unit_id,
                        location: location_id,
                        _token: csrf_token
                    },
                    success: function(response) {
                        let options = '<option value="">Select Department</option>';
                        if (response.department && response.department.length > 0) {
                            response.department.forEach(function(department) {
                                options +=
                                    `<option value="${department.id}">${department.department_name}</option>`;
                            });
                        } else {
                            options = '<option value="">No Department available</option>';
                        }
                        $('#department_id').html(options);
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX Error:", error);
                        $('#department_id').html(
                            '<option value="">Error loading Unit</option>');
                    }
                });
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
                    url: "{{ admin_url('msds/list') }}",
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]')
                            .attr('content')
                    },
                    data: function(d) {
                        d.location_id = $('#location_id').val();
                        d.department_id = $('#department_id').val();
                        d.unit_id = $('#unit_id').val();
                        d.from_date = $('#from_date').val();
                        d.to_date = $('#to_date').val();
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
                        data: 'location_name',
                        name: 'location_name',
                    },
                    {
                        data: 'department_name',
                        name: 'department_name',
                    },
                    {
                        data: 'unit_name',
                        name: 'unit_name',
                    },
                    {
                        data: 'created_date',
                        name: 'created_date',
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
                                    location_id = $('#location_id').val();
                                    unit_id = $('#unit_id').val();
                                    department_id = $('#department_id').val();
                                    var from_date = $('#from_date').val();
                                    var to_date = $('#to_date').val();
                                    $(".dt-button").removeClass('processing');
                                    $('body').click();
                                    window.location.href =
                                        "{{ admin_url('msds/export/pdf') }}" +
                                        '?search=' + searchValue +
                                        '&location_id=' + location_id +
                                        '&unit_id=' + unit_id +
                                        '&from_date=' + from_date +
                                        '&to_date=' + to_date +
                                        '&department_id=' + department_id
                                }
                            },
                            {
                                extend: 'excel',
                                text: '{{ __('common.excel') }}',
                                action: function(e, dt, button, config) {
                                    var searchValue = $('#datatable-list_filter input').val();
                                    location_id = $('#location_id').val();
                                    unit_id = $('#unit_id').val();
                                    department_id = $('#department_id').val();
                                    var from_date = $('#from_date').val();
                                    var to_date = $('#to_date').val();
                                    $(".dt-button").removeClass('processing');
                                    $('body').click();
                                    window.location.href =
                                        "{{ admin_url('msds/export/excel') }}" +
                                        '?search=' + searchValue +
                                        '&location_id=' + location_id +
                                        '&unit_id=' + unit_id +
                                        '&from_date=' + from_date +
                                        '&to_date=' + to_date +
                                        '&department_id=' + department_id
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
