@extends('admin.layouts.admin')
@section('title', 'EHS Audit Calendar')
@section('pageurl', admin_url('audit/monthly-audit/audit-plan/list'))

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">

                <div class="card">
                    <h4 class="card-title"></h4>
                    <div class="d-flex justify-content-end p-2">

                        <x-button-filter dataId="" class="search me-1" href=""></x-button-filter>
                        {{-- @if (CheckUserPermission('import')) --}}
                        {{-- <x-button-import href="{{ admin_url('audit/master/task/import') }}"></x-button-import> --}}
                        {{-- @endif --}}
                        {{-- @if (CheckUserPermission('add')) --}}
                        <x-button-add dataId="" class="add btn btn-primary ms-1"
                            href="{{ admin_url('audit/monthly-audit/audit-plan/add') }}">Add</x-button-add>
                        {{-- @endif --}}
                    </div>

                    <div id="search" class="collapse">
                        <form action="" id="formsearch">
                            <div class="card-body">
                                <div class="col-md-12">
                                    <div class="row">

                                        <input type="hidden" name="company_name" value="{{ $companyId }}">
                                        <input type="hidden" name="fromDate" value="{{ $fromdate }}">
                                        <input type="hidden" name="toDate" value="{{ $toDate }}">
                                        <div class="col-md-4">
                                            <div class="form-group form-input">
                                                <label class="form-label require">Auditee Name</label>
                                                <input type="text" name="auditee_name" id = "auditee_name"
                                                    class="form-control" placeholder="Auditee Name">
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

                                        <div class="col-md-4 mb-3">
                                            <div class="form-group form-input">
                                                <label for="unit_id" class="form-label">
                                                    Task Name</label>
                                                <select name="task_name" id="task_name" class=" form-control single-select"
                                                    style="width: 100%">
                                                    <option value="">Select Unit</option>
                                                    @foreach ($audit_task as $task)
                                                        <option value="{{ encryptId($task->id) }}">
                                                            {{ $task->task_name }}</option>
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
                                        <div class="col-md-4">
                                            <div class="form-group form-input">
                                                <label for="unit_id" class="form-label">Compliance Category
                                                </label>
                                                <select name="compliance_category" id="compliance_category"
                                                    class=" form-control single-select" style="width: 100%">
                                                    <option value="">Select Category</option>
                                                    <option value="{{ encryptId(FIRE) }}">Fire</option>
                                                    <option value="{{ encryptId(HEALTH) }}">Health</option>
                                                    <option value="{{ encryptId(SAFETY) }}">Saftey</option>
                                                    <option value="{{ encryptId(MIS) }}">MIS</option>
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
                                        <th>Auditee Name</th>
                                        <th>Unit</th>
                                        <th>Task Name</th>
                                        <th>Complaince Category</th>
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
        <script type="text/javascript">
            $(document).ready(function() {
                var firstTh = $('.datatable-list thead th:first');
                firstTh.removeClass('sorting_asc');

                var fromDatepicker = flatpickr("#issue_date", {
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
                    minDate: "today"
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
                        url: "{{ admin_url('audit/monthly-audit/audit-plan/list') }}",
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
                            data: 'auditee_name',
                            name: 'auditee_name'
                        },

                        {
                            data: 'unit_name',
                            name: 'unit_name'
                        },
                        {
                            data: 'task_name',
                            name: 'task_name'
                        },
                        {
                            data: 'complaince_category',
                            name: 'complaince_category'
                        },
                        {
                            data: 'created_by',
                            name: 'created_by'
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
                                        var auditee_name = $('#auditee_name').val();
                                        var unit_id = $('#unit_id').val();
                                        var task_name = $('#task_name').val();
                                        var compliance_category = $('#compliance_category').val();
                                        var from_date = $('#from_date').val();
                                        var to_date = $('#to_date').val();
                                        $(".dt-button").removeClass('processing');
                                        $('body').click();
                                        window.location.href =
                                            "{{ admin_url('audit/monthly-audit/audit-plan/export/pdf') }}" +
                                            '?search=' + searchValue +
                                            '&auditee_name=' + auditee_name +
                                            '&unit_id=' + unit_id +
                                            '&task_name=' + task_name +
                                            '&from_date=' + from_date +
                                            '&to_date=' + to_date +
                                            '&compliance_category=' + compliance_category

                                    }
                                },
                                {
                                    extend: 'excel',
                                    text: '{{ __('common.excel') }}',
                                    action: function(e, dt, button, config) {

                                        var searchValue = $('#datatable-list_filter input').val();
                                        var auditee_name = $('#auditee_name').val();
                                        var unit_id = $('#unit_id').val();
                                        var task_name = $('#task_name').val();
                                        var compliance_category = $('#compliance_category').val();
                                        var from_date = $('#from_date').val();
                                        var to_date = $('#to_date').val();
                                        $(".dt-button").removeClass('processing');
                                        $('body').click();
                                        window.location.href =
                                            "{{ admin_url('audit/monthly-audit/audit-plan/export/excel') }}" +
                                            '?search=' + searchValue +
                                            '&auditee_name=' + auditee_name +
                                            '&unit_id=' + unit_id +
                                            '&task_name=' + task_name +
                                            '&from_date=' + from_date +
                                            '&to_date=' + to_date +
                                            '&compliance_category=' + compliance_category
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
