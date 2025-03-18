@extends('admin.layouts.admin')
@section('title', 'Accident Report')
@section('pageurl', admin_url('accidentReport/list'))


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

                                <div class="align-back-btc">
                                    <x-button-back href="{{ admin_url('accidentReport/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form">
                                    <form method="POST" id="accidentReportAdd"
                                        action="{{ admin_url('accidentReport/add/submit') }}">
                                        @csrf
                                        <input type="hidden" name="unit_id" id="hidden_unit_id">
                                        <input type="hidden" name="department_id" id="hidden_department_id">
                                        <div class="row">
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label">Sr. No</label>
                                                    <input type="text" name="accident_report_no" id="accident_report_no"
                                                        class="form-control" placeholder=""
                                                        value = "{{ getsequence('accidentReportNo') }}" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Date and Time</label>
                                                    <input type="text" name="date_and_time" id="date_and_time"
                                                        class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <div class="form-group form-input">
                                                    <label for="emp_code" class="form-label require">Employee Code</label>
                                                    <select name="emp_code" id="emp_code"
                                                        class="form-control single-select" style="width: 100%">
                                                        <option value="">Select Employee Code</option>
                                                        @foreach ($employeeList as $emp)
                                                            <option value="{{ $emp->emp_id }}">
                                                                {{ $emp->emp_id }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Unit</label>
                                                    <select name="unit_id" id="unit_id" class="form-control single-select"
                                                        style="width: 100%" required>
                                                        <option value="">Select Unit</option>
                                                        @foreach ($unitList as $unit)
                                                            <option value="{{ encryptId($unit->id) }}">
                                                                {{ $unit->unit_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Designation</label>
                                                    <input type="text" name="designation" id="designation"
                                                        class="form-control" placeholder="Designation">
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Department</label>
                                                    <select name="department_id" id="department_id"
                                                        class="form-control single-select" style="width: 100%">
                                                        <option value="">Select Department</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mt-3">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Shift</label>
                                                    <input type="text" name="shift" id="shift" class="form-control"
                                                        placeholder="Shift">
                                                </div>
                                            </div>
                                            <div class="col-md-4 mt-3">
                                                <div class="form-group form-input">
                                                    <label for="location_id" class="form-label require">Accident
                                                        Location</label>
                                                    <select name="location_id" id="location_id"
                                                        class=" form-control single-select" style="width: 100%">
                                                        <option value="">Select Accident Location</option>
                                                        @foreach ($locationList as $loc)
                                                            <option value="{{ encryptId($loc->id) }}">
                                                                {{ $loc->location_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-4 mt-3">
                                                <div class="form-group form-input">
                                                    <label class="form-label">Exact Location</label>
                                                    <input type="text" name="exact_location" id="exact_location"
                                                        class="form-control" placeholder="Exact Location">
                                                </div>
                                            </div>

                                            <div class="col-md-4 mt-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label">Address of the injured person</label>
                                                    <textarea class="form-control" name="address_of_the_injuredperson" id="address_of_the_injuredperson"></textarea>

                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="submit-button" style="text-align: right;">
                                            <x-button-submit class="submit"></x-button-submit>
                                            <x-button-reset class=""></x-button-reset>
                                            <x-button-cancel
                                                href="{{ admin_url('accidentReport/list') }}"></x-button-cancel>
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

            flatpickr("#date_and_time", {
                enableTime: true,
                dateFormat: "d-m-Y H:i",
                time_24hr: true,
                maxDate: new Date()
            });


            $('#resetform').on('click', function(e) {
                e.preventDefault();
                location.reload();
            });

            $('#emp_code').change(function() {
                var emp_code = $(this).val();

                if (emp_code) {
                    $.ajax({
                        url: "{{ url('accidentReport/fetchEmployeeDetails') }}/" + emp_code,
                        type: "GET",
                        dataType: "json",
                        success: function(response) {
                            if (response.employee) {
                                $('#designation').val(response.employee.designation);

                                // Reset and Set Unit
                                $('#unit_id').val('').prop('disabled', false);
                                if (response.employee.unit_id) {
                                    // Check if the unit already exists in the dropdown
                                    if ($('#unit_id option[value="' + response.employee
                                            .unit_id + '"]').length > 0) {
                                        $('#unit_id').val(response.employee.unit_id).prop(
                                            'disabled', true);
                                    } else {
                                        $('#unit_id').append('<option value="' + response
                                                .employee.unit_id + '">' +
                                                response.employee.unit_name + '</option>')
                                            .val(response.employee.unit_id).prop('disabled',
                                                true);
                                    }

                                    // Store encrypted unit_id in the hidden input (for secure submission)
                                    $('#hidden_unit_id').val(response.employee
                                        .encrypted_unit_id);
                                }
                                // Reset and Set Department
                                if (response.employee.department_id) {
                                    $('#department_id').html('<option value="' + response
                                            .employee.department_id + '">' +
                                            response.employee.department_name + '</option>')
                                        .val(response.employee.department_id).prop('disabled',
                                            true);

                                    $('#hidden_department_id').val(response.employee
                                        .encrypted_department_id);
                                } else {
                                    $('#department_id').html(
                                        '<option value="">Select Department</option>').prop(
                                        'disabled', false);
                                    $('#hidden_department_id').val('');
                                }
                            } else {
                                Swal.fire({
                                    icon: "error",
                                    title: "Error",
                                    text: "Employee data could not be fetched."
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                icon: "error",
                                title: "Error",
                                text: "An error occurred while fetching employee details."
                            });
                        }
                    });
                } else {
                    $('#designation').val('');
                    $('#unit_id').val('').prop('disabled', false);
                    $('#department_id').html('<option value="">Select Department</option>').prop('disabled',
                        false);
                    $('#hidden_unit_id').val('');
                    $('#hidden_department_id').val('');
                }
            });


            function unitChangeHandler() {
                var unitId = $(this).val();
                if (unitId) {
                    $.ajax({
                        url: "{{ admin_url('department/ajax-list') }}/" + unitId + "/0",
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            var departmentOptions = '<option value="">Select Department</option>';
                            $.each(data, function(key, value) {
                                departmentOptions += '<option value="' + value.id + '">' + value
                                    .name + '</option>';
                            });
                            $('#department_id').html(departmentOptions).prop('disabled', false);
                        },
                        error: function(xhr) {
                            alert('Error fetching department. Please try again.');
                        }
                    });
                } else {
                    $('#department_id').html('<option value="">Select Department</option>').prop('disabled', true);
                }
            }

            $('#unit_id').change(unitChangeHandler);


            $(function() {
                $('#accidentReportAdd').validate({
                    rules: {
                        date_and_time: {
                            required: true,
                        },
                        unit_id: {
                            required: true,
                        },
                        shift: {
                            required: true,
                            minlength: 2,
                            maxlength: 2000,
                            pattern: /^[a-zA-Z0-9\s\-_'"()]+$/,
                        },
                        exact_location: {
                            pattern: /^[a-zA-Z0-9\s\-\_\'\"()\n\r]+$/,
                        },
                        location_id: {
                            required: true,
                        },
                        designation: {
                            required: true,
                            pattern: /^[a-zA-Z\s\-\_\'\"()\n\r]+$/,
                        },
                        department_id: {
                            required: true,
                        },
                        emp_code: {
                            required: true,
                        },
                        address_of_the_injuredperson: {
                            required: true,
                            minlength: 3,
                            maxlength: 2000,
                            pattern: /^[a-zA-Z0-9\s\-\_\'\"()\n\r]+$/,
                        },
                    },
                    messages: {
                        date_and_time: {
                            required: "Date and Time is required.",
                        },
                        unit_id: {
                            required: "Unit is required.",
                        },
                        exact_location: {
                            pattern: "Only alphanumeric characters and - _ ' \" ( ) are allowed.",
                        },
                        shift: {
                            required: "Shift is required.",
                            minlength: "Shift Required must be exactly 2 characters.",
                            maxlength: "Brief Description Required must be exactly 2000 characters.",
                            pattern: "Only alphanumeric characters and (-, _, ‘, “, ()) are allowed.",
                        },
                        location_id: {
                            required: "Accident Location is required.",
                        },
                        designation: {
                            required: "Designation is required.",
                            pattern: "Only alphanumeric characters and - _ ' \" ( ) are allowed.",
                        },
                        department_id: {
                            required: "Department is required.",
                        },
                        emp_code: {
                            required: "Employee Code is required.",
                        },
                        address_of_the_injuredperson: {
                            required: "Address of the injured person is required.",
                            minlength: "Minimum 3 characters required.",
                            maxlength: "Maximum 2000 characters allowed.",
                            pattern: "Only alphanumeric characters and - _ ' \" ( ) are allowed.",
                        },
                    },

                    errorElement: 'span',
                    errorPlacement: function(error, element) {
                        error.addClass('invalid-feedback');
                        element.closest('.form-input').append(error);
                    },
                    highlight: function(element) {
                        $(element).addClass('is-invalid');
                    },
                    unhighlight: function(element) {
                        $(element).removeClass('is-invalid');
                    },
                    submitHandler: function(form) {
                        form.submit();
                    },
                    invalidHandler: function(event, validator) {
                        var errors = validator.numberOfInvalids();
                        if (errors) {
                            console.log(`There are ${errors} validation errors.`);
                            validator.errorList.forEach(function(error) {
                                console.log(
                                    `Field: ${error.element.name}, Error: ${error.message}`
                                );
                            });
                        }
                    },
                });
            });
        });
    </script>
@endpush
