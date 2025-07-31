@extends('admin.layouts.admin')
@section('title', 'Certified Fire Fighter')
@section('pageurl', admin_url('fire/certified-fire-fighter/list'))


@section('content')
    <div class="clearfix"></div>
    <div class="page-titles">
        <div class="d-flex align-items-center">

        </div>

    </div>

    <div class="content-body  default-height">
        <div class="container-fluid main-content">
            <!-- row -->
            <div class="row">

                <div class="col-12">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title"></h4>
                                <div class="align-back-btc">
                                    <x-button-back
                                        href="{{ admin_url('fire/certified-fire-fighter/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form">
                                    <form method="POST" id="addfire"
                                        action="{{ admin_url('fire/certified-fire-fighter/add/submit') }}">
                                        @csrf
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="card-header-inner d-flex justify-content-between">
                                                    <h4 class="text-white">Certified Fire Fighter</h4>
                                                </div>
                                            </div>
                                            <div class="row">

                                                <div class="col-md-4 form-input">
                                                    <label class="form-label">Certified Fire Fighter No</label>
                                                    <input type="text" class="form-control"
                                                        name="certified_fire_fighter_no" id="certified_fire_fighter_no"
                                                        value = "{{ getsequence('certifiedFireFighterNo') }}" readonly>
                                                </div>
                                                <input type="hidden" class="form-control" name="docNo_id"
                                                    value="{{ encryptId($staticDocno->id) }}">

                                                <div class="col-md-4 form-input">

                                                    <label class="form-label">Doc. No</label>
                                                    <input type="text" class="form-control" name="doc_no" id="doc_no"
                                                        readonly value="{{ $staticDocno->doc_no }}">
                                                </div>

                                                <div class="col-md-4 form-input">
                                                    <label class="form-label">Issue Dt.</label>

                                                    <div class="input-group date form-input custom-height">
                                                        <input type="text" class="form-control" name="issue_date"
                                                            id="issue_date" readonly
                                                            value="{{ Displaydateformat($staticDocno->issue_date) }}">
                                                        <div class="input-group-addon input-group-text">
                                                            <span class="fa fa-calendar"></span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-4 form-input mt-2">
                                                    <label class="form-label">Rev. & Dt.</label>
                                                    <input type="text" class="form-control" name="rev_dt" id="rev_dt"
                                                        readonly value="{{ $staticDocno->rev_dt }}">
                                                </div>



                                            </div>

                                        </div>

                                        <div class="form-wrapper">
                                            <div class="card-header-inner d-flex justify-content-between">
                                                <h4 class="text-white ms-2">
                                                    {{ __('inspection.certified_fire_fighter_details') }}</h4>
                                                <button class="btn btn-primary add-row mb-2 " type="button" id="add-row"
                                                    style="margin-left: 10px;  margin-right: 10px; width: 84px;">
                                                    Add
                                                </button>
                                            </div>

                                            <div class="row mt-4 form-set">

                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.sr_no') }}</label>
                                                        <input type="text" name="sr_no[1]" id = "sr_no"
                                                            class="form-control"
                                                            value="{{ FireSequence(CERTIFIED_FIRE_FIGHTER) }}" readonly>
                                                    </div>

                                                </div>

                                                <div class="col-md-4 mb-2 form-input">
                                                    <label class="form-label require">Unit Name</label>
                                                    <select name="unit_id[1]"
                                                        class="form-control unit-select  single-select select2"
                                                        style="width: 100%" id="unit_id">
                                                        <option value="">Select the Unit Name</option>
                                                        @foreach ($unit as $list)
                                                            <option value="{{ encryptId($list->id) }}">
                                                                {{ $list->unit_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-md-4  mb-2 form-input">
                                                    <label for="department-select"
                                                        class="form-label  require">Department</label>
                                                    <select class="form-control single-select department-select"
                                                        name="department_id[1]" style="width: 100%" id="department_id">
                                                        <option value="">Select Department</option>

                                                    </select>
                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.exact_location') }}</label>
                                                        <input type="text" name="exact_location[1]" id = "exact_location"
                                                            class="form-control exact_location"
                                                            placeholder="Exact Location">

                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-2 form-input">
                                                    <label class=" form-label require">Employee Name</label>
                                                    <select name="emp_name[1]"
                                                        class="form-control emp-select single-select select2"
                                                        style="width:100%" id="emp_name">
                                                        <option value="">Select Employee</option>
                                                    </select>
                                                </div>

                                                <div class="col-md-4 mb-2 form-input">
                                                    <label class="form-label require">Emp Code</label>
                                                    <input type="text" name="emp_code[1]" class="form-control"
                                                        id="emp_code" readonly>
                                                </div>

                                                <div class="col-md-4 mb-2 form-input">
                                                    <label class="form-label require">Contact Number</label>
                                                    <input type="text" name="emp_phone[1]" class="form-control"
                                                        id="emp_phone" readonly>
                                                </div>
                                                <div class="col-md-4 mb-2 form-input">
                                                    <label class="form-label require">Status</label>
                                                    <select class="form-control single-select" name="emp_status[1]"
                                                        style="width: 100%" id="emp_status">
                                                        <option value="">Select Status </option>
                                                        <option value="{{ encryptId(1) }}">Active</option>
                                                        <option value="{{ encryptId(2) }}">Not Active</option>
                                                    </select>
                                                </div>
                                            </div>



                                        </div>

                                        <div class="submit-button" style="text-align: right;">
                                            <x-button-submit class="submit"></x-button-submit>
                                            <x-button-reset class=""></x-button-reset>
                                            <x-button-cancel
                                                href="{{ admin_url('fire/certified-fire-fighter/list') }}"></x-button-cancel>
                                        </div>

                                    </form>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@stop

@push('script')
    <script type="text/javascript" nonce="projectcab">
        $(document).ready(function() {
            $('#resetform').on('click', function(e) {
                e.preventDefault();
                location.reload();
            });


            $(function() {
                $.validator.addMethod("noSpaces", function(value, element) {
                    return this.optional(element) || value.trim().length > 0;
                }, "This field cannot contain only spaces");

                $('#addfire').validate({
                    rules: {
                        "unit_id[1]": {
                            required: true,
                        },
                        "department_id[1]": {
                            required: true,
                        },
                        "emp_name[1]": {
                            required: true,
                        },
                        "emp_status[1]": {
                            required: true,
                        },
                        "exact_location[1]": {
                            required: true,
                        },

                    },
                    messages: {

                        "unit_id[1]": {
                            required: "Please   Select The Unit",
                        },
                        "department_id[1]": {
                            required: "Please Select The Department",
                        },
                        "emp_name[1]": {
                            required: "Please Select The Employee",
                        },
                        "emp_status[1]": {
                            required: "Please Select The Employee Status",
                        },
                        "exact_location[1]": {
                            required: "Please enter the Exact Location",
                        },


                    },
                    errorElement: 'span',
                    errorPlacement: function(error, element) {
                        error.addClass('invalid-feedback');
                        element.closest('.form-input').append(error);
                    },
                    highlight: function(element, errorClass, validClass) {
                        $(element).addClass('is-invalid');
                    },
                    unhighlight: function(element, errorClass, validClass) {
                        $(element).removeClass('is-invalid');
                    },
                    submitHandler: function(form) {
                        form.submit();

                    },
                    invalidHandler: function(event, validator) {
                        var errors = validator.numberOfInvalids();
                    }
                });
            });


        });

        let form_set_count = 2;
        let formIndex = 1;
        const minFormSets = 1;
        const maxFormSets = 200;
        let serial_number = 2;
        const maxObsSets = 5;

        $(document).ready(function() {
            $(document).on('click', '#add-row', function() {
                let currentFormSets = $('.form-wrapper .form-set').length;



                if (currentFormSets >= maxFormSets) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Maximum Hooter Inspection CheckList Reached',
                        text: 'You can only add up to 200 Hooter Inspection CheckList.',
                        confirmButtonColor: '#3085d6'
                    });
                    return;
                }

                let newSerialNumber = 'SNO-' + ('00000' + serial_number).slice(-5);

                var newFormSet = `
                        <div class="row mt-4 form-set">

                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.sr_no') }}</label>
                                                        <input type="text" name="sr_no[${form_set_count}]" id = "sr_no_[${form_set_count}]"
                                                            class="form-control" readonly>
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-2 form-input">
                                                    <label class="form-label require">Unit Name</label>
                                                    <select name="unit_id[${form_set_count}]"
                                                        class="form-control unit-select  single-select select2"
                                                        style="width: 100%" id="unit_id_${form_set_count}">
                                                        <option value="">Select the Unit Name</option>
                                                        @foreach ($unit as $list)
                                                            <option value="{{ encryptId($list->id) }}">
                                                                {{ $list->unit_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                 <div class="col-md-4  mb-2 form-input">
                                                    <label for="department-select" class="form-label  require">Department</label>
                                                    <select class="form-control single-select department-select" name="department_id[${form_set_count}]"
                                                        style="width: 100%" id="department_id_${form_set_count}">
                                                        <option value="">Select Department</option>

                                                    </select>
                                                </div>


<div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.exact_location') }}</label>
                                                        <input type="text" name="exact_location[${form_set_count}]"
                                                            id = "exact_location[${form_set_count}]"
                                                            class="form-control exact_location"
                                                            placeholder="Exact Location"
                                                            >

                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-2 form-input">
                                                    <label class=" form-label require">Employee Name</label>
                                                    <select name="emp_name[${form_set_count}]"
                                                        class="form-control emp-select single-select select2"
                                                        style="width:100%" id="emp_name_${form_set_count}">
                                                        <option value="">Select Employee</option>
                                                    </select>
                                                </div>

                                                <div class="col-md-4 mb-2 form-input">
                                                    <label class="form-label require">Emp Code</label>
                                                    <input type="text" name="emp_code[${form_set_count}]" class="form-control"
                                                        id="emp_code_${form_set_count}">
                                                </div>

                                                <div class="col-md-4 mb-2 form-input">
                                                    <label class="form-label require">Contact Number</label>
                                                    <input type="text" name="emp_phone[${form_set_count}]" class="form-control"
                                                        id="emp_phone_${form_set_count}">
                                                </div>
                                                <div class="col-md-4 mb-2 form-input">
                                                    <label class="form-label require">Status</label>
                                                    <select class="form-control single-select" name="emp_status[${form_set_count}]"
                                                        style="width: 100%" id="emp_status_${form_set_count}">
                                                        <option value="">Select Status </option>
                                                        <option value="{{ encryptId(1) }}">Active</option>
                                                        <option value="{{ encryptId(2) }}">Not Active</option>
                                                    </select>
                                                </div>

                                                 <div class="col-md-2 text-right mb-1  mt-4">
                                                    <button class="btn btn-danger remove-row" type="button"
                                                        style="margin:10px;"><i class="fa fa-trash"></i></button>

                                                </div>
                                            </div>
                      `;

                let newFormSetElement = $(newFormSet);

                let locationSelect = newFormSetElement.find('select[name^="department"]');

                $('.form-wrapper').append(newFormSetElement);

                $("select[name='unit_id[" + form_set_count + "]']").rules('add', {
                    required: true,
                    messages: {
                        required: 'Please select the Unit',
                    }
                });

                $("select[name='department_id[" + form_set_count + "]']").rules('add', {
                    required: true,
                    messages: {
                        required: 'Please select the Department',
                    }
                });
                $("select[name='emp_name[" + form_set_count + "]']").rules('add', {
                    required: true,
                    messages: {
                        required: 'Please select the Employee Name',
                    }
                });
                $("select[name='emp_status[" + form_set_count + "]']").rules('add', {
                    required: true,
                    messages: {
                        required: 'Please select the Employee Status',
                    }
                });
                $("input[name='exact_location[" + form_set_count + "]']").rules('add', {
                    required: true,
                    messages: {
                        required: 'Please Enter the Exact Location',
                    }
                });

                serial_number++;
                form_set_count++;
                updatePageIndices();
            });


            $(document).ready(function() {
                // for unit select
                $(document).on('change', '.unit-select', function() {
                    let row = $(this).closest('.form-set');
                    let unitId = $(this).val();

                    if (unitId) {
                        $.ajax({
                            url: "{{ admin_url('department/ajax-list') }}/" + unitId +
                                "/0",
                            type: 'GET',
                            dataType: 'json',
                            success: function(data) {
                                let departmentSelect = row.find('.department-select');
                                departmentSelect.empty().append(
                                    '<option value="">Select Department</option>');
                                $.each(data, function(key, value) {
                                    departmentSelect.append('<option value="' +
                                        value.id +
                                        '">' + value.name + '</option>');
                                });
                                departmentSelect.trigger('change');
                            },
                            error: function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Error fetching department. Please try again.'
                                });
                            }
                        });
                    } else {
                        row.find('.department-select').empty().append(
                            '<option value="">Select Department</option>').trigger('change');
                    }
                });

                // for department select
                $(document).on('change', '.unit-select, .department-select', function() {
                    let row = $(this).closest('.form-set');
                    let unitId = row.find('.unit-select').val();
                    let departmentId = row.find('.department-select').val();

                    if (unitId && departmentId) {
                        $.ajax({
                            url: "{{ admin_url('ohc/first-aider/employeename') }}",
                            type: 'GET',
                            data: {
                                unit_id: unitId,
                                department: departmentId
                            },
                            dataType: 'json',
                            success: function(response) {
                                let empSelect = row.find('.emp-select');
                                empSelect.empty().append(
                                    '<option value="">Select Employee</option>'
                                );
                                $.each(response.employee, function(index,
                                    employee) {
                                    empSelect.append(
                                        '<option value="' +
                                        employee.id + '">' +
                                        employee.emp_name +
                                        '</option>');
                                });
                            },
                            error: function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Error fetching employees. Please try again.'
                                });
                            }
                        });
                    } else {
                        row.find('.emp-select').empty().append(
                            '<option value="">Select Employee</option>');
                    }
                });

                // Employee Select
                $(document).on('change', '.emp-select', function() {
                    let row = $(this).closest('.form-set');
                    let emp_id = $(this).val();
                    let unitId = row.find('.unit-select').val();
                    let departmentId = row.find('.department-select').val();

                    if (emp_id) {
                        // Check for duplicate
                        let isDuplicate = false;
                        $('.emp-select').each(function() {
                            let otherRow = $(this).closest('.form-set');
                            if (
                                $(this).val() === emp_id &&
                                otherRow.find('.unit-select').val() ===
                                unitId &&
                                otherRow.find('.department-select').val() ===
                                departmentId &&
                                row[0] !== otherRow[0]
                            ) {
                                isDuplicate = true;
                                return false;
                            }
                        });

                        if (isDuplicate) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Duplicate Employee',
                                text: 'This employee is already selected for the same unit and department!',
                                confirmButtonColor: '#3085d6'
                            });
                            $(this).val('').trigger('change');
                            return;
                        }

                        //employee details
                        $.ajax({
                            url: "{{ admin_url('fire/certified-fire-fighter/employeedetails') }}",
                            type: 'GET',
                            data: {
                                emp_name: emp_id
                            },
                            dataType: 'json',
                            success: function(data) {
                                row.find('input[name^="emp_code"]').val(data.employee
                                    ?.employee_id || "").prop('readonly', true);
                                row.find('input[name^="emp_phone"]').val(data.employee
                                    ?.mobile_no || "").prop('readonly', true);
                            },
                            error: function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Error fetching employee details. Please try again.'
                                });
                            }
                        });

                    } else {
                        row.find('input[name^="emp_code"], input[name^="emp_phone"]')
                            .val('').prop('readonly', true);
                    }
                });
            });


        });


        function updatePageIndices() {
            $('.form-wrapper .form-set').each(function(index) {
                let idx = index + 1;
                let newSerialNumber = 'SNO-' + ('000000' + idx).slice(-6);
                $(this).find("input[name^='sr_no']").val(newSerialNumber);

                $(this).find('input[name^="sr_no"]').attr('name', 'sr_no[' + idx + ']');
                $(this).find('select[name^="unit_id"]').attr('name', 'unit_id[' + idx + ']');
                $(this).find('select[name^="department_id"]').attr('name', 'department_id[' + idx + ']');
                $(this).find('select[name^="emp_name"]').attr('name', 'emp_name[' + idx + ']');
                $(this).find('input[name^="emp_code"]').attr('name', 'emp_code[' + idx + ']');
                $(this).find('input[name^="emp_phone"]').attr('name', 'emp_phone[' + idx + ']');
                $(this).find('input[name^="exact_location"]').attr('name', 'exact_location[' + idx + ']');
                $(this).find('select[name^="emp_status"]').attr('name', 'emp_status[' + idx + ']');


                $(this).find('select').select2();
            });
        }

        $(document).on('click', '.remove-row', function() {
            let currentFormSets = $('.form-wrapper .form-set').length;

            if (currentFormSets <= minFormSets) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Minimum One CheckList Required',
                    text: 'At least One Hooter Inspection Checklist is required.',
                    confirmButtonColor: '#3085d6'
                });
                return;
            }
            $(this).closest('.form-set').remove();
            updatePageIndices();
        });
    </script>
@endpush
