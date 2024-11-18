@extends('admin.layouts.admin')
@section('title', 'Location Type Add')
@section('pageurl', admin_url('administration/role/list'))


@section('content')
    <div class="clearfix"></div>
    <div class="page-titles">
        <div class="d-flex align-items-center">
            <h2 class="text-black">Employee Password Reset</h2>

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
            <li class="breadcrumb-item"><a href="javascript:void(0)">{{ __('leftmenu.menu_8') }}</a></li>
            <li class="breadcrumb-item"><a href="javascript:void(0)">Employee Password Reset</a></li>
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
                                <h4 class="card-title">Employee Password Reset</h4>
                                <div>
                                    <x-button-back href="{{ admin_url('employee/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body ">
                                <div class="basic-form">
                                    <form method="POST" id="password_update"
                                        action="{{ admin_url('employee/passwordchange/submit') }}">
                                        @csrf
                                        <div class="card view_card">
                                            <div class="card-header">
                                                <h4 class="card-title">Basic Details</h4>

                                            </div>
                                            <div class="card-body">

                                                <input type="hidden" name="id" value="{{ encryptId($employee->id) }}">
                                                <div class="row">
                                                    <div class="col-md-4 form-input">
                                                        <label for="emp_id" class="form-label require">Employee ID</label>
                                                        <input type="text" name="emp_id" class="form-control" id="emp_id"
                                                            value="{{ $employee->employee_no }}" required readonly>
                                                    </div>
                                                    <div class="col-md-4 form-input">
                                                        <label for="emp_name" class="form-label require">Employee First Name</label>
                                                        <input type="text" name="emp_name" class="form-control" id="emp_name"
                                                            value="{{ $employee->first_name }}" required readonly>
                                                    </div>

                                                    <div class="col-md-4 form-input">
                                                        <label for="emp_name" class="form-label require">Employee Last Name</label>
                                                        <input type="text" name="emp_name" class="form-control" id="emp_name"
                                                            value="{{ $employee->last_name }}" required readonly>
                                                    </div>
                                                 </div>
                                        </div>
                                    </div>
                                        <div class="card view_card">
                                            <div class="card-header">
                                                <h4 class="card-title">Password Details</h4>
                                                
                                            </div>
                                            <div class="card-body">

                                                <input type="hidden" name="id" value="{{ encryptId($employee->id) }}">
                                                <div class="row">
                                                    <div class="col-md-4 form-input">
                                                        <label for="password" class="form-label require">New Password</label>
                                                        <div class="input-group date form-input">
                                                            <input type="password" required="" class="form-control password"
                                                                id="password" name="password" value="">
                                                            <div class="input-group-addon input-group-text password_view">
                                                                <span style="color:#0053a1" class="fa fa-eye"></span>
                                                            </div>
                                                        </div>
                                                    </div>
            
                                                    <div class="col-md-4 form-input">
                                                        <label for="password_confirmation" class="form-label require">Re-enter Password</label>
            
                                                        <div class="input-group date form-input">
                                                            <input type="password" required="" class="form-control password"
                                                                id="password_confirmation" name="password_confirmation">
                                                            <div class="input-group-addon input-group-text password_view">
                                                                <span style="color:#0053a1" class="fa fa-eye"></span>
                                                            </div>
                                                        </div>
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

        $.validator.addMethod("passwordPolicy", function(value, element) {
            return /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*()_+\-=[\]{};':"\\|,.<>/?]).{8,}$/
                .test(value);
        }, "Password must contain a minimum of 8 alphanumeric characters with a combination of uppercase, lowercase, number and symbol.");

        $('#password_update').validate({
            rules: {
                emp_id: {
                    required: true,
                },
                emp_name: {
                    required: true,
                },
                password: {
                    required: true,
                    passwordPolicy : true
                },
                password_confirmation: {
                    required: true,
                    passwordPolicy : true,
                    equalTo: "#password",
                },
            },
            messages: {
                emp_id: {
                    required: "Please enter Employee ID",
                },
                emp_name: {
                    required: "Please enter Employee Name",
                },
                password: {
                    required: "Please enter the Password",
                },
                password_confirmation: {
                    required: "Please enter the Confirm Password",
                    equalTo: "Password and confirm password Mismatch",

                },

            },
            errorElement: 'span',
            errorPlacement: function(error, element) {
                error.addClass('invalid-feedback');
                element.closest('.form-input').append(error);
            },
            highlight: function(element, errorClass, validClass) {
                $(element).addClass('is-invalid');
                $(element).closest(".form-input").addClass("selecterror");
            },
            unhighlight: function(element, errorClass, validClass) {
                $(element).removeClass('is-invalid');
                $(element).closest(".form-input").removeClass("selecterror");
            },
        });
    });
</script>
@endpush
