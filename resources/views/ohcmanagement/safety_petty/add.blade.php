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
                                                    <label class="form-label require">Revision Date</label>
                                                    <input type="text" name ="revision_date" class="form-control"
                                                        placeholder="Revision Date" value="{{ getDocumentReviewDate('SPLB-0') }}"
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
                                                    {{-- <div class="d-flex justify-content-end">
                                                        <button class="btn btn-primary add-row me-3" type="button"
                                                            id="add-row" style="width: 84px;">
                                                            Add
                                                        </button>
                                                        <button type="button" class="btn btn-danger remove-row">
                                                            <i class="fa-solid fa-trash"></i> Remove
                                                        </button>
                                                    </div> --}}
                                                    <div class="row">
                                                        <div class="col-md-4 mt-2">
                                                            <div class="form-group form-input">
                                                                <label class="form-label require">Serial Number</label>
                                                                <input type="text" name="serial_number"
                                                                    class="form-control" placeholder="Serial Number"
                                                                    value="SPLB-00001" readonly>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-4 mt-2">
                                                            <div class="form-group form-input">
                                                                <label class="form-label require">Employee Name </label>
                                                                <select name="emp_id" id="emp_id"
                                                                    class="form-control single-select"
                                                                    style="width: 100%">
                                                                    <option value="">Select Employee Name</option>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-4 mt-2">
                                                            <div class="form-group form-input">
                                                                <label class="form-label require">Employee Code</label>
                                                                <input type="text" name="employee_code"
                                                                    class="form-control" placeholder="Employee Code"
                                                                    value="">
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
                                                                    class="form-control single-select"
                                                                    style="width: 100%">
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
                                                        <div class="col-md-4 form-group form-input mb-2 mt-2">
                                                            @if (isset(Auth::user()->signature_upload))
                                                                <label class="form-label"
                                                                    style="display: block; ">{{ __('inspection.signature') }}</label>
                                                                <img src="{{ admin_url('public/' . Auth::user()->signature_upload) }}"
                                                                    alt="Signature Upload" style="width: 150px; margin-top:-10px">
                                                            @else
                                                                <div class="form-input col-md-12 mb-2">
                                                                    <label class="form-label require">Signature</label>
                                                                    <input type="file" name="signature_givenby_image" id="signature_upload"
                                                                        class="form-control form-control-sm" accept="image/*"
                                                                        placeholder="Enter the image">
                                                                    <small>Allowed file types: jpg, jpeg, png</small>
                                                                    <div id="signature_upload" class="text-danger"></div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div class="col-md-4 mt-2">
                                                            <div class="form-group form-input">
                                                                <label class="form-label require">Amount Received
                                                                    By</label>
                                                                <select name="amnt_receivedby_id" id="amnt_receivedby_id"
                                                                    class="form-control single-select"
                                                                    style="width: 100%">
                                                                    <option value="">Select Amount Received by</option>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-4 form-group form-input mb-2 mt-2">
                                                            @if (isset(Auth::user()->signature_upload))
                                                                <label class="form-label"
                                                                    style="display: block; ">{{ __('inspection.signature') }}</label>
                                                                <img src="{{ admin_url('public/' . Auth::user()->signature_upload) }}"
                                                                    alt="Signature Upload" style="width: 150px; margin-top:-10px">
                                                            @else
                                                                <div class="form-input col-md-12 mb-2">
                                                                    <label class="form-label require">Signature</label>
                                                                    <input type="file" name="signature_receivedby_image" id="signature_upload"
                                                                        class="form-control form-control-sm" accept="image/*"
                                                                        placeholder="Enter the image">
                                                                    <small>Allowed file types: jpg, jpeg, png</small>
                                                                    <div id="signature_upload" class="text-danger"></div>
                                                                </div>
                                                            @endif
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
                minDate: new Date(),
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

            // $(document).on('change', '[id^="unit_id-"]', function() {
            //     var unitId = $(this).val();
            //     var formSetCount = $(this).attr('id').split('-')[
            //     1]; 
            //     var departmentSelect = $('#department_id-' +
            //     formSetCount); 

            //     if (unitId) {
            //         $.ajax({
            //             url: "{{ admin_url('department/ajax-list') }}/" + unitId + "/0",
            //             type: 'GET',
            //             dataType: 'json',
            //             success: function(data) {
            //                 departmentSelect.empty().append(
            //                     '<option value="">Select Department</option>');
            //                 $.each(data, function(key, value) {
            //                     departmentSelect.append('<option value="' + value.id +
            //                         '">' + value.name + '</option>');
            //                 });
            //                 departmentSelect.trigger('change');
            //             },
            //             error: function(xhr) {
            //                 alert('Error fetching department. Please try again.');
            //             }
            //         });
            //     } else {
            //         departmentSelect.empty().append('<option value="">Select Department</option>');
            //         departmentSelect.trigger('change');
            //     }
            // });


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

            // function initEmployeeSelect2() {
            //     $('.emp-select').select2({
            //         ajax: {
            //             url: '{{ admin_url('ohc/safety-petty-logbook/employeeid') }}',
            //             dataType: 'json',
            //             delay: 250,
            //             data: function(params) {
            //                 return {
            //                     search: params.term
            //                 };
            //             },
            //             processResults: function(data) {
            //                 return {
            //                     results: $.map(data, function(item) {
            //                         return {
            //                             id: item.id,
            //                             text: item.text
            //                         };
            //                     })
            //                 };
            //             }
            //         },
            //         minimumInputLength: 1,
            //         dropdownCssClass: 'form-control',
            //         selectionCssClass: 'form-control'
            //     });
            // }

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
                        uniqueEmployeeCode: true,
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
                    signature_image: {
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
                    signature_image: {
                        required: "Signature is Required",
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


            $.validator.addMethod("uniqueEmployeeCode", function(value, element) {
                var employeeCodes = [];

                $("input[name^='employee_code']").each(function() {
                    var employeeCodeValue = $(this).val();
                    if (employeeCodeValue) {
                        employeeCodes.push(employeeCodeValue);
                    }
                });

                return employeeCodes.indexOf(value) === employeeCodes.lastIndexOf(value);
            }, "Employee Code must be unique");

            let form_set_count = 2;
            let serial_number = parseInt("{{ getSPLBCount() }}", 10) + 1;
            const maxFormSets = 200;
            const minFormSets = 1;


            // $(document).on('click', ".add-row", function() {
            //     let currentFormSets = $('#form-wrapper .form-set').length;

            //     if (currentFormSets >= maxFormSets) {
            //         Swal.fire({
            //             icon: 'warning',
            //             title: 'Maximum Safety Petty Logbook CheckList Reached',
            //             text: 'You can only add up to 200 Safety Petty Logbook CheckList.',
            //             confirmButtonColor: '#3085d6'
            //         });
            //         return;
            //     }

            //     let newSerialNumber = 'SPLB-' + ('0000' + serial_number).slice(-5);

            //     var newFormSet = `
            //     <div class="form-set mb-3">
            //         <div class="card-header-inner">
            //             <h4 class="text-white">Safety Petty Logbook CheckList</h4>
            //         </div>
            //         <div class="d-flex justify-content-end">
            //             <button class="btn btn-primary add-row me-3" type="button"
            //                 id="add-row" style="width: 84px;">
            //                 Add
            //             </button>
            //             <button type="button" class="btn btn-danger remove-row">
            //                 <i class="fa-solid fa-trash"></i> Remove
            //             </button>
            //         </div>
            //         <div class="row">
            //             <div class="col-md-4">
            //                 <div class="form-group form-input">
            //                     <label class="form-label require">Serial Number</label>
            //                     <input type="text" name="serial_number[${form_set_count}]" class="form-control" placeholder="Serial Number" value="${newSerialNumber}" readonly>
            //                 </div>
            //             </div>

            //            <div class="col-md-4">
            //                 <div class="form-group form-input">
            //                     <label class="form-label require">Employee Name </label>
            //                     <select name="emp_id[${form_set_count}]"
            //                         class="form-control single-select emp-select" id="${form_set_count}" style="width: 100%">
            //                     <option value="">Select Employee Name</option>
            //                 </select>
            //                 </div>
            //             </div>

            //             <div class="col-md-4">
            //                 <div class="form-group form-input">
            //                     <label class="form-label require">Employee Code</label>
            //                     <input type="text" name="employee_code[${form_set_count}]"
            //                         class="form-control" placeholder="Employee Code"
            //                         value="">
            //                 </div>
            //             </div>

            //             <div class="col-md-4 mt-2">
            //                 <div class="form-group form-input">
            //                     <label class="form-label require">Unit</label>
            //                     <select name="unit_id[${form_set_count}]" id="unit_id-${form_set_count}"
            //                         class="form-control single-select" style="width: 100%">
            //                         <option value="">Select Unit</option>
            //                         @foreach ($unit as $list)
            //                             <option value="{{ encryptId($list->id) }}">
            //                                 {{ $list->unit_name }}</option>
            //                         @endforeach
            //                     </select>
            //                 </div>
            //             </div>

            //              <div class="col-md-4 mt-2">
            //                 <div class="form-group form-input">
            //                     <label class="form-label require">Department</label>
            //                     <select name="department_id[${form_set_count}]" id="department_id-${form_set_count}"
            //                         class="form-control single-select" style="width: 100%">
            //                         <option value="">Select Department</option>
            //                     </select>
            //                 </div>
            //             </div>

            //             <div class="col-md-4 mt-2">
            //                 <div class="form-group form-input">
            //                     <label class="form-label require">Date</label>
            //                     <input type="text" name ="date[${form_set_count}]" id="date-${form_set_count}"
            //                         class="form-control date-picker" placeholder="Date" value="">
            //                 </div>
            //             </div>

            //             <div class="col-md-4 mt-2">
            //                 <div class="form-group form-input">
            //                     <label class="form-label require">Amount</label>
            //                     <input type="text" name ="amount[${form_set_count}]" id="amount"
            //                         class="form-control" placeholder="Amount" value="">
            //                 </div>
            //             </div>

            //             <div class="col-md-4 mt-2">
            //                 <div class="form-group form-input">
            //                     <label class="form-label require">Amount Given By</label>
            //                     <input type="text" name ="amount_given_by[${form_set_count}]"
            //                     class="form-control" placeholder="Amount Given By" value="">
            //                 </div>
            //             </div>

            //             <div class="col-md-4 form-group form-input mb-2 mt-2">
            //                 @if (isset(Auth::user()->signature_upload))
            //                     <label class="form-label"
            //                         style="display: block; ">{{ __('inspection.signature') }}</label>
            //                     <img src="{{ admin_url('public/' . Auth::user()->signature_upload) }}"
            //                         alt="Signature Upload" style="width: 150px; margin-top:-10px">
            //                 @else
            //                     <div class="form-input col-md-12 mb-2">
            //                         <label class="form-label require">Signature</label>
            //                         <input type="file" name="signature_image[${form_set_count}]" id="signature_upload"
            //                             class="form-control form-control-sm" accept="image/*"
            //                             placeholder="Enter the image">
            //                         <small>Allowed file types: jpg, jpeg, png</small>
            //                         <div id="signature_upload" class="text-danger"></div>
            //                     </div>
            //                 @endif
            //             </div>

            //             <div class="col-md-4 mt-2">
            //                 <div class="form-group form-input">
            //                     <label class="form-label require">Amount Received By</label>
            //                     <input type="text" name ="amount_received_by[${form_set_count}]"
            //                     class="form-control" placeholder="Amount Received By" value="">
            //                 </div>
            //             </div>

            //             <div class="col-md-4 form-group form-input mb-2 mt-2">
            //                 @if (isset(Auth::user()->signature_upload))
            //                     <label class="form-label"
            //                         style="display: block; ">{{ __('inspection.signature') }}</label>
            //                     <img src="{{ admin_url('public/' . Auth::user()->signature_upload) }}"
            //                         alt="Signature Upload" style="width: 150px; margin-top:-10px">
            //                 @else
            //                     <div class="form-input col-md-12 mb-2">
            //                         <label class="form-label require">Signature</label>
            //                         <input type="file" name="signature_image[${form_set_count}]" id="signature_upload"
            //                             class="form-control form-control-sm" accept="image/*"
            //                             placeholder="Enter the image">
            //                         <small>Allowed file types: jpg, jpeg, png</small>
            //                         <div id="signature_upload" class="text-danger"></div>
            //                     </div>
            //                 @endif
            //             </div>

            //             <div class="col-md-12 mt-2">
            //                 <div class="form-group form-input">
            //                     <label class="form-label require">Description</label>
            //                     <textarea name="description[${form_set_count}]" class="form-control" placeholder="Description" rows="3"></textarea>
            //                 </div>
            //             </div>

            //             <div class="col-md-12 mt-2">
            //                 <div class="form-group form-input">
            //                     <label class="form-label require">Remark</label>
            //                     <textarea name="remark[${form_set_count}]" class="form-control" placeholder="Remark" rows="3"></textarea>
            //                 </div>
            //             </div>
            //         </div>
            //     </div>`;

            //     $('#form-wrapper').append(newFormSet);

            //     serial_number++;

            //     flatpickr(".date-picker", {
            //         dateFormat: "d-m-Y",
            //     });

            //     $('select[name^="emp_id["]').each(function() {
            //         $(this).select2({
            //             placeholder: "Select Employee Name",
            //             width: '100%'
            //         });
            //     });

            //     $('select[name^="department_id["]').each(function() {
            //         $(this).select2({
            //             placeholder: "Select Department",
            //             width: '100%'
            //         });
            //     });

            //     $('select[name^="unit_id["]').each(function() {
            //         $(this).select2({
            //             placeholder: "Select Unit",
            //             width: '100%'
            //         });
            //     });

            //     $("select[name='emp_id[" + form_set_count + "]']").rules('add', {
            //         required: true,
            //         messages: {
            //             required: 'Employee Name is required',
            //         }
            //     });

            //     $("input[name='employee_code[" + form_set_count + "]']").rules('add', {
            //         required: true,
            //         uniqueEmployeeCode: true,
            //         noSpaces: true,
            //         messages: {
            //             required: 'Employee Code is required',
            //             uniqueEmployeeCode: 'Employee Code must be unique',
            //             noSpaces: 'Employee Code cannot be empty or only spaces'
            //         }
            //     });

            //     $("select[name='department_id[" + form_set_count + "]']").rules('add', {
            //         required: true,
            //         messages: {
            //             required: 'Department is required',
            //         }
            //     });

            //     $("select[name='unit_id[" + form_set_count + "]']").rules('add', {
            //         required: true,
            //         messages: {
            //             required: 'Unit is required',
            //         }
            //     });

            //     $("input[name='date[" + form_set_count + "]']").rules('add', {
            //         required: true,
            //         messages: {
            //             required: 'Date is required',
            //         }
            //     });

            //     $("input[name='amount[" + form_set_count + "]']").rules('add', {
            //         required: true,
            //         noSpaces: true,
            //         messages: {
            //             required: 'Amount is required',
            //             noSpaces: 'Amount cannot be empty or only spaces'
            //         }
            //     });

            //     $("input[name='amount_given_by[" + form_set_count + "]']").rules('add', {
            //         required: true,
            //         noSpaces: true,
            //         messages: {
            //             required: 'Amount Given By is required',
            //             noSpaces: 'Amount Given By cannot be empty or only spaces'
            //         }
            //     });

            //     $("input[name='signature_image[" + form_set_count + "]']").rules('add', {
            //         required: true,
            //         messages: {
            //             required: 'Signature Image is required',
            //         }
            //     });

            //     $("input[name='amount_received_by[" + form_set_count + "]']").rules('add', {
            //         required: true,
            //         noSpaces: true,
            //         messages: {
            //             required: 'Amount Received By is required',
            //             noSpaces: 'Amount Received By cannot be empty or only spaces'
            //         }
            //     });

            //     $("textarea[name='description[" + form_set_count + "]']").rules('add', {
            //         required: true,
            //         noSpaces: true,
            //         messages: {
            //             required: 'Description is required',
            //             noSpaces: 'Description cannot be empty or only spaces'
            //         }
            //     });

            //     $("textarea[name='remark[" + form_set_count + "]']").rules('add', {
            //         required: true,
            //         noSpaces: true,
            //         messages: {
            //             required: 'Remark is required',
            //             noSpaces: 'Remark cannot be empty or only spaces'
            //         }
            //     });
            //     form_set_count++;
            //     updatePageIndices();
            //     initEmployeeSelect2()
            // });

            // $(document).on('click', '.remove-row', function() {
            //     let currentFormSets = $('#form-wrapper .form-set').length;

            //     if (currentFormSets <= minFormSets) {
            //         Swal.fire({
            //             icon: 'warning',
            //             title: 'Minimum Safety Petty Logbook CheckList Required',
            //             text: 'At least 1 Safety Petty Logbook CheckList is required.',
            //             confirmButtonColor: '#3085d6'
            //         });
            //         return;
            //     }
            //     $(this).closest('.form-set').remove();
            //     updatePageIndices();
            // });

            // function updatePageIndices() {
            //     $('#form-wrapper .form-set').each(function(index) {
            //         $(this).find("input[name^='serial_number']").val('SPLB-' + ('0000' + (index + 1)).slice(
            //             -5));

            //         $(this).find('input[name^="serial_number"]').attr('name', 'serial_number[' + (index +
            //             1) + ']');
            //         $(this).find('select[name^="emp_id"]').attr('name', 'emp_id[' + (index + 1) + ']');
            //         $(this).find('input[name^="employee_code"]').attr('name', 'employee_code[' + (index +
            //             1) + ']');
            //         $(this).find('select[name^="department_id"]').attr('name', 'department_id[' + (index +
            //             1) + ']');
            //         $(this).find('select[name^="unit_id"]').attr('name', 'unit_id[' + (index + 1) + ']');
            //         $(this).find('input[name^="date"]').attr('name', 'date[' + (index + 1) + ']');
            //         $(this).find('input[name^="amount"]').attr('name', 'amount[' + (index + 1) + ']');
            //         $(this).find('input[name^="amount_given_by"]').attr('name', 'amount_given_by[' + (
            //             index + 1) + ']');
            //         $(this).find('input[name^="amount_received_by"]').attr('name', 'amount_received_by[' + (
            //             index + 1) + ']');
            //         $(this).find('textarea[name^="remark"]').attr('name', 'remark[' + (index + 1) + ']');
            //     });
            // }

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
