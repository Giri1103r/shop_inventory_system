@extends('layouts.app')
@section('title', 'Forgot Password')
@section('content')
    <div class="card">
        <div class="card-body p-4">

            <div class="text-center mb-4">
                <h4 class="text-uppercase mt-0">Reset Password</h4>
            </div>

            <form id="resetform" action="{{ admin_url('password/forgot/submit') }}"  method="post">
                @csrf
                <div class="mb-3 form-input">
                    <label for="emailaddress" class="form-label required">Email address</label>
                    <input class="form-control" type="email" id="email" name="email" required
                        placeholder="Enter your email">
                </div>

                <div class="mb-3 d-grid text-center">
                    <button class="btn btn-primary" type="submit"> Send OTP </button>
                </div>
            </form>

        </div> <!-- end card-body -->
    </div>
    <!-- end card -->

    <div class="row mt-3">
        <div class="col-12 text-center">
            <p> <a href="{{ admin_url('login') }}" class="text-muted ms-1"><i class="fa fa-lock me-1"></i>Login</a></p>

        </div> <!-- end col -->
    </div>
    <!-- end row -->
@endsection

@push('script')
    <script type="text/javascript" nonce="neoehsscript">
        $(function() {
            $('#resetform').validate({
                rules: {
                    email: {
                        required: true,
                    },
                },
                messages: {
                    email: {
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
