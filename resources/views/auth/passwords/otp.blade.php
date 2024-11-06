@extends('layouts.app')
@section('title', 'Forgot Password')
@section('content')
    <div class="card">
        <div class="card-body p-4">

            <div class="text-center mb-4">
                <h4 class="text-uppercase mt-0">Reset Password</h4>
            </div>

            <form id="resetform" action="{{ admin_url('password/otp/submit') }}"  method="post">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
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

                <div class="mb-3">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="checkbox-signin" name="remember">
                        <label class="form-check-label" for="checkbox-signin">Remember me</label>
                    </div>
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
