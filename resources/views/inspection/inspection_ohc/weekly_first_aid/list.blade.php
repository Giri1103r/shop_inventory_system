@extends('admin.layouts.admin')
@section('title', 'Weekly First Aid Checklist')
@section('pageurl', admin_url('ohc/first-aid-box/weekly-inspection/list'))


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
                            href="{{ admin_url('ohc/first-aid-box/weekly-inspection/add') }}">Add</x-button-add>
                        {{-- @endif --}}
                    </div>
                    <div id="search" class="collapse">
                        <form action="" id="formsearch">
                            <div class="card-body">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-4 mb-3 form-input">
                                            <label for="document_number" class="form-label ">First Aid Box No</label>
                                            <input type="text" name="first_aid_box_no" id="first_aid_box_no"
                                                class="form-control">
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label require">Shift</label>
                                                <select name="shift" id="shift" style="width: 100%"
                                                    class="form-control single-select">
                                                    <option value="">Select the option</option>
                                                    @foreach ($shift as $list)
                                                        <option value="{{ encryptId($list->id) }}">{{ $list->shift }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group form-input">
                                                <label for="location_id" class="form-label">
                                                    Location</label>
                                                <select name="location_id" id="location_id"
                                                    class=" form-control single-select" style="width: 100%">
                                                    <option value="">Select Location</option>
                                                    @foreach ($location as $loc)
                                                        <option value="{{ encryptId($loc->id) }}">
                                                            {{ $loc->location_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label require">Unit</label>
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
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label require">First Aider Name</label>
                                                <select name="first_aider" id="first_aider"
                                                    class="form-control single-select" style="width: 100%">
                                                    <option value="">Select the option</option>
                                                    @foreach ($First_aid as $list)
                                                        <option value="{{ encryptId($list->id) }}">
                                                            {{ $list->certifier_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mt-3">
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
                                        <th>First Aid Box Number</th>
                                        <th>Shift</th>
                                        <th>Location</th>
                                        <th>Unit</th>
                                        <th>First Aider Name</th>
                                        <th>Created By</th>
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

            var fromDatepicker = flatpickr("#issue_date", {
                dateFormat: "d-m-Y",
            });

            var fromDatepicker = flatpickr("#revision_date", {
                dateFormat: "d-m-Y",
            });
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
                    url: "{{ admin_url('ohc/first-aid-box/weekly-inspection/list') }}",
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]')
                            .attr('content')
                    },
                    data: function(d) {
                        d.first_aid_box_no = $('#first_aid_box_no').val();
                        d.shift = $('#shift').val();
                        d.location = $('#location_id').val();
                        d.unit = $('#unit_id').val();
                        d.first_aider = $('#first_aider').val();
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
                        data: 'first_aid_box_no',
                        name: 'first_aid_box_no'
                    },
                    {
                        data: 'shift',
                        name: 'shift'
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
                        data: 'certifier_name',
                        name: 'certifier_name'
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
                                    first_aid_box_no = $('#first_aid_box_no').val();
                                    shift = $('#shift').val();
                                    loc = $('#location_id').val();
                                    unit = $('#unit_id').val();
                                    first_aider = $('#first_aider').val();
                                    var from_date = $('#from_date').val();
                                    var to_date = $('#to_date').val();

                                    $(".dt-button").removeClass('processing');
                                    $('body').click();
                                    window.location.href =
                                        "{{ admin_url('ohc/first-aid-box/weekly-inspection/export/pdf') }}" +
                                        '?search=' + searchValue +
                                        '&first_aid_box_no=' + first_aid_box_no +
                                        '&shift=' + shift +
                                        '&location_id=' + loc +
                                        '&from_date=' + from_date +
                                        '&to_date=' + to_date +
                                        '&unit_id=' + unit +
                                        '&first_aider' + first_aider

                                }
                            },
                            {
                                extend: 'excel',
                                text: '{{ __('common.excel') }}',
                                action: function(e, dt, button, config) {

                                    var searchValue = $('#datatable-list_filter input').val();
                                    doc_no = $('#document_number').val();
                                    issue_date = $('#issue_date').val();
                                    loc = $('#location_id').val();
                                    unit = $('#unit_id').val();
                                    var from_date = $('#from_date').val();
                                    var to_date = $('#to_date').val();
                                    $(".dt-button").removeClass('processing');
                                    $('body').click();
                                    window.location.href =
                                        "{{ admin_url('ohc/first-aid-box/weekly-inspection/export/excel') }}" +
                                        '?search=' + searchValue +
                                        '&document_number=' + doc_no +
                                        '&issue_date=' + issue_date +
                                        '&from_date=' + from_date +
                                        '&to_date=' + to_date +
                                        '&location_id=' + loc +
                                        '&unit_id=' + unit


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
