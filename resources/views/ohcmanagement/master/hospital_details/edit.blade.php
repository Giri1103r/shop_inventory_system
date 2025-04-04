@extends('admin.layouts.admin')
@section('title', 'Hospital Details Edit')
@section('pageurl', admin_url('ohc/hospital-details/list'))


@section('content')
    <div class="clearfix"></div>
    <div class="page-titles">
        <div class="d-flex align-items-center">


        </div>

    </div>

    <div class="content-body  default-height">
        <div class="container-fluid main-content">
            <!-- row -->
            <div class="row">

                <div class="col-12">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">

                                <div class="align-back-btc">
                                    <x-button-back href="{{ admin_url('ohc/hospital-details/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form">
                                    <form method="POST" id="HospitalDetails"
                                        action="{{ admin_url('ohc/hospital-details/edit/submit') }}">
                                        @csrf
                                        <input type="hidden" name="id" id="id"
                                            value="{{ encryptId($hospitalDetails->id) }}">

                                        <div class="row">
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Hospital Name</label>
                                                    <input type="text" name="hospital_name" id="hospital_name"
                                                        value="{{ $hospitalDetails->hospital_name }}" class="form-control"
                                                        placeholder="Hospital Name">
                                                    @error('hospital_name')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Mobile Number </label>
                                                    <input type="text" name="mobile_no" id="mobile_no"
                                                        class="form-control"value="{{ $hospitalDetails->mobile_no }}"
                                                        placeholder="Enter the Mobile Number">
                                                    @error('mobile_no')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Telephone Number </label>
                                                    <input type="text" name="tel_no" id="tel_no" class="form-control"
                                                        value="{{ $hospitalDetails->tel_no }}"
                                                        placeholder="Enter the Mobile Number">
                                                    @error('tel_no')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Address</label>
                                                    <textarea name="address" class="form-control" placeholder="Enter the Address">{{ $hospitalDetails->address }}</textarea>
                                                    @error('address')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="submit-button" style="text-align: right;">
                                            <x-button-submit class="submit"></x-button-submit>
                                            <x-button-reset class=""></x-button-reset>
                                            <x-button-cancel
                                                href="{{ admin_url('ohc/hospital-details/list') }}"></x-button-cancel>
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
            // Custom method for regex validation
            $.validator.addMethod("regex", function(value, element, regexp) {
                var re = new RegExp(regexp);
                return this.optional(element) || re.test(value);
            }, "Enter a valid telephone number with 7-15 digits.");

            $('#HospitalDetails').validate({
                rules: {
                    hospital_name: {
                        required: true,
                        remote: {
                            url: '{{ admin_url('ohc/hospital-details/unique') }}',
                            type: 'post',
                            data: {
                                _token: "{{ csrf_token() }}",
                                hospital_name: function() {
                                    return $('#hospital_name').val();
                                },

                                id: function() {
                                    return $('#id').val();
                                },
                            },
                        },
                    },
                    mobile_no: {
                        required: true,
                        digits: true,
                        minlength: 10,
                        maxlength: 10,
                        remote: {
                            url: '{{ admin_url('ohc/hospital-details/unique') }}',
                            type: 'post',
                            data: {
                                _token: "{{ csrf_token() }}",

                                mobile_no: function() {
                                    return $('#mobile_no').val();
                                },
                                id: function() {
                                    return $('#id').val();
                                },
                            },
                        },
                    },
                    tel_no: {
                        required: true,
                        regex: /^(\+?[0-9]{1,4})?[0-9]{7,15}$/,
                    },
                    address: {
                        required: true,
                        maxlength: 300,
                    },
                },
                messages: {
                    hospital_name: {
                        required: "Hospital name is required.",
                        remote: "Hospital Name already exists.",
                    },
                    tel_no: {
                        required: "Telephone Number is required.",
                        regex: "Enter a valid telephone number with 7-15 digits.",
                    },
                    mobile_no: {
                        required: "Mobile Number is required.",
                        digits: "Mobile Number should be numeric.",
                        maxlength: "Maximum 10 digits are required.",
                        minlength: "Minimum 10 digits are required.",
                        remote: "Mobile Number Should Be Unique.",
                    },
                    address: {
                        required: "Address is required.",
                        maxlength: "Address cannot exceed 300 characters.",
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
                        // Optionally log or handle errors here
                        // console.log("Field: " + error.element.name + ", Error: " + error.message);
                    });
                }
            });
        });
    </script>
@endpush
