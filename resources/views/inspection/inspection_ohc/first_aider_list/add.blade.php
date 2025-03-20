@extends('admin.layouts.admin')
@section('title', 'First Aider List')
@section('pageurl', admin_url('ohc/first-aider/list'))


@section('content')
    <div class="clearfix"></div>
    <div class="page-titles">
        <div class="d-flex align-items-center">
            {{-- <h4 class="text-black">Company Add</h4> --}}

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
                                    <x-button-back href="{{ admin_url('ohc/first-aider/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form">
                                    <form method="POST" id="medicineRequisitionFloor"
                                        action="{{ admin_url('ohc/first-aider/add/submit') }}" autocomplete="off"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" value="3" name="ohc_type">
                                        <div class="row">
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Document Number</label>
                                                    <input type="text" name="document_no" id = "document_no"
                                                        class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label for="rate" class="form-label require ">Issued
                                                        Date</label>
                                                    <div class="input-group date form-input custom-height">
                                                        <input type="text" name="issue_date" id="issue_date"
                                                            class="form-control"autocomplete="off">
                                                        <div class="input-group-addon input-group-text">
                                                            <span class="fa fa-calendar"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label for="rate" class="form-label require ">Review
                                                        Date</label>
                                                    <div class="input-group date form-input custom-height">
                                                        <input type="text" value="{{ getDocumentReviewDate('0') }}"
                                                            name="review_date" id="review_date" class="form-control"
                                                            autocomplete="off" readonly>

                                                        <div class="input-group-addon input-group-text">
                                                            <span class="fa fa-calendar"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label for="rate" class="form-label require ">
                                                        Last Updated Date</label>
                                                    <div class="input-group date form-input custom-height">
                                                        <input type="text" name="last_updated_date"
                                                            id="last_updated_date" class="form-control"autocomplete="off">
                                                        <div class="input-group-addon input-group-text">
                                                            <span class="fa fa-calendar"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label for="rate" class="form-label require ">
                                                        Next review Date</label>
                                                    <div class="input-group date form-input custom-height">
                                                        <input type="text" name="next_review_date" id="next_review_date"
                                                            class="form-control"autocomplete="off">
                                                        <div class="input-group-addon input-group-text">
                                                            <span class="fa fa-calendar"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mt-2">
                                            <div class="card-header-inner">
                                                <h4 class="text-white">Employee details</h4>

                                            </div>
                                            <div
                                                class="d-flex justify-content-end align-items-center me-2 mb-3 button-container">
                                                <button class="btn btn-primary add-row me-3" type="button" id="add-row"
                                                    style="width: 84px;">
                                                    Add
                                                </button>

                                            </div>


                                        </div>

                                        <div class="table-responsive">
                                            <div class="col-md-12">
                                                <table class="table table-bordered ">

                                                    <thead class="bg-secondary" style="color: #ffff">
                                                        <tr>
                                                            <th>Unit</th>
                                                            <th>Department</th>
                                                            <th>Employee Name</th>
                                                            <th>Designation</th>
                                                            <th>Mobile Number</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>

                                                    <tbody id="medicine-tbody">
                                                        <tr>
                                                            <td>
                                                                <div class="form-group form-input">
                                                                    <label class="require">Unit Name</label>
                                                                    <select name="unit_id[0]"
                                                                        class="form-control unit-select  single-select select2"
                                                                        style="width: 100%">
                                                                        <option value="">Select the Unit Name</option>
                                                                        @foreach ($unit as $list)
                                                                            <option value="{{ encryptId($list->id) }}">
                                                                                {{ $list->unit_name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="form-group form-input">
                                                                    <label class="require">Department</label>
                                                                    <select name="department_id[0]"
                                                                        class="form-control department-select select2 single-select"
                                                                        style="width:100%">
                                                                        <option value="">Select Department</option>
                                                                    </select>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="form-group form-input">
                                                                    <label class="require">Employee Name</label>
                                                                    <select name="emp_name[0]"
                                                                        class="form-control emp-select single-select select2"
                                                                        style="width:100%">
                                                                        <option value="">Select Employee</option>
                                                                    </select>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="form-group form-input">
                                                                    <label class="require">Designation</label>
                                                                    <input type="text" name="designation_id[0]"
                                                                        class="form-control designation-field" readonly>
                                                                </div>
                                                            </td>

                                                            <td>
                                                                <div class="form-group form-input">
                                                                    <label class="require">Mobile Number</label>
                                                                    <input type="text" name="mobile_no[0]"
                                                                        class="form-control mobile-field" readonly>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="d-flex justify-content-center align-items-center bg-danger mt-2 ml-2 text-white rounded delete-row"
                                                                    style="width: 30px; height: 30px;">
                                                                    <i class="fa-solid fa-trash"></i>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                </div>
                                <hr>
                                <div class="submit-button" style="text-align: right;">
                                    <x-button-submit class="submit"></x-button-submit>
                                    <x-button-reset class="submit"></x-button-reset>
                                    <x-button-cancel href="{{ admin_url('ohc/first-aider/list') }}"></x-button-cancel>
                                </div>

                                </form>
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
        });
        var IssueDatepicker = flatpickr("#issue_date", {
            dateFormat: "d-m-Y",
            minDate: new Date()

        });
        var Datepicker = flatpickr("#next_review_date", {
            dateFormat: "d-m-Y",


        });
        var dueDate = flatpickr("#last_updated_date", {
            dateFormat: "d-m-Y",


        });
        $(document).ready(function() {
            let rowCount = 1;

            $(".add-row").click(function() {
    let rowCount = $('#medicine-tbody tr').length;
    let newRow = `
        <tr>
            <td>
                <div class="form-group form-input">
                    <label class="require">Unit Name</label>
                    <select name="unit_id[${rowCount}]" class="form-control unit-select select2" style="width: 100%">
                        <option value="">Select the Unit Name</option>
                        @foreach ($unit as $list)
                            <option value="{{ encryptId($list->id) }}">{{ $list->unit_name }}</option>
                        @endforeach
                    </select>
                </div>
            </td>
            <td>
                <div class="form-group form-input">
                    <label class="require">Department</label>
                    <select name="department_id[${rowCount}]" class="form-control department-select select2" style="width:100%">
                        <option value="">Select Department</option>
                    </select>
                </div>
            </td>
            <td>
                <div class="form-group form-input">
                    <label class="require">Employee Name</label>
                    <select name="emp_name[${rowCount}]" class="form-control emp-select select2" style="width:100%">
                        <option value="">Select Employee</option>
                    </select>
                </div>
            </td>
            <td>
                <div class="form-group form-input">
                    <label class="require">Designation</label>
                    <input type="text" name="designation_id[${rowCount}]" class="form-control designation-field" readonly>
                </div>
            </td>
            <td>
                <div class="form-group form-input">
                    <label class="require">Mobile Number</label>
                    <input type="text" name="mobile_no[${rowCount}]" class="form-control mobile-field" readonly>
                </div>
            </td>
            <td>
                <div class="d-flex justify-content-center align-items-center bg-danger mt-2 ml-2 text-white rounded delete-row" style="width: 30px; height: 30px; cursor: pointer;">
                    <i class="fa-solid fa-trash"></i>
                </div>
            </td>
        </tr>
    `;

    // Append the new row first
    $('#medicine-tbody').append(newRow);

    // Initialize Select2 for new elements
    $('.select2').select2({
        width: '100%'
    });

    // Now apply validation rules
    $('select[name="unit_id[' + rowCount + ']"]').rules('add', {
        required: true,
        messages: {
            required: 'This unit name is required'
        }
    });

    $('select[name="department_id[' + rowCount + ']"]').rules('add', {
        required: true,
        messages: {
            required: 'This department name is required'
        }
    });

    $('select[name="emp_name[' + rowCount + ']"]').rules('add', {
        required: true,
        messages: {
            required: 'This Employee name is required'
        }
    });

    $('input[name="designation_id[' + rowCount + ']"]').rules('add', {
        required: true,
        messages: {
            required: 'This Designation name is required'
        }
    });

    $('input[name="mobile_no[' + rowCount + ']"]').rules('add', {
        required: true,
        messages: {
            required: 'This mobile number is required'
        }
    });
});

            // Load departments based on unit selection
            $(document).on('change', '.unit-select', function() {
                let unitId = $(this).val();
                let row = $(this).closest('tr');

                if (unitId) {
                    $.ajax({
                        url: "{{ admin_url('department/ajax-list') }}/" + unitId + "/0",
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            let departmentSelect = row.find('.department-select');
                            departmentSelect.empty().append(
                                '<option value="">Select Department</option>');
                            $.each(data, function(key, value) {
                                departmentSelect.append('<option value="' + value.id +
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

            // Load employees based on unit and department selection
            $(document).on('change', '.unit-select, .department-select', function() {
                let row = $(this).closest('tr');
                let unitId = row.find('.unit-select').val();
                let departmentId = row.find('.department-select').val();

                if (unitId && departmentId) {
                    $.ajax({
                        url: "{{ admin_url('ohc/first-aider/emplyeename') }}",
                        type: 'GET',
                        data: {
                            unit_id: unitId,
                            department: departmentId
                        },
                        dataType: 'json',
                        success: function(response) {
                            let empSelect = row.find('.emp-select');
                            empSelect.empty().append(
                                '<option value="">Select Employee</option>');
                            $.each(response.employee, function(index, employee) {
                                empSelect.append('<option value="' + employee.login_id +
                                    '">' + employee.emp_name + '</option>');
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
                    row.find('.emp-select').empty().append('<option value="">Select Employee</option>');
                }
            });

            // Load employee details
            $(document).on('change', '.emp-select', function() {
                let emp_id = $(this).val();
                let row = $(this).closest('tr');
                let unitId = row.find('.unit-select').val();
                let departmentId = row.find('.department-select').val();

                if (emp_id) {
                    let isDuplicate = false;

                    $(".emp-select").each(function() {
                        let currentEmp = $(this).val();
                        let currentRow = $(this).closest('tr');
                        let currentUnit = currentRow.find('.unit-select').val();
                        let currentDepartment = currentRow.find('.department-select').val();

                        if (currentEmp === emp_id && currentUnit === unitId && currentDepartment ===
                            departmentId && row[0] !== currentRow[0]) {
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
                        row.find('.emp-select').val('').trigger('change');
                        return;
                    }

                    $.ajax({
                        url: "{{ admin_url('ohc/first-aider/employeedetails') }}",
                        type: 'GET',
                        data: {
                            emp_name: emp_id
                        },
                        dataType: 'json',
                        success: function(data) {
                            row.find('.designation-field').val(data.employee ? data.employee
                                .designation : "").prop('readonly', true);
                            row.find('.mobile-field').val(data.employee ? data.employee
                                .mobile_no : "").prop('readonly', true);
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
                    row.find('.designation-field, .mobile-field').val('').prop('readonly', true);
                }
            });

            // Handle row deletion
            $(document).on("click", ".delete-row", function() {
                let rowCount = $('#medicine-tbody tr').length;
                if (rowCount > 1) {
                    $(this).closest("tr").remove();
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Warning',
                        text: 'At least one row is required.',
                        confirmButtonColor: '#3085d6'
                    });
                }
            });
        });



        $(function() {

            $.validator.addMethod(
                "regex",
                function(value, element, regex) {
                    return this.optional(element) || regex.test(value);
                },
                "Invalid format."
            );

            $('#medicineRequisitionFloor').validate({
                rules: {

                    next_review_date: {
                        required: true,
                    },
                    last_updated_date: {
                        required: true,
                    },
                    issue_date: {
                        required: true,
                    },
                    document_no: {
                        required: true,
                        minlength: 3,
                        maxlength: 30,
                    },
                    first_aider: {
                        required: true,
                        minlength: 3,
                        maxlength: 30,
                    },
                    first_aid_box_no: {
                        required: true,
                        digits: true,
                    },
                    shift: {
                        required: true,
                    },
                    review_date: {
                        required: true,
                    },
                    date: {
                        required: true,
                    },
                    'emp_name[0]': {
                        required: true,
                    },
                    'designation_id[0]': {
                        required: true,

                    },
                    'unit_id[0]': {
                        required: true,

                    },
                    'department_id[0]': {
                        required: true,

                    },
                    'mobile_no[0]': {
                        required: true,
                        minlength: 3,
                        maxlength: 600,
                    }

                },
                messages: {
                    next_review_date: {
                        required: "Please select the Next Review Date.",
                    },
                    last_updated_date: {
                        required: "Please select the Last Updated date.",
                    },
                    issue_date: {
                        required: "Please select the issue date.",
                    },
                    document_no: {
                        required: "Document Number is Required",
                        minlength: "Minimum Characters should be 3",
                        maxlength: "Maximum Characters should not exceed 100",
                    },

                    review_date: {
                        required: "Please select the review date.",
                    },
                    'emp_name[0]': {
                        required: 'Employee Name is required',
                    },
                    'department_id[0]': {
                        required: 'Department name is required',

                    },
                    'unit_id[0]': {
                        required: 'Unit Name is required',

                    },
                    'designation_id[0]': {
                        required: 'Desigantion Name is required',

                    },
                    'mobile_no[0]': {
                        required: 'mobile number is required',
                    }
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
                    console.log("Form has " + errors + " invalid fields.");
                },
            });


        });
    </script>
@endpush
