@extends('layouts.login')
@section('title', 'Login')

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

                            <form id="resetform" action="{{ admin_url('password/forgot/submit') }}" method="post" autocomplete="false">
                                <div class="text-center mb-4">
                                    <img  src="{{ public_image('energy-logo.png') }}" alt="logo" style="width: 145px;">

                                </div>
                                @csrf
                                <h3 class="text-center mb-2 text-dark">Reset Password</h3>

                                <div class="mb-3 form-input">
                                    <label for="exampleFormControlInput1" class="form-label required"> Email address</label>
                                    <input type="email" class="form-control" id="exampleFormControlInput1" name="email" id="email"
                                        value="" placeholder="Enter Your Registered Email Address">
                                </div>

                                <div class="form-row d-flex justify-content-between mt-4 mb-2">


                                </div>
                                <button class="btn btn-block btn-primary">Send OTP</button>
                                <div class="text-center">
                                    <a href="{{ admin_url('login') }}" class="btn-link text-primary">Login</a>
                                </div>
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
                    email: {
                        required: true,

                    },

                },
                messages: {
                    username: {
                        required: "Please enter your email",
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
