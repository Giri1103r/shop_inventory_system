@extends('admin.layouts.admin')
@section('title', 'Employee Edit')
@section('pageurl', admin_url('employee/list'))

@php
    global $contract_end_date_show;
@endphp
@section('content')
    <div class="clearfix"></div>
    <div class="page-titles">
        <div class="d-flex align-items-center">
            <h2 class="text-black">{{ __('administration.employee') }}</h2>

        </div>
        <ol class="breadcrumb">
            <li class="breadcrumb-item active ms-auto">
                <a class="d-flex align-self-center" href="{{ admin_url('dashboard') }}">
                    <svg class="me-2 svg-main-icon" xmlns="http://www.w3.org/2000/svg"
                        xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24"
                        version="1.1">
                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                            <rect x="0" y="0" width="24" height="24"></rect>
                            <path
                                d="M3.95709826,8.41510662 L11.47855,3.81866389 C11.7986624,3.62303967 12.2013376,3.62303967 12.52145,3.81866389 L20.0429,8.41510557 C20.6374094,8.77841684 21,9.42493654 21,10.1216692 L21,19.0000642 C21,20.1046337 20.1045695,21.0000642 19,21.0000642 L4.99998155,21.0000673 C3.89541205,21.0000673 2.99998155,20.1046368 2.99998155,19.0000673 L2.99999828,10.1216672 C2.99999935,9.42493561 3.36258984,8.77841732 3.95709826,8.41510662 Z M10,13 C9.44771525,13 9,13.4477153 9,14 L9,17 C9,17.5522847 9.44771525,18 10,18 L14,18 C14.5522847,18 15,17.5522847 15,17 L15,14 C15,13.4477153 14.5522847,13 14,13 L10,13 Z"
                                fill="#aaa9ff"></path>
                        </g>
                    </svg>
                    {{ __('common.dashboard') }}
                </a>
            </li>
            <li class="breadcrumb-item"><a href="javascript:void(0)">{{ __('leftmenu.menu_2') }}</a></li>
            <li class="breadcrumb-item"><a href="javascript:void(0)">{{ __('leftmenu.menu_15') }}</a></li>
        </ol>
    </div>

    <div class="content-body  default-height">
        <div class="container-fluid main-content">
            <!-- row -->
            <div class="row">

                <div class="col-12">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Employee Edit</h4>
                                <div>
                                    <x-button-back href="{{ admin_url('employee/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body ">
                                <div class="basic-form">
                                    <form method="POST" id="employeeedit"
                                        action="{{ admin_url('employee/edit/submit') }}">
                                        @csrf
                                        <div class="card view_card">
                                            <div class="card-header">
                                                <h4 class="card-title">{{ __('common.basic_detail') }}</h4>

                                            </div>
                                            <div class="card-body">

                                                <input type="hidden" name="id" id="id" value="{{ encryptId( $employee->id) }}">
                                                <div class="row">
                                                    <div class="mb-3 col-md-4 form-input">
                                                        <label
                                                            class="form-label required">{{ __('administration.employee_no') }}</label>
                                                        <input type="text" value="{{ $employee->employee_no }}" class="form-control"
                                                            placeholder="" name="employee_no" id="employee_no">
                                                    </div>
                                                    <div class="mb-3 col-md-4 form-input">
                                                        <label
                                                            class="form-label required">{{ __('administration.first_name') }}</label>
                                                        <input type="text" name="first_name" id="first_name" value="{{ $employee->first_name }}"
                                                            class="form-control" placeholder="">
                                                    </div>
                                                    <div class="mb-3 col-md-4 form-input">
                                                        <label
                                                            class="form-label required">{{ __('administration.last_name') }}</label>
                                                        <input type="text" name="last_name" id="last_name" value="{{ $employee->last_name }}"
                                                            class="form-control" placeholder="">
                                                    </div>
                                                    <div class="mb-3 col-md-4 form-input">
                                                        <label
                                                            class="form-label required">{{ __('administration.joining_date') }}</label>
                                                        <input type="text" name="joining_date" id="joining_date" value="{{ Displaydateformat($employee->joining_date) }}"
                                                            class="form-control" placeholder="">
                                                    </div>

                                                    <div class="mb-3 col-md-4 form-input">
                                                        <label
                                                            class="form-label required">{{ __('administration.employee_type') }}</label>
                                                        <select name="employee_type" id="employee_type"
                                                            class=" form-control  single-select">
                                                            <option value="">Select Employee Type</option>
                                                            @foreach ($employeetypelist as $employeetype)
                                                            
                                                                <option @if($employee->employee_type == $employeetype->id) selected @endif value="{{ encryptId($employeetype->id) }}">
                                                                    {{ $employeetype->employee_type }}</option>
                                                            @endforeach
                                                        </select>

                                                    </div>
                                                    <div class="mb-3 col-md-4 form-input">
                                                        <label
                                                            class="form-label required">{{ __('administration.date_contract_end') }}</label>
                                                        <input disabled type="text" name="contract_end_date" value="{{ isset($employee->contract_end_date)?Displaydateformat($employee->contract_end_date):NULL }}"
                                                            id="date_contract_end" class="form-control" placeholder="">
                                                    </div>



                                                    <div class="mb-3 col-md-4 form-input">
                                                        <label
                                                            class="form-label required">{{ __('administration.designation') }}</label>
                                                        <select name="designation_id" id="designation"
                                                            class=" form-control  single-select">
                                                            <option value="">Select Designation</option>
                                                            @foreach ($designationlist as $designation)
                                                                <option @if($employee->designation_id == $designation->id) selected @endif value="{{ encryptId($designation->id) }}">
                                                                    {{ $designation->designation }}</option>
                                                            @endforeach
                                                        </select>

                                                    </div>
                                                    <div class="mb-3 col-md-4 form-input">
                                                        <label
                                                            class="form-label required">{{ __('administration.department') }}</label>
                                                        <select name="department_id" id="department"
                                                            class=" form-control  single-select">
                                                            <option value="">Select Department</option>
                                                            @foreach ($departmentlist as $department)
                                                                <option @if($employee->department_id == $department->id) selected @endif value="{{ encryptId($department->id) }}">
                                                                    {{ $department->department }}</option>
                                                            @endforeach
                                                        </select>

                                                    </div>

                                                    <div class="col-md-4 form-input">
                                                        <label for="emp_role_id" class="form-label require">User Role</label>
                                                        @php
                                                        $roleIds = string_to_array($employee->role_id);

                                                    @endphp
                                                        <select name="role_id[]" multiple id="role_id" class="form-control  single-select"
                                                            required>
                                                            <option value="">Select Role</option>
            
                                                            @foreach ($rolelist as $role)
                                                                <option @if (in_array($role->id,$roleIds)) selected @endif value="{{ encryptId($role->id) }}">
                                                                    {{ $role->role_name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="mb-3 col-md-4 form-input">
                                                        <label
                                                            class="form-label required">{{ __('administration.reporting_manager') }}</label>
                                                        <select name="reporting_manager_id" id="reporting_manager_id"
                                                            class=" form-control  single-select">
                                                            <option value="">Select Reporting Manager</option>
                                                            @foreach ($employeelist as $employeelist)
                                                                <option  @if($employee->reporting_manager_id == $employeelist->id) selected @endif value="{{ encryptId($employeelist->id) }}">
                                                                    {{ $employeelist->name }}</option>
                                                            @endforeach
                                                        </select>

                                                    </div>

                                                    <div class="mb-3 col-md-4 form-input">
                                                        <label
                                                            class="form-label required">{{ __('administration.factory') }}</label>
                                                        <select name="factory_id" id="factory_id"
                                                            class=" form-control  single-select">
                                                            <option value="">Select Factory</option>
                                                            @foreach ($factorylist as $factory)
                                                                <option @if($employee->factory_id == $factory->id) selected @endif value="{{ encryptId($factory->id) }}">
                                                                    {{ $factory->factory }}</option>
                                                            @endforeach
                                                        </select>

                                                    </div>


                                                </div>
                                            </div>
                                        </div>

                                        <div class="card view_card">
                                            <div class="card-header">
                                                <h4 class="card-title">{{ __('administration.contact_details') }}</h4>

                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="mb-3 col-md-4 form-input">
                                                        <label
                                                            class="form-label required">{{ __('administration.employee_email') }}</label>
                                                        <input type="text" name="email" id="email" value="{{ $employee->email }}"
                                                            class="form-control" placeholder="">
                                                    </div>

                                                    <div class="mb-3 col-md-4 form-input">
                                                        <label
                                                            class="form-label required">{{ __('administration.employee_phone') }}</label>
                                                        <input type="text" name="phone" id="phone" value="{{ $employee->phone }}"
                                                            class="form-control" placeholder="">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="submit-button">
                                            <x-button-submit class="submit"></x-button-submit>
                                            <x-button-cancel></x-button-cancel>
                                        </div>
                                        <hr>
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
    <script type="text/javascript">

        var contractperson = [
            @foreach($contract_end_date_show['show'] as $emp_type)
                "{{ encryptId($emp_type) }}",
            @endforeach
        ];
        $('#employee_type').on('change', function() {
            var selectedValue = $(this).val();
            if (contractperson.includes(selectedValue)) {
                $('#date_contract_end').prop('disabled', false);
            } else {
                $('#date_contract_end').val('');
                $('#date_contract_end').prop('disabled', true);
            }
        });
        var emptypeselectedValue = $('#employee_type').val();
        if (contractperson.includes(emptypeselectedValue)) {
            $('#date_contract_end').prop('disabled', false);
        } else {
            $('#date_contract_end').val('');
            $('#date_contract_end').prop('disabled', true);
        }

        $('#joining_date').datepicker({
            format: 'dd-mm-yyyy',
            autoclose: true,
        });
        $('#date_contract_end').datepicker({
            format: 'dd-mm-yyyy',
            startDate: new Date(),
            autoclose: true,
        });

        $(function() {
            $('#employeeedit').validate({
                rules: {

                    employee_no: {
                        required: true,
                        remote: {
                            url: '{{ admin_url('employee/unique') }}',
                            type: 'post',
                            data: {
                                type: 'employee_no',
                                value: function() {
                                    return $('#employee_no').val();
                                },
                                id : function(){
                                    return $('#id').val(); 
                                }
                            }
                        }
                    },
                    first_name: {
                        required: true,
                        minlength: 3,
                    },
                    last_name: {
                        required: true,
                    },
                    joining_date: {
                        required: true,
                    },
                    employee_type: {
                        required: true,
                    },
                    contract_end_date: {
                        required: true,
                    },
                    designation_id: {
                        required: true,
                    },
                    department_id: {
                        required: true,
                    },
                    "role_id[]": {
                        required: true,
                    },

                    factory_id: {
                        required: true,
                    },
                    reporting_manager_id: {
                        required: true,
                    },

                    email: {
                        required: true,
                        email: true,
                        minlength: 3,
                        remote: {
                            url: '{{ admin_url('employee/unique') }}',
                            type: 'post',
                            data: {
                                type: 'email',
                                value: function() {
                                    return $('#email').val();
                                },
                                id : function(){
                                    return $('#id').val(); 
                                }
                            }
                        }
                    },

                    phone: {
                        required: true,
                        mobileNumber: true
                    },

                },
                messages: {
                    employee_no: {
                        required: "{{ __('Employee No is Required') }}",
                        remote: "{{ __('administration.validate_employee_no_unique') }}"
                    },
                    first_name: {
                        required: "{{ __('Employee First Name is Required') }}",
                        minlength: "{{ __('common.validate_min_length') }}",
                    },
                    last_name: {
                        required: "{{ __('Employee Last Name is Required') }}",
                    },
                    joining_date: {
                        required: "{{ __('Joining Date is Required') }}",
                    },
                    employee_type: {
                        required: "{{ __('Employee Type is Required') }}",
                    },
                    contract_end_date: {
                        required: "{{ __('For contract Employee, contract End date  is Required') }}",
                    },
                    date_contract_end: {
                        required: "{{ __('For contract Employee, contract End date  is Required') }}",
                    },
                    designation_id: {
                        required: "{{ __('administration.validate_designation_require') }}",
                    },
                    department_id: {
                        required: "{{ __('administration.validate_department_require') }}",
                    },
                    "role_id[]": {
                        required: "{{ __('administration.validate_role_require') }}",
                    },

                    factory_id: {
                        required: "{{ __('administration.validate_factory_require') }}",
                    },
                    reporting_manager_id: {
                        required: "{{ __('Reporting Manager is Required') }}",
                    },


                    email: {
                        required: "{{ __('Employee Email is Required') }}",
                        minlength: "{{ __('common.validate_min_length') }}",
                        maxlength: "{{ __('common.validate_max_length') }}",
                        remote: "{{ __('administration.validate_employee_email_unique') }}"
                    },
                    phone: {
                        required: "{{ __('Employee Phone is Required') }}",
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
