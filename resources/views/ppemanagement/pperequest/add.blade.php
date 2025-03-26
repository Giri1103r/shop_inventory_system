@extends('admin.layouts.admin')
@section('title', 'PPE Request')
@section('pageurl', admin_url('ppe_request/list'))


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
                                    <x-button-back href="{{ admin_url('ppe_request/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form">
                                    <form method="POST" id="pperequestadd" enctype="multipart/form-data"
                                        action="{{ admin_url('ppe_request/add/submit') }}">
                                        @csrf

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
                                                    <label for="department"
                                                        class="form-label require">Department</label>
                                                    <input type="text" name="department" id="department"
                                                        class="form-control form-control-sm" readonly>
                                                    <div class="text-danger"></div>
                                                </div>
                                            </div>


                                            <input type="hidden" name="unit" id="unit"
                                                class="form-control form-control-sm" readonly>
                                            <input type="hidden" name="company" id="company"
                                                class="form-control form-control-sm" readonly>
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
                                            <input type="hidden" name="company" id="company"
                                                class="form-control form-control-sm"
                                                value="{{ getCompanyname($employee->company_id) }}" readonly>
                                            <input type="hidden" name="unit" id="unit"
                                                class="form-control form-control-sm"
                                                value="{{ getUnitname($employee->unit_id) }}" readonly>
                                        @endif
                                            <div class="col-md-4 mb-3">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Item Code</label>
                                                    <select name="item_code" id="item_code" style="width: 100%"
                                                        class="form-select form-select-sm single-select">
                                                        <option value="">Select the Item Code</option>
                                                        @foreach ($itemCode as $list)
                                                            <option value="{{ $list->id }}">{{ $list->item_code }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('ppe_type')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                    <div class="text-danger" id="ppe_type_error"></div>
                                                </div>
                                            </div>
                                            {{-- <div class="col-md-4 mb-3">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">PPE Type</label>
                                                    <input type="text" name="ppe_type" id="ppe_type"
                                                        class="form-control form-control-sm" readonly>
                                                    <input type="hidden" name="ppe_type_id" id="ppe_type_id">
                                                    @error('ppe_type')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                    <div class="text-danger" id="ppe_type_error"></div>
                                                </div>
                                            </div> --}}
                                            <div class="col-md-4 mb-3">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">PPE Name</label>
                                                    <input type="text" name="ppe_name" id="ppe_name"
                                                        class="form-control form-control-sm" readonly>
                                                    <input type="hidden" name="ppe_name_id" id="ppe_name_id">
                                                    @error('ppe_name')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                    <div class="text-danger" id="ppe_name_error"></div>
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-3">
                                                <div class="form-group form-input">
                                                    <label class="form-label ">Image</label>
                                                    <input type="file" name="ppe_file" id="ppe_file"
                                                        class="form-control form-control-sm"
                                                        accept="image/png, image/jpeg, image/jpg"
                                                        placeholder="Enter the image" onchange="validateImage()">
                                                    <small>Allowed file types: png, jpeg , jpg</small>
                                                    @error('ppe_name')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                    <div class="text-danger" id="ppe_name_error"></div>
                                                </div>
                                            </div>

                                            <div class="col-md-12 mb-2">
                                                <label for="remarks" class="form-label require">Remarks</label>
                                                <textarea name="remarks" id="remarks" cols="3" rows="4" class="form-control form-control-sm"
                                                    placeholder="Enter the remarks"></textarea>
                                                @error('reason')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                                <div class="text-danger" id="reason_error"></div>
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
                                        </div>
                                        <hr>
                                        <div class="submit-button float-end">
                                            <x-button-submit class="submit" id="submit"></x-button-submit>
                                            <x-button-reset class="submit"></x-button-reset>
                                            <x-button-cancel href="{{ admin_url('ppe_request/list') }}"></x-button-cancel>
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
    </div>
@stop

@push('script')
    <script>
        $(document).ready(function() {
            $('#resetform').on('click', function(e) {
                e.preventDefault();
                location.reload();
            });
        });

        $(document).ready(function() {
            $('input[name="request_for"]').on("change", function() {
                var requestFor = $(this).val();
                var empIdContainer = $("#emp_id_container");
                var authEmployeeId =
                "{{ auth()->user()->employee_id }}"; // Authenticated user's employee ID
                var authDepartment =
                "{{ getDepartment(auth()->user()->department_id) ?? 'N/A' }}"; // Authenticated user's department

                if (requestFor === "1") {
                    // If "Myself" is selected
                    empIdContainer.html(`
                <label for="emp_id" class="form-label require">Employee ID</label>
                <input type="text" name="emp_id" id="emp_id" class="form-control form-control-sm" value="${authEmployeeId}" readonly>
                <div class="text-danger"></div>
            `);

                    $("#emp_name").val("{{ auth()->user()->name }}");
                    $("#department").val(authDepartment); // Set auth user's department
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
                    $("#department").val(""); // Clear department until employee is selected
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
                                    .department_name : "No department available");
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
            var empId = $('#emp_id').val();
            var department = $('#department').val();

            function checkRequestCondition(empId, department) {
                $.ajax({
                    url: 'checkuserDepartment',
                    method: 'GET',
                    data: {
                        department: department,
                        empId: empId
                    },
                    success: function(response) {
                        if (response.showFields) {
                            $('#ppe_file').closest('.col-md-4').show();
                            $('#remarks').closest('.col-md-12').show();
                            $('#reason').closest('.col-md-12').hide();

                        } else {
                            $('#ppe_file').closest('.col-md-4').hide();
                            $('#remarks').closest('.col-md-12').hide();
                        }
                    }
                });
            }

            checkRequestCondition(empId, department);
        });

        $(document).on('change', '#item_code', function() {
            let PPEtypeId = $(this).val();
            console.log(PPEtypeId);

            if (PPEtypeId) {
                $.ajax({
                    url: "{{ admin_url('ppe_stock_inventory/ajax-list') }}",
                    type: 'GET',
                    data: {
                        id: PPEtypeId,
                        _ts: new Date().getTime()
                    },
                    success: function(data) {
                        console.log(data);
                        if (data.length > 0) {
                            // let ppeType = data[0].ppe_type;
                            let ppeName = data[0].ppe_name;
                            // let ppeTypeId = data[0].ppe_type_id;
                            let PPENameId = data[0].id;

                            // $('#ppe_type').val(ppeType);
                            $('#ppe_name').val(ppeName);

                            // $('#ppe_type_id').val(ppeTypeId);
                            $('#ppe_name_id').val(PPENameId);
                        } else {
                            // $('#ppe_type').val('');
                            $('#ppe_name').val('');
                            // $('#ppe_type_id').val('');
                            $('#ppe_name_id').val('');
                        }
                    },
                    error: function(xhr) {
                        alert('Error fetching PPE Types. Please try again.');
                    }
                });
            } else {
                // $('#ppe_type').val('');
                $('#ppe_name').val('');
                // $('#ppe_type_id').val('');
                $('#ppe_name_id').val('');
            }
        });


        $(document).ready(function() {
            $('#pperequestadd').validate({
                rules: {
                    emp_id: {
                        required: true,
                    },
                    request_for: {
                        required: true, // Ensures the request_for radio group is validated
                    },
                    emp_name: {
                        required: true,
                    },
                    department: {
                        required: true,
                    },
                    item_code: {
                        required: true,
                    },
                    ppe_name: {
                        required: true,
                    },
                    reason: {
                        required: true,
                        minlength: 3,
                        maxlength: 600,
                    },
                    ppe_file: {
                        extension: "png|jpeg|jpg",
                    },
                    remarks: {
                        required: true,
                        minlength: 3,
                        maxlength: 600,
                    },
                },
                messages: {
                    emp_id: {
                        required: "Employee Id cannot be empty.",
                    },
                    request_for: {
                        required: "Please select the Request For option.",
                    },
                    emp_name: {
                        required: "Employee name cannot be empty.",
                    },
                    department: {
                        required: "Department cannot be empty.",
                    },
                    item_code: {
                        required: "Please select the Item Code.",
                    },
                    ppe_name: {
                        required: "Please select the PPE Name.",
                    },
                    reason: {
                        required: "Reason cannot be empty.",
                        minlength: "Reason must contain between 3 and 600 characters.",
                        maxlength: "Reason must contain between 3 and 600 characters.",
                    },
                    ppe_file: {
                        extension: "Please select a file with .jpeg, .jpg, or .png.",
                    },
                    remarks: {
                        required: "Remarks cannot be empty.",
                        minlength: "Remarks must contain between 3 and 600 characters.",
                        maxlength: "Remarks must contain between 3 and 600 characters.",
                    },
                },
                errorElement: 'div',
                errorPlacement: function(error, element) {
                    if (element.attr("name") === "request_for") {

                        error.appendTo(element.closest(".form-group").find(".text-danger"));
                    } else {

                        var errorDiv = element.siblings('div.text-danger');
                        errorDiv.html(error);
                    }
                },
                highlight: function(element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                },
                submitHandler: function(form) {
                    $('#submit').prop('disabled', true);
                    form.submit();
                },
                invalidHandler: function(event, validator) {
                    var errors = validator.numberOfInvalids();
                    console.log(errors + " field(s) are invalid");
                    validator.errorList.forEach(function(error) {
                        console.log("Field: " + error.element.name + ", Error: " + error
                            .message);
                    });
                },
            });

            $.validator.addMethod("regex", function(value, element, regexp) {
                return this.optional(element) || regexp.test(value);
            }, "Please check your input.");
        });
    </script>
@endpush
