@extends('admin.layouts.admin')
@section('title', ' User Role')
@section('pageurl', admin_url('administration/role/list'))


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
                                    <x-button-back href="{{ admin_url('administration/role/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form">
                                    <form method="POST" id="roleadd"
                                        action="{{ admin_url('administration/role/add/submit') }}">
                                        @csrf

                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Role ID</label>
                                                    <input type="text" name ="role_id" class="form-control"
                                                        placeholder="Role ID" readonly value="{{ getsequence('role') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Role Name</label>
                                                    <input type="text" name="role_name" id="role_name"
                                                        class="form-control" placeholder="Role Name">
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="submit-button" style="text-align: right;">

                                            <x-button-submit class="submit"></x-button-submit>
                                            <x-button-reset class="submit"></x-button-reset>
                                            <x-button-cancel
                                                href="{{ admin_url('administration/role/list') }}"></x-button-cancel>
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
        $.validator.addMethod("regex", function(value, element, param) {
            return this.optional(element) || param.test(value);
        }, "Invalid input.");
        $(function() {
            $('#roleadd').validate({
                rules: {
                    role_name: {
                        required: true,
                        minlength: 3,
                        maxlength: 20,
                        regex: /^[A-Za-z]+$/,

                        remote: {
                            url: '{{ admin_url('administration/role/unique') }}',
                            type: 'post',
                            data: {
                                role_name: function() {
                                    return $('#role_name').val();
                                },

                            }
                        }
                    },
                },
                messages: {
                    role_name: {
                        required: "{{ __('Role Name is Required') }}",
                        minlength: "{{ __('common.validate_min_length') }}",
                        maxlength: "Maximum length should not exceed 20 characters.",
                        regex: "Only alphabetic characters are allowed (no spaces or special characters).",
                        remote: "{{ __('Role Name should be unique') }}"
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
