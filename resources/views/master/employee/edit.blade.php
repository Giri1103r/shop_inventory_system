@extends('admin.layouts.admin')
@section('title', 'Employee Master Edit')
@section('pageurl', admin_url('employee/list'))


@section('content')
    <div class="clearfix"></div>
    <div class="page-titles">
        <div class="d-flex align-items-center">
            {{-- <h4 class="text-black">{{ __('Department Edit') }}</h4> --}}

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
                                {{-- <h4 class="card-title">{{ __('master.company_add') }}</h4> --}}
                                <div class="align-back-btc">
                                    <x-button-back href="{{ admin_url('employee/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form">
                                    <form method="POST" id="employeeedit" action="{{ admin_url('employee/edit/submit') }}">
                                        @csrf
                                        <input type="hidden" name="id" id="id"
                                            value="{{ encryptId($employee->id) }}">

                                        <div class="row">
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Employee ID</label>
                                                    <input type="text" name ="emp_id" class="form-control"
                                                        placeholder="Employee ID" value="{{ $employee->emp_id }}" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Employee Name</label>
                                                    <input type="text" name ="emp_name" class="form-control"
                                                        placeholder="Employee Name" value="{{ $employee->emp_name }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Gender</label>
                                                    <select name="gender" id="gender" class="form-control single-select"
                                                        style="width: 100%;">
                                                        <option value="">Select Gender</option>
                                                        <option value="Male"
                                                            {{ $employee->gender === 'Male' ? 'selected' : '' }}>Male
                                                        </option>
                                                        <option value="Female"
                                                            {{ $employee->gender === 'Female' ? 'selected' : '' }}>Female
                                                        </option>
                                                        <option value="Other"
                                                            {{ $employee->gender === 'Other' ? 'selected' : '' }}>Other
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Nationality</label>
                                                    <input type="text" name="nationality" id="nationality"
                                                        class="form-control form-control-sm" placeholder=" Enter the Nationality"
                                                        value="{{ $employee->nationality }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">ID Type</label>
                                                    <input type="text" name="id_type" id="id_type"
                                                        class="form-control form-control-sm"  placeholder=" Enter the ID Type"
                                                        value="{{ $employee->id_type }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">ID Number</label>
                                                    <input type="text" name="id_number" id="id_number"
                                                        class="form-control form-control-sm" placeholder="ID Number"
                                                        value="{{ $employee->id_number }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">User Role</label>
                                                    <select name="user_role[]" multiple id="user_role"
                                                        class="select2 form-control">
                                                        <option value="">Select User Role</option>
                                                        @foreach ($userrole as $role)
                                                            <option value="{{ $role->id }}"
                                                                @if (in_array($role->id, explode(',', $employee->user_role ?? ''))) selected @endif>
                                                                {{ $role->role_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Employee Email</label>
                                                    <input type="email" name ="email" id ="email" class="form-control"
                                                        placeholder="Employee Email" value="{{ $employee->email }}">
                                                </div>
                                            </div>


                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Joining Date</label>
                                                    <input type="text" name ="joining_date"
                                                        id="joining_date_datetime_datepicker" class="form-control"
                                                        placeholder="Joining Date"
                                                        value="{{ Displaydateformat($employee->joining_date) }}">
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Phone Number</label>
                                                    <input type="text" name ="mobile_no"
                                                        id="mobile_no" class="form-control"
                                                        placeholder="Mobile No"
                                                        value="{{$employee->mobile_no}}">
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Employee Status</label>
                                                    <input type="text" name ="employee_status" class="form-control"
                                                        placeholder="Employee Status"
                                                        value="{{ $employee->employee_status }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Company Name</label>
                                                    <select name="company" id="company_id"
                                                        class="form-control single-select" style="width: 100%">
                                                        <option value="">Select Company Name</option>

                                                        @foreach ($companyList as $list)
                                                            <option value="{{ encryptId($list->id) }}"
                                                                @if ($employee->company == $list->id) selected @endif>
                                                                {{ $list->company_name }}
                                                            </option>
                                                        @endforeach


                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Location Name</label>
                                                    <select name="location" id="location_id"
                                                        class=" form-control single-select" style="width: 100%">
                                                        <option value="">Select Location Name</option>

                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Unit Name</label>
                                                    <select name="unit" id="unit_id"
                                                        class=" form-control single-select" style="width: 100%">
                                                        <option value="">Select Unit Name</option>

                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Department Name</label>
                                                    <select name="department" id="department_id"
                                                        class=" form-control single-select" style="width: 100%">
                                                        <option value="">Select Department Name</option>

                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Designation</label>
                                                    <input type="text" name="designation" id="designation"
                                                        class="form-control form-control-sm"
                                                        value="{{ $employee->designation }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Reporting Manager</label>
                                                    <input type="text" name="reporting_manager" id="reporting_manager"
                                                        class="form-control form-control-sm"
                                                        value="{{ $employee->reporting_manager }}">
                                                </div>
                                            </div>
                                        </div>
                                </div>
                                <hr>
                                <div class="submit-button" style="text-align: right;">
                                    <x-button-submit class="submit"></x-button-submit>
                                    <x-button-reset class="submit"></x-button-reset>
                                    <x-button-cancel href="{{ admin_url('employee/list') }}"></x-button-cancel>
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
    <script type="text/javascript" nonce="projectcab">
        $('#user_role').select2({
            placeholder: "Select Role",
            allowClear: true,
            closeOnSelect: true,
        });
        $(document).ready(function() {
            $('#resetform').on('click', function(e) {
                e.preventDefault();
                location.reload();
            });
        });

        flatpickr("#joining_date_datetime_datepicker", {
            dateFormat: "d-m-Y ",
            minuteIncrement: 5,
            minDate: "1995-01-01",
            clickOpens: true,
            disableMobile: true,
            allowInput: false,
        });

        $(document).ready(function() {



            var initialCompanyId = $('#company_id').val();
            var preselectedLocationId = "{{ encryptId($employee->location) ?? '0' }}";
            var preselectedUnitId = "{{ encryptId($employee->unit) ?? '0' }}";
            var preselectedDepartmentId = "{{ encryptId($employee->department) ?? '0' }}";

            if (initialCompanyId) {
                fetchLocations(initialCompanyId, preselectedLocationId, function() {
                    var location_id = preselectedLocationId;
                    fetchUnits(location_id, preselectedUnitId, function() {
                        var unit_id = preselectedUnitId;
                        fetchDepartments(unit_id, preselectedDepartmentId);
                    });
                });
            }

            $('#company_id').on('change', function() {
                var company_id = $(this).val();
                fetchLocations(company_id, preselectedLocationId, function() {
                    $('#location_id').trigger('change');
                });
            });

            $('#location_id').on('change', function() {
                var location_id = $(this).val();
                fetchUnits(location_id, preselectedUnitId, function() {
                    $('#unit_id').trigger('change');
                });
            });

            $('#unit_id').on('change', function() {
                var unit_id = $(this).val();
                fetchDepartments(unit_id, preselectedDepartmentId, function() {
                    $('#department_id').trigger('change');
                });
            });

            function fetchLocations(company_id, preselectedLocationId, callback) {
                if (company_id) {
                    $.ajax({
                        url: "{{ admin_url('location/ajax-list/') }}" + company_id + '/' +
                            preselectedLocationId,
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            $('#location_id').empty().append(
                                '<option value="">Select Location</option>');
                            $.each(data, function(key, value) {
                                var selected = (value.id == preselectedLocationId) ?
                                    'selected' : '';
                                $('#location_id').append('<option value="' + value.id + '" ' +
                                    selected + '>' + value.name + '</option>');
                            });
                            if (callback) callback();
                        }
                    });
                } else {
                    $('#location_id').empty().append('<option value="">Select Location</option>');
                }
            }

            function fetchUnits(location_id, preselectedUnitId, callback) {
                if (location_id) {
                    $.ajax({
                        url: "{{ admin_url('unit/ajax-list/') }}" + location_id + '/' + preselectedUnitId,
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            $('#unit_id').empty().append('<option value="">Select Unit</option>');
                            $.each(data, function(key, value) {
                                var selected = (value.id == preselectedUnitId) ? 'selected' :
                                    '';
                                $('#unit_id').append('<option value="' + value.id + '" ' +
                                    selected + '>' + value.name + '</option>');
                            });
                            if (callback) callback();
                        }
                    });
                } else {
                    $('#unit_id').empty().append('<option value="">Select Unit</option>');
                }
            }

            function fetchDepartments(unit_id, preselectedDepartmentId, callback) {
                if (unit_id) {
                    $.ajax({
                        url: "{{ admin_url('department/ajax-list/') }}" + unit_id + '/' +
                            preselectedDepartmentId,
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            $('#department_id').empty().append(
                                '<option value="">Select Department</option>');
                            $.each(data, function(key, value) {
                                var selected = (value.id == preselectedDepartmentId) ?
                                    'selected' : '';
                                $('#department_id').append('<option value="' + value.id + '" ' +
                                    selected + '>' + value.name + '</option>');
                            });
                            if (callback) callback();
                        }
                    });
                } else {
                    $('#department_id').empty().append('<option value="">Select Department</option>');
                }
            }
        });

        $(document).ready(function() {
            // Custom method for strict email validation
            jQuery.validator.addMethod("strictEmail", function(value, element) {
                return this.optional(element) || /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(
                    value);
            }, "Please enter a valid email address");

            // Custom method for regex validation
            jQuery.validator.addMethod("regex", function(value, element, regexpr) {
                return regexpr.test(value);
            }, "Please check your input.");

            $('#employeeedit').validate({
                rules: {
                    emp_name: {
                        required: true,
                    },
                    email: {
                        required: true,
                        email: true,
                        strictEmail: true,
                        remote: {
                            url: '{{ admin_url('employee/unique') }}',
                            type: 'post',
                            data: {
                                email: function() {
                                    return $('#email').val();
                                },
                                id: function() {
                                    return $('#id').val();
                                }
                            }
                        }
                    },
                    'user_role[]': {
                        required: true
                    },
                    gender: {
                        required: true,
                    },
                    joining_date: {
                        required: true,
                    },
                    employee_status: {
                        required: true,
                    },
                    nationality: {
                        required: true,
                        minlength: 2,
                        maxlength: 30,
                        regex: /^[a-zA-Z]{2,}$/
                    },
                    id_type: {
                        required: true,
                        regex: /^[a-zA-Z0-9\-_'"()\s]+$/
                    },
                    id_number: {
                        required: true,
                        regex: /^[a-zA-Z0-9\-_'"()\s]+$/
                    },
                    company: {
                        required: true,
                    },
                    location: {
                        required: true,
                    },
                    department: {
                        required: true,
                    },
                    designation: {
                        required: true,
                        minlength: 2,
                        maxlength: 30,
                         regex: /^[a-zA-Z]{2,}$/
                    },
                    unit: {
                        required: true,
                    },
                    reporting_manager: {
                        required: true,
                        minlength: 2,
                        maxlength: 30,
                        regex: /^[a-zA-Z]{2,}$/
                    },
                    mobile_no:{
                        required:true,
                        minlength: 10,
                        maxlength: 10,
                        digits:true,
                    }
                },
                messages: {
                    emp_name: {
                        required: "{{ __('Employee Name is Required') }}",
                    },
                    email: {
                        required: "{{ __('Employee Email is Required') }}",
                        email: "Please enter a valid email address",
                        strictEmail: "Please enter a valid email address",
                        remote: "{{ __('Email should be unique') }}"
                    },
                    'user_role[]': {
                        required: "{{ __('User Role is Required') }}"
                    },
                    gender: {
                        required: "{{ __('Gender is Required') }}",
                    },
                    joining_date: {
                        required: "{{ __('Joining Date is Required') }}",
                    },
                    mobile_no:{
                        required: "{{ __('Mobile Number is Required') }}",
                        minlength: "{{ __('Mobile number minimum length should be 10') }}",
                        maxlength: "{{ __('Mobile number maximum length should be 10') }}",
                        digits: "{{ __('Mobile number allows only numeric') }}",
                    },
                    employee_status: {
                        required: "{{ __('Employee Status is Required') }}",
                    },
                    nationality: {
                        required: "{{ __('Nationality is Required') }}",
                        minlength: "{{ __('Nationality must be at least 2 characters') }}",
                        maxlength: "{{ __('Nationality must be less than 30 characters') }}",
                        regex: "{{ __('Nationality accepts only alphabets') }}",
                    },
                    id_type: {
                        required: "{{ __('ID Type is Required') }}",
                        regex: "{{ __('ID Type allows alphanumeric characters') }}",
                    },
                    id_number: {
                        required: "{{ __('ID Number is Required') }}",
                        regex: "{{ __('ID Number allows alphanumeric characters') }}",
                    },
                    reporting_manager: {
                        required: "{{ __('Reporting Manager is Required') }}",
                        minlength: "{{ __('Reporting Manager must be at least 2 characters') }}",
                        maxlength: "{{ __('Reporting Manager must be less than 30 characters') }}",
                        regex: "{{ __('Reporting Manager accepts only alphabets') }}",
                    },
                    company: {
                        required: "{{ __('Company is Required') }}",
                    },
                    location: {
                        required: "{{ __('Location is Required') }}",
                    },
                    department: {
                        required: "{{ __('Department is Required') }}",
                    },
                    unit: {
                        required: "{{ __('Unit is Required') }}",
                    },
                    designation: {
                        required: "{{ __('Designation is Required') }}",
                        minlength: "{{ __('Designation must be at least 2 characters') }}",
                        maxlength: "{{ __('Designation must be less than 30 characters') }}",
                        regex: "{{ __('Designation accepts only alphabets') }}",
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
                    console.log('test');
                    form.submit();
                },
                invalidHandler: function(event, validator) {
                    var errors = validator.numberOfInvalids();
                    console.log(errors + " field(s) are invalid");
                    validator.errorList.forEach(function(error) {
                        console.log("Field: " + error.element.name + ", Error: " + error
                            .message);
                    });
                }
            });
        });
    </script>
@endpush
