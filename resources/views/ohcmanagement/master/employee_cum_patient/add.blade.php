@extends('admin.layouts.admin')
@section('title', 'Employee Cum Patient Add')
@section('pageurl', admin_url('ohc/employee-cum-patient/list'))


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
                                <div class="align-back-btc">
                                    <x-button-back href="{{ admin_url('ohc/employee-cum-patient/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form">
                                    <form method="POST" id="EmployeeCumPatientAdd"
                                        action="{{ admin_url('ohc/employee-cum-patient/add/submit') }}">
                                        @csrf

                                        <div class="row">
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label"> Is OutSide Worker</label><br>
                                                    <input type="checkbox" id="is_outside_worker" name="is_outside_worker"
                                                        value="1">
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label">Employee ID</label>
                                                    <select name="emp_id" class="form-control " id="emp_id"
                                                        style="width: 100%">
                                                        <option value="">Select the Employee ID</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Employee Name</label>
                                                    <input type="text" name="emp_name" id="emp_name"
                                                        class="form-control" placeholder="Employee Name" >
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Employee Type</label>
                                                    <select name="employee_type" class="form-control single-select"
                                                        id="employee_type" style="width: 100%">
                                                        <option value="">Select the Employee type</option>
                                                        @foreach ($employeeType as $list)
                                                            <option value="{{ $list->id }}">
                                                                {{ $list->employee_type_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Date of Birth</label>
                                                    <input type="text" name="dateofbirth" id="dateofbirth" class="form-control"
                                                        placeholder="Enter the DOB">
                                                </div>
                                            </div>

                                            <div class="col-md-12">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Address</label>
                                                    <textarea name="address" class="form-control" placeholder="Enter the Address"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="submit-button" style="text-align: right;">
                                            <x-button-submit class="submit"></x-button-submit>
                                            <x-button-reset class=""></x-button-reset>
                                            <x-button-cancel
                                                href="{{ admin_url('ohc/employee-cum-patient/list') }}"></x-button-cancel>
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
        $(document).ready(function() {
            flatpickr("#dateofbirth", {
                dateFormat: "d-m-Y",
                allowInput: true,
                maxDate: function() {
                    var today = new Date();
                    var minDate = new Date();
                    minDate.setFullYear(today.getFullYear() -
                        18);
                    return minDate;
                }(),

            });
        });
        $(document).ready(function() {
    $('#is_outside_worker').change(function() {
        if ($(this).is(':checked')) {
            $('#emp_id').val(null).trigger('change').prop('disabled', true);
            $('#emp_name').prop('readonly', false); 
        } else {
            $('#emp_id').prop('disabled', false);
            $('#emp_name').prop('readonly', true);
        }
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

                        } else {
                            $('#emp_name').val('').prop('readonly', true);
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
            $('#EmployeeCumPatientAdd').validate({
                rules: {
                    emp_name: {
                        required: true,
                        // remote: {
                        //     url: '{{ admin_url('ohc/employee-cum-patient/unique') }}',
                        //     type: 'post',
                        //     data: {
                        //         _token: "{{ csrf_token() }}",
                        //         employee_name: function() {
                        //             return $('#emp_name').val();
                        //         },
                        //     },
                        // },
                    },
                    employee_type: {
                        required: true,
                    },
                    dateofbirth: {
                        required: true,
                    },
                    emp_id: {
                        required: true,
                        // remote: {
                        //     url: '{{ admin_url('ohc/employee-cum-patient/empunique') }}',
                        //     type: 'post',
                        //     data: {
                        //         _token: "{{ csrf_token() }}",
                        //         employee_id: function() {
                        //             return $('#emp_id').val();
                        //         },
                        //     },
                        // },
                    },
                    address: {
                        required: true,
                        maxlength: 300,
                    },
                },
                messages: {
                    emp_name: {
                        required: "Employee name is required.",
                        remote: "Employee Name already Exist",
                    },
                    employee_type: {
                        required: "Please select an employee type.",
                    },
                    dateofbirth: {
                        required: "Date of birth is required.",
                    },
                    emp_id: {
                        required: "Employee ID is required .",
                        remote: "Employee ID already Exist",
                    },
                    address: {
                        required: "Address is required.",
                        maxlength: "Address cannot exceed 300 characters.",
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
                        // Optionally log or handle errors here
                        // console.log("Field: " + error.element.name + ", Error: " + error.message);
                    });
                }
            });
        });
    </script>
@endpush
