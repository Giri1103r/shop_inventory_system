@extends('layouts.app')
@section('title', 'Login')
<style>
    /* Positioning the eye icon */
    .form-input {
        position: relative;
    }
    
    .show-pass.eye {
        position: absolute;
        top: 70%;
        right: 10px;
        transform: translateY(-50%);
        cursor: pointer;
        color: #6c757d; /* Optional: matches Bootstrap input styles */
    }
    
    .show-pass.eye:hover {
        color: #000; /* Optional: hover effect */
    }
    </style>
@section('content')
    <div class="card">
        <div class="card-body p-4">

            <div class="text-center mb-4">
                <h4 class="text-uppercase mt-0">Sign In</h4>
            </div>

            <form id="login_form_validate" action="{{ admin_url('logintry') }}" method="post">
                @csrf
                <div class="mb-3 form-input">
                    <label for="emailaddress" class="form-label">Email address</label>
                    <input class="form-control" type="email" id="email" name="email" required=""
                        placeholder="Enter your email">
                </div>

                <div class="mb-3 form-input">
                    <label for="password" class="form-label">Password</label>
                    <input class="form-control"id="dlab-password" type="password" name="password" required="" id="password"
                        placeholder="Enter your password">

                    <span class="show-pass eye">
                        <i class="fa fa-eye-slash" id="eye-slash"></i>
                        <i class="fa fa-eye d-none" id="eye"></i>
                    </span>
                </div>


                {{-- <div class="mb-3">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="checkbox-signin" name="remember">
                        <label class="form-check-label" for="checkbox-signin">Remember me</label>
                    </div>
                </div> --}}


                <div class="mb-3 form-input">
                    <div class="form-check custom-checkbox mb-0">
                        <input type="checkbox" class="form-check-input" id="customCheckBox1" name="remember">
                        <label class="form-check-label remember_me" for="customCheckBox1">Remember me</label>
                    </div>
                </div>
                <div class="mb-3">

                    <div class="g-recaptcha" data-sitekey="{{ env('GOOGLE_RECAPTCHA_KEY') }}"></div>
                    <div class="recaptcha-error" style="color:red;"></div>
                </div>
                <div class="mb-3 d-grid text-center">
                    <button class="btn btn-primary" type="submit"> Log In </button>
                </div>
            </form>

        </div> <!-- end card-body -->
    </div>
    <!-- end card -->

    <div class="row mt-3">
        <div class="col-12 text-center">
            <p> <a href="{{ admin_url('password/forgot') }}" class="text-muted ms-1"><i class="fa fa-lock me-1"></i>Forgot
                    your password?</a></p>

        </div> <!-- end col -->
    </div>
    <!-- end row -->
@endsection

@push('script')
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

    <script type="text/javascript">
        $('#login_form_validate').on('submit', function(e) {
            if (grecaptcha.getResponse() == "") {
                e.preventDefault();
                $(".recaptcha-error").html('The recaptcha field is required');
                return false;
            } else {
                $(".recaptcha-error").empty();
                return true;
            }
        });

        jQuery('.show-pass').on('click',function(){
			jQuery(this).toggleClass('active');
			if(jQuery('#dlab-password').attr('type') == 'password'){
				jQuery('#dlab-password').attr('type','text');
			}else if(jQuery('#dlab-password').attr('type') == 'text'){
				jQuery('#dlab-password').attr('type','password');
			}
		});
        $(function() {
            $('#login_form_validate').validate({
                rules: {
                    email: {
                        required: true,
                    },
                    password: {
                        required: true,
                        'minlength': 8,
                        'maxlength': 50,
                    },
                },
                messages: {
                    email: {
                        required: "Please enter your email",
                    },
                    password: {
                        required: "Please enter your Password",
                        maxlength: "Maximum character limit reached",
                    },
                },
                errorElement: 'div',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.form-input').append(error);
                    if (element.attr('name') === 'password') {
                    element.closest('.form-input').find('.show-pass.eye').css('top', '40%');
                
                }
                

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
