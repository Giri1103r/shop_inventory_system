@extends('admin.layouts.admin')
@section('title', 'Current New Ext Code Dailing')
@section('pageurl', admin_url('ohc/current-new-ext-code-dialing/list'))

@section('content')
    <div class="clearfix"></div>
    <div class="page-titles">
        <div class="d-flex align-items-center">
            {{-- <h4 class="text-black">{{ __('Department Edit') }}</h4> --}}

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
                                <div class="align-back-btc">
                                    <x-button-back
                                        href="{{ admin_url('ohc/current-new-ext-code-dialing/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form">
                                    <form method="POST" id="currentNewExtCodeDailingEdit"
                                        action="{{ admin_url('ohc/current-new-ext-code-dialing/edit/submit') }}">
                                        @csrf
                                        <input type="hidden" name="id" id="id"
                                            value="{{ encryptId($current_new_ext_code->id) }}">

                                        <div class="row">

                                            <div class="col-md-4 mb-3">
                                                <div class="form-group form-input">
                                                    <label for="unit_id" class="form-label require">Unit</label>
                                                    <select name="unit_id" id="unit_id" class="form-control single-select"
                                                        style="width: 100%">
                                                        <option value="">Select Unit</option>
                                                        @foreach ($unitList as $unit)
                                                            <option @if ($current_new_ext_code->unit_id == $unit->id) selected @endif
                                                                value="{{ encryptId($unit->id) }}">
                                                                {{ $unit->unit_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- Department Selection -->
                                            <div class="col-md-4 mb-3">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Department</label>
                                                    <select name="department_id" id="department_id"
                                                        class="form-control department-select select2 single-select"
                                                        style="width:100%">
                                                        <option value="">Select Department Name</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- Employee Name Selection -->
                                            <div class="col-md-4 mb-3">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Employee Name</label>
                                                    <select name="emp_name" id="emp_name"
                                                        class="form-control emp-select single-select select2"
                                                        style="width:100%">
                                                        <option value="">Select Employee</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="col-md-4">
                                                    <div class="form-group form-input">
                                                        <label class="form-label require">Number</label>
                                                        <input type="text" name="number" id = "number"
                                                            class="form-control" placeholder="Department Name"
                                                            value="{{ $current_new_ext_code->number }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="submit-button" style="text-align: right;">
                                            <x-button-submit class="submit"></x-button-submit>
                                            <x-button-reset class="submit"></x-button-reset>
                                            <x-button-cancel href="{{ admin_url('department/list') }}"></x-button-cancel>
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

        $(document).ready(function () {
                $("#currentNewExtCodeDailingEdit").validate({
                    rules: {
                        unit_id: {
                            required: true
                        },
                        department_id: {
                            required: true
                        },
                        emp_name: {
                            required: true
                        },
                        number: {  // Updated to match the input field name
                            required: true,
                            remote: {
                                url: '{{ admin_url('ohc/current-new-ext-code-dialing/unique') }}',
                                type: "POST",
                                data: {
                                    unit_id: function () {
                                        return $('#unit_id').val();
                                    },
                                    department_id: function () {
                                        return $('#department_id').val();
                                    },
                                    emp_name_id: function () {
                                        return $('#emp_name').val();
                                    },
                                    number: function () {
                                        return $('#number').val();
                                    },
                                    id: function () {
                                        return $('#id').val();
                                    }
                                }
                            }
                        }
                    },
                    messages: {
                        unit_id: {
                            required: "Please select a unit."
                        },
                        department_id: {
                            required: "Please select a department."
                        },
                        emp_name: {
                            required: "Please select an employee."
                        },
                        number: {  
                            required: "Please enter a number.",
                            remote: "This number is already in use. Please enter a unique number."
                        }
                    },
                    errorElement: 'span',
                    errorPlacement: function (error, element) {
                        error.addClass('invalid-feedback');
                        element.closest('.form-input').append(error);
                    },
                    highlight: function (element, errorClass, validClass) {
                        $(element).addClass('is-invalid');
                    },
                    unhighlight: function (element, errorClass, validClass) {
                        $(element).removeClass('is-invalid');
                    },
                    submitHandler: function (form) {
                        console.log('Form submitted successfully.');
                        form.submit();
                    },
                    invalidHandler: function (event, validator) {
                        var errors = validator.numberOfInvalids();
                        console.log(errors + " field(s) are invalid");
                        validator.errorList.forEach(function (error) {
                            console.log("Field: " + error.element.name + ", Error: " + error.message);
                        });
                    }
                });
            });


        $(document).ready(function() {

            var initialUnitId = $('#unit_id').val();
            var preselectedDepartmentId = "{{ encryptId($current_new_ext_code->department_id) ?? '0' }}";
            var preselectedEmployeeId = "{{ encryptId($current_new_ext_code->emp_name_id) ?? '0' }}";

            if (initialUnitId) {
                fetchDepartment(initialUnitId, preselectedDepartmentId, function() {
                    var department_id = preselectedDepartmentId;
                    fetchEmployee(department_id, preselectedEmployeeId);

                });
            }

            $('#unit_id').on('change', function() {
                var unit_id = $(this).val();
                fetchDepartment(unit_id, preselectedDepartmentId, function() {
                    $('#department_id').trigger('change');
                });
            });

            $('#department_id').on('change', function() {
                var department_id = $(this).val();
                fetchEmployee(department_id, preselectedEmployeeId, function() {
                    $('#type_id').trigger('change');
                });
            });

            function fetchDepartment(unit_id, preselectedDepartmentId, callback) {
                if (unit_id) {
                    $.ajax({
                        url: "{{ admin_url('department/ajax-list/') }}" + unit_id + '/' +
                            preselectedDepartmentId,
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            $('#department_id').empty().append(
                                '<option value="">Select Department</option>');
                            $.each(data, function(key, value) {
                                var selected = (value.id == preselectedDepartmentId) ?
                                    'selected' : '';
                                $('#department_id').append('<option value="' + value.id + '" ' +
                                    selected + '>' + value.name + '</option>');
                            });
                            if (callback) callback();
                        }
                    });
                } else {
                    $('#department_id').empty().append('<option value="">Select Department</option>');
                }
            }

            function fetchEmployee(department_id, preselectedEmployeeId, callback) {
                var unit_id = $('#unit_id').val();

                if (unit_id && department_id) {
                    $.ajax({
                        url: "{{ admin_url('ohc/first-aider/employeename') }}",
                        type: 'GET',
                        data: {
                            unit_id: unit_id,
                            department: department_id
                        },
                        dataType: 'json',

                        success: function(response) {
                            $('#emp_name').empty().append('<option value="">Select Employee</option>');
                            $.each(response.employee, function(index, employee) {
                                var selected = (employee.id == preselectedEmployeeId) ?
                                    'selected' : '';
                                $('#emp_name').append('<option value="' + employee.id + '" ' +
                                    selected + '>' + employee.emp_name + '</option>');
                            });
                            if (callback) callback();
                        }
                    });
                } else {
                    $('#emp_name').empty().append('<option value="">Select Employee</option>');
                }
            }



        });
    </script>
@endpush
