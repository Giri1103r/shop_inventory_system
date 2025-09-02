@extends('admin.layouts.admin')
@section('title', 'Gemba Walk Inspection (Safety Observation)')
@section('pageurl', admin_url('inspection/gemba-walk/list'))

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">

                <div class="card">
                    <h4 class="card-title"></h4>
                    <div class="d-flex justify-content-end p-2">

                        <x-button-filter dataId="" class="search me-1" href=""></x-button-filter>
                        {{-- @if (CheckUserPermission('import')) --}}
                        {{-- <x-button-import href="{{ admin_url('inspection/checklist-type/import') }}"></x-button-import> --}}
                        {{-- @endif --}}
                        {{-- @if (CheckUserPermission('add'))    --}}
                        <x-button-add dataId="" class="add btn btn-primary ms-1"
                            href="{{ admin_url('inspection/gemba-walk/add') }}">Add</x-button-add>
                        {{-- @endif --}}
                    </div>

                    <div id="search" class="collapse">
                        <form action="" id="formsearch">
                            <div class="card-body">
                                <div class="col-md-12">
                                    <div class="row">
                                        <input type="hidden" name="observation_type" value="{{ $observationType }}">
                                        <input type="hidden" name="fromDate" value="{{ $fromDate }}">
                                        <input type="hidden" name="toDate" value="{{ $toDate }}">
                                        <input type="hidden" name="unitId" value="{{ $unitId }}">
                                        <div class="col-md-4 mb-3 form-input">
                                            <label for="gemba_walk_auto_id" class="form-label ">Gemba Walk ID</label>
                                            <input type="text" name="gemba_walk_auto_id" id="gemba_walk_auto_id"
                                                class="form-control">
                                        </div>
                                        <div class="col-md-4 mb-3 form-input">
                                            <label for="date" class="form-label ">Date</label>
                                            <input type="text" name="date" id="date" class="form-control">
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label">Shift</label>
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
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label">Safety Officer</label>
                                                <select name="ehs_officer" id="ehs_officer" style="width: 100%"
                                                    class="form-control single-select">
                                                    <option value="">Select the Safety Officer</option>
                                                    @foreach ($ehs_officers as $ehs_officer)
                                                        <option value="{{ encryptId($ehs_officer->login_id) }}">
                                                            {{ $ehs_officer->emp_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-3 form-input">
                                            <label for="inspection_status"
                                                class="form-label ">{{ __('common.status') }}</label>
                                            <select name="inspection_status" id="inspection_status" style="width: 100%"
                                                class="form-control single-select">
                                                <option value="">Select Status</option>

                                                <option value="{{ encryptId('2') }}">Waiting For CAPA Action
                                                </option>
                                                <option value="{{ encryptId('3') }}">Waiting for EHS Officer Verification
                                                </option>
                                                <option value="{{ encryptId('4') }}">EHS Officer Rejected - Resubmit to
                                                    Waiting For CAPA Action
                                                </option>
                                                <option value="{{ encryptId('5') }}">Closed</option>
                                            </select>
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

                                        @if (!empty($dashboard_search))

                                            <div class="col-md-4 mb-3 form-input">
                                                <label for="date" class="form-label ">Unit</label>
                                                <input type="text" name="unit_name" id="unit_name"
                                                    value="{{ $dashboard_search }}" readonly class="form-control">
                                            </div>
                                        @else
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label">Unit</label>
                                                    <select name="unit_id" id="unit_id" style="width: 100%"
                                                        class="form-control single-select">
                                                        <option value="">Select the option</option>
                                                        @foreach ($unit as $list)
                                                            <option value="{{ encryptId($list->id) }}">
                                                                {{ $list->unit_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                        @endif

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
                                        <th>Gemba Walk ID</th>
                                        <th>Date</th>
                                        <th>Shift</th>
                                        <th>{{ __('common.unit') }}</th>
                                        <th>{{ __('common.status') }}</th>
                                        <th>Safety Officer</th>
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

                const savedData = localStorage.getItem('searchData');
                if (savedData) {
                    const searchValues = JSON.parse(savedData);

                    for (let key in searchValues) {
                        const $element = $(`[name="${key}"]`);
                        const value = searchValues[key];

                        if ($element.is(':checkbox')) {
                            $element.prop('checked', value === 'on' || value === true);
                        } else if ($element.is(':radio')) {
                            $(`input[name="${key}"][value="${value}"]`).prop('checked', true);
                        } else {
                            $element.val(value);
                        }

                        if ($element.is('select') || $element.hasClass('select2')) {
                            $element.trigger('change');
                        }
                    }
                }


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

                })

                var toDatepicker = flatpickr("#to_date", {
                    dateFormat: "d-m-Y",

                });


            });



            // $(document).ready(function() {
            //     var fromDatepicker = flatpickr("#from_date", {
            //         dateFormat: "d-m-Y",
            //         onChange: function(selectedDates) {
            //             if (selectedDates.length > 0) {
            //                 var startDate = selectedDates[0];
            //                 toDatepicker.set('minDate', startDate);
            //                 toDatepicker.clear();
            //             }
            //         }
            //     });

            //     var toDatepicker = flatpickr("#to_date", {
            //         dateFormat: "d-m-Y",
            //         minDate: "today"
            //     });
            // });

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
                        url: "{{ admin_url('inspection/gemba-walk/list') }}",
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
                            data: 'gemba_walk_auto_id',
                            name: 'gemba_walk_auto_id'
                        },
                        {
                            data: 'date',
                            name: 'date'
                        },
                        {
                            data: 'shift',
                            name: 'shift'
                        },
                        {
                            data: 'unit_name',
                            name: 'unit_name'
                        },
                        {
                            data: 'gemba_walk_status',
                            name: 'gemba_walk_status'
                        },
                        {
                            data: 'inspection_created_by',
                            name: 'inspection_created_by'
                        },
                        {
                            data: 'inspection_created_date',
                            name: 'inspection_created_date'
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
                                            "{{ admin_url('inspection/gemba-walk/export/pdf') }}";
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
                                            "{{ admin_url('inspection/gemba-walk/export/excel') }}";
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

                $(document).on('click', '#searchform', function(e) {
                    table.draw();
                    e.preventDefault();
                    const form = document.getElementById('formsearch');

                    const formData = new FormData(form);
                    const searchValues = {};

                    formData.forEach((value, key) => {
                        searchValues[key] = value;
                    });
                    localStorage.setItem('searchData', JSON.stringify(searchValues));
                });

                $(document).on('click', '#resetform', function() {
                    $('#formsearch .single-select').val('');
                    $('#formsearch .single-select').trigger('change');
                    localStorage.removeItem('searchData');
                    setTimeout(function() {
                        table.draw();
                    }, 150);
                });

                /* Status Change */
                $(document).on('click', '.statusChange', function() {
                    var id = $(this).data('id');
                    var types = $(this).data('type');
                    if (types == 1) {
                        var title = '{{ __('Do You want to In-Activate HIRA') }}';
                        var text = '{{ __('common.inactive') }}';
                        var btncolor = '#dc3545'

                    } else {
                        var title = '{{ __('Do You want to Activate HIRA') }}';
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
                                url: "{{ admin_url('incident/initial-incident/status') }}",
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
                                url: "{{ admin_url('incident/initial-incident/delete') }}",
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
