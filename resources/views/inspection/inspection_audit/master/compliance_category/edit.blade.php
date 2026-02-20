@extends('admin.layouts.admin')
@section('title', 'Compliance Category')
@section('pageurl', admin_url('audit/master/compliance_category/list'))


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
                                    <x-button-back
                                        href="{{ admin_url('audit/master/compliance_category/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form">
                                    <form method="POST" id="compliance_categoryEdit"
                                        action="{{ admin_url('audit/master/compliance_category/edit/submit') }}"
                                        autocomplete="off" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" value="{{ encryptId($compliance_category->id) }}" name="id"
                                            id="id" />

                                        <div class="row">
                                            <div class="form-input col-md-4 mb-2">
                                                <label class="form-label require">{{__('inspection.compliance_category')}}</label>
                                                <input type="text" name="compliance_category" id = "compliance_category"
                                                    class="form-control"
                                                    value="{{ $compliance_category->compliance_category }}">
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="submit-button" style="text-align: right;">
                                            <x-button-submit class="submit"></x-button-submit>
                                            <x-button-reset class="submit"></x-button-reset>
                                            <x-button-cancel
                                                href="{{ admin_url('audit/master/compliance_category/list') }}"></x-button-cancel>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
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

            $.validator.addMethod("noSpaces", function(value, element) {
                return this.optional(element) || value.trim().length > 0;
            }, "This field cannot contain only spaces");

            $.validator.addMethod("filesize", function(value, element, param) {
                if (this.optional(element)) {
                    return true;
                }
                var fileSize = element.files[0].size / 1024;
                return fileSize >= param[0] && fileSize <= param[
                    1];
            }, "File size must be between 50KB and 5MB");

            $('#compliance_categoryEdit').validate({
                rules: {
                    compliance_category : {
                        required: true,
                        minlength: 3,
                        maxlength: 100,
                        noSpaces: true,
                        remote: {
                            url: '{{ admin_url('audit/master/compliance_category/unique') }}',
                            type: 'post',
                            data: {
                                compliance_category: function() {
                                    return $('#compliance_category').val();
                                },
                                id: function() {
                                    return $('#id').val();
                                }
                            }
                        }
                    }
                },
                messages: {
                    compliance_category: {
                        required: "{{ __('Compliance Category Name is Required') }}",
                        minlength: "Minimum Characters should be 3",
                        maxlength: "Maximum Characters should not exceed 100",
                        remote: "Compliance Category Name should be unique",
                    }
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
                },
                submitHandler: function(form) {
                    form.submit();
                },
                invalidHandler: function(event, validator) {
                    var errors = validator.numberOfInvalids();
                    validator.errorList.forEach(function(error) {});
                }
            });
        });
    </script>
@endpush
