@extends('admin.layouts.admin')
@section('title', 'PPE Shoe Exemption Add')
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
                                        <input type="hidden" name="unit" id="unit"
                                            value="{{ $userData->unit_id }}">
                                        <input type="hidden" name="company" id="company"
                                            value="{{ $userData->company_id }}">
                                        <hr>
                                        <div class="row">
                                            @if (checkUserrole(ROLE_SUPERADMIN) || checkUserRole(ROLE_STORE_MANAGER))
                                                <div class="col-md-4 mb-3">
                                                    <div class="form-group form-input">
                                                        <label for="emp_id" class="form-label require">Employee ID</label>
                                                        <select name="emp_id" id="emp_id"
                                                            class="form-select form-select-sm single-select"
                                                            style="width: 100%">
                                                            <option value="">Select the employee</option>
                                                            @foreach ($employeelist as $list)
                                                                <option value="{{ $list->employee_id }}">
                                                                    {{ $list->employee_id }}</option>
                                                            @endforeach
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
        $(document).on("change", "#emp_id", function() {
            var emp_id = $(this).val();
            var currentRow = $(this).closest(".row");
            var departmentInput = currentRow.find('input[name="department"]');


            if (emp_id) {
                $.ajax({
                    url: "{{ url('ppe_request/fetchEmployeeDetails') }}/" +
                        emp_id,
                    type: "GET",
                    success: function(data) {

                        if (data && data.employee) {
                            currentRow.find('input[name="emp_name"]').val(data.employee.emp_name);

                            if (data.employee.department && data.departments) {
                                departmentInput.val(data.departments
                                    .department_name);
                                console.log("Department Name: ", data.departments
                                    .department_name);
                            } else {
                                departmentInput.val(
                                    "No department available");
                                console.log("No department found");
                            }
                        } else {
                            Swal.fire({
                                icon: "error",
                                title: "Error",
                                text: "Employee data could not be fetched.",
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log("Error during AJAX request: ", status,
                            error);
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: "An error occurred while fetching employee details.",
                        });
                    },
                });
            } else {

                currentRow.find('input[name="emp_name"]').val("");
                departmentInput.val("");
            }
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
                    department: {
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
                    department: {
                        required: "Department cannot be empty.",
                    },
                    from_date: {
                        required: "Please Select the From date."
                    },
                    to_date: {
                        required: "Please Select the To date."
                    },
                    'ppe_file[0][]': {
                        required: "Please Select the file",
                        extension: "Please select a file with .doc, .docx, .pdf, .png, .jpeg, .jpg extensions.",
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
                    if (element.attr("name") == "from_date") {
                        error.appendTo("#from_date_error");
                    } else if (element.attr("name") == "to_date") {
                        error.appendTo("#to_date_error");
                    } else if (element.attr("name") == "reason") {
                        error.appendTo("#reason_error");
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
