@extends('admin.layouts.admin')
@section('title', 'Current New Ext Code Dailing')
@section('pageurl', admin_url('ohc/current-new-ext-code-dialing/list'))


@section('content')
    <div class="clearfix"></div>
    <div class="page-titles">
        <div class="d-flex align-items-center">
            {{-- <h4 class="text-black">Company Add</h4> --}}

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
                                        href="{{ admin_url('ohc/current-new-ext-code-dialing/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form">
                                    <form method="POST" id="currentNewExtCodeDialingAdd"
                                        action="{{ admin_url('ohc/current-new-ext-code-dialing/add/submit') }}">
                                        @csrf


                                        <div id="form-wrapper">
                                            <div class="form-set mb-3">

                                                <div class="d-flex justify-content-end">
                                                    <button class="btn btn-primary add-row me-3" type="button"
                                                        id="add-row" style="width: 84px;">
                                                        Add
                                                    </button>

                                                </div>
                                                <div class="row">

                                                    <div class="col-md-4 mb-3">
                                                        <div class="form-group form-input">
                                                            <label for="unit_id" class="form-label require">Unit</label>
                                                            <select name="unit_id[1]" id="unit_id"
                                                                class="form-control unit-select  single-select"
                                                                style="width: 100%">
                                                                <option value="">Select Unit</option>
                                                                @foreach ($unitList as $unit)
                                                                    <option value="{{ encryptId($unit->id) }}">
                                                                        {{ $unit->unit_name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <!-- Department Selection -->
                                                    <div class="col-md-4 mb-3">
                                                        <div class="form-group form-input">
                                                            <label class="form-label require">Department</label>
                                                            <select name="department_id[1]" id="department_id"
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
                                                            <select name="emp_name[1]" id="emp_name"
                                                                class="form-control emp-select single-select select2"
                                                                style="width:100%">
                                                                <option value="">Select Employee</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <!-- Enter Number -->
                                                    <div class="col-md-4 mb-3">
                                                        <div class="form-group form-input">
                                                            <label class="form-label require">Enter Number</label>
                                                            <input type="text" name="number[1]" id="number" placeholder="Enter the number"
                                                                class="form-control">
                                                        </div>
                                                    </div>

                                                </div>

                                            </div>

                                        </div>

                                        <hr>

                                        <div class="submit-button text-end">
                                            <x-button-submit class="submit"></x-button-submit>
                                            <x-button-reset class="submit"></x-button-reset>
                                            <x-button-cancel
                                                href="{{ admin_url('ohc/current-new-ext-code-dialing/list') }}"></x-button-cancel>
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
        $("#currentNewExtCodeDialingAdd").validate({
            rules: {
                "unit_id[1]": {
                    required: true
                },
                "department_id[1]": {
                    required: true
                },
                "emp_name[1]": {
                    required: true
                },
                "number[1]": {
                    required: true,
                    number: true,
                    remote: {
                        url: '{{ admin_url('ohc/current-new-ext-code-dialing/unique') }}',
                        type: 'post',
                        data: {
                            unit_id: function() {
                                return $('#unit_id').val();
                            },
                            department_id: function() {
                                return $('#department_id').val();
                            },
                            emp_name_id: function() {
                                return $('#emp_name').val();
                            }
                        }
                    }
                }
            },
            messages: {
                "unit_id[1]": "Please select a unit.",
                "department_id[1]": "Please select a department.",
                "emp_name[1]": "Please select an employee.",
                "number[1]": {
                    required: "Please enter a number.",
                    number: "Only numeric values are allowed.",
                    remote: "This number is already in use. Please enter a unique number."
                }
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
                let employeeValues = [];
                let isDuplicate = false;

                $('.emp-select').each(function() {
                    let empVal = $(this).val();
                    let currentSelect = $(this);

                    if (empVal) {
                        if (employeeValues.includes(empVal)) {
                            isDuplicate = true;

                            currentSelect.addClass('is-invalid');
                            currentSelect.closest('.form-input').find('span.error').remove();

                            let error = $(
                                '<span class="error invalid-feedback">This employee is already selected in another row.</span>'
                            );
                            currentSelect.closest('.form-input').append(error);
                        } else {
                            employeeValues.push(empVal);
                            currentSelect.removeClass('is-invalid');
                            currentSelect.closest('.form-input').find('span.error').remove();
                        }
                    }
                });

                if (isDuplicate) {
                    return false;
                }

                form.submit();
            },
            invalidHandler: function(event, validator) {
                var errors = validator.numberOfInvalids();
                console.log(errors + " field(s) are invalid");
                validator.errorList.forEach(function(error) {
                    console.log("Field: " + error.element.name + ", Error: " + error
                        .message);
                });
            }
        });

        let form_set_count = 2;
        const maxFormSets = 200;
        const minFormSets = 1;

        $(document).on('click', ".add-row", function() {
            let currentFormSets = $('#form-wrapper .form-set').length;

            if (currentFormSets >= maxFormSets) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Maximum Forms Reached',
                    text: 'You can only add up to 200 forms.',
                    confirmButtonColor: '#3085d6'
                });
                return;
            }

            let newFormSet = `
                        <div class="form-set mb-3">
                            <div class="d-flex justify-content-end mb-2">
                                <button class="btn btn-primary add-row me-3" type="button" style="width: 84px;">Add</button>
                                <button type="button" class="btn btn-danger remove-row">
                                    <i class="fa-solid fa-trash"></i> Remove
                                </button>
                            </div>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <div class="form-group form-input">
                                        <label class="form-label require">Unit</label>
                                        <select name="unit_id[${form_set_count}]" id="unit_id-${form_set_count}" class="form-control unit-select single-select">
                                            <option value="">Select Unit</option>
                                            @foreach ($unitList as $unit)
                                                <option value="{{ encryptId($unit->id) }}">{{ $unit->unit_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="form-group form-input">
                                        <label class="form-label require">Department</label>
                                        <select name="department_id[${form_set_count}]" id="department_id-${form_set_count}" class="form-control department-select select2">
                                            <option value="">Select Department</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="form-group form-input">
                                        <label class="form-label require">Employee Name</label>
                                        <select name="emp_name[${form_set_count}]" id="emp_name-${form_set_count}" class="form-control emp-select single-select select2">
                                            <option value="">Select Employee</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="form-group form-input">
                                        <label class="form-label require">Enter Number</label>
                                        <input type="text" name="number[${form_set_count}]" id="number-${form_set_count}" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;

            $('#form-wrapper').append(newFormSet);

            $('.unit-select, .department-select, .emp-select').select2({
                width: '100%'
            });

            $(`select[name='unit_id[${form_set_count}]']`).rules('add', {
                required: true,
                messages: {
                    required: 'Unit is required'
                }
            });

            $(`select[name='department_id[${form_set_count}]']`).rules('add', {
                required: true,
                messages: {
                    required: 'Department is required'
                }
            });

            $(`select[name='emp_name[${form_set_count}]']`).rules('add', {
                required: true,
                messages: {
                    required: 'Employee name is required'
                }
            });

            let numberInput = $(`#number-${form_set_count}`);

            numberInput.rules('add', {
                required: true,
                digits: true,
                remote: {
                    url: '{{ admin_url('ohc/current-new-ext-code-dialing/unique') }}',
                    type: 'post',
                    data: {
                        unit_id: function() {
                            return numberInput.closest('.form-set').find('.unit-select').val();
                        },
                        department_id: function() {
                            return numberInput.closest('.form-set').find('.department-select').val();
                        },
                        emp_name_id: function() {
                            return numberInput.closest('.form-set').find('.emp-select').val();
                        }
                    }
                },
                messages: {
                    required: 'Number is required',
                    digits: 'Only numeric values are allowed',
                    remote: "This number is already in use. Please enter a unique number."
                }
            });

            form_set_count++;
            updatePageIndices();
        });

        $(document).on('change', '.unit-select', function() {
            let unitId = $(this).val();
            let row = $(this).closest('.form-set');
            let departmentSelect = row.find('.department-select');
            let empSelect = row.find('.emp-select');

            if (unitId) {
                $.ajax({
                    url: "{{ admin_url('department/ajax-list') }}/" + unitId + "/0",
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        departmentSelect.empty().append('<option value="">Select Department</option>');
                        $.each(data, function(key, value) {
                            departmentSelect.append(
                                `<option value="${value.id}">${value.name}</option>`);
                        });
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error fetching department. Please try again.'
                        });
                    }
                });
            } else {
                departmentSelect.html('<option value="">Select Department</option>');
                empSelect.html('<option value="">Select Employee</option>');
            }
        });

        $(document).on('change', '.unit-select, .department-select', function() {
            let row = $(this).closest('.form-set');
            let unitId = row.find('.unit-select').val();
            let departmentId = row.find('.department-select').val();

            if (unitId && departmentId) {
                $.ajax({
                    url: "{{ admin_url('ohc/first-aider/employeename') }}",
                    type: 'GET',
                    data: {
                        unit_id: unitId,
                        department: departmentId
                    },
                    dataType: 'json',
                    success: function(response) {
                        let empSelect = row.find('.emp-select');
                        empSelect.empty().append('<option value="">Select Employee</option>');
                        $.each(response.employee, function(index, employee) {
                            empSelect.append(
                                `<option value="${employee.id}">${employee.emp_name}</option>`
                            );
                        });
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error fetching employees. Please try again.'
                        });
                    }
                });
            } else {
                row.find('.emp-select').html('<option value="">Select Employee</option>');
            }
        });






        $(document).on('click', '.remove-row', function() {
            let currentFormSets = $('#form-wrapper .form-set').length;

            if (currentFormSets <= minFormSets) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Minimum Code Dailing required',
                    text: 'At least 1 Code Dailing is required.',
                    confirmButtonColor: '#3085d6'
                });
                return;
            }
            $(this).closest('.form-set').remove();
            updatePageIndices();

        });

        function updatePageIndices() {
            $('#form-wrapper .form-set').each(function(index) {

                $(this).find('select[name^="unit_id"]').attr('name', 'unit_id[' + (index + 1) + ']');
                $(this).find('select[name^="department_id"]').attr('name', 'department_id[' + (index +
                    1) + ']');
                $(this).find('select[name^="emp_name"]').attr('name', 'emp_name[' + (index +
                    1) + ']');

                $(this).find('input[name^="number"]').attr('name', 'number[' + (index + 1) + ']');
            });
        }
    </script>
@endpush
