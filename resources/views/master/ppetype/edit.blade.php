@extends('admin.layouts.admin')
@section('title', 'PPE Type Edit')
@section('pageurl', admin_url('ppe_type/list'))
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
                                <h4 class="card-title"></h4>
                                <div class="align-back-btc">
                                    <x-button-back href="{{ admin_url('ppe_type/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form">
                                    <form method="POST" id="PpeTypeForm" enctype="multipart/form-data"
                                        action="{{ admin_url('ppe_type/edit/submit') }}">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $encryptid }}">
                                        <div class="row">

                                            <div class="col-md-4 mb-3">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">PPE Type</label>
                                                    <input type="text"name="ppe_type" id="ppe_type" class="form-control"
                                                        placeholder="Enter the PPE name" value="{{ $ppetype->ppe_type }}">
                                                    <div class="text-danger" id="ppe_type_error"></div>
                                                </div>
                                            </div>

                                        </div>
                                        <hr>
                                        <div class="submit-button float-end">
                                            <x-button-submit class="submit" id="submit"></x-button-submit>
                                            <x-button-reset class="submit"></x-button-reset>
                                            <x-button-cancel href="{{ admin_url('ppe_type/list') }}"></x-button-cancel>
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

@endsection
@push('script')
    <script>
       $(function() {

    $.validator.addMethod('regex', function(value, element, regexpr) {
        return this.optional(element) || regexpr.test(value);
    }, "Invalid format");

    $('#PpeTypeForm').validate({
        rules: {
            ppe_type: {
                required: true,
                minlength: 3,
                maxlength: 100,
                regex: /^[a-zA-Z0-9\s-]*$/,
                remote: {
                    url: '{{ admin_url('ppe_type/unique') }}',
                    type: 'post',
                    data: {
                        ppe_type: function() {
                            return $('#ppe_type').val();
                        },
                        id: function() {
                            return $('#id').val();
                        }
                    }
                }
            }
        },
        messages: {
            ppe_type: {
                required: "{{ __('PPE Type is Required') }}",
                minlength: "{{ __('common.validate_min_length') }}",
                maxlength: "Maximum Characters should not exceed 100",
                remote: "{{ __('PPE Type should be unique') }}",
                regex: "{{ __('PPE Type is alphanumeric') }}"
            }
        },
        errorElement: 'div',
        errorPlacement: function(error, element) {
            var errorDiv = element.siblings('div.text-danger');
            if (errorDiv.length === 0) {
                errorDiv = $('<div class="text-danger"></div>').insertAfter(element);
            }
            errorDiv.html(error);
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
            console.log(errors + " field(s) are invalid");
            validator.errorList.forEach(function(error) {
                console.log("Field: " + error.element.name + ", Error: " + error.message);
            });
        }
    });
});

    </script>
@endpush
