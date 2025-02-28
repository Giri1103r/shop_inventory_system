@extends('admin.layouts.admin')
@section('title', 'Vendor Edit')
@section('pageurl', admin_url('ohc/vendor/list'))


@section('content')
    <div class="clearfix"></div>
    <div class="page-titles">
        <div class="d-flex align-items-center">
            {{-- <h4 class="text-black">{{ __('ohc/vendor Edit') }}</h4> --}}

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
                                {{-- <h4 class="card-title">{{ __('master.ohc/vendor_add') }}</h4> --}}
                                <div class="align-back-btc">
                                    <x-button-back href="{{ admin_url('ohc/vendor/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form">
                                    <form method="POST" id="vendoredit" action="{{ admin_url('ohc/vendor/edit/submit') }}">
                                        @csrf
                                        <input type="hidden" name="id" id="id"
                                            value="{{ encryptId($vendor->id) }}">

                                        <div class="row">
                                            <div class="col-md-4 mb-2 ">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Vendor Name</label>
                                                    <input type="text" name ="vendor_name" id="vendor_name"
                                                        class="form-control" placeholder="Enter the vendor name"
                                                        value="{{ $vendor->vendor_name }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">License Number</label>
                                                    <input type="text" name="license_no" id="license_no"
                                                        class="form-control" placeholder="Enter the License_no"
                                                        value="{{ $vendor->license_no }}">
                                                </div>
                                            </div>


                                            <div class="col-md-12">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Address</label>
                                                    <textarea name="address" class="form-control" placeholder="Enter the Address">{{ $vendor->address }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="submit-button" style="text-align: right;">
                                            <x-button-submit class="submit"></x-button-submit>
                                            <x-button-reset class=""></x-button-reset>
                                            <x-button-cancel href="{{ admin_url('ohc/vendor/list') }}"></x-button-cancel>
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
            $.validator.addMethod(
                "regex",
                function(value, element, pattern) {
                    return this.optional(element) || new RegExp(pattern).test(value);
                },
                "Invalid format."
            );
            $('#vendoredit').validate({
                rules: {
                    vendor_name: {
                        required: true,
                        minlength: 3,
                        maxlength: 30,
                        regex: /^(?!\s*$)[a-zA-Z0-9\s]+$/,
                        remote: {
                            url: '{{ admin_url('ohc/vendor/unique') }}',
                            type: 'post',
                            data: {
                                vendor_name: function() {
                                    return $('#vendor_name').val();
                                },
                                id: function() {
                                    return $('#id').val();
                                }
                            }
                        }
                    },
                    license_no: {
                        required: true,
                        minlength: 3,
                        maxlength: 30,
                        regex: /^(?!\s*$)[a-zA-Z0-9\s-/]+$/,
                        remote: {
                            url: '{{ admin_url('ohc/vendor/unique') }}',
                            type: 'post',
                            data: {
                                license_no: function() {
                                    return $('#license_no').val();
                                },
                                id: function() {
                                    return $('#id').val();
                                }
                            }
                        }

                    },
                    address: {
                        required: true,
                        maxlength: 600,
                    },
                },
                messages: {
                    vendor_name: {
                        required: "{{ __('Vendor Name is Required') }}",
                        minlength: " Minimum character should not less than 3 ",
                        maxlength: "Maximum Characters should not exceed 30 ",
                        regex: "Vendor name contains invalid characters.",
                        remote: "{{ __('Vendor Name should be unique') }}"
                    },
                    license_no: {
                        required: "{{ __('license No is Required') }}",
                        minlength: "Minimum character should not less than 3 ",
                        regex: "license No contains invalid characters.",
                        remote: "{{ __('license No should be unique') }}"
                        maxlength: "Maximum Characters should not exceed 30",

                    },
                    address: {
                        required: "{{ __(' Address is Required') }}",
                        maxlength: "Maximum Characters should not exceed 600",
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

                    });
                }
            });
        });
    </script>
@endpush
