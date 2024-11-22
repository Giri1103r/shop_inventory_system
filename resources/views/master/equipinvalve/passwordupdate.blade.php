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
            <div class="content-header">
                <div class="d-flex align-items-center">
                    <div class="mr-auto">

                        <div class="d-inline-block align-items-center">
                            <nav>
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ url('') }}"><i class="mdi mdi-home-outline"></i></a> </li>
                                    <li class="breadcrumb-item" aria-current="page">Tables</li>
                                    <li class="breadcrumb-item active" aria-current="page">Data Tables</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>

            <section class="content">
                <div class="row">

                    <div class="col-12">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Company Password Reset</h4>
                                    <div>
                                        <x-button-back href="{{ admin_url('company/list') }}"></x-button-back>
                                    </div>
                                </div>

                                <div class="card-body ">
                                    <div class="basic-form">
                                        <form method="POST" id="useradd" action="{{ admin_url('company/passwordchange/submit') }}">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ encryptId($company->id)}}">
                                            <div class="card view_card">
                                                <div class="card-header">
                                                    <h4 class="card-title">Basic Details</h4>

                                                </div>
                                                <div class="card-body">


                                                    <div class="row">
                                                        <div class="col-md-4 form-input">
                                                            <label for="emp_id" class="form-label require">Company ID</label>
                                                            <input type="text" name="emp_id" class="form-control"
                                                                id="emp_id" value="{{ $company->company_id }}" required
                                                                readonly>
                                                        </div>
                                                        <div class="col-md-4 form-input">
                                                            <label for="emp_name" class="form-label require">Company
                                                                Name</label>
                                                            <input type="text" name="emp_name" class="form-control"
                                                                id="emp_name" value="{{ $company->company_name }}"  required
                                                                readonly>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>

                                            <hr>
                                            <div class="card view_card">
                                                <div class="card-header">
                                                    <h4 class="card-title">Password Details</h4>

                                                </div>
                                                <div class="card-body">

                                                    <div class="row">
                                                        <div class="col-md-4 form-input">
                                                            <label for="password" class="form-label require">New
                                                                Password</label>
                                                            <div class="input-group date form-input">
                                                                <input type="password" required=""
                                                                    class="form-control password" id="password" name="password"
                                                                    value="">
                                                                <div class="input-group-addon input-group-text password_view">
                                                                    <span style="color:#0053a1" class="fa fa-eye"></span>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-4 form-input">
                                                            <label for="password_confirmation"
                                                                class="form-label require">Re-enter Password</label>

                                                            <div class="input-group date form-input">
                                                                <input type="password" required=""
                                                                    class="form-control password" id="password_confirmation"
                                                                    name="password_confirmation">
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

        $('#useradd').validate({
            rules: {
                password: {
                    required: true,
                },
                password_confirmation: {
                    required: true,
                    passwordMatch: true
                },
            },
            messages: {
                password: {
                    required: "{{ __('Password is Required') }}",
                },
                password_confirmation: {
                    required: "{{ __('Re-enter Password is Required') }}",
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
                    console.log("Field: " + error.element.name + ", Error: " + error.message);
                });
            }
        });
    });
</script>
@endpush
