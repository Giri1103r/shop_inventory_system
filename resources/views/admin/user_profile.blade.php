@extends('admin.layouts.admin')
@section('title', 'Profile')
@section('pageurl', admin_url('dashboard'))


@section('content')
    <div class="content-wrapper">
        <div class="container-full">
            <div class="content-header">
                <div class="d-flex align-items-center">
                    <div class="mr-auto">

                        <div class="d-inline-block align-items-center">
                            <nav>
                                {{-- <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ admin_url('dashboard') }}"><i
                                                class="mdi mdi-home-outline"></i></a>
                                    </li>
                                    <li class="breadcrumb-item active" aria-current="page">Profile</li>
                                </ol> --}}
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
                                            <img src="{{ url(profileImage(Auth::id())) }}" alt=""
                                                class="rounded-circle p-1 bg-primary" width="150" height="150">
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
                                            <li class="list-group-item d-flex align-items-center">
                                                <div class="row w-100 ">
                                                    <div class="col-sm-4 mb-2">
                                                        <button class="btn btn-outline-primary w-100 " data-bs-toggle="modal"
                                                            id="chpi" data-bs-target="#profileImageModal">Change Image</button>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <button class="btn btn-outline-secondary w-100" data-bs-toggle="modal"
                                                            id="chpw" data-bs-target="#changePasswordLargeModal">Change Password</button>
                                                    </div>
                                                   
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
                                        <form id="profileform" action="{{ admin_url('profile/update') }}" method="post"
                                            enctype="multipart/form-data">
                                            @csrf
                                            <div class="row mb-3">
                                                <div class="col-sm-3">
                                                    <h6 class="mb-0 required">First Name</h6>
                                                </div>
                                                <div class="col-sm-9 text-secondary form-input">
                                                    <input type="text" name="name" class="form-control"
                                                        value="{{ Auth::user()->name }}" />
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-sm-3">
                                                    <h6 class="mb-0 required">Last Name</h6>
                                                </div>
                                                <div class="col-sm-9 text-secondary form-input">
                                                    <input type="text" name="last_name" class="form-control"
                                                        value="{{ Auth::user()->last_name }}" />
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-sm-3">
                                                    <h6 class="mb-0 required">Email</h6>
                                                </div>
                                                <div class="col-sm-9 text-secondary form-input">
                                                    <input type="email" name="email" class="form-control"
                                                        value="{{ Auth::user()->email }}" />
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-sm-3">
                                                    <h6 class="mb-0 required">Phone</h6>
                                                </div>

                                                <div class="col-sm-9 text-secondary form-input">
                                                    <input type="text" name="phone" class="form-control" maxlength="10"
                                                        minlength="10" oninput="validatePhone()"
                                                        value="{{ Auth::user()->mobile }}" />
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <button type="submit" class="btn btn-block btn-primary">Change
                                                    Profile</button>
                                            </div>
                                        </form>
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
                                        <div class="file-pop " style  = "border :2px solid black; padding:30px;">
                                            <span class="text-green btn-file">
                                                <span class="photo fileinput-new" title="Add Image">
                                                    <img class="imgupload"
                                                        src='{{ admin_url('public/assets/images/common/camera.png') }}'
                                                        style="height: 30px;" />
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

    {{-- Signature Upload --}}
    <div class="modal fade" id="signatureModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Signature Upload</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form id="signatureUpload" action="{{ admin_url('profile/signature-upload/update') }}" method="post"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="col-md-12">
                            <div class="row addMorecompet1">
                                <div class="col-md-4 form-input">
                                    <label class="form-label ">Signature Upload</label>
                                    <br>
                                    <div class="fileinput fileinput-new apprFileinput message" style=""
                                        data-provides="fileinput">
                                        <div class="fileinput-preview thumbnail bootimgheight appbootimgheight"
                                            data-trigger="fileinput">
                                        </div>
                                        <p class="mini-txt ">(png, jpeg, jpg )</p>
                                        <div class="file-pop " style  = "border :2px solid black; padding:30px;">
                                            <span class="text-green btn-file">
                                                <span class="photo fileinput-new" title="Add Image">
                                                    <img class="imgupload"
                                                        src='{{ admin_url('public/assets/images/common/camera.png') }}'
                                                        style="height: 30px;" />
                                                </span>
                                                <span class="fileinput-exists" title="Add Image"></span>
                                                <input type="file" name="signature_image" class='atarfile'
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
@stop

@push('script')
    <script type="text/javascript">
        function validatePhone() {
            const input = document.getElementById('phone');
            let value = input.value.trim();
            if (!value.startsWith('+91')) {
                input.value = '+91';
            }
            value = input.value.replace(/[^\d+]/g, '');

            if (value.length > 13) {
                value = value.slice(0, 13);
            }
            // Update the input value
            input.value = value;
        }
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


            $('#profileform').validate({
                rules: {
                    name: {
                        required: true,
                    },
                    email: {
                        required: true,
                    },
                    last_name: {
                        required: true,
                    },
                    phone: {
                        required: true,
                    },

                },
                messages: {
                    name: {
                        required: "Please Enter First Name",
                    },
                    last_name: {
                        required: "Please Enter Last Name",
                    },
                    email: {
                        required: "Please Enter Email Id",
                    },
                    phone: {
                        required: "Please Enter Phone Number",
                        minlength: "Please enter at least 10 characters.",
                    },

                },
                errorElement: 'span',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.form-input').append(error);
                },
                highlight: function(element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                    $(element).closest(".form-input").addClass("selecterror");
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                    $(element).closest(".form-input").removeClass("selecterror");
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
