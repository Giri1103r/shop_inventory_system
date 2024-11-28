@extends('admin.layouts.admin')
@section('title', 'Employee')
@section('pageurl', admin_url('employee/list'))


@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">

                <div class="card">
                    <h4 class="card-title"></h4>
                    <div class="align-add-btc" style="margin-left: 90%;">

                        <x-button-filter dataId="" class="search" href=""></x-button-filter>
                    </div>
                    <div id="search" class="collapse">
                        <form action="" id="formsearch">
                            <div class="card-body">
                                <div class="col-md-12">
                                    <div class="row">

                                        <div class="col-md-3 mb-3 form-input">
                                            <label for="emp_id" class="form-label ">Employee ID</label>
                                            <input type="text" name="emp_id" id="emp_id" class="form-control"
                                                placeholder="Employee ID">
                                        </div>
                                        <div class="col-md-3 mb-3 form-input">
                                            <label for="emp_name" class="form-label ">Employee Name</label>
                                            <input type="text" name="emp_name" id="emp_name" class="form-control"
                                                placeholder="Employee Name">
                                        </div>
                                        <div class="col-md-3 mb-3 form-input">
                                            <label for="email" class="form-label ">Employee Email</label>
                                            <input type="text" name="email" id="email" class="form-control"
                                                placeholder="Employee Email">
                                        </div>
                                        <div class="col-md-3 mb-3 form-input">
                                            <label for="email" class="form-label ">Employee Status</label>
                                            <input type="text" name="employee_status" id="employee_status"
                                                class="form-control" placeholder="Employee Status">
                                        </div>
                                       
                                        {{-- <div class="col-md-3 mb-3 form-input">
                                            <label for="company_id" class="form-label ">Company</label>
                                            <select name="company_id" id="company_id" class=" form-control single-select"
                                                style="width: 100%">
                                                <option value="">Select Company Name</option>
                                                @foreach ($companyList as $company)
                                                    <option value="{{ encryptId($company->id) }}">
                                                        {{ $company->company_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-3 mb-3 form-input">
                                            <label for="location_id" class="form-label ">Location </label>
                                            <select name="location_id" id="location_id" class=" form-control single-select"
                                                style="width: 100%">
                                                <option value="">Select Location Name</option>

                                            </select>
                                        </div>

                         
                                        <div class="col-md-3 mb-3 form-input">
                                            <label for="unit_id" class="form-label ">Unit Name </label>
                                            <select name="unit_id" id="unit_id" class=" form-control single-select"
                                                style="width: 100%">
                                                <option value="">Select Unit Name</option>

                                            </select>
                                        </div>
                                        <div class="col-md-3 mb-3 form-input">
                                            <label for="unit_id" class="form-label ">Department Name </label>
                                            <select name="dept_id" id="dept_id" class=" form-control single-select"
                                                style="width: 100%">
                                                <option value="">Select Department Name</option>

                                            </select>
                                        </div> --}}

                                        <div class="col-md-12 d-flex justify-content-end gap-2 mt-3">
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
                                        <th>Employee Id</th>
                                        <th>Employee Name</th>
                                        <th>Employee Email</th>
                                        <th>Employee Status</th>
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
            });
            // $(document).on('change', '#company_id', function() {
            //     var company_id = $(this).val();
            //     if (company_id) {
            //         $.ajax({
            //             url: "{{ admin_url('location/alllist/') }}" + company_id,
            //             type: 'GET',
            //             dataType: 'json',
            //             success: function(data) {
            //                 $('#location_id').empty().append(
            //                     '<option value="">Select Location Name</option>');
            //                 $.each(data, function(key, value) {
            //                     $('#location_id').append('<option value="' + value.id +
            //                         '">' + value.name + '</option>');
            //                 });

            //                 $('#location_id').trigger('change.');
            //             }
            //         });
            //     } else {
            //         $('#location_id').empty().append('<option value="">Select Location Name</option>');
            //         $('#location_id').trigger('change.');
            //     }
            // });
            // $(document).on('change', '#location_id', function() {
            //     var location_id = $(this).val();
            //     if (location_id) {
            //         $.ajax({
            //             url: "{{ admin_url('unit/alllist/') }}" + location_id,
            //             type: 'GET',
            //             dataType: 'json',
            //             success: function(data) {
            //                 $('#unit_id').empty().append(
            //                     '<option value="">Select Unit Name</option>');
            //                 $.each(data, function(key, value) {
            //                     $('#unit_id').append('<option value="' + value.id +
            //                         '">' + value.name + '</option>');
            //                 });

            //                 $('#unit_id').trigger('change.');
            //             }
            //         });
            //     } else {
            //         $('#unit_id').empty().append('<option value="">Select Unit Name</option>');
            //         $('#unit_id').trigger('change.');
            //     }
            // });
            // $(document).on('change', '#unit_id', function() {
            //     var unit_id = $(this).val();
            //     if (location_id) {
            //         $.ajax({
            //             url: "{{ admin_url('department/alllist/') }}" + unit_id,
            //             type: 'GET',
            //             dataType: 'json',
            //             success: function(data) {
            //                 $('#dept_id').empty().append(
            //                     '<option value="">Select Department Name</option>');
            //                 $.each(data, function(key, value) {
            //                     $('#dept_id').append('<option value="' + value.id +
            //                         '">' + value.name + '</option>');
            //                 });

            //                 $('#dept_id').trigger('change.');
            //             }
            //         });
            //     } else {
            //         $('#dept_id').empty().append('<option value="">Select Department Name</option>');
            //         $('#dept_id').trigger('change.');
            //     }
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
                        url: "{{ admin_url('employee/list') }}",
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]')
                                .attr('content')
                        },
                        data: function(d) {
                            d.emp_id = $('#emp_id').val();
                            d.emp_name = $('#emp_name').val();
                            d.email = $('#email').val();
                            d.employee_status = $('#employee_status').val();
                            d.status = $('#status').val();

                        }
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'emp_id',
                            name: 'emp_id'
                        },
                        {
                            data: 'emp_name',
                            name: 'emp_name'
                        },
                        {
                            data: 'email',
                            name: 'email'
                        },
                        {
                            data: 'employee_status',
                            name: 'employee_status'
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
                                        emp_id = $('#emp_id').val();
                                        emp_name = $('#emp_name').val();
                                        email = $('#email').val();
                                        employee_status = $('#employee_status').val();
                                        status = $('#status').val();
                                        $(".dt-button").removeClass('processing');
                                        $('body').click();
                                        window.location.href =
                                            "{{ admin_url('employee/export/pdf') }}" +
                                            '?search=' + searchValue +
                                            '&emp_id=' + emp_id +
                                            '&emp_name=' + emp_name +
                                            '&email=' + email;
                                        '&employee_status=' + employee_status;
                                        '&status=' + status;
                                    } // Closing brace for action function
                                },
                                {
                                    extend: 'excel',
                                    text: '{{ __('common.excel') }}',
                                    action: function(e, dt, button, config) {
                                        var searchValue = $('#datatable-list_filter input').val();
                                        emp_id = $('#emp_id').val();
                                        emp_name = $('#emp_name').val();
                                        email = $('#email').val();
                                        employee_status = $('#employee_status').val();
                                        status = $('#status').val();
                                        $(".dt-button").removeClass('processing');
                                        $('body').click();
                                        window.location.href =
                                            "{{ admin_url('employee/export/excel') }}" +
                                            '?search=' + searchValue +
                                            '&emp_id=' + emp_id +
                                            '&emp_name=' + emp_name +
                                            '&email=' + email;
                                            '&employee_status=' + employee_status;
                                            '&status=' + status;
                                    } // Closing brace for action function
                                }
                            ]
                        },
                        {
                            "extend": 'pageLength',
                            "text": '{{ __('common.show') }} 10 {{ __('common.records') }}'
                        }
                    ]


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


            });
        </script>
    @endpush
