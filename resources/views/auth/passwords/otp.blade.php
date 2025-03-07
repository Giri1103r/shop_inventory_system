@extends('layouts.app')
@section('title', 'Forgot Password')
@section('content')
    <div class="card">
        <div class="card-body p-4">

            <div class="text-center mb-4">
                <h4 class="text-uppercase mt-0">Reset Password</h4>
            </div>

            <form id="resetform" action="{{ admin_url('password/otp/submit') }}" method="post">
                @csrf
                {{-- <input type="hidden" name="token" value="{{ $token }}"> --}}
                <div class="mb-3">
                    <label for="emailaddress" class="form-label">Email address</label>
                    <input class="form-control" type="email" id="email" name="email" value="{{ $email }}"
                        readonly placeholder="Enter your email">
                </div>

                <div class="mb-3">
                    <label for="otp" class="form-label">Enter OTP</label>
                    <input class="form-control" type="text" name="otp" required="" id="otp"
                        placeholder="Enter your password">
                </div>


                <div class="mb-3 d-grid text-center">
                    <button class="btn btn-primary" type="submit"> Send OTP </button>
                </div>
                <p>OTP will expire in <span id="otp-timer">{{ $expire }}</span>.</p>
            </form>
            <form action="{{ route('password.resend.otp') }}" method="POST" id="resend-otp-form">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <button type="submit" id="resend-otp-btn" class="btn btn-block text-primary"
                    style="display: none;margin-left: 115;border-color: white;margin-top: -23px;">Resend OTP</button>
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
    <script type="text/javascript" nonce="ardhasscript">
        $(function() {
            $('#resetform').validate({
                rules: {
                    otp: {
                        required: true,

                    },

                },
                messages: {
                    otp: {
                        required: "Please enter your otp",
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
        var timer = parseInt("{{ $expire * 60 }}", 10);

        function countdown() {
            var minutes = Math.floor(timer / 60);
            var seconds = timer % 60;

            document.getElementById('otp-timer').innerHTML = minutes + ' min ' + (seconds < 10 ? '0' : '') + seconds +
                ' sec';

            if (timer > 0) {
                timer--;
                setTimeout(countdown, 1000);
            } else {
                document.getElementById('resend-otp-btn').style.display = 'block';

            }
        }
        countdown();
    </script>
@endpush
