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
                                                    <label class="form-label require">Revision & Data</label>
                                                    <input type="text" name ="revision_date" class="form-control"
                                                        placeholder="Revision Data"
                                                        value="{{ getDocumentReviewDate('SPLB-0') }}" readonly>
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
                                                    <div class="row">
                                                        <div class="col-md-4 mt-2">
                                                            <div class="form-group form-input">
                                                                <label class="form-label require">Serial Number</label>
                                                                <input type="text" name="serial_number"
                                                                    class="form-control" placeholder="Serial Number"
                                                                    value="{{ getsequence('SPLB') }}" readonly>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-4 mt-2">
                                                            <div class="form-group form-input">
                                                                <label class="form-label require">Employee Name </label>
                                                                <select name="emp_id" id="emp_id"
                                                                    class="form-control single-select" style="width: 100%">
                                                                    <option value="">Select Employee Name</option>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-4 mt-2">
                                                            <div class="form-group form-input">
                                                                <label class="form-label require">Employee Code</label>
                                                                <input type="text" name="employee_code"
                                                                    id="employee_code" class="form-control"
                                                                    placeholder="Employee Code" value="">
                                                            </div>
                                                        </div>

                                                        <div class="col-md-4 mt-2">
                                                            <div class="form-group form-input">
                                                                <label class="form-label require">Unit</label>
                                                                <select name="unit_id" id="unit_id"
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
                                                                <select name="department_id" id="department_id"
                                                                    class="form-control single-select" style="width: 100%">
                                                                    <option value="">Select Department</option>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-4 mt-2">
                                                            <div class="form-group form-input">
                                                                <label class="form-label require">Date</label>
                                                                <input type="text" name ="date" id="date"
                                                                    class="form-control date-picker" placeholder="Date"
                                                                    value="">
                                                            </div>
                                                        </div>

                                                        <div class="col-md-4 mt-2">
                                                            <div class="form-group form-input">
                                                                <label class="form-label require">Amount</label>
                                                                <input type="text" name ="amount" id="amount"
                                                                    class="form-control" placeholder="Amount"
                                                                    value="">
                                                            </div>
                                                        </div>

                                                        <div class="col-md-4 mt-2">
                                                            <div class="form-group form-input">
                                                                <label class="form-label require">Amount Given By</label>
                                                                <select name="amnt_givenby_id" id="amnt_givenby_id"
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
                                                                <select name="amnt_receivedby_id" id="amnt_receivedby_id"
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
                                                                <textarea name="description" class="form-control" placeholder="Description" rows="3"></textarea>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-12 mt-2">
                                                            <div class="form-group form-input">
                                                                <label class="form-label require">Remark</label>
                                                                <textarea name="remark" class="form-control" placeholder="Remark" rows="3"></textarea>
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

            var fromDatepicker = flatpickr("#issue_date", {
                dateFormat: "d-m-Y",
                // minDate: new Date(),
            });

            var fromDatepicker = flatpickr("#date", {
                dateFormat: "d-m-Y",
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
                                '<input type="file" name="signature_givenby_image" id="signature_givenby" ' +
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
                                '<input type="file" name="signature_receivedby_image" id="signature_receivedby" ' +
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
                console.log("Selected employee ID for Given By: " + loginId);
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
                    emp_id: {
                        required: true,
                    },
                    employee_code: {
                        required: true,
                        noSpaces: true,
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
                    department_id: {
                        required: true,
                    },
                    unit_id: {
                        required: true,
                    },
                    date: {
                        required: true,
                    },
                    amount: {
                        required: true,
                        noSpaces: true,
                    },
                    description: {
                        required: true,
                        noSpaces: true,
                    },
                    amnt_givenby_id: {
                        required: true,
                        noSpaces: true,
                    },
                    amnt_receivedby_id: {
                        required: true,
                        noSpaces: true,
                    },
                    remark: {
                        required: true,
                        noSpaces: true,
                    },
                    signature_givenby_image: {
                        required: true,
                    },
                    signature_receivedby_image: {
                        required: true,
                    }
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
                    emp_id: {
                        required: "Employee Name is Required",
                    },
                    employee_code: {
                        required: "Employee Code is Required",
                        remote: "{{ __('Employee Code should be unique') }}",
                    },
                    department_id: {
                        required: "Department is Required",
                    },
                    unit_id: {
                        required: "Unit is Required",
                    },
                    date: {
                        required: "date is Required",
                    },
                    amount: {
                        required: "Amount is Required",
                    },
                    description: {
                        required: "Description is Required",
                    },
                    amnt_givenby_id: {
                        required: "Amount Given by is Required",
                    },
                    amnt_receivedby_id: {
                        required: "Amount Received by is Required",
                    },
                    remark: {
                        required: "Remark is Required",
                    },
                    signature_givenby_image: {
                        required: "Signature Given by Image is Required",
                    },
                    signature_receivedby_image: {
                        required: "Signature Received by Image is Required",
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
