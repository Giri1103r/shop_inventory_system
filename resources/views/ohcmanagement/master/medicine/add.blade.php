@extends('admin.layouts.admin')
@section('title', 'Medicine')
@section('pageurl', admin_url('ohc/medicine/list'))


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
                                {{-- <h4 class="card-title">{{ __('master.company_add') }}</h4> --}}
                                
                                <div class="align-back-btc d-flex justify-content-end align-items-center">
                                    <x-button-back href="{{ admin_url('ohc/medicine/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form">
                                    <form method="POST" id="medicineadd"
                                        action="{{ admin_url('ohc/medicine/add/submit') }}">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label require">{{ __('ohc_management.medicine_name') }}</label>
                                                    <input type="text" name ="medicine" id="medicine"
                                                        class="form-control" placeholder=" Enter the medicine Name">
                                                    @error('medicine')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label require">{{ __('ohc_management.pack') }}</label>
                                                    <input type="text" name="pack" id="pack" class="form-control"
                                                        placeholder="Enter the Pack name">
                                                    @error('pack')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label require">{{ __('ohc_management.threshold_limit') }}</label>
                                                    <input type="text" name="threshold_limit" id="threshold_limit"
                                                        class="form-control" placeholder="Enter the threshold limit">
                                                    @error('threshold_limit')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-12">
                                                <div class="form-group form-input">
                                                    <label class="form-label ">{{ __('ohc_management.remarks') }}</label>
                                                    <textarea name="remarks" id="remarks" class="form-control" placeholder="Enter the Remarks"></textarea>
                                                    @error('remarks')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="submit-button" style="text-align: right;">
                                            <x-button-submit class="submit"></x-button-submit>
                                            <x-button-reset class=""></x-button-reset>
                                            <x-button-cancel href="{{ admin_url('ohc/medicine/list') }}"></x-button-cancel>
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
        var fromDatepicker = flatpickr("#expire_date", {
            dateFormat: "d-m-Y",
            minDate: new Date(),

        });
        $(function() {
            // Add custom regex rule
            $.validator.addMethod(
                "regex",
                function(value, element, pattern) {
                    return this.optional(element) || new RegExp(pattern).test(value);
                },
                "Invalid format."
            );

            $('#medicineadd').validate({
                rules: {
                    medicine: {
                        required: true,
                        minlength: 3,
                        maxlength: 30,
                        regex: /^(?!\s*$)[a-zA-Z0-9\s]+$/,
                        remote: {
                            url: "{{ admin_url('ohc/medicine/unique') }}",
                            type: "post",
                            data: {
                                medicine_name: function() {
                                    return $('#medicine').val();
                                }
                            }
                        }
                    },
                    pack: {
                        required: true,
                        minlength: 3,
                        maxlength: 100
                    },
                    hsn: {
                        required: true,
                        minlength: 3,
                        maxlength: 30,
                        regex: /^(?!\s*$)[a-zA-Z0-9\s]+$/,
                        remote: {
                            url: "{{ admin_url('ohc/medicine/hsn-unique') }}",
                            type: "post",
                            data: {
                                hsn: function() {
                                    return $('#hsn').val();
                                }
                            }
                        }
                    },
                    unit_id: {
                        required: true
                    },
                    threshold_limit: {
                        required: true,
                        digits: true
                    },
                    expire_date: {
                        required: true
                    }
                },
                messages: {
                    medicine: {
                        required: "Medicine Name Cannot Be Empty.",
                        minlength: "Medicine name must be at least 3 characters.",
                        maxlength: "Medicine name cannot exceed 30 characters.",
                        regex: "Medicine name contains invalid characters.",
                        remote: "This Medicine Name should be unique."
                    },
                    pack: {
                        required: "Pack details Cannot be empty.",
                        minlength: "Pack details must be at least 3 characters.",
                        maxlength: "Pack details cannot exceed 100 characters."
                    },
                    hsn: {
                        required: "HSN Number cannot be empty.",
                        minlength: "HSN code must be at least 3 characters.",
                        maxlength: "HSN code cannot exceed 30 characters.",
                        regex: "HSN code contains invalid characters.",
                        remote: "This HSN Number already exists."
                    },
                    unit_id: {
                        required: "Please select a unit."
                    },
                    threshold_limit: {
                        required: "Please enter the threshold limit.",
                        digits: "Threshold limit contains only numeric."
                    },
                    expire_date: {
                        required: "Please select the expiry date."
                    }
                },
                errorElement: "span",
                errorPlacement: function(error, element) {
                    error.addClass("invalid-feedback");
                    element.closest(".form-input").append(error); // Ensure `.form-input` exists
                },
                highlight: function(element, errorClass, validClass) {
                    $(element).addClass("is-invalid");
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).removeClass("is-invalid");
                },
                submitHandler: function(form) {
                    form.submit(); // Default form submission
                },
                invalidHandler: function(event, validator) {
                    var errors = validator.numberOfInvalids();
                    console.log("Form has " + errors + " invalid fields."); // Optional debugging
                }
            });
        });
    </script>
@endpush
