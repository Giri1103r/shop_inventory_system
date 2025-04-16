@extends('admin.layouts.admin')
@section('title', 'Certified First Aider Add')
@section('pageurl', admin_url('ohc/certified-first-aider/list'))


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
                                    <x-button-back href="{{ admin_url('ohc/certified-first-aider/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form">
                                    <form method="POST" id="CertifiedFirstAiderAdd"
                                        action="{{ admin_url('ohc/certified-first-aider/add/submit') }}">
                                        @csrf

                                        <div class="row">
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Unit</label>
                                                    <select name="unit_id" id="unit_id" class="form-control single-select"
                                                        style="width: 100%">
                                                        <option value="">Select the unit</option>
                                                        @foreach ($unit as $list)
                                                            <option value="{{ encryptId($list->id) }}">
                                                                {{ $list->unit_name }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('unit_id')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Department</label>
                                                    <select name="department_id" id="department_id"
                                                        class=" form-control single-select" style="width: 100%">
                                                        <option value="">Select Department </option>

                                                    </select>
                                                    @error('department_id')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Employee Code</label>
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
                                                    <label class="form-label require">Certified First Aider Name</label>
                                                    <input type="text" name="certifier_name" id="certifier_name"
                                                        class="form-control"
                                                        placeholder="Enter the Certified First Aider Name" readonly>
                                                    @error('certifier_name')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Mobile Number</label>
                                                    <input type="text" name="mobile_no" id="mobile_no"
                                                        class="form-control" placeholder="Mobile Number">
                                                    @error('mobile_no')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-12">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Address</label>
                                                    <textarea name="address" class="form-control" placeholder="Enter the Address"></textarea>
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
                                                href="{{ admin_url('ohc/certified-first-aider/list') }}"></x-button-cancel>
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
        $(document).on('change', '#unit_id', function() {
            var unitId = $(this).val();
            if (unitId) {
                $.ajax({
                    url: "{{ admin_url('department/ajax-list') }}/" + unitId + "/0",
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        $('#department_id').empty().append(
                            '<option value="">Select Department</option>');
                        $.each(data, function(key, value) {
                            $('#department_id').append('<option value="' + value
                                .id + '">' + value.name + '</option>');
                        });
                        $('#department_id').trigger('change.');
                    },
                    error: function(xhr) {
                        alert('Error fetching department. Please try again.');
                    }
                });
            } else {
                $('#department_id').empty().append('<option value="">Select Department</option>');
                $('#department_id').trigger('change.');
            }
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
                            $('#certifier_name').val(response.employee.emp_name).prop('readonly', true);
                            $('#mobile_no').val(response.employee.mobile_no).prop('readonly', true);
                        } else {
                            $('#certifier_name').val('').prop('readonly', true);
                            $('#mobile_no').val('').prop('readonly', true);

                        }
                    },
                    error: function(xhr) {
                        alert('Error fetching employee name. Please try again.');
                    }
                });
            } else {
                $('#certifier_name').val('').prop('readonly', true);
                $('#mobile_no').val('').prop('readonly', true);

            }
        });


        $(function() {

            $('#CertifiedFirstAiderAdd').validate({
                rules: {
                    unit_id: {
                        required: true,
                    },
                    department_id: {
                        required: true,
                    },
                    emp_id: {
                        required: true,
                        remote: {
                            url: '{{ admin_url('ohc/certified-first-aider/unique') }}',
                            type: 'post',
                            data: {
                                _token: "{{ csrf_token() }}",
                                employee_id: function() {
                                    return $('#emp_id').val();
                                },
                            },
                        },
                    },
                    certifier_name: {
                        required: true,
                    },
                    mobile_no: {
                        required: true,
                        minlength: 10,
                        maxlength: 10,
                        digits: true,
                        remote: {
                            url: '{{ admin_url('ohc/certified-first-aider/unique') }}',
                            type: 'post',
                            data: {
                                _token: "{{ csrf_token() }}",
                                mobile_no: function() {
                                    return $('#mobile_no').val();
                                },
                            },
                        },
                    },
                    address: {
                        required: true,
                        maxlength: 300,
                    },
                },
                messages: {
                    unit_id: {
                        required: "Unit ID is required.",
                    },
                    department_id: {
                        required: "Department ID is required.",
                    },
                    emp_id: {
                        required: "Employee ID is required.",
                        remote: "Employee Code already exists.",
                    },
                    certifier_name: {
                        required: "Certifier name is required.",
                    },
                    mobile_no: {
                        required: "Mobile number is required.",
                        minlength: "Mobile number must be exactly 10 digits.",
                        maxlength: "Mobile number must be exactly 10 digits.",
                        digits: "Please enter only digits for the mobile number.",
                        remote: "Mobile number must be unique.",
                    },
                    address: {
                        required: "Address is required.",
                        maxlength: "Address cannot exceed 300 characters.",
                    },
                },
                errorElement: 'span',
                errorPlacement: function(error, element) {
                    // Add the 'invalid-feedback' class to the error element
                    error.addClass('invalid-feedback');
                    // Append the error message to the closest '.form-input' container
                    element.closest('.form-input').append(error);
                },
                highlight: function(element) {
                    // Add the 'is-invalid' class to the invalid input
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element) {
                    // Remove the 'is-invalid' class when the input becomes valid
                    $(element).removeClass('is-invalid');
                },
                submitHandler: function(form) {
                    // Submit the form when all validations pass
                    form.submit();
                },
                invalidHandler: function(event, validator) {
                    // Handle invalid form submissions
                    var errors = validator.numberOfInvalids();
                    if (errors) {
                        console.log(`There are ${errors} validation errors.`);
                        validator.errorList.forEach(function(error) {
                            console.log(
                                `Field: ${error.element.name}, Error: ${error.message}`);
                        });
                    }
                },
            });
        });
    </script>
@endpush
