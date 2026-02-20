@extends('admin.layouts.admin')
@section('title', 'Medical Fitness Certificate')
@section('pageurl', admin_url('ohc/medical-fitness/list'))
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
                                <div class="align-back-btc d-flex justify-content-end align-items-center">
                                    <x-button-back href="{{ admin_url('ohc/medical-fitness/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="basic-form">
                                    <form method="POST" id="medicalfitnessform" enctype="multipart/form-data"
                                        action="{{ admin_url('ohc/medical-fitness/add/submit') }}">
                                        @csrf

                                        <hr>
                                        <div class="row">

                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label">{{ __('common.employee_or_worker_code') }}</label>
                                                    <select name="emp_id" class="form-control " id="emp_id"
                                                        style="width: 100%">
                                                        <option value="">Select the Employee ID</option>
                                                    </select>
                                                    @error('emp_id')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label require">{{ __('common.employee_or_worker_name') }}</label>
                                                    <input type="text" name="emp_name" id="emp_name"
                                                        class="form-control" placeholder="Employee Name" readonly>
                                                    @error('emp_name')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">{{ __('common.company') }}</label>
                                                    <input type="text" name="company_id" id="company_id"
                                                        class="form-control" placeholder="Enter the Company name" readonly>
                                                    @error('company_id')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">{{ __('common.unit') }}</label>
                                                    <input type="text" name="unit_id" id="unit_id" class="form-control"
                                                        placeholder="Enter the Unit name" readonly>
                                                    @error('company_id')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">{{ __('common.department') }}</label>
                                                    <input type="text" name="department_id" id="department_id"
                                                        class="form-control" placeholder="Enter the Department" readonly>
                                                    @error('company_id')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label for="rate"
                                                        class="form-label require ">{{ __('common.date') }}
                                                    </label>
                                                    <div class="input-group date form-input custom-height">
                                                        <input type="text" name="date" id="date"
                                                            class="form-control"autocomplete="off">
                                                        <div class="input-group-addon input-group-text">
                                                            <span class="fa fa-calendar"></span>
                                                        </div>
                                                    </div>

                                                    @error('date')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label require">{{ __('ohc_management.medical_fitness_certificate_upload') }}</label>
                                                    <input type="file" name="file" id="file"
                                                        class="form-control">
                                                    <small>Allowed file types: PDF, DOCX, DOC</small>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label require">{{ __('ohc_management.chief_complaint') }}</label>
                                                    <textarea name="cheif_complaint" id="cheif_complaint" class="form-control " cols="30" rows="5"></textarea>
                                                    @error('cheif_complaint')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-12 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label require">{{ __('ohc_management.remarks') }}</label>
                                                    <textarea name="remarks" id="remarks" class="form-control " cols="30" rows="5"></textarea>
                                                    @error('remarks')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <hr>
                                        <div class="submit-button float-end">
                                            <x-button-submit class="submit" id="submit"></x-button-submit>
                                            <x-button-reset class="submit"></x-button-reset>
                                            <x-button-cancel
                                                href="{{ admin_url('ohc/medical-fitness/list') }}"></x-button-cancel>
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
    <script>
        $(document).ready(function() {
            var fromDatepicker = flatpickr("#date", {
                dateFormat: "d-m-Y",
                minDate: new Date(),

            });
        });

        $('#emp_id').select2({
            ajax: {
                url: '{{ admin_url('ohc/employee-cum-patient/employeeid') }}',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        search: params.term
                    };
                },
                processResults: function(data) {
                    return {
                        results: $.map(data, function(item) {
                            return {
                                id: item.id,
                                text: item.text
                            };
                        })
                    };
                }
            },
            minimumInputLength: 1,
            dropdownCssClass: 'form-control',
            selectionCssClass: 'form-control'
        });
        $(document).on('change', '#emp_id', function() {
            var empId = $(this).val();
            if (empId) {
                $.ajax({
                    url: "{{ admin_url('ohc/employee-cum-patient/employeename') }}",
                    type: 'GET',
                    data: {
                        empId: empId
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.employee) {
                            $('#emp_name').val(response.employee.emp_name).prop('readonly', true);
                            $('#company_id').val(response.company.company_name).prop('readonly', true);
                            $('#unit_id').val(response.unit.unit_name).prop('readonly', true);
                            $('#department_id').val(response.department.department_name).prop(
                                'readonly', true);
                        } else {
                            $('#emp_name').val('').prop('readonly', true);
                            $('#company_id').val('').prop('readonly', true);
                            $('#unit_id').val('').prop('readonly', true);
                            $('#department_id').val('').prop('readonly', true);
                        }
                    },
                    error: function(xhr) {
                        alert('Error fetching employee name. Please try again.');
                    }
                });
            } else {
                $('#emp_name').val('').prop('readonly', true);
            }
        });
        $(function() {

            $.validator.addMethod(
                "regex",
                function(value, element, regex) {
                    return this.optional(element) || regex.test(value);
                },
                "Invalid format."
            );

            $('#medicalfitnessform').validate({
                rules: {
                    emp_id: {
                        required: true,
                    },
                    emp_name: {
                        required: true,
                    },
                    company_id: {
                        required: true,
                    },
                    unit_id: {
                        required: true,
                    },
                    department_id: {
                        required: true,
                    },
                    date: {
                        required: true,
                    },
                    file: {
                        required: true,
                        extension: "pdf|doc|docx"
                    },
                    remarks: {
                        required: true,
                        minlength: 3,
                        maxlength: 600,
                    },
                    cheif_complaint: {
                        required: true,
                        minlength: 3,
                        maxlength: 600,
                    },
                },
                messages: {
                    emp_id: {
                        required: "Please select the Employee Code.",
                    },
                    emp_name: {
                        required: "Please select the Employee Name.",
                    },
                    date: {
                        required: "Please select the date.",
                    },
                    company_id: {
                        required: "Please Enter the Company name.",
                    },
                    unit_id: {
                        required: "Please Enter the Unit name.",
                    },
                    department_id: {
                        required: "Please Enter the department name.",
                    },
                    file: {
                        required: "File is required.",
                        extension: "Please Select the valid mime Type."
                    },
                    remarks: {
                        required: "Remarks are required.",
                        minlength: "Remarks should have at least 3 characters.",
                        maxlength: "Remarks should not exceed 600 characters.",
                    },
                    cheif_complaint: {
                        required: "Cheif Complaint are required.",
                        minlength: "Cheif Complaint should have at least 3 characters.",
                        maxlength: "Cheif Complaint should not exceed 600 characters.",
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
                    console.log("Form has " + errors + " invalid fields.");
                },
            });



        });
    </script>
@endpush
