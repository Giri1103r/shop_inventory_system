@extends('layouts.app')
@section('title', 'Reset Password')
@section('content')
    <div class="container">
        <div class="card">
            <div class="card-body p-4">

                <div class="text-center mb-4">
                    <h4 class="text-uppercase mt-0">Reset Password</h4>
                </div>
                <form id="resetpassword" action="{{ admin_url('password/reset-password/submit') }}" method="post">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                    <div class="mb-3 form-input">
                        <label class="form-label required">Enter New Password</label>
                        <input type="password" required class="form-control" id="password" name="password"
                            placeholder="Enter New Password" minlength="8">
                    </div>
                    <div class="mb-3 form-input">
                        <label class="form-label required">Confirm New Password</label>
                        <input type="password" required class="form-control" id="confirmpassword" name="confirmpassword"
                            placeholder="Confirm New Password" minlength="8">
                    </div>
                    <div class="mb-3 d-grid text-center">
                        <button class="btn btn-primary" type="submit">Reset Password</button>
                    </div>
                </form>


            </div> <!-- end card-body -->
        </div>

    @endsection
    @push('script')
        <script type="text/javascript" nonce="ardhasscript">
            $(function() {
                $('#resetpassword').validate({
                    rules: {
                        password: {
                            required: true,
                            minlength: 8,
                            maxlength: 20,
                        },
                        confirmpassword: {
                            required: true,
                            equalTo: "#password"
                        },
                    },
                    messages: {
                        password: {
                            required: "Please enter your password",
                            minlength: "Password must be at least 8 characters long",
                            maxlength: "Password must not exceed 20 characters"
                        },
                        confirmpassword: {
                            required: "Please enter your confirm password",
                            equalTo: "Please enter the same password"
                        },
                    },
                    errorElement: 'div',
                    errorPlacement: function(error, element) {
                        error.addClass('invalid-feedback');
                        element.closest('.form-input').append(error);
                    },
                    highlight: function(element, errorClass, validClass) {
                        $(element).addClass('is-invalid');
                    },
                    unhighlight: function(element, errorClass, validClass) {
                        $(element).removeClass('is-invalid');
                    }
                });
            });
        </script>
    @endpush
