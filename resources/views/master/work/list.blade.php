@extends('admin.layouts.admin')
@section('title', 'Worker Master')
@section('pageurl', admin_url('work/list'))


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
                                            <label for="emp_id" class="form-label ">Worker ID</label>
                                            <input type="text" name="emp_id" id="emp_id" class="form-control"
                                                placeholder="Worker ID">
                                        </div>
                                        <div class="col-md-3 mb-3 form-input">
                                            <label for="emp_name" class="form-label ">Worker Name</label>
                                            <input type="text" name="emp_name" id="emp_name" class="form-control"
                                                placeholder="Worker Name">
                                        </div>

                                        <div class="col-md-3 mb-3 form-input">
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
                                            <select name="location" id="location_id" class=" form-control single-select"
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
                                            <select name="dept_id" id="department_id" class=" form-control single-select"
                                                style="width: 100%">
                                                <option value="">Select Department Name</option>

                                            </select>
                                        </div>
                                        <div class="col-md-3 mb-3 form-input">
                                            <label for="wfemptype" class="form-label ">Worker Type</label>
                                            <input type="text" name="wfemptype" id="wfemptype" class="form-control"
                                                placeholder="Worker Type">
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
                                        <th>Worker Id</th>
                                        <th>Worker Name</th>
                                        <th>Phone Number</th>
                                        <th>Company Name</th>
                                        <th>Location Name</th>
                                        <th>Unit Name</th>
                                        <th>Department Name</th>
                                        <th>Exit Date</th>
                                        <th>Worker Type</th>
                                        <th>{{ __('common.status') }}</th>
                                        <th>{{ __('common.created_date') }}</th>
                                        <th data-priority="5">{{ __('common.action') }}</th>
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
            $(document).on('change', '#company_id', function() {
                var companyId = $(this).val();
                if (companyId) {
                    $.ajax({
                        url: "{{ admin_url('location/ajax-list') }}/" + companyId + "/0",
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            $('#location_id').empty().append('<option value="">Select Location</option>');
                            $.each(data, function(key, value) {
                                $('#location_id').append('<option value="' + value.id + '">' + value
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
                            $('#unit_id').empty().append('<option value="">Select Unit</option>');
                            $.each(data, function(key, value) {
                                $('#unit_id').append('<option value="' + value.id + '">' + value
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
                var unit_id = $(this).val();
                if (unit_id) {
                    $.ajax({
                        url: "{{ admin_url('department/ajax-list') }}/" + unit_id + "/0",
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            $('#department_id').empty().append(
                                '<option value="">Select Department</option>');
                            $.each(data, function(key, value) {
                                $('#department_id').append('<option value="' + value.id + '">' +
                                    value
                                    .name + '</option>');
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
                        url: "{{ admin_url('work/list') }}",
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]')
                                .attr('content')
                        },
                        data: function(d) {
                            d.emp_id = $('#emp_id').val();
                            d.emp_name = $('#emp_name').val();
                            d.company_id = $('#company_id').val();
                            d.location_id = $('#location_id').val();
                            d.unit_id = $('#unit_id').val();
                            d.department_id = $('#department_id').val();
                            d.wfemptype = $('#wfemptype').val();
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
                            data: 'mobile_no',
                            name: 'mobile_no'
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
                            data: 'exit_date',
                            name: 'exit_date'
                        },
                        {
                            data: 'wfemptype',
                            name: 'wfemptype'
                        },
                        {
                            data: 'status',
                            name: 'status'
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
                                        company_id = $('#company_id').val();
                                        location_id = $('#location_id').val();
                                        unit_id = $('#unit_id').val();
                                        department_id = $('#department_id').val();
                                        wfemptype = $('#wfemptype').val();
                                        status = $('#status').val();

                                        $(".dt-button").removeClass('processing');
                                        $('body').click();
                                        window.location.href =
                                            "{{ admin_url('work/export/pdf') }}" +
                                            '?search=' + searchValue +
                                            '&emp_id=' + emp_id +
                                            '&emp_name=' + emp_name +
                                            '&company_id=' + company_id +
                                            '&location_id=' + location_id +
                                            '&unit_id=' + unit_id +
                                            '&department_id=' + department_id +
                                            '&wfemptype=' + wfemptype +
                                            '&status=' + status;
                                    }
                                },
                                {
                                    extend: 'excel',
                                    text: '{{ __('common.excel') }}',
                                    action: function(e, dt, button, config) {
                                        var searchValue = $('#datatable-list_filter input').val();
                                        emp_id = $('#emp_id').val();
                                        emp_name = $('#emp_name').val();
                                        company_id = $('#company_id').val();
                                        location_id = $('#location_id').val();
                                        unit_id = $('#unit_id').val();
                                        department_id = $('#department_id').val();
                                        wfemptype = $('#wfemptype').val();
                                        status = $('#status').val();

                                        $(".dt-button").removeClass('processing');
                                        $('body').click();
                                        window.location.href =
                                            "{{ admin_url('work/export/excel') }}" +
                                            '?search=' + searchValue +
                                            '&emp_id=' + emp_id +
                                            '&emp_name=' + emp_name +
                                            '&company_id=' + company_id +
                                            '&location_id=' + location_id +
                                            '&unit_id=' + unit_id +
                                            '&department_id=' + department_id +
                                            '&wfemptype=' + wfemptype +
                                            '&status=' + status;
                                    }
                                }
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
                        var title = '{{ __('Do You want to In-Activate Worker Details') }}';
                        var text = '{{ __('common.inactive') }}';
                        var btncolor = '#dc3545'

                    } else {
                        var title = '{{ __('Do You want to Activate Worker Details') }}';
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
                                url: "{{ admin_url('work/status') }}",
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



            });
        </script>
    @endpush
