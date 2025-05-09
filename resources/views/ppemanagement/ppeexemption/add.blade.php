@extends('admin.layouts.admin')
@section('title', 'PPE Shoe Exemption Request')
@section('pageurl', admin_url('ppe_exemption/list'))
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
                                    <x-button-back href="{{ admin_url('ppe_exemption/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="basic-form">
                                    <form method="POST" id="ppeExemptionForm" enctype="multipart/form-data"
                                        action="{{ admin_url('ppe_exemption/add/submit') }}">
                                        @csrf


                                        <hr>
                                        <div class="row">
                                            @if (checkUserrole(ROLE_SUPERADMIN) || checkUserRole(ROLE_WORKER_REQUEST))
                                                <div class="col-md-4 mb-3">
                                                    <div class="form-group form-input">
                                                        <label class="form-label require">Request For</label>
                                                        <div class="gap-2">
                                                            <label class="form-check form-check-inline">
                                                                <input type="radio" name="request_for"
                                                                    id="request_for_myself" class="form-check-input"
                                                                    value="1">
                                                                <span class="form-check-label">Myself</span>
                                                            </label>
                                                            <label class="form-check form-check-inline">
                                                                <input type="radio" name="request_for"
                                                                    id="request_for_worker" class="form-check-input"
                                                                    value="2">
                                                                <span class="form-check-label">Worker</span>
                                                            </label>
                                                        </div>
                                                        <div class="text-danger"></div>
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-3" id="emp_id_container">
                                                    <div class="form-group form-input">
                                                        <label for="emp_id" class="form-label require">Employee ID</label>
                                                        <select name="emp_id" id="emp_id"
                                                            class="form-select form-select-sm single-select"
                                                            style="width: 100%">
                                                            <option value="">Select the employee</option>

                                                        </select>
                                                        <div class="text-danger"></div>
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-3">
                                                    <div class="form-group form-input">
                                                        <label for="emp_name" class="form-label require">Employee
                                                            Name</label>
                                                        <input type="text" name="emp_name"
                                                            class="form-control form-control-sm" id="emp_name" readonly>
                                                        <div class="text-danger"></div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <div class="form-group form-input">
                                                        <label for="department" class="form-label require">Company</label>
                                                        <input type="text" name="company_id" id="company_id"
                                                            class="form-control form-control-sm" readonly>
                                                        <div class="text-danger"></div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <div class="form-group form-input">
                                                        <label for="department" class="form-label require">location</label>
                                                        <input type="text" name="location_id" id="location_id"
                                                            class="form-control form-control-sm" readonly>
                                                        <div class="text-danger"></div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <div class="form-group form-input">
                                                        <label for="department" class="form-label require">Unit</label>
                                                        <input type="text" name="unit_id" id="unit_id"
                                                            class="form-control form-control-sm" readonly>
                                                        <div class="text-danger"></div>
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-3">
                                                    <div class="form-group form-input">
                                                        <label for="department"
                                                            class="form-label require">Department</label>
                                                        <input type="text" name="department" id="department"
                                                            class="form-control form-control-sm" readonly>
                                                        <div class="text-danger"></div>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="col-md-4 mb-3">
                                                    <div class="form-group form-input">
                                                        <label for="emp_id" class="form-label require">Employee ID</label>
                                                        <input type="text" name="emp_id"
                                                            class="form-control form-control-sm "id="emp_id"
                                                            value="{{ $employee->employee_id }}" readonly>
                                                        <div class="text-danger"></div>

                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <div class="form-group form-input">
                                                        <label for="emp_name" class="form-label require">Employee
                                                            Name</label>
                                                        <input type="text" name="emp_name"
                                                            class="form-control form-control-sm " id="emp_name"
                                                            value="{{ $employee->name }}" readonly>
                                                        <div class="text-danger"></div>

                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <div class="form-group form-input">
                                                        <label for="department" class="form-label require">Company</label>
                                                        <input type="text" name="company_id" id="company_id"
                                                            class="form-control form-control-sm"
                                                            value="{{ getCompanyname($employee->company_id) }}" readonly>
                                                        <div class="text-danger"></div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <div class="form-group form-input">
                                                        <label for="department"
                                                            class="form-label require">location</label>
                                                        <input type="text" name="location_id" id="location_id"
                                                            class="form-control form-control-sm"
                                                            value="{{ getLocationame($employee->location_id) }}" readonly>
                                                        <div class="text-danger"></div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <div class="form-group form-input">
                                                        <label for="department" class="form-label require">Unit</label>
                                                        <input type="text" name="unit_id" id="unit_id"
                                                            class="form-control form-control-sm"
                                                            value="{{ getUnitname($employee->unit_id) }}" readonly>
                                                        <div class="text-danger"></div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <div class="form-group form-input">
                                                        <label for="department"
                                                            class="form-label require">Department</label>
                                                        <input type="text" name="department" id="department"
                                                            class="form-control form-control-sm"
                                                            value="{{ getDepartment($employee->department_id) }}" readonly>
                                                        <div class="text-danger"></div>

                                                    </div>
                                                </div>
                                            @endif
                                            <div class="col-md-4 mb-2">
                                                <label for="date" class="form-label require">From Date</label>
                                                <div class="input-group date form-input">
                                                    <input type="text" class="form-control form-control-sm"
                                                        name="from_date" id="from_date" placeholder="Enter the From Date"
                                                        autocomplete="off">
                                                    <div class="input-group-addon input-group-text">
                                                        <span class="fa fa-calendar"></span>
                                                    </div>
                                                </div>
                                                <div class="text-danger" id="from_date_error"></div>
                                                @error('from_date')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror

                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <label for="date" class="form-label require">To Date</label>
                                                <div class="input-group date form-input">
                                                    <input type="text" class="form-control form-control-sm"
                                                        name="to_date" autocomplete="off" id="to_date"
                                                        placeholder="Enter the To Date">
                                                    <div class="input-group-addon input-group-text">
                                                        <span class="fa fa-calendar"></span>
                                                    </div>

                                                </div>
                                                <div class="text-danger" id="to_date_error"></div>
                                                @error('to_date')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror

                                            </div>


                                            <div class="col-md-12 mb-2">
                                                <label for="reason" class="form-label require">Reason</label>
                                                <textarea name="reason" id="reason" cols="3" rows="4" class="form-control form-control-sm"
                                                    placeholder="Enter the Reason"></textarea>
                                                @error('reason')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                                <div class="text-danger" id="reason_error"></div>
                                            </div>

                                            <div id="file-upload-container" class="row">
                                                <div class="col-12 mb-3">
                                                    <button class="btn btn-primary addmorebutton" type="button"
                                                        id="dynamic-add-more">
                                                        Add
                                                    </button>
                                                </div>
                                                <div class="col-md-4 mb-3 file-upload-block" id="file-upload-0">
                                                    <label for="ppe_file_0" class="form-label require">Reference Document
                                                        Upload</label>
                                                    <input type="file" class="form-control ppe-file-input"
                                                        accept: "image/png, image/jpeg, image/jpg, application/pdf, application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document"
                                                        name="ppe_file[0][]" id="ppe_file_0" multiple>
                                                    <div class="text-danger"></div>
                                                    <small>Allowed file types: png, jpeg, jpg, pdf, .docx, .doc</small>
                                                </div>
                                            </div>




                                        </div>
                                        <hr>
                                        <div class="submit-button float-end">
                                            <x-button-submit class="submit" id="submit"></x-button-submit>
                                            <x-button-reset class="submit"></x-button-reset>
                                            <x-button-cancel
                                                href="{{ admin_url('ppe_exemption/list') }}"></x-button-cancel>
                                        </div>
                                    </form>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        </form>

        <div id="termsModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="termsModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="termsModalLabel">Terms and Conditions</h5>
                    </div>
                    <div class="modal-body">
                        <ul>
                            <!-- Display the 10 points (for example) -->
                            <li>The PPE Shoe Exemption Policy is designed to address cases where individuals are unable to
                                wear safety shoes due to medical, religious, or other legitimate reasons.</li>

                        </ul>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="agreeTerms">
                            <label class="form-check-label" for="agreeTerms">I agree to the terms and conditions.</label>
                        </div>
                    </div>
                    <div class="modal-footer">

                        <button type="button" class="btn btn-secondary" id="closeModal">Close</button>

                    </div>
                </div>
            </div>
        </div>


    </div>

