@extends('admin.layouts.admin')
@section('title', 'UA/UC Category Add')
@section('pageurl', admin_url('oper_manage/factory/list'))


@section('content')
<div class="content-wrapper">
    <div class="container-full">
        <div class="content-header">
            <div class="d-flex align-items-center">
                <div class="mr-auto">

                    <div class="d-inline-block align-items-center">
                        <nav>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ admin_url('dashboard') }}"><i class="mdi mdi-home-outline"></i></a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">Profile</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="row">
                            <!-- left -->
                            <div class="col-lg-5">
                                <div class="card-body ">
                                    <div class="d-flex flex-column align-items-center text-center">
                                        <img src="{{ url(profileImage(Auth::id())) }}" alt="Admin" class="rounded-circle p-1 bg-primary" width="150" height="150">
                                        <div class="mt-3">
                                            <h4> {{ Auth::user()->name }} </h4>
                                            <p class="text-secondary mb-1">
                                                {{ getUserRoleName(Auth::id()) }}</p>

                                        </div>
                                    </div>
                                    <hr class="my-4" />
                                    <ul class="list-group list-group-flush">
                                        <li
                                            class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                            <h6 class="mb-0">User Status</h6>
                                            <span class="text-secondary">
                                                @if (Auth::user()->status == 1)
                                                    Active
                                                @else
                                                    In-Active
                                                @endif
                                            </span>
                                        </li>
                                        <li
                                            class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                            <div class="col-sm-4"style="margin-right:1px;">
                                            <button class="btn btn-outline-primary" data-bs-toggle="modal" id="chpi"
                                                data-bs-target="#profileImageModal">Change Image</button>
                                            </div>
                                            <div class="col-sm-5">
                                            <button class="btn btn-outline-secondary" data-bs-toggle="modal" id="chpw"
                                                data-bs-target="#changePasswordLargeModal">Change Password</button>
                                            </div>
                                        </li>

                                    </ul>
                                </div>
                            </div>
                            <!-- left end-->
                           <!-- right end-->

                           <div class="col-lg-7">
                                <div class="card-body">
                                    <h4>Profile Details</h4>
                                    <div class="row mb-3">
                                        <div class="col-sm-3">
                                            <h6 class="mb-0">Full Name</h6>
                                        </div>
                                        <div class="col-sm-9 text-secondary">{{ Auth::user()->name }}
                                            {{-- <input readonly type="text" class="form-control"
                                                value="{{ Auth::user()->name }}" /> --}}
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-sm-3">
                                            <h6 class="mb-0">Email</h6>
                                        </div>
                                        <div class="col-sm-9 text-secondary">{{ Auth::user()->email }}
                                            {{-- <input readonly type="text" class="form-control"
                                                value="{{ Auth::user()->email }}"/> --}}
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-sm-3">
                                            <h6 class="mb-0">Phone</h6>
                                        </div>
                                        <div class="col-sm-9 text-secondary">{{ Auth::user()->mobile }}
                                            {{-- <input readonly type="text" class="form-control"
                                                value="{{ Auth::user()->mobile }}" /> --}}
                                        </div>
                                    </div>
                                </div>
                        </div>
                           <!-- right end-->
                        </div>
                    </div>
                </div>

            </div>
        </section>

    </div>
