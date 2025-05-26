@extends('admin.layouts.admin')
@section('title', 'Medical Fitness Certificate Edit')
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
                                <div class="align-back-btc">
                                    <x-button-back href="{{ admin_url('ohc/medical-fitness/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="basic-form">
                                    <form method="POST" id="medicalfitnessform" enctype="multipart/form-data"
                                        action="{{ admin_url('ohc/medical-fitness/edit/submit') }}">
                                        @csrf
                                        <input type="hidden" name="id" id="id"
                                            value="{{ encryptId($medicalfitness->id) }}">
                                        <hr>
                                        <div class="row">

                                            <div class="col-md-4 employee-id mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label ">Employee code</label>
                                                    <select name="emp_id" class="form-control " id="emp_id"
                                                        style="width: 100%">
                                                        <option value="">Select the Employee ID</option>
                                                        @if (isset($medicalfitness->emp_id) && isset($medicalfitness->emp_id))
                                                            <option value="{{ $medicalfitness->emp_id }}" selected>
                                                                {{ $medicalfitness->emp_id }}</option>
                                                        @endif
                                                    </select>
                                                    @error('emp_id')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Employee Name</label>
                                                    <input type="text" name="emp_name" id="emp_name"
                                                        class="form-control" value="{{ $medicalfitness->emp_name }}"
                                                        placeholder="Employee Name" readonly>
                                                </div>
                                                @error('emp_name')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Company Name</label>
                                                    <input type="text" name="company_id" id="company_id"
                                                        class="form-control"
                                                        value="{{ getCompanyname($medicalfitness->company_id) }}"
                                                        placeholder="Enter the Company Name" readonly>
                                                    @error('company_id')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Unit Name</label>
                                                    <input type="text" name="unit_id" id="unit_id" class="form-control"
                                                        value="{{ getUnitname($medicalfitness->unit_id) }}"
                                                        placeholder="Enter the Unit Name" readonly>
                                                    @error('company_id')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Department Name</label>
                                                    <input type="text" name="department_id" id="department_id"
                                                        class="form-control"
                                                        value="{{ getDepartment($medicalfitness->department_id) }}"
                                                        placeholder="Enter the Department Name" readonly>
                                                    @error('company_id')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label for="rate" class="form-label require ">Date
                                                    </label>
                                                    <div class="input-group date form-input custom-height">
                                                        <input type="text" name="date" id="date"
                                                            value="{{ displaydateformat($medicalfitness->date) }}"
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
                                                    <label class="form-label require">Medical Fitness Certificate
                                                        Upload</label>
                                                    <input type="file" name="file" id="file"
                                                        class="form-control">
                                                    <small>Allowed file types: pdf, docx , doc</small>
                                                    @if (isset($medicalfitness) && $medicalfitness->file)
                                                        <p>
                                                            <a href="{{ asset('public/' . $medicalfitness->file) }}"
                                                                target="_blank" class="d-block mt-2">
                                                                <i class="fa-solid fa-eye text-danger"></i> View
                                                            </a>
                                                        </p>
                                                        <input type="hidden" name="existing_pre_image"
                                                            id="existing_pre_image" value="{{ $medicalfitness->file }}">
                                                    @else
                                                        <p>No file is uploaded</p>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-md-12 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Chief Complaint</label>
                                                    <textarea name="cheif_complaint" id="cheif_complaint" class="form-control " cols="30" rows="5">{{ $medicalfitness->cheif_complaint }}</textarea>
                                                    @error('cheif_complaint')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-12 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Remarks</label>
                                                    <textarea name="remarks" id="remarks" class="form-control " cols="30" rows="5">{{ $medicalfitness->remarks }}</textarea>
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

        $(document).ready(function() {
            // Initialize select2 for Employee ID
            $('#emp_id').select2({
                ajax: {
                    url: '{{ admin_url('ohc/prescribe-to-patient/fetchemployeename') }}',
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





        });
        $(document).on('change', '#emp_id', function() {
            var empId = $(this).val();

            // Clear all fields initially to avoid retaining old data
            $('#emp_name').val('').prop('readonly', true);
            $('#company_id').val('').prop('readonly', true);
            $('#unit_id').val('').prop('readonly', true);
            $('#department_id').val('').prop('readonly', true);

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

                            if (response.company) {
                                $('#company_id').val(response.company.company_name).prop('readonly',
                                    true);
                            }

                            if (response.unit) {
                                $('#unit_id').val(response.unit.unit_name).prop('readonly', true);
                            }

                            if (response.department) {
                                $('#department_id').val(response.department.department_name).prop(
                                    'readonly', true);
                            }
                        }
                    },
                    error: function() {
                        alert('Error fetching employee details. Please try again.');
                    }
                });
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
                        required: true
                    },
                    emp_name: {
                        required: true
                    },
                    date: {
                        required: true
                    },
                    file: {
                        required: function(element) {
                            return $('#existing_pre_image').length === 0 || $('#existing_pre_image')
                                .val() === "";
                        },
                        extension: "pdf|doc|docx"
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
                    remarks: {
                        required: true,
                        minlength: 3,
                        maxlength: 600
                    },
                    cheif_complaint: {
                        required: true,
                        minlength: 3,
                        maxlength: 600
                    }
                },
                messages: {
                    emp_id: {
                        required: "Please select the Employee Code."
                    },
                    emp_name: {
                        required: "Please select the Employee Name."
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
                    date: {
                        required: "Please select the date."
                    },
                    file: {
                        required: "File is required.",
                        extension: "Please Select a valid file type (pdf, doc, docx)."
                    },
                    remarks: {
                        required: 'Remarks is required',
                        minlength: 'Remarks must be at least 3 characters.',
                        maxlength: 'Remarks cannot exceed 600 characters.'
                    },
                    cheif_complaint: {
                        required: 'Chief Complaint is required',
                        minlength: 'Chief Complaint must be at least 3 characters.',
                        maxlength: 'Chief Complaint cannot exceed 600 characters.'
                    }
                },
                errorElement: 'span',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.form-input').append(error);
                },
                highlight: function(element) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element) {
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
