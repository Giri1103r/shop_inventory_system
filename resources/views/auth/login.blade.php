@extends('layouts.app')
@section('title', 'Login')

@section('content')
    <div class="card">
        <div class="card-body p-4">

            <div class="text-center mb-4">
                <h4 class="text-uppercase mt-0">Sign In</h4>
            </div>

            <form  id="loginform" action="{{ admin_url('logintry') }}" method="post">
                @csrf
                <div class="mb-3 form-input">
                    <label for="emailaddress" class="form-label">Email address</label>
                    <input class="form-control" type="email" id="email" name="email" required=""
                        placeholder="Enter your email">
                </div>

                <div class="mb-3 form-input">
                    <label for="password" class="form-label">Password</label>
                    <input class="form-control" type="password" name="password" required="" id="password"
                        placeholder="Enter your password">
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="checkbox-signin" name="remember">
                        <label class="form-check-label" for="checkbox-signin">Remember me</label>
                    </div>
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
    <script type="text/javascript" nonce="neoehsscript">
        $(function() {
            $('#loginform').validate({
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
