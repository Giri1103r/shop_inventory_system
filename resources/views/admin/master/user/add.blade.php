@extends('admin.layouts.admin')
@section('title', 'User Add')
@section('pageurl', admin_url('dashboard'))

@push('style')
    <style>


    </style>
@endpush

@section('content')
    <div class="content-wrapper">
        <div class="container-full">
            <section class="content">
                <div class="row">

                    <div class="col-12">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">User Add</h4>
                                    <div>
                                        <x-button-back href="{{ admin_url('administration/users/list') }}"></x-button-back>
                                    </div>
                                </div>

                                <div class="card-body ">
                                    <div class="basic-form">
                                        <form method="POST" id="useradd"
                                            action="{{ admin_url('administration/users/add/submit') }}">
                                            @csrf

                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>First Name</label>
                                                        <input type="text" class="form-control" placeholder="First Name">
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Last Name</label>
                                                        <input type="text" class="form-control" placeholder="First Name">
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Email ID</label>
                                                        <input type="email" class="form-control" placeholder="First Name">
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Role</label>
                                                        <select name="emp_role_id[]" id="emp_role_id"
                                                            class="select2 form-control">

                                                            <option value="">Select Role</option>
                                                            @foreach ($rolelist as $role)
                                                                <option value="{{ encryptId($role->id) }}">
                                                                    {{ $role->role_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>


                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label class="form-label required">Company</label>
                                                        <select name="company_id" id="company_id"
                                                            class="select2 form-control">
                                                            <option value="">Select Company</option>
                                                            @foreach ($company as $company)
                                                                <option value="{{ encryptId($company->id) }}">
                                                                    {{ $company->company_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label class="form-label require">Location Name</label>
                                                        <select name="location_id" id="location_id"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Location Name</option>

                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-3">
                                                    <div class="form-group form-input">
                                                        <label class="form-label require">Unit Name</label>
                                                        <select name="unit_id" id="unit_id"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Unit Name</option>

                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-3">
                                                    <div class="form-group form-input">
                                                        <label class="form-label require">Department Name</label>
                                                        <select name="department_id" id="department_id"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Department Name</option>

                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="submit-button">
                                                <x-button-submit class="submit"></x-button-submit>
                                                <x-button-reset class="submit"></x-button-reset>
                                                <x-button-cancel
                                                    href="{{ admin_url('administration/users/list') }}"></x-button-cancel>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

        </div>
    </div>


@endsection


@push('scripts')
@endpush

@push('script')
    <script type="text/javascript" nonce="projectcab">
        $(function() {
            $('#useradd').validate({
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
