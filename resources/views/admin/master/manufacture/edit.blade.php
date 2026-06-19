@extends('admin.layouts.admin')
@section('title', 'Manufacture Master Edit')
@section('pageurl', admin_url('master/manufacture/list'))
@push('style')
    <style>
        .card-header {
            position: relative;
        }

        .align-back-btc d-flex justify-content-end align-items-center {
            display: flex;
        }

        @media (max-width: 480px) {
            .align-back-btc d-flex justify-content-end align-items-center {
                width: 100%;
            }

            .align-back-btc d-flex justify-content-end align-items-center x-button-back,
            .align-back-btc d-flex justify-content-end align-items-center button {
                width: auto;
                max-width: 100%;
            }
        }
    </style>
@endpush
@section('content')
    <div class="clearfix"></div>
    <div class="page-titles">
        <div class="d-flex align-items-center">
            {{-- <h4 class="text-black">Company Add</h4> --}}

        </div>

    </div>

    <div class="content-body  default-height">
        <div class="container-fluid main-content">
            <!-- row -->
            <div class="row">

                <div class="col-12">
                    <div class="col-12">
                        <div class="card">

                            <div class="card-header d-flex justify-content-end align-items-center">
                                <x-button-back href="{{ admin_url('master/manufacture/list') }}"></x-button-back>
                            </div>

                            <div class="card-body">

                                <div class="basic-form">
                                    <form method="POST" id="edit_page"
                                        action="{{ admin_url('master/manufacture/edit/submit') }}">
                                        @csrf
                                        <input type="hidden" name="id" id="id"
                                            value="{{ encryptId($manufacture->id) }}">
                                        <div class="row">
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Manufacturer Id</label>
                                                    <input type="text" name ="manufacturer_id" class="form-control"
                                                        placeholder="Enter the Manufacuter Id"
                                                        value="{{ $manufacture->manufacture_id }}" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Manufacture Name</label>
                                                    <input type="text" name="manufacture_name" id="manufacture_name"
                                                        value="{{ $manufacture->manufacturer_name }}" class="form-control"
                                                        placeholder="Enter the Manufacture Name">
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">License Number</label>
                                                    <input type="text" name="license_number" class="form-control"
                                                        value="{{ $manufacture->license_number }}"
                                                        placeholder="Enter the License Number">
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Contact Person</label>
                                                    <input type="text" name="contact_person" class="form-control"
                                                        value="{{ $manufacture->contact_person }}"
                                                        placeholder="Enter the Contact Person">
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Mobile Number</label>
                                                    <input type="number" name="mobile_no"
                                                        class="form-control"value="{{ $manufacture->mobile_no }}"
                                                        placeholder="Enter the mobile number">
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Email</label>
                                                    <input type="email" name="email" class="form-control"
                                                        value="{{ $manufacture->email }}"
                                                        placeholder="Enter the mobile number">
                                                </div>
                                            </div>

                                            <div class="col-md-12">
                                                <div class="form-group form-input">
                                                    <label class="form-label ">Address</label>
                                                    <textarea name="address" class="form-control" placeholder="Enter the Address">{{ $manufacture->address }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="submit-button" style="text-align: right;">
                                            <x-button-submit type="submit"></x-button-submit>
                                            <x-button-reset class=""></x-button-reset>
                                        </div>
                                    </form>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </form>
    </div>

@stop

@push('script')
    <script type="text/javascript" nonce="projectcab">
        $(document).ready(function() {
            $('#resetform').on('click', function(e) {
                e.preventDefault();
                location.reload();
            });
        });
        $(function() {
            $('#edit_page').validate({
                rules: {
                    manufacture_name: {
                        required: true,
                        remote: {
                            url: '{{ admin_url('master/manufacture/unique') }}',
                            type: 'post',
                            data: {
                                manufacture_name: function() {
                                    return $('#manufacture_name').val();
                                }
                            }
                        }
                    },
                    license_number: {
                        required: true,
                        remote: {
                            url: '{{ admin_url('master/manufacture/unique') }}',
                            type: 'post',
                            data: {
                                license_number: function() {
                                    return $('#license_number').val();
                                }
                            }
                        }
                    },
                    email: {
                        required: true,
                        email: true,
                        remote: {
                            url: '{{ admin_url('master/manufacture/unique') }}',
                            type: 'post',
                            data: {
                                email: function() {
                                    return $('#email').val();
                                }
                            }
                        }
                    },
                    mobile_no: {
                        required: true,
                        digits: true,
                        rangelength: [10, 10]
                    },
                    contact_person: {
                        required: true,
                    },

                },
                messages: {
                    license_number: {
                        required: "License number is Required",
                        remote: "License number is should be unique",

                    },
                    email: {
                        required: "Please enter the email",
                        remote: "Email should be unique",
                    },
                    mobile_no: {
                        required: "Please enter the Mobile No",
                        digits: "Only numbers are allowed",
                        rangelength: "Mobile number must be exactly 10 digits"
                    },
                    contact_person: {
                        required: "Please Enter the Contact Person"
                    },
                    manufacture_name: {
                        required: "Manufacture name is Required",
                        remote: "Manufacture name should be unique",

                    },

                },
                errorElement: 'span',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.form-input').append(error);
                },
                highlight: function(element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                },
                submitHandler: function(form) {
                    form.submit();

                },
                invalidHandler: function(event, validator) {
                    var errors = validator.numberOfInvalids();
                    validator.errorList.forEach(function(error) {
                        // console.log("Field: " + error.element.name + ", Error: " + error
                        //     .message);
                    });
                }
            });
        });
    </script>
@endpush