@stop

@push('script')
    <script>
        $(document).ready(function() {
            $('input[name="request_for"]').on("change", function() {
                var requestFor = $(this).val();
                var empIdContainer = $("#emp_id_container");
                var authEmployeeId =
                    "{{ auth()->user()->employee_id }}";
                var authDepartment =
                    "{{ getDepartment(auth()->user()->department_id) ?? 'N/A' }}";
                var authCompany =
                    "{{ getCompanyname(auth()->user()->company_id) ?? 'N/A' }}";
                var authLocation =
                    "{{ getLocationname(auth()->user()->location_id) ?? 'N/A' }}";
                var authUnit =
                    "{{ getUnitname(auth()->user()->unit_id) ?? 'N/A' }}";
                if (requestFor === "1") {
                    // If "Myself" is selected
                    empIdContainer.html(`
                <label for="emp_id" class="form-label require">Employee ID</label>
                <input type="text" name="emp_id" id="emp_id" class="form-control form-control-sm" value="${authEmployeeId}" readonly>
                <div class="text-danger"></div>
            `);

                    $("#emp_name").val("{{ auth()->user()->name }}");
                    $("#department").val(authDepartment);
                    $("#company_id").val(authCompany);
                    $("#unit_id").val(authUnit);
                    $("#location_id").val(authLocation);

                } else if (requestFor === "2") {
                    empIdContainer.html(`
                <label for="emp_id" class="form-label require">Employee ID</label>
                <select name="emp_id" id="emp_id" class="form-select form-select-sm " style="width: 100%">
                    <option value="">Select the Worker</option>
                </select>
                <div class="text-danger"></div>
            `);

                    $('#emp_id').select2({
                        ajax: {
                            url: '{{ admin_url('ppe_request/employeeid') }}',
                            dataType: 'json',
                            delay: 250,
                            data: function(params) {
                                return {
                                    search: params.term
                                };
                            },
                            processResults: function(data) {
                                return {
                                    results: $.map(data, function(item) {
                                        return {
                                            id: item.id,
                                            text: item.text
                                        };
                                    })
                                };
                            }
                        },
                        minimumInputLength: 1,
                        dropdownCssClass: 'form-control',
                        selectionCssClass: 'form-control'
                    });

                    $("#emp_name").val("");
                    $("#department").val("");
                    $("#unit").val("");
                    $("#company").val("");
                }
            });

            // Handle Employee ID change for Worker
            $(document).on("change", "#emp_id", function() {
                var emp_id = $(this).val();

                if (emp_id) {
                    $.ajax({
                        url: "{{ url('ppe_request/fetchEmployeeDetails') }}/" + emp_id,
                        type: "GET",
                        success: function(data) {
                            if (data && data.employee) {
                                $("#emp_name").val(data.employee.emp_name);
                                $("#department").val(data.departments ? data.departments
                                    .department_name : "");
                                $("#unit_id").val(data.units ? data.units
                                    .unit_name : "");
                                $("#company_id").val(data.companys ? data.companys
                                    .company_name : "");
                                $("#location_id").val(data.location ? data.location
                                    .location_name : "");
                            } else {
                                Swal.fire({
                                    icon: "error",
                                    title: "Error",
                                    text: "Employee data could not be fetched.",
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("Error during AJAX request:", error);
                            Swal.fire({
                                icon: "error",
                                title: "Error",
                                text: "An error occurred while fetching employee details.",
                            });
                        },
                    });
                } else {
                    $("#emp_name").val("");
                    $("#department").val(""); // Clear department when no employee is selected
                }
            });
        });


        $(document).ready(function() {

            const maxUploads = 3;

            $('#dynamic-add-more').on('click', function() {
                let currentFileUploads = $('.file-upload-block').length;

                if (currentFileUploads >= maxUploads) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Sorry!',
                        text: 'Maximum 3 records only.',
                    });
                    return;
                }

                let newFileUploadBlock = `
        <div class="col-md-4 mb-3 file-upload-block">
            <label for="ppe_file_${currentFileUploads}" class="form-label require">Reference Document Upload</label>
            <input type="file" class="form-control ppe-file-input" accept="image/png, image/jpeg, image/jpg, application/pdf, application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document"
                name="ppe_file[${currentFileUploads}][]" id="ppe_file_${currentFileUploads}" multiple data-error="Please upload a valid file type">
            <div class="text-danger"></div>
            <small>Allowed file types: png, jpeg , jpg, pdf, .docx, .doc</small>
            <button type="button" class="btn btn-danger btn-sm remove-upload-block">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;

                // Append the new file upload block
                $('#file-upload-container').append(newFileUploadBlock);

                // Add validation rule for the new input
                $('input[name="ppe_file[' + currentFileUploads + '][]"]').rules('add', {
                    required: true,
                    extension: "doc|docx|pdf|png|jpeg|jpg",
                    accept: "image/png, image/jpeg, image/jpg, application/pdf, application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document",
                    messages: {
                        required: 'Please select the file',
                        extension: "Please select a file with .doc, .docx, .pdf, .png, .jpeg, .jpg extensions.",
                        accept: "Please upload a valid file with the correct MIME type (PNG, JPEG, JPG, PDF, DOC, DOCX)."
                    }
                });

                // Remove file upload block
                $(document).on('click', '.remove-upload-block', function() {
                    $(this).closest('.file-upload-block').remove();
                });
            });

            // Initialize the date pickers
            var fromDatepicker = flatpickr("#from_date", {
                dateFormat: "d-m-Y",
                minDate: new Date(),
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
                minDate: new Date()
            });

            // jQuery Validation Setup
            $('#ppeExemptionForm').validate({
                rules: {
                    emp_id: {
                        required: true,
                    },
                    emp_name: {
                        required: true,
                    },
                    request_for: {
                        required: true,
                    },
                    department: {
                        required: true,
                    },
                    company_id: {
                        required: true,
                    },
                    location_id: {
                        required: true,
                    },
                    unit_id: {
                        required: true,
                    },
                    from_date: {
                        required: true
                    },
                    to_date: {
                        required: true
                    },
                    'ppe_file[0][]': {
                        required: true,
                        extension: "doc|docx|pdf|png|jpeg|jpg",
                        accept: "image/png, image/jpeg, image/jpg, application/pdf, application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document"
                    },
                    reason: {
                        required: true,
                        minlength: 3,
                        maxlength: 600,
                    },
                },
                messages: {
                    emp_id: {
                        required: "Employee Id cannot be empty.",
                    },
                    emp_name: {
                        required: "Employee name cannot be empty.",
                    },
                    request_for: {
                        required: "Please select the Request For option",
                    },
                    department: {
                        required: "Department cannot be empty.",
                    },
                    location_id: {
                        required: "location cannot be empty.",
                    },
                    company_id: {
                        required: "Company cannot be empty.",
                    },
                    unit_id: {
                        required: "Unit cannot be empty.",
                    },
                    from_date: {
                        required: "Please Select the From date."
                    },
                    to_date: {
                        required: "Please Select the To date."
                    },
                    'ppe_file[0][]': {
                        required: "Please Select the file",
                        extension: "Please select a file with .doc, .docx, .pdf, .png, .jpeg, .jpg .",
                        accept: "Please upload a valid file with the correct MIME type (PNG, JPEG, JPG, PDF, DOC, DOCX)."
                    },
                    reason: {
                        required: "Reason cannot be empty.",
                        minlength: "Reason must contain between 3 and 600 characters.",
                        maxlength: "Reason must contain between 3 and 600 characters.",
                    },
                },
                errorElement: 'div',
                errorPlacement: function(error, element) {
                    if (element.attr("name") === "from_date") {
                        error.appendTo("#from_date_error");
                    } else if (element.attr("name") === "to_date") {
                        error.appendTo("#to_date_error");
                    } else if (element.attr("name") === "reason") {
                        error.appendTo("#reason_error");
                    } else if (element.attr("name") == "request_for") {

                        error.appendTo(element.closest('.form-group').find('.text-danger'));
                    } else {
                        error.appendTo(element.siblings('div.text-danger'));
                    }
                },
                highlight: function(element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                },
                submitHandler: function(form) {
                    if ($('#agreeTerms').is(':checked')) {
                        form.submit();
                    } else {
                        $('#termsModal').modal('show');
                    }
                }
            });

            // Enable/Disable Submit button based on terms agreement
            $('#agreeTerms').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#submitBtn').prop('disabled', false);
                } else {
                    $('#submitBtn').prop('disabled', true);
                }
            });

            // Close terms modal
            $('#closeModal').on('click', function() {
                $('#termsModal').modal('hide');
            });

        });
    </script>
@endpush
