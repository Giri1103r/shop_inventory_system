@extends('admin.layouts.admin')
@section('title', 'Employee Edit')
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
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Employee ID</label>
                                                    <input type="text" name ="emp_id" class="form-control"
                                                        placeholder="Employee ID" value="{{ $employee->emp_id }}" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Employee Name</label>
                                                    <input type="text" name ="emp_name" class="form-control"
                                                        placeholder="Employee Name" value="{{ $employee->emp_name }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
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

                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">User Role</label>
                                                    <select name="user_role" id="user_role"
                                                        class="form-control single-select" style="width: 100%;">
                                                        <option value="">Select User Role</option>
                                                        @foreach ($userrole as $role)
                                                            <option @if ($employee->user_role == $role->id) selected @endif
                                                                value="{{ encryptId($role->id) }}">{{ $role->role_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Employee Email</label>
                                                    <input type="email" name ="email" class="form-control"
                                                        placeholder="Employee Email" value="{{ $employee->email }}">
                                                </div>
                                            </div>

                                         
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Joining Date</label>
                                                    <input type="text" name ="joining_date"
                                                        id="joining_date_datetime_datepicker" class="form-control"
                                                        placeholder="Joining Date"
                                                        value="{{ Displaydatetimeformat($employee->joining_date) }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Employee Status</label>
                                                    <input type="text" name ="employee_status" class="form-control"
                                                        placeholder="Employee Status"
                                                        value="{{ $employee->employee_status }}">
                                                </div>
                                            </div>
                                            {{-- <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Company Name</label>
                                                    <select name="company" id="company_id"
                                                        class="form-control single-select" style="width: 100%">
                                                        <option value="">Select Company Name</option>
                                                        @foreach ($companyList as $company)
                                                            <option @if ($employee->company == $company->id) selected @endif
                                                                value="{{ encryptId($company->id) }}">
                                                                {{ $company->company_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Location Name</label>
                                                    <select name="location" id="location_id"
                                                        class=" form-control single-select" style="width: 100%">
                                                        <option value="">Select Location Name</option>

                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Unit Name</label>
                                                    <select name="unit" id="unit_id"
                                                        class=" form-control single-select" style="width: 100%">
                                                        <option value="">Select Unit Name</option>

                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Department Name</label>
                                                    <select name="department" id="department_id"
                                                        class=" form-control single-select" style="width: 100%">
                                                        <option value="">Select Department Name</option>

                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">SubDepartment Name</label>
                                                    <input type="text" name ="subdepartment" class="form-control"
                                                        placeholder="SubDepartment Name"
                                                        value="{{ $employee->subdepartment }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Designation </label>
                                                    <input type="text" name ="designation" class="form-control"
                                                        placeholder="Designation " value="{{ $employee->designation }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">WFEmptype </label>
                                                    <input type="text" name ="wfemptype" class="form-control"
                                                        placeholder="WFEmptype " value="{{ $employee->wfemptype }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Skill </label>
                                                    <input type="text" name ="skill" class="form-control"
                                                        placeholder="Skill " value="{{ $employee->skill }}">
                                                </div>
                                            </div> --}}
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
        flatpickr("#joining_date_datetime_datepicker", {
                enableTime: true, 
                dateFormat: "d-m-Y H:i",
                time_24hr: true, 
                minuteIncrement: 5, 
            });

        $(function() {
            $('#employeeedit').validate({
                rules: {
                    emp_name: {
                        required: true,
                    },
                    email: {
                        required: true,
                        email: true,
                    },
                    user_role: {
                        required: true,
                    },
                    gender: {
                        required: true,
                    },
                    joining_date: {
                        required: true,
                    },
                    employee_status: {
                        required: true,
                    }

                },
                messages: {
                    emp_name: {
                        required: "{{ __('Employee Name is Required') }}",
                    },
                    email: {
                        required: "{{ __('Employee Email is Required') }}",
                        email: "Please enter a valid email address",
                    },
                    user_role: {
                        required: "{{ __('User Role is Required') }}",
                    },
                    gender: {
                        required: "{{ __('Gender is Required') }}",
                    },
                    joining_date: {
                        required: "{{ __('Joining Date is Required') }}",

                    },
                    employee_status: {
                        required: "{{ __('Employee Status is Required') }}",

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