</div>

    <!-- Profile Image -->
    <div class="modal fade" id="profileImageModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Change Profile Image</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form id="profileupload" action="{{ admin_url('profile/image/update') }}" method="post"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="col-md-12">
                            <div class="row addMorecompet1">
                                <div class="col-md-4 form-input">
                                    <label class="form-label ">Image Upload</label>
                                    <br>
                                    <div class="fileinput fileinput-new apprFileinput message" style="  "
                                        data-provides="fileinput">
                                        <div class="fileinput-preview thumbnail bootimgheight appbootimgheight"
                                            data-trigger="fileinput">
                                        </div>
                                        <p class="mini-txt ">(png, jpeg, jpg )</p>
                                        <div class="file-pop ">
                                            <span class="text-green btn-file">
                                                <span class="photo fileinput-new" title="Add Image">
                                                    <img class="imgupload"
                                                        src='{{ admin_url('public/assets/images/common/camera.png') }}'
                                                        style=" width: 30%; " />
                                                </span>
                                                <span class="fileinput-exists" title="Add Image"></span>
                                                <input type="file" name="profile_image" class='atarfile'
                                                    accept="image/*">
                                            </span>

                                            <button type="button" name="re"
                                                class="btn btn-nothing text-maroon fileinput-exists"
                                                data-dismiss="fileinput" title="Remove Image"><i
                                                    class="fa fa-times-circle-o" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                    </div>

                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>

                </form>


            </div>
        </div>
    </div>


    <!-- Change Password -->
    <div class="modal fade" id="changePasswordLargeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Change Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="ChangePassword" action="{{ admin_url('profile/password/update') }}" method="post"
                    enctype="multipart/form-data">

                    @csrf
                    <div class="modal-body">

                        <div class="row mb-3">
                            <label for="old_password" class="col-sm-3 col-form-label">Old Password</label>
                            <div class="col-sm-9 message">

                                <div class="input-group date form-input">
                                    <input type="password" required="" class="form-control password"
                                        placeholder="Old Password" id="old_password" name="old_password">
                                    <div class="input-group-addon input-group-text password_view">
                                        <span style="color:#0053a1" class="fa fa-eye"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3 ">
                            <label for="password" class="col-sm-3 col-form-label">New Password</label>
                            <div class="col-sm-9 message">

                                <div class="input-group date form-input">
                                    <input type="password" required="" class="form-control password" id="password"
                                        placeholder="New Password" name="password">
                                    <div class="input-group-addon input-group-text password_view">
                                        <span style="color:#0053a1" class="fa fa-eye"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="confirm_password" class="col-sm-3 col-form-label">Confirm Password</label>
                            <div class="col-sm-9 message">
                                <div class="input-group date form-input">
                                    <input type="password" required="" class="form-control password"
                                        placeholder="Confirm Password" id="confirm_password" name="confirm_password">
                                    <div class="input-group-addon input-group-text password_view">
                                        <span style="color:#0053a1" class="fa fa-eye"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="Submit" class="btn btn-primary">Update Password</button>
                    </div>
                </form>
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
            },
            "Password must contain a minimum of 8 alphanumeric characters with a combination of uppercase, lowercase, number and symbol."
        );

        $('#chpi').click(function() {
            // Reset the form content before displaying the modal
            $('#profileupload')[0].reset();
            $('#profileupload').validate().resetForm();
        });

        $('#chpw').click(function() {
            // Reset the form content before displaying the modal
            $('#ChangePassword')[0].reset();
            $('#ChangePassword').validate().resetForm();
        });

        $('#ChangePassword').validate({
            rules: {
                old_password: {
                    required: true,
                },
                password: {
                    required: true,
                    passwordPolicy: true
                },
                confirm_password: {
                    required: true,
                    equalTo: "#password",
                    passwordPolicy: true
                },
            },
            messages: {
                old_password: {
                    required: "Please Enter Old-Password",
                },
                password: {
                    required: "Please Enter Password",

                },
                confirm_password: {
                    required: "Please Enter Confirm-Password",
                    equalTo: "Password and confirm password Mismatch",

                },
            },
            errorElement: 'span',
            errorPlacement: function(error, element) {
                error.addClass('invalid-feedback');
                element.closest('.message').append(error);
            },
            highlight: function(element, errorClass, validClass) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function(element, errorClass, validClass) {
                $(element).removeClass('is-invalid');
            },
        });


        $('#profileupload').validate({
            rules: {
                profile_image: {
                    required: true,
                },

            },
            messages: {
                profile_image: {
                    required: "Please upload image",
                },

            },
            errorElement: 'span',
            errorPlacement: function(error, element) {
                error.addClass('invalid-feedback');
                element.closest('.message').append(error);
            },
            highlight: function(element, errorClass, validClass) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function(element, errorClass, validClass) {
                $(element).removeClass('is-invalid');
            },
        });
    });
</script>
@endpush
