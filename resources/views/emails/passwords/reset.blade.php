@extends('layouts.login')
@section('title', 'Password Reset')

<div class="authincation d-flex flex-column flex-lg-row flex-column-fluid bgimage">
    <div class="login-aside text-center  d-flex flex-column flex-row-auto">

        <div class="aside-image position-relative">
            <img class="img1" src="{{ public_image('password-img.png') }}" alt="" style="top: -150px; !important">

        </div>
    </div>
    <div
        class="container flex-row-fluid d-flex flex-column justify-content-center position-relative overflow-hidden p-7 mx-auto">
        <div class="d-flex justify-content-center h-100 align-items-center">
            <div class="authincation-content style-2">
                <div class="row no-gutters">
                    <div class="col-xl-12 tab-content">
                        <div id="sign-up" class="auth-form tab-pane fade show active  form-validation">

                            <form id="resetform" action="{{ admin_url('password/finalreset/submit') }}" method="post"
                                autocomplete="false">
                                <div class="text-center mb-4">
                                    <img src="{{ public_image('energy-logo.png') }}" alt="logo"
                                        style="width: 145px;">
                                </div>
                                <h3 class="text-center mb-2 text-dark">New Password</h3>
                                @csrf
                                <input type="hidden" name="token" value="{{ $token }}">
                                <div class="mb-3 form-input">
                                    <label for="exampleFormControlInput1" class="form-label required">Enter New
                                        Password</label>

                                    <input type="password" required class="form-control password" id="password"
                                        name="password" placeholder="Enter New Password" minlength="8">
                                </div>

                                <div class="mb-3 form-input">
                                    <label for="exampleFormControlInput1" class="form-label required">Confirm
                                        New Password</label>
                                    <input type="password" required class="form-control password" id="confirmpassword"
                                        name="confirmpassword" placeholder="Confirm New Password" minlength="8">
                                </div>


                                <button class="btn btn-block btn-primary">Confirm Password</button>
                            </form>
                            <form action="{{ route('password.resend.otp') }}" method="POST">
                                @csrf
                                <input type="hidden" name="token" value="{{ $token }}">
                                <button type="submit" id="resendOTPButton"  class="btn btn-block text-primary"
                                    style="display: none;margin-left: 115;border-color: white;">Resend OTP</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('script')
    <script type="text/javascript" nonce="ardhasscript">
        $(function() {
            $('#resetform').validate({
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
