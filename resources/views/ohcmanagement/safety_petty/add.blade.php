@extends('admin.layouts.admin')
@section('title', 'ohc/Safety Petty Logbook Add')
@section('pageurl', admin_url('ohc/safety-petty-logbook/list'))

@section('content')

    <style>
        .card-header-inner {
            padding: 11px;
        }
    </style>

    <div class="clearfix"></div>
    <div class="page-titles">
        <div class="d-flex align-items-center">
           
        </div>
    </div>

    <div class="content-body  default-height">
        <div class="container-fluid main-content">
            <div class="row">
                <div class="col-12">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title"></h4>
                                <div class="align-back-btc">
                                    <x-button-back href="{{ admin_url('ohc/safety-petty-logbook/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form">
                                    <form method="POST" id="sftyAdd" action="{{ admin_url('ohc/safety-petty-logbook/add/submit') }}">
                                        @csrf

                                        <div class="row">
                                            <div class="card-header-inner">
                                                <h4 class="text-white">Safety Petty Logbook Details</h4>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Document Number</label>
                                                    <input type="text" name ="document_number" class="form-control"
                                                        placeholder="Document Number" value="">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Issue Date</label>
                                                    <input type="text" name ="issue_date" id="issue_date"
                                                        class="form-control" placeholder="Issue Date" value="">
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Revision Date</label>
                                                    <input type="text" name ="revision_date" class="form-control"
                                                        placeholder="Revision Date" value="{{ todaydate('todaydate') }}"
                                                        readonly>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mt-4">
                                            <div class="row mt-2">
                                                <div
                                                    class="d-flex justify-content-end align-items-center me-2 mb-3 button-container">
                                                </div>
                                            </div>

                                            <div id="form-wrapper">
                                                <div class="form-set mb-3">
                                                    <div class="card-header-inner">
                                                        <h4 class="text-white">Safety Petty Logbook CheckList</h4>
                                                    </div>
                                                    <div class="d-flex justify-content-end">
                                                        <button class="btn btn-primary add-row me-3" type="button"
                                                            id="add-row" style="width: 84px;">
                                                            Add
                                                        </button>
                                                        <button type="button" class="btn btn-danger remove-row">
                                                            <i class="fa-solid fa-trash"></i> Remove
                                                        </button>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="form-group form-input">
                                                                <label class="form-label require">Serial Number</label>
                                                                <input type="text" name="serial_number[1]"
                                                                    class="form-control" placeholder="Serial Number"
                                                                    value="SPLB-00001" readonly>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-4">
                                                            <div class="form-group form-input">
                                                                <label class="form-label require">Employee Name </label>
                                                                <select name="employee_name[1]"
                                                                    class="form-control single-select" style="width: 100%">
                                                                <option value="">Select Employee Name</option>
                                                            </select>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-4">
                                                            <div class="form-group form-input">
                                                                <label class="form-label require">Employee Code</label>
                                                                <input type="text" name="employee_code[1]"
                                                                    class="form-control" placeholder="Employee Code"
                                                                    value="">
                                                            </div>
                                                        </div>

                                                        <div class="col-md-4 mt-2">
                                                            <div class="form-group form-input">
                                                                <label class="form-label require">Department</label>
                                                                <select name="department[1]"
                                                                    class="form-control single-select" style="width: 100%">
                                                                    <option value="">Select Department</option>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-4 mt-2">
                                                            <div class="form-group form-input">
                                                                <label class="form-label require">Unit</label>
                                                                <select name="unit[1]"
                                                                    class="form-control single-select" style="width: 100%">
                                                                    <option value="">Select Unit</option>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-4">
                                                            <div class="form-group form-input">
                                                                <label class="form-label require">Date</label>
                                                                <input type="text" name ="date[1]" id="date"
                                                                    class="form-control" placeholder="Date" value="">
                                                            </div>
                                                        </div>

                                                        <div class="col-md-4">
                                                            <div class="form-group form-input">
                                                                <label class="form-label require">Amount</label>
                                                                <input type="text" name ="amount[1]" id="amount"
                                                                    class="form-control" placeholder="Amount" value="">
                                                            </div>
                                                        </div>

                                                        <div class="col-md-4 mt-2">
                                                            <div class="form-group form-input">
                                                                <label class="form-label require">Amount Given By</label>
                                                                <input type="text" name ="amount_given_by[1]"
                                                                class="form-control" placeholder="Amount Given By" value="">
                                                            </div>
                                                        </div>

                                                        <div class="col-md-4 mt-2">
                                                            <div class="form-group form-input">
                                                                <label class="form-label require">Amount Received By</label>
                                                                <input type="text" name ="amount_received_by[1]"
                                                                class="form-control" placeholder="Amount Received By" value="">
                                                            </div>
                                                        </div>

                                                        <div class="col-md-12 mt-2">
                                                            <div class="form-group form-input">
                                                                <label class="form-label require">Description</label>
                                                                <textarea name="description[1]" class="form-control" placeholder="Description" rows="3"></textarea>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-12 mt-2">
                                                            <div class="form-group form-input">
                                                                <label class="form-label require">Remark</label>
                                                                <textarea name="remark[1]" class="form-control" placeholder="Remark" rows="3"></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <hr>
                                        <div class="submit-button" style="text-align: right;">

                                            <x-button-submit class="submit"></x-button-submit>
                                            <x-button-reset class="submit"></x-button-reset>
                                            <x-button-cancel href="{{ admin_url('ohc/safety-petty-logbook/list') }}"></x-button-cancel>
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

        var fromDatepicker = flatpickr("#issue_date", {
            dateFormat: "d-m-Y",
            minDate: new Date(),
        });

        $.validator.addMethod("noSpaces", function(value, element) {
            return this.optional(element) || value.trim().length > 0;
        }, "This field cannot contain only spaces");

        $('#sftyAdd').validate({
            rules: {
                document_number: {
                    required: true,
                    noSpaces: true,
                },
                issue_date: {
                    required: true,
                },
                revision_date: {
                    required: true,
                },
                'employee_name[1]': {
                    required: true,
                },
                'employee_code[1]': {
                    required: true,
                    noSpaces: true,
                    uniqueEmployeeCode: true,
                },
                'department[1]': {
                    required: true,
                },
                'unit[1]': {
                    required: true,
                },
                'date[1]': {
                    required: true,
                },
                'amount[1]': {
                    required: true,
                    noSpaces: true,
                },
                'description[1]': {
                    required: true,
                    noSpaces: true,
                },
                'amount_given_by[1]': {
                    required: true,
                    noSpaces: true,
                },
                'amount_received_by[1]': {
                    required: true,
                    noSpaces: true,
                },
                'remark[1]': {
                    required: true,
                    noSpaces: true,
                },
            },
            messages: {
                document_number: {
                    required: "Document Number is Required",
                },
                issue_date: {
                    required: "Please Select Issue Date",
                },
                revision_date: {
                    required: "Please Select Revision Date",
                },
                'employee_name[1]': {
                    required: "Employee Name is Required",
                },
                'employee_code[1]': {
                    required: "Employee Code is Required",
                },
                'department[1]': {
                    required: "Department is Required",
                },
                'unit[1]': {
                    required: "Unit is Required",
                },
                'date[1]': {
                    required: "date is Required",
                },
                'amount[1]': {
                    required: "Amount is Required",
                },
                'description[1]': {
                    required: "Description is Required",
                },
                'amount_given_by[1]': {
                    required: "Amount Given by is Required",
                },
                'amount_received_by[1]': {
                    required: "Amount Received by is Required",
                },
                'remark[1]': {
                    required: "Remark is Required",
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
                validator.errorList.forEach(function(error) {});
            }
        });

        $.validator.addMethod("uniqueEmployeeCode", function(value, element) {
            var employeeCode = [];
            
            $("input[name^='item_code']").each(function() {
                var employeeCodeValue = $(this).val();
                if (employeeCodeValue) {
                    employeeCode.push(employeeCodeValue);  
                }
            });
           
            return employeeCodes.indexOf(value) === employeeCodes.lastIndexOf(value);
        }, "Employee Code must be unique");

        let form_set_count = 2;
        let serial_number = parseInt("{{ getSPLBCount() }}", 10) + 1;
        const maxFormSets = 200;
        const minFormSets = 1;


        $(document).on('click',".add-row",function() {
            let currentFormSets = $('#form-wrapper .form-set').length;

            if (currentFormSets >= maxFormSets) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Maximum Safety Petty Logbook CheckList Reached',
                    text: 'You can only add up to 200 Safety Petty Logbook CheckList.',
                    confirmButtonColor: '#3085d6'
                });
                return;
            }

            let newSerialNumber = 'SPLB-' + ('0000' + serial_number).slice(-5);

            var newFormSet = `
                <div class="form-set mb-3">
                    <div class="card-header-inner">
                        <h4 class="text-white">Safety Petty Logbook CheckList</h4>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button class="btn btn-primary add-row me-3" type="button"
                            id="add-row" style="width: 84px;">
                            Add
                        </button>
                        <button type="button" class="btn btn-danger remove-row">
                            <i class="fa-solid fa-trash"></i> Remove
                        </button>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group form-input">
                                <label class="form-label require">Serial Number</label>
                                <input type="text" name="serial_number[${form_set_count}]" class="form-control" placeholder="Serial Number" value="${newSerialNumber}" readonly>
                            </div>
                        </div>

                       <div class="col-md-4">
                            <div class="form-group form-input">
                                <label class="form-label require">Employee Name </label>
                                <select name="employee_name[${form_set_count}]"
                                    class="form-control single-select" style="width: 100%">
                                <option value="">Select Employee Name</option>
                            </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group form-input">
                                <label class="form-label require">Employee Code</label>
                                <input type="text" name="employee_code[${form_set_count}]"
                                    class="form-control" placeholder="Employee Code"
                                    value="">
                            </div>
                        </div>

                        <div class="col-md-4 mt-2">
                            <div class="form-group form-input">
                                <label class="form-label require">Department</label>
                                <select name="department[${form_set_count}]"
                                    class="form-control single-select" style="width: 100%">
                                    <option value="">Select Department</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4 mt-2">
                            <div class="form-group form-input">
                                <label class="form-label require">Unit</label>
                                <select name="unit[${form_set_count}]"
                                    class="form-control single-select" style="width: 100%">
                                    <option value="">Select Unit</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group form-input">
                                <label class="form-label require">Date</label>
                                <input type="text" name ="date[${form_set_count}]" id="date"
                                    class="form-control" placeholder="Date" value="">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group form-input">
                                <label class="form-label require">Amount</label>
                                <input type="text" name ="amount[${form_set_count}]" id="amount"
                                    class="form-control" placeholder="Amount" value="">
                            </div>
                        </div>

                        <div class="col-md-4 mt-2">
                            <div class="form-group form-input">
                                <label class="form-label require">Amount Given By</label>
                                <input type="text" name ="amount_given_by[${form_set_count}]"
                                class="form-control" placeholder="Amount Given By" value="">
                            </div>
                        </div>

                        <div class="col-md-4 mt-2">
                            <div class="form-group form-input">
                                <label class="form-label require">Amount Received By</label>
                                <input type="text" name ="amount_received_by[${form_set_count}]"
                                class="form-control" placeholder="Amount Received By" value="">
                            </div>
                        </div>

                        <div class="col-md-12 mt-2">
                            <div class="form-group form-input">
                                <label class="form-label require">Description</label>
                                <textarea name="description[${form_set_count}]" class="form-control" placeholder="Description" rows="3"></textarea>
                            </div>
                        </div>

                        <div class="col-md-12 mt-2">
                            <div class="form-group form-input">
                                <label class="form-label require">Remark</label>
                                <textarea name="remark[${form_set_count}]" class="form-control" placeholder="Remark" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                </div>`;

            $('#form-wrapper').append(newFormSet);

            serial_number++;

            $('select[name^="employee_name["]').each(function() {
                $(this).select2({
                    placeholder: "Select Employee Name",
                    width: '100%'
                });
            });

            $('select[name^="department["]').each(function() {
                $(this).select2({
                    placeholder: "Select Department",
                    width: '100%'
                });
            });

            $('select[name^="unit["]').each(function() {
                $(this).select2({
                    placeholder: "Select Unit",
                    width: '100%'
                });
            });

            $("select[name='employee_name[" + form_set_count + "]']").rules('add', {
                required: true,
                messages: {
                    required: 'Employee Name is required',
                }
            });

            $("input[name='employee_code[" + form_set_count + "]']").rules('add', {
                required: true,
                uniqueEmployeeCode: true,
                noSpaces: true,
                messages: {
                    required: 'Employee Code is required',
                    uniqueEmployeeCode: 'Employee Code must be unique',
                    noSpaces: 'Employee Code cannot be empty or only spaces'
                }
            });

            $("select[name='department[" + form_set_count + "]']").rules('add', {
                required: true,
                messages: {
                    required: 'Department is required',
                }
            });

            $("select[name='unit[" + form_set_count + "]']").rules('add', {
                required: true,
                messages: {
                    required: 'Unit is required',
                }
            });

            $("input[name='date[" + form_set_count + "]']").rules('add', {
                required: true,
                messages: {
                    required: 'Date is required',
                }
            });

            $("input[name='amount[" + form_set_count + "]']").rules('add', {
                required: true,
                noSpaces: true, 
                messages: {
                    required: 'Amount is required',
                    noSpaces: 'Amount cannot be empty or only spaces'
                }
            });

            $("input[name='amount_given_by[" + form_set_count + "]']").rules('add', {
                required: true,
                noSpaces: true, 
                messages: {
                    required: 'Amount Given By is required',
                    noSpaces: 'Amount Given By cannot be empty or only spaces'
                }
            });

            $("input[name='amount_received_by[" + form_set_count + "]']").rules('add', {
                required: true,
                noSpaces: true, 
                messages: {
                    required: 'Amount Received By is required',
                    noSpaces: 'Amount Received By cannot be empty or only spaces'
                }
            });

            $("textarea[name='description[" + form_set_count + "]']").rules('add', {
                required: true,
                noSpaces: true,
                messages: {
                    required: 'Description is required',
                    noSpaces: 'Description cannot be empty or only spaces'
                }
            });

            $("textarea[name='remark[" + form_set_count + "]']").rules('add', {
                required: true,
                noSpaces: true,
                messages: {
                    required: 'Remark is required',
                    noSpaces: 'Remark cannot be empty or only spaces'
                }
            });
            form_set_count++;
            updatePageIndices(); 
        });

        $(document).on('click', '.remove-row', function() {
            let currentFormSets = $('#form-wrapper .form-set').length;

            if (currentFormSets <= minFormSets) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Minimum Safety Petty Logbook CheckList Required',
                    text: 'At least 1 Safety Petty Logbook CheckList is required.',
                    confirmButtonColor: '#3085d6'
                });
                return;
            }
            $(this).closest('.form-set').remove();
            updatePageIndices();
        });

        function updatePageIndices() {
            $('#form-wrapper .form-set').each(function(index) {
                $(this).find("input[name^='serial_number']").val('SPLB-' + ('0000' + (index + 1)).slice(-5));

                $(this).find('input[name^="serial_number"]').attr('name', 'serial_number[' + (index + 1) + ']'); 
                $(this).find('select[name^="employee_name"]').attr('name', 'employee_name[' + (index + 1) + ']'); 
                $(this).find('input[name^="employee_code"]').attr('name', 'employee_code[' + (index + 1) + ']'); 
                $(this).find('select[name^="department"]').attr('name', 'department[' + (index + 1) + ']'); 
                $(this).find('select[name^="unit"]').attr('name', 'unit[' + (index + 1) + ']'); 
                $(this).find('input[name^="date"]').attr('name', 'date[' + (index + 1) + ']'); 
                $(this).find('input[name^="amount"]').attr('name', 'amount[' + (index + 1) + ']'); 
                $(this).find('input[name^="amount_given_by"]').attr('name', 'amount_given_by[' + (index + 1) + ']');
                $(this).find('input[name^="amount_received_by"]').attr('name', 'amount_received_by[' + (index + 1) + ']');
                $(this).find('textarea[name^="remark"]').attr('name', 'remark[' + (index + 1) + ']');
            });
        }

        $(".submit").on('click', function() {
            if ($("#sftyAdd").valid()) {
                $("#sftyAdd").submit();
            } else {
                return false;
            }
        });
    });
</script>
@endpush

