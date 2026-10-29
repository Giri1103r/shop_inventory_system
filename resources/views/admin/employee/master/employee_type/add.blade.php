@extends('admin.layouts.admin')
@section('title', 'Employee Type Master Add')
@section('pageurl', admin_url('employee-management/master/employee-type/list'))


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
                                <div class="align-back-btc d-flex justify-content-end align-items-center">
                                    <x-button-back href="{{ admin_url('employee-management/master/employee-type/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form">
                                    <form method="POST" id="employeetypeadd"
                                        action="{{ admin_url('employee-management/master/employee-type/add/submit') }}">
                                        @csrf

                                        <div class="row">
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Employee Type ID</label>
                                                    <input type="text" name ="ety_code" class="form-control"
                                                        placeholder="Employee Type ID"
                                                        value="{{ getsequence('employeetype') }}" readonly>
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Employee Type Name</label>
                                                    <input type="text" name="employee_type_name" id="employee_type_name"
                                                        class="form-control" placeholder="Employee Type Name"
                                                        value="{{ old('employee_type_name') }}">
                                                </div>
                                            </div>

                                        </div>
                                        <hr>
                                        <div class="submit-button" style="text-align: right;">

                                            <x-button-submit class="submit"></x-button-submit>
                                            <x-button-reset class="submit"></x-button-reset>
                                            <x-button-cancel
                                                href="{{ admin_url('employee-management/master/employee-type/list') }}"></x-button-cancel>
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
            $('#employeetypeadd').validate({
                rules: {
                    employee_type_name: {
                        required: true,
                        maxlength: 70,
                        remote: {
                            url: '{{ admin_url('employee-management/master/employee-type/unique') }}',
                            type: 'post',
                            data: {
                                employee_type_name: function() {
                                    return $('#employee_type_name').val();
                                },
                            }
                        }
                    },

                },
                messages: {
                    employee_type_name: {
                        required: "{{ __('Employee Type Name is Required') }}",
                        maxlength: "Maximum Characters should not exceed 70",
                        remote: "{{ __('Employee Type Name should be unique') }}",
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
