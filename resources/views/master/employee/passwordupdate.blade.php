@extends('admin.layouts.admin')
@section('title', 'Employee Password Reset')
@section('pageurl', admin_url('employee/list'))

@section('content')
    <div class="clearfix"></div>
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


                            <div class="card-body ">

                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 style="color:#fff">Employee Details</h4>
                                    </div>
                                </div>
                                <div class="row">

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Employee ID') }}</label>
                                        <div class="view_data">
                                            {{ isset($employee->emp_id) ? $employee->emp_id : '' }}
                                        </div>
                                    </div>

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Employee Name') }}</label>
                                        <div class="view_data">
                                            {{ isset($employee->emp_name) ? $employee->emp_name : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Gender') }}</label>
                                        <div class="view_data">
                                            {{ isset($employee->gender) ? $employee->gender : '' }}
                                        </div>
                                    </div>

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Nationality') }}</label>
                                        <div class="view_data">
                                            {{ isset($employee->nationality) ? $employee->nationality : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('ID Type') }}</label>
                                        <div class="view_data">
                                            {{ isset($employee->id_type) ? $employee->id_type : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('ID Number') }}</label>
                                        <div class="view_data">
                                            {{ isset($employee->id_number) ? $employee->id_number : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Employee Email') }}</label>
                                        <div class="view_data">
                                            {{ isset($employee->email) ? $employee->email : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('User Role') }}</label>
                                        <div class="view_data">

                                            @php
                                                $userRoles = explode(',', $employee->user_role ?? '');
                                                $roleNames = [];
                                            @endphp

                                            @foreach ($userrole as $role)
                                                @if (in_array($role->id, $userRoles))
                                                    @php
                                                        $roleNames[] = $role->role_name;
                                                    @endphp
                                                @endif
                                            @endforeach

                                            {{ implode(', ', $roleNames) }}

                                        </div>
                                    </div>

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Joining Date') }}</label>
                                        <div class="view_data">
                                            {{ Displaydateformat($employee->joining_date) ?? '-' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Phone Number') }}</label>
                                        <div class="view_data">
                                            {{ $employee->mobile_no ? $employee->mobile_no : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Employee Status') }}</label>
                                        <div class="view_data">
                                            {{ isset($employee->employee_status) ? $employee->employee_status : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Company Name') }}</label>
                                        <div class="view_data">
                                            {{ getCompanyname(isset($employee->company) ? $employee->company : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Location Name') }}</label>
                                        <div class="view_data">
                                            {{ getLocationname(isset($employee->location) ? $employee->location : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Unit Name') }}</label>
                                        <div class="view_data">
                                            {{ getUnitname(isset($employee->unit) ? $employee->unit : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Department Name') }}</label>
                                        <div class="view_data">
                                            {{ getDepartment(isset($employee->department) ? $employee->department : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Designation') }}</label>
                                        <div class="view_data">
                                            {{ $employee->designation ? $employee->designation : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Reporting Manager') }}</label>
                                        <div class="view_data">
                                            {{ $employee->reporting_manager ? $employee->reporting_manager : '' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body ">

                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 style="color:#fff">Password Reset</h4>
                                    </div>
                                </div>
                                <form method="POST" id="employeeepass"
                                    action="{{ admin_url('employee/passwordchange/submit') }}">
                                    @csrf
                                    <input type="hidden" name="id" id="id"
                                        value="{{ encryptId($employee->id) }}">

                                    <div class="row">

                                        <div class="col-md-4 form-input">
                                            <label for="password" class="form-label require">New
                                                Password</label>
                                            <div class="input-group date form-input">
                                                <input type="password" required="" class="form-control password"
                                                    id="password" name="password" placeholder="New Password">
                                                <div class="input-group-addon input-group-text password_view">
                                                    <span style="color:#0053a1" class="fa fa-eye"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 form-input">
                                            <label for="password_confirmation" class="form-label require">Confirm
                                                Password</label>

                                            <div class="input-group date form-input">
                                                <input type="password" required="" class="form-control password"
                                                    id="password_confirmation" name="password_confirmation"
                                                    placeholder="Confirm Password">
                                                <div class="input-group-addon input-group-text password_view">
                                                    <span style="color:#0053a1" class="fa fa-eye"></span>
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

@stop
@push('script')
    <script type="text/javascript" nonce="projectcab">
        $('.password_view').on('click', function() {
            var passInput = $(this).prev('.password');
            var eyeIcon = $(this).find('span.fa');

            if (passInput.attr('type') === 'password') {
                passInput.attr('type', 'text');
                eyeIcon.removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                passInput.attr('type', 'password');
                eyeIcon.removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });

        $(function() {
            $.validator.addMethod("passwordMatch", function(value, element) {
                return value === $('#password').val();
            }, "{{ __('Passwords do not match') }}");

            $.validator.addMethod("strongPassword", function(value, element) {
                    return this.optional(element) ||
                        /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,16}$/.test(value);
                },
                "{{ __('Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character') }}"
                );

            // Validate the form
            $('#employeeepass').validate({
                rules: {
                    password: {
                        required: true,
                        minlength: 8, 
                        maxlength: 16, 
                        strongPassword: true
                    },
                    password_confirmation: {
                        required: true,
                        minlength: 8, 
                        maxlength: 16, 
                        passwordMatch: true
                    },
                },
                messages: {
                    password: {
                        required: "{{ __('Password is required') }}",
                        minlength: "{{ __('Password must be at least 8 characters') }}",
                        maxlength: "{{ __('Password must not exceed 16 characters') }}",
                        strongPassword: "{{ __('Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character') }}"
                    },
                    password_confirmation: {
                        required: "{{ __('Re-enter password is required') }}",
                        minlength: "{{ __('Password confirmation must be at least 8 characters') }}",
                        maxlength: "{{ __('Password confirmation must not exceed 16 characters') }}",
                        passwordMatch: "{{ __('Passwords do not match') }}"
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
