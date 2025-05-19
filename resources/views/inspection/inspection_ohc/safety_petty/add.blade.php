@extends('admin.layouts.admin')
@section('title', 'Safety Petty Logbook')
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
                                    <form method="POST" id="sftyAdd" enctype="multipart/form-data"
                                        action="{{ admin_url('ohc/safety-petty-logbook/add/submit') }}">
                                        @csrf

                                        <div class="row">
                                            <div class="card-header-inner">
                                                <h4 class="text-white">Safety Petty Logbook Details</h4>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Document Number</label>
                                                    <input type="text" name ="document_number" class="form-control"
                                                        placeholder="Document Number" value="{{ $document_no->doc_no }}"
                                                        readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Issue Date</label>
                                                    <div class="input-group date form-input custom-height">
                                                        <input type="text" name ="issue_date" id="issue_date"
                                                            class="form-control" placeholder="Issue Date"
                                                            value="{{ displaydateformat($document_no->issue_date) }}"
                                                            readonly>
                                                        <div class="input-group-addon input-group-text">
                                                            <span class="fa fa-calendar"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Revision & Data</label>
                                                    <input type="text" name ="revision_date" class="form-control"
                                                        placeholder="Revision Data" value="{{ $document_no->rev_dt }}"
                                                        readonly>
                                                </div>
                                            </div>

                                            <input type="hidden" name="document_reference_id"
                                                value="{{ encryptId($document_no->id) }}">
                                        </div>

                                        <div class="row mt-4">
                                            <div class="row mt-2">
                                                <div
                                                    class="d-flex justify-content-end align-items-center me-2 mb-3 button-container">
                                                </div>
                                            </div>

                                            <div id="form-wrapper">
                                                <div class="card-header-inner d-flex justify-content-between">
                                                    <h4 class="text-white ms-2">Safety Petty Logbook CheckList</h4>
                                                    <button class="btn btn-primary add-row mb-2 " type="button"
                                                        id="add-row"
                                                        style="margin-left: 10px;  margin-right: 10px; width: 84px;">
                                                        Add
                                                    </button>
                                                </div>
                                                <div class="form-set mb-3">

                                                    <div class="row">
                                                        <div class="col-md-4 mt-2">
                                                            <div class="form-group form-input">
                                                                <label class="form-label require">Serial Number</label>
                                                                <input type="text" name="serial_number[1]"
                                                                    class="form-control" placeholder="Serial Number"
                                                                    value="SPLB-00001" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 mt-2">
                                                            <div class="form-group form-input">
                                                                <label class="form-label require">Employee Code</label>
                                                                <select name="employee_code[1]" id="employee_code"
                                                                    class="form-control single-select" style="width: 100%">
                                                                    <option value="">Select Employee Code</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 mt-2">
                                                            <div class="form-group form-input">
                                                                <label class="form-label require">Employee Name </label>
                                                                <input type="text" name="emp_name[1]" id="emp_name"
                                                                    class="form-control" placeholder="Employee Name"
                                                                    value="" readonly>
                                                            </div>
                                                        </div>



                                                        <div class="col-md-4 mt-2">
                                                            <div class="form-group form-input">
                                                                <label class="form-label require">Unit</label>
                                                                <select name="unit_id[1]" id="unit_id"
                                                                    class="form-control single-select" style="width: 100%">
                                                                    <option value="">Select Unit</option>
                                                                    @foreach ($unit as $list)
                                                                        <option value="{{ encryptId($list->id) }}">
                                                                            {{ $list->unit_name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-4 mt-2">
                                                            <div class="form-group form-input">
                                                                <label class="form-label require">Department</label>
                                                                <select name="department_id[1]" id="department_id"
                                                                    class="form-control single-select"
                                                                    style="width: 100%">
                                                                    <option value="">Select Department</option>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-4 mt-2">
                                                            <div class="form-group form-input">
                                                                <label class="form-label require">Date</label>
                                                                <div class="input-group date form-input custom-height">
                                                                    <input type="text" name ="date[1]" id="date"
                                                                        class="form-control date-picker"
                                                                        placeholder="Date" value="">
                                                                    <div class="input-group-addon input-group-text">
                                                                        <span class="fa fa-calendar"></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-4 mt-2">
                                                            <div class="form-group form-input">
                                                                <label class="form-label require">Amount</label>
                                                                <input type="text" name ="amount[1]" id="amount"
                                                                    class="form-control" placeholder="Amount"
                                                                    value="">
                                                            </div>
                                                        </div>

                                                        <div class="col-md-4 mt-2">
                                                            <div class="form-group form-input">
                                                                <label class="form-label require">Amount Given By</label>
                                                                <select name="amnt_givenby_id[1]" id="amnt_givenby_id"
                                                                    class="form-control single-select"
                                                                    style="width: 100%">
                                                                    <option value="">Select Amount Given by</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 form-group form-input mb-2 mt-2"
                                                            id="signature_givenby" style="display:none;">
                                                        </div>

                                                        <div class="col-md-4 mt-2">
                                                            <div class="form-group form-input">
                                                                <label class="form-label require">Amount Received
                                                                    By</label>
                                                                <select name="amnt_receivedby_id[1]"
                                                                    id="amnt_receivedby_id"
                                                                    class="form-control single-select"
                                                                    style="width: 100%">
                                                                    <option value="">Select Amount Received by
                                                                    </option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 form-group form-input mb-2 mt-2"
                                                            id="signature_receivedby" style="display:none;">
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

                                                        <div class="col-md-2 text-right  mt-4">
                                                            <button class="btn btn-danger remove-row" type="button"
                                                                style="margin:10px;"><i class="fa fa-trash"></i></button>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <hr>
                                        <div class="submit-button" style="text-align: right;">
                                            <x-button-submit class="submit"></x-button-submit>
                                            <x-button-reset class="submit"></x-button-reset>
                                            <x-button-cancel
                                                href="{{ admin_url('ohc/safety-petty-logbook/list') }}"></x-button-cancel>
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

            var fromDatepicker = flatpickr("#date", {
                dateFormat: "d-m-Y",
            });

            function initDatePicker(selector) {
                flatpickr(selector, {
                    dateFormat: "d-m-Y",
                });
            }

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

            $(document).on('change', '[id^="unit_id-"]', function() {
                var unitId = $(this).val();
                var departmentSelect = $(this).closest('form').find('.department');

                if (unitId) {
                    $.ajax({
                        url: "{{ admin_url('department/ajax-list') }}/" + unitId + "/0",
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            departmentSelect.empty().append(
                                '<option value="">Select Department</option>');
                            $.each(data, function(key, value) {
                                departmentSelect.append('<option value="' + value.id +
                                    '">' + value.name + '</option>');
                            });
                            departmentSelect.trigger('change');
                        },
                        error: function(xhr) {
                            alert('Error fetching department. Please try again.');
                        }
                    });
                } else {
                    departmentSelect.empty().append('<option value="">Select Department</option>');
                    departmentSelect.trigger('change');
                }
            });

            // getting the employee/worker details
            $('#employee_code').select2({
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

            // department and number & emp name

            $(document).on('change', '#employee_code', function() {
                var empId = $(this).val();



                if (empId) {
                    $.ajax({
                        url: "{{ admin_url('ohc/prescribe-to-patient/emp-details/') }}" +
                            empId,
                        type: 'GET',
                        dataType: 'json',
                        success: function(response) {
                            if (response.employee) {
                                $('#emp_name').val(response.employee.emp_name).prop(
                                    'readonly', true);


                            } else {
                                alert("No employee details found.");
                            }
                        },
                        error: function(xhr) {
                            alert('Error fetching employee details. Please try again.');
                        }
                    });
                } else {
                    $('#emp_name').val('').prop('readonly', false);
                }

            });

            function initEmpSelect2(selector) {
                $(selector).select2({
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
                    selectionCssClass: 'form-control',
                    placeholder: "Select Employee Name",
                    width: '100%'
                });

                // Bind change event specific to the current selector
                $(selector).off('change').on('change', function() {
                    var empId = $(this).val();
                    var formIndex = $(this).attr('id').split('-')[1]; // e.g., "employee_code-0" → 0
                    var nameInput = `#emp_name-${formIndex}`;

                    if (empId) {
                        $.ajax({
                            url: "{{ admin_url('ohc/prescribe-to-patient/emp-details/') }}" +
                                empId,
                            type: 'GET',
                            dataType: 'json',
                            success: function(response) {
                                if (response.employee) {
                                    $(nameInput).val(response.employee.emp_name).prop(
                                        'readonly', true);
                                } else {
                                    alert("No employee details found.");
                                    $(nameInput).val('').prop('readonly', false);
                                }
                            },
                            error: function(xhr) {
                                alert('Error fetching employee details. Please try again.');
                                $(nameInput).val('').prop('readonly', false);
                            }
                        });
                    } else {
                        $(nameInput).val('').prop('readonly', false);
                    }
                });
            }


            $('#amnt_givenby_id').select2({
                ajax: {
                    url: '{{ admin_url('ohc/safety-petty-logbook/employeeid') }}',
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

            $('#amnt_receivedby_id').select2({
                ajax: {
                    url: '{{ admin_url('ohc/safety-petty-logbook/employeeid') }}',
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

            function updateGivenBySignatureField(loginId) {
                $.ajax({
                    url: '{{ admin_url('ohc/safety-petty-logbook/get-signature') }}',
                    method: 'GET',
                    data: {
                        login_id: loginId
                    },
                    success: function(response) {
                        $("#signature_givenby").empty();

                        if (response.signature_upload) {
                            $("#signature_givenby").html(
                                '<label class="form-label" style="display: block;">Signature</label>' +
                                '<img src="{{ admin_url('public/') }}' + response
                                .signature_upload +
                                '" alt="Signature Upload" style="width: 150px; margin-top:-10px">'
                            );
                        } else {
                            $("#signature_givenby").html(
                                '<label class="form-label require">Signature</label>' +
                                '<input type="file" name="signature_givenby_image[1]" id="signature_givenby" ' +
                                'class="form-control form-control-sm" accept="image/*">' +
                                '<small>Allowed file types: jpg, jpeg, png</small>' +
                                '<div id="signature_givenby_error" class="text-danger"></div>'
                            );
                        }

                        $("#signature_givenby").show();
                    }
                });
            }

            function updateReceivedBySignatureField(loginId) {
                $.ajax({
                    url: '{{ admin_url('ohc/safety-petty-logbook/get-signature') }}',
                    method: 'GET',
                    data: {
                        login_id: loginId
                    },
                    success: function(response) {
                        $("#signature_receivedby").empty();

                        if (response.signature_upload) {
                            $("#signature_receivedby").html(
                                '<label class="form-label" style="display: block;">Signature</label>' +
                                '<img src="{{ admin_url('public/') }}' + response
                                .signature_upload +
                                '" alt="Signature Upload" style="width: 150px; margin-top:-10px">'
                            );
                        } else {
                            $("#signature_receivedby").html(
                                '<label class="form-label require">Signature</label>' +
                                '<input type="file" name="signature_receivedby_image[1]" id="signature_receivedby" ' +
                                'class="form-control form-control-sm" accept="image/*">' +
                                '<small>Allowed file types: jpg, jpeg, png</small>' +
                                '<div id="signature_receivedby_error" class="text-danger"></div>'
                            );
                        }

                        $("#signature_receivedby").show();
                    }
                });
            }

            $('#amnt_givenby_id').on('select2:select', function(e) {
                var loginId = $(this).val();
                if (loginId) {
                    updateGivenBySignatureField(loginId);
                } else {
                    $('#signature_givenby').hide();
                }
            });

            $('#amnt_receivedby_id').on('select2:select', function(e) {
                var loginId = $(this).val();
                if (loginId) {
                    updateReceivedBySignatureField(loginId);
                } else {
                    $('#signature_receivedby').hide();
                }
            });

            $.validator.addMethod("noSpaces", function(value, element) {
                return this.optional(element) || value.trim().length > 0;
            }, "This field cannot contain only spaces");

            $('#sftyAdd').validate({
                rules: {
                    'emp_name[1]': {
                        required: true,
                    },
                    'employee_code[1]': {
                        required: true,
                        noSpaces: true,
                        uniqueItemCode: true,
                        remote: {
                            url: '{{ admin_url('ohc/safety-petty-logbook/unique') }}',
                            type: 'post',
                            data: {
                                location_type_name: function() {
                                    return $('#employee_code').val();
                                }
                            }
                        }
                    },
                    'department_id[1]': {
                        required: true,
                    },
                    'unit_id[1]': {
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
                    'amnt_givenby_id[1]': {
                        required: true,
                        noSpaces: true,
                    },
                    'amnt_receivedby_id[1]': {
                        required: true,
                        noSpaces: true,
                    },
                    'remark[1]': {
                        required: true,
                        noSpaces: true,
                    },
                    'signature_givenby_image[1]': {
                        required: true,
                        filesize: 15728640,
                    },
                    'signature_receivedby_image[1]': {
                        required: true,
                        filesize: 15728640,
                    }
                },
                messages: {
                    'emp_name[1]': {
                        required: "Employee Name is Required",
                    },
                    'employee_code[1]': {
                        required: "Employee Code is Required",
                        uniqueItemCode: "Employee Code must be unique",
                        remote: "Employee Code should be unique",
                    },
                    'department_id[1]': {
                        required: "Department is Required",
                    },
                    'unit_id[1]': {
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
                    'amnt_givenby_id[1]': {
                        required: "Amount Given by is Required",
                    },
                    'amnt_receivedby_id[1]': {
                        required: "Amount Received by is Required",
                    },
                    'remark[1]': {
                        required: "Remark is Required",
                    },
                    'signature_givenby_image[1]': {
                        required: "Signature Given by Image is Required",
                        filesize: "File must be less than 15MB."
                    },
                    'signature_receivedby_image[1]': {
                        required: "Signature Received by Image is Required",
                        filesize: "File must be less than 15MB."
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
                    form.submit();
                },
                invalidHandler: function(event, validator) {
                    var errors = validator.numberOfInvalids();
                    validator.errorList.forEach(function(error) {});
                }
            });


            $.validator.addMethod("uniqueItemCode", function(value, element) {
                var itemCodes = [];

                $("input[name^='employee_code']").each(function() {
                    var itemCodeValue = $(this).val();
                    if (itemCodeValue) {
                        itemCodes.push(itemCodeValue);
                    }
                });

                return itemCodes.indexOf(value) === itemCodes.lastIndexOf(value);
            }, "Employe Code must be unique");

            let form_set_count = 2;
            let serial_number = parseInt("{{ getSPLBCount() }}", 10) + 1;
            const maxFormSets = 200;
            const minFormSets = 1;


            $(document).on('click', ".add-row", function() {
                let currentFormSets = $('#form-wrapper .form-set').length;

                if (currentFormSets >= maxFormSets) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Maximum Safety Petty Logbook Reached',
                        text: 'You can only add up to 200 Safety Petty Logbook.',
                        confirmButtonColor: '#3085d6'
                    });
                    return;
                }

                let newSerialNumber = 'SPLB-' + ('0000' + serial_number).slice(-5);

                var newFormSet = `
                    <div class="form-set mb-3">

                        <div class="row">
                            <div class="col-md-4 mt-2">
                                <div class="form-group form-input">
                                    <label class="form-label require">Serial Number</label>
                                    <input type="text" name="serial_number[${form_set_count}]" class="form-control" placeholder="Serial Number" value="${newSerialNumber}" readonly>
                                </div>
                            </div>
                            <div class="col-md-4 mt-2">
                                                            <div class="form-group form-input">
                                                                <label class="form-label require">Employee Code</label>
                                                                <select name="employee_code[${form_set_count}]" id="employee_code-${form_set_count}"
                                                                    class="form-control single-select" style="width: 100%">
                                                                    <option value="">Select Employee Code</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 mt-2">
                                                            <div class="form-group form-input">
                                                                <label class="form-label require">Employee Name </label>
                                                                <input type="text" name="emp_name[${form_set_count}]"
                                                                    id="emp_name-${form_set_count}" class="form-control"
                                                                    placeholder="Employee Name" value="" readonly>
                                                            </div>
                                                        </div>


                            <div class="col-md-4 mt-2">
                                <div class="form-group form-input">
                                    <label class="form-label require">Unit</label>
                                    <select name="unit_id[${form_set_count}]" id="unit_id-${form_set_count}"
                                        class="form-control single-select" style="width: 100%">
                                        <option value="">Select Unit</option>
                                        @foreach ($unit as $list)
                                            <option value="{{ encryptId($list->id) }}">
                                                {{ $list->unit_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4 mt-2">
                                <div class="form-group form-input">
                                    <label class="form-label require">Department</label>
                                    <select name="department_id[${form_set_count}]" id="department_id-${form_set_count}"
                                        class="form-control single-select department" style="width: 100%">
                                        <option value="">Select Department</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4 mt-2">
                                <div class="form-group form-input">
                                    <label class="form-label require">Date</label>

                                         <div class="input-group date form-input custom-height">
                                                                   <input type="text" name ="date[${form_set_count}]" id="date-${form_set_count}"
                                        class="form-control date-picker" placeholder="Date"
                                        value="">

                                                                    <div class="input-group-addon input-group-text">
                                                                        <span class="fa fa-calendar"></span>
                                                                    </div>
                                                                </div>
                                </div>
                            </div>

                            <div class="col-md-4 mt-2">
                                <div class="form-group form-input">
                                    <label class="form-label require">Amount</label>
                                    <input type="text" name ="amount[${form_set_count}]" id="amount-${form_set_count}"
                                        class="form-control" placeholder="Amount"
                                        value="">
                                </div>
                            </div>

                            <div class="col-md-4 mt-2">
                                <div class="form-group form-input">
                                    <label class="form-label require">Amount Given By</label>
                                    <select name="amnt_givenby_id[${form_set_count}]" id="amnt_givenby_id-${form_set_count}"
                                        class="form-control single-select"
                                        style="width: 100%">
                                        <option value="">Select Amount Given by</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 form-group form-input mb-2 mt-2"
                                id="signature_givenby-${form_set_count}" style="display:none;">
                            </div>

                            <div class="col-md-4 mt-2">
                                <div class="form-group form-input">
                                    <label class="form-label require">Amount Received
                                        By</label>
                                    <select name="amnt_receivedby_id[${form_set_count}]" id="amnt_receivedby_id-${form_set_count}"
                                        class="form-control single-select"
                                        style="width: 100%">
                                        <option value="">Select Amount Received by
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 form-group form-input mb-2 mt-2"
                                id="signature_receivedby-${form_set_count}" style="display:none;">
                            </div>

                            <div class="col-md-12 mt-2">
                                <div class="form-group form-input">
                                    <label class="form-label require">Description</label>
                                    <textarea name="description[${form_set_count}]" id="description-${form_set_count}" class="form-control" placeholder="Description" rows="3"></textarea>
                                </div>
                            </div>

                            <div class="col-md-12 mt-2">
                                <div class="form-group form-input">
                                    <label class="form-label require">Remark</label>
                                    <textarea name="remark[${form_set_count}]" id="remark-${form_set_count}" rows="3"  class="form-control"
                                        placeholder="Remark"></textarea>
                                </div>
                            </div>
 <div class="col-md-2 text-right  mt-4">
                                                            <button class="btn btn-danger remove-row" type="button"
                                                                style="margin:10px;"><i class="fa fa-trash"></i></button>

                                                        </div>
                        </div>
                    </div>
                `;

                $('#form-wrapper').append(newFormSet);

                serial_number++;

                $('select[name^="unit_id["]').each(function() {
                    $(this).select2({
                        placeholder: "Select Unit",
                        width: '100%'
                    });
                });

                $('select[name^="department_id["]').each(function() {
                    $(this).select2({
                        placeholder: "Select Department",
                        width: '100%'
                    });
                });

                $("select[name='emp_name[" + form_set_count + "]']").rules('add', {
                    required: true,
                    messages: {
                        required: 'Employee Name is required',
                    }
                });

                $("input[name='employee_code[" + form_set_count + "]']").rules('add', {
                    required: true,
                    noSpaces: true,
                    uniqueItemCode: true,
                    remote: {
                        url: '{{ admin_url('ohc/safety-petty-logbook/unique') }}',
                        type: 'post',
                        data: {
                            location_type_name: function() {
                                return $('#employee_code').val();
                            }
                        }
                    },
                    messages: {
                        required: 'Employee Code is required',
                        noSpaces: 'Employee Code cannot be empty or only spaces',
                        uniqueItemCode: 'Employee Code must be unique',
                        remote: "Employee Code should be unique",
                    }
                });

                $("select[name='unit_id[" + form_set_count + "]']").rules('add', {
                    required: true,
                    messages: {
                        required: 'Unit is required',
                    }
                });

                $("select[name='department_id[" + form_set_count + "]']").rules('add', {
                    required: true,
                    messages: {
                        required: 'Department is required',
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
                        noSpaces: 'Amount cannot be empty or only spaces',
                    }
                });

                $("select[name='amnt_givenby_id[" + form_set_count + "]']").rules('add', {
                    required: true,
                    messages: {
                        required: 'Amount Given By is required',
                    }
                });

                $("select[name='amnt_receivedby_id[" + form_set_count + "]']").rules('add', {
                    required: true,
                    messages: {
                        required: 'Amount Received By is required',
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

                initializeNewFormSet(form_set_count);
                initDatePicker(`#date-${form_set_count}`);
                initEmpSelect2(`#employee_code-${form_set_count}`);
                form_set_count++;

                updatePageIndices();

            });

            function initializeNewFormSet(form_set_count) {

                const givenBySelector = '#amnt_givenby_id-' + form_set_count;

                const receivedBySelector = '#amnt_receivedby_id-' + form_set_count;

                $(givenBySelector).select2({
                    ajax: {
                        url: '{{ admin_url('ohc/safety-petty-logbook/employeeid') }}',
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
                        },
                        error: function(xhr, status, error) {
                            // console.log('Error during AJAX call:', error);
                            // console.log('Response:', xhr.responseText);
                        }
                    },
                    minimumInputLength: 1,
                    dropdownCssClass: 'form-control',
                    selectionCssClass: 'form-control'
                });


                $(receivedBySelector).select2({
                    ajax: {
                        url: '{{ admin_url('ohc/safety-petty-logbook/employeeid') }}',
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
                        },
                        error: function(xhr, status, error) {
                            // console.log('Error during AJAX call:', error);
                            // console.log('Response:', xhr.responseText);
                        }
                    },
                    minimumInputLength: 1,
                    dropdownCssClass: 'form-control',
                    selectionCssClass: 'form-control'
                });

                $('#amnt_givenby_id-' + form_set_count).on('select2:select', function() {
                    var loginId = $(this).val();
                    if (loginId) {
                        updateGivenByDynamicSignatureField(loginId, form_set_count);
                    } else {
                        $('#signature_givenby-' + form_set_count).hide();
                    }
                });

                $('#amnt_receivedby_id-' + form_set_count).on('select2:select', function() {
                    var loginId = $(this).val();
                    if (loginId) {
                        updateReceivedByDynamicSignatureField(loginId, form_set_count);
                    } else {
                        $('#signature_receivedby-' + form_set_count).hide();
                    }
                });
            }


            function updateGivenByDynamicSignatureField(loginId, form_set_count) {

                $.ajax({
                    url: '{{ admin_url('ohc/safety-petty-logbook/get-signature') }}',
                    method: 'GET',
                    data: {
                        login_id: loginId
                    },
                    success: function(response) {
                        var signatureDiv = $("#signature_givenby-" + form_set_count);
                        signatureDiv.empty();

                        if (response.signature_upload) {
                            signatureDiv.html(
                                '<label class="form-label" style="display: block;">Signature</label>' +
                                '<img src="{{ admin_url('public/') }}' + response
                                .signature_upload +
                                '" alt="Signature Upload" style="width: 150px; margin-top:-10px">'
                            );
                        } else {
                            signatureDiv.html(
                                '<label class="form-label require">Signature</label>' +
                                '<input type="file" name="signature_givenby_image[' +
                                form_set_count + ']" id="signature_givenby-' + form_set_count +
                                '" ' +
                                'class="form-control form-control-sm" accept="image/*">' +
                                '<small>Allowed file types: jpg, jpeg, png</small>' +
                                '<div id="signature_givenby_error-' + form_set_count +
                                '" class="text-danger"></div>'
                            );
                        }

                        updatePageIndices();
                        signatureDiv.show();
                    }
                });
            }

            function updateReceivedByDynamicSignatureField(loginId, form_set_count) {
                $.ajax({
                    url: '{{ admin_url('ohc/safety-petty-logbook/get-signature') }}',
                    method: 'GET',
                    data: {
                        login_id: loginId
                    },
                    success: function(response) {
                        var signatureDiv = $("#signature_receivedby-" + form_set_count);
                        signatureDiv.empty();

                        if (response.signature_upload) {
                            signatureDiv.html(
                                '<label class="form-label" style="display: block;">Signature</label>' +
                                '<img src="{{ admin_url('public/') }}' + response
                                .signature_upload +
                                '" alt="Signature Upload" style="width: 150px; margin-top:-10px">'
                            );
                        } else {
                            signatureDiv.html(
                                '<label class="form-label require">Signature</label>' +
                                '<input type="file" name="signature_receivedby_image[' +
                                form_set_count + ']" id="signature_receivedby-' + form_set_count +
                                '" ' +
                                'class="form-control form-control-sm" accept="image/*">' +
                                '<small>Allowed file types: jpg, jpeg, png</small>' +
                                '<div id="signature_receivedby_error-' + form_set_count +
                                '" class="text-danger"></div>'
                            );
                        }

                        updatePageIndices();
                        signatureDiv.show();
                    }
                });
            }

            $(document).on('click', '.remove-row', function() {
                let currentFormSets = $('#form-wrapper .form-set').length;

                if (currentFormSets <= minFormSets) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Minimum Safety Petty Logbook Required',
                        text: 'At least 1 Safety Petty Logbook is required.',
                        confirmButtonColor: '#3085d6'
                    });
                    return;
                }
                $(this).closest('.form-set').remove();
                updatePageIndices();

            });

            function updatePageIndices() {
                $('#form-wrapper .form-set').each(function(index) {
                    $(this).find("input[name^='serial_number']").val('SPLB-' + ('0000' + (index + 1)).slice(
                        -5));

                    $(this).find('input[name^="serial_number"]').attr('name', 'serial_number[' + (index +
                        1) + ']');
                    $(this).find('select[name^="emp_id"]').attr('name', 'emp_id[' + (index + 1) + ']');
                    $(this).find('input[name^="employee_code"]').attr('name', 'employee_code[' + (index +
                        1) + ']');
                    $(this).find('select[name^="unit_id"]').attr('name', 'unit_id[' + (index + 1) + ']');
                    $(this).find('select[name^="department_id"]').attr('name', 'department_id[' + (index +
                        1) + ']');
                    $(this).find('input[name^="date"]').attr('name', 'date[' + (index + 1) + ']');
                    $(this).find('input[name^="amount"]').attr('name', 'amount[' + (index + 1) + ']');
                    $(this).find('select[name^="amnt_givenby_id"]').attr('name', 'amnt_givenby_id[' + (
                        index + 1) + ']');
                    $(this).find('input[name^="signature_givenby_image"]').attr('name',
                        'signature_givenby_image[' + (index + 1) + ']');
                    $(this).find('input[name^="signature_receivedby_image"]').attr('name',
                        'signature_receivedby_image[' + (index + 1) + ']');
                    $(this).find('select[name^="amnt_receivedby_id"]').attr('name', 'amnt_receivedby_id[' +
                        (index + 1) + ']');
                    $(this).find('textarea[name^="description"]').attr('name', 'description[' + (index +
                        1) + ']');
                    $(this).find('textarea[name^="remark"]').attr('name', 'remark[' + (index + 1) + ']');


                    $(this).find("input[name^='signature_givenby_image']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Signature Given by Image is required',
                        }
                    });
                    $(this).find("input[name^='signature_receivedby_image']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Signature Received by Image is required',
                        }
                    });
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
