@extends('admin.layouts.admin')
@section('title', 'Sprinklar System Inspection')
@section('pageurl', admin_url('fire/sprinkler-inspection/list'))
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
                                    <x-button-back href="{{ admin_url('fire/sprinkler-inspection/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form mx-3">
                                    <form method="POST" id="eyewashAdd"
                                        action="{{ admin_url('fire/sprinkler-inspection/add/submit') }}" autocomplete="off"
                                        enctype="multipart/form-data">
                                        @csrf

                                        <input type="hidden" name="document_reference_id"
                                            value="{{ encryptId($document_no->id) }}">
                                        <div class="row">
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">{{ __('inspection.doc_no') }}</label>
                                                    <input type="text" name="doc_no" id = "doc_no" class="form-control"
                                                        placeholder="Enter the Document Number"
                                                        value="{{ $document_no->doc_no }}" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label require">{{ __('inspection.issue_date') }}</label>

                                                    <div class="input-group date form-input custom-height">
                                                        <input type="text" name="issue_date" id = "issue_date"
                                                            class="form-control" placeholder="Issued Date"
                                                            value="{{ Displaydateformat($document_no->issue_date) }}"
                                                            readonly>
                                                        <div class="input-group-addon input-group-text">
                                                            <span class="fa fa-calendar"></span>
                                                        </div>
                                                    </div>


                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label require">{{ __('inspection.rev_date') }}</label>
                                                    <input type="text" name="rev_date" id = "rev_date"
                                                        class="form-control" value="{{ $document_no->rev_dt }}" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label require">{{ __('inspection.inspection_date') }}</label>
                                                    <div class="input-group date form-input custom-height">
                                                        <input type="text" name="inspection_date" id = "inspection_date"
                                                            class="form-control">
                                                        <div class="input-group-addon input-group-text">
                                                            <span class="fa fa-calendar"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label require">{{ __('inspection.next_due') }}</label>
                                                    <div class="input-group date form-input custom-height">
                                                        <input type="text" name="next_due" id = "next_due"
                                                            class="form-control">
                                                        <div class="input-group-addon input-group-text">
                                                            <span class="fa fa-calendar"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label require">{{ __('inspection.location') }}</label>
                                                    <select name="location_id" id="location_id"
                                                        class=" form-control single-select" style="width: 100%">
                                                        <option value="">Select {{ __('inspection.location') }}
                                                        </option>
                                                        @foreach ($locations as $location)
                                                            <option value="{{ encryptId($location->id) }}">
                                                                {{ $location->location_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">{{ __('inspection.unit') }}</label>
                                                    <select name="unit_id" id="unit_id"
                                                        class=" form-control single-select" style="width: 100%">
                                                        <option value="">Select Unit</option>

                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Shift</label>
                                                    <select name="shift_id" id="shift_id"
                                                        class=" form-control single-select" style="width: 100%">
                                                        <option value="">Select Shift</option>
                                                        @foreach ($shifts as $shift)
                                                            <option value="{{ encryptId($shift->id) }}">
                                                                {{ $shift->shift }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>


                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label require">{{ __('inspection.frequency') }}</label>
                                                    <select name="frequency_id" id="frequency_id"
                                                        class=" form-control single-select" style="width: 100%">
                                                        <option value="">Select Frequency</option>
                                                        @foreach ($frequency as $frequency)
                                                            <option value="{{ encryptId($frequency->id) }}">
                                                                {{ $frequency->frequency_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label require">{{ __('inspection.upload_image') }}</label>
                                                    <input type="file" name="device_image" class="form-control"
                                                        accept="image/*">
                                                </div>
                                            </div>
                                            {{-- <div class="col-md-4 form-group form-input mb-2">
                                                @if (isset(Auth::user()->signature_upload))
                                                    <label class="form-label"
                                                        style="display: block; ">{{ __('inspection.signature') }}</label>
                                                    <img src="{{ admin_url(Auth::user()->signature_upload) }}"
                                                        alt="Signature Upload" style="width: 150px; margin-top:-10px">
                                                @else
                                                    <div class="form-input col-md-12 mb-2">
                                                        <label class="form-label require">Signature</label>
                                                        <input type="file" name="signature_image"
                                                            id="signature_upload" class="form-control form-control-sm"
                                                            accept="image/*" placeholder="Enter the image">
                                                        <small>Allowed file types: jpg, jpeg, png</small>
                                                        <div id="signature_upload" class="text-danger"></div>
                                                    </div>
                                                @endif
                                            </div> --}}
                                        </div>
                                        <hr>
                                        <div class="form-wrapper">
                                            <div class="card-header-inner d-flex justify-content-between">
                                                <h4 class="text-white ms-3">Sprinklar System Inspection Checklist</h4>
                                                <button class="btn btn-primary add-row mb-2 " type="button"
                                                    id="add-row"
                                                    style="margin-left: 10px;  margin-right: 10px; width: 84px;">
                                                    Add
                                                </button>
                                            </div>
                                            <div class="row mt-4 form-set">


                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.sr_no') }}</label>
                                                        <input type="text" name="sr_no[1]" id = "sr_no"
                                                            class="form-control"
                                                            value="{{ FireSequence(SPRINKLAR_SYSTEM_INSPECTION) }}"
                                                            readonly>
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.department') }}</label>
                                                        <select name="department[1]" id="department"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Department</option>
                                                            @foreach ($department as $department)
                                                                <option value="{{ encryptId($department->id) }}">
                                                                    {{ $department->department_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.exact_location') }}</label>
                                                        <input type="text" name="exact_location[1]"
                                                            id = "exact_location" class="form-control exact_location"
                                                            placeholder="Exact Location">
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.resource_code') }}</label>
                                                        <input type="text" name="resource_code[1]"
                                                            id = "resource_code" class="form-control" value="">
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.quantity') }}</label>
                                                        <input type="number" name="quantity[1]" id = "quantity"
                                                            class="form-control" value="">
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.water_leakage') }}</label>
                                                        <select name="water_leakage[1]" id="water_leakage"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Of Water Leakage In Pipe</option>
                                                            <option value="{{ encryptId(OK) }}">
                                                                {{ __('inspection.ok') }}</option>
                                                            <option value="{{ encryptId(NOT_OK) }}">
                                                                {{ __('inspection.not_ok') }}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.painting') }}</label>
                                                        <select name="painting[1]" id="painting"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Of Condition Of Painting</option>
                                                            <option value="{{ encryptId(OK) }}">
                                                                {{ __('inspection.ok') }}</option>
                                                            <option value="{{ encryptId(NOT_OK) }}">
                                                                {{ __('inspection.not_ok') }}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.QBD') }}</label>
                                                        <select name="qbd[1]" id="qbd"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Of Quality By Design Condition
                                                            </option>
                                                            <option value="{{ encryptId(OK) }}">
                                                                {{ __('inspection.ok') }}</option>
                                                            <option value="{{ encryptId(NOT_OK) }}">
                                                                {{ __('inspection.not_ok') }}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.condition_of_flow_meter') }}</label>
                                                        <select name="condition_of_flow_meter[1]"
                                                            id="condition_of_flow_meter"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Status Of Flow Meter Condition</option>
                                                            <option value="{{ encryptId(OK) }}">
                                                                {{ __('inspection.ok') }}</option>
                                                            <option value="{{ encryptId(NOT_OK) }}">
                                                                {{ __('inspection.not_ok') }}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.main_isolation') }}</label>
                                                        <select name="main_isolation[1]" id="main_isolation"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Status Of Main Isolation Valve Condition
                                                            </option>
                                                            <option value="{{ encryptId(OK) }}">
                                                                {{ __('inspection.ok') }}</option>
                                                            <option value="{{ encryptId(NOT_OK) }}">
                                                                {{ __('inspection.not_ok') }}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.drain_condition') }}</label>
                                                        <select name="drain_condition[1]" id="drain_condition"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Status Of Drain Valve Condition</option>
                                                            <option value="{{ encryptId(OK) }}">
                                                                {{ __('inspection.ok') }}</option>
                                                            <option value="{{ encryptId(NOT_OK) }}">
                                                                {{ __('inspection.not_ok') }}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-8 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.remarks') }}</label>
                                                        <textarea name="remarks[1]" id="remarks" class="form-control" style="resize: none;"></textarea>

                                                    </div>
                                                </div>

                                                <div class="col-md-2 text-right  mt-4">
                                                    <button class="btn btn-danger remove-row" type="button"
                                                        style="margin:10px;"><i class="fa fa-trash"></i></button>

                                                </div>
                                                <hr>
                                            </div>
                                        </div>
                                        <div class="form-observation">
                                            <div class="row mt-4 form-obs form-input">
                                                <div class="card-header-inner p-2">
                                                    <h4 class="text-white">Sprinklar System Inspection Observation</h4>
                                                </div>

                                                <div class="mb-2">
                                                    <label class="me-3">
                                                        <input type="radio" name="observation"
                                                            value="{{ encryptId(1) }}" class="validate-radio-required">
                                                        Yes
                                                    </label>
                                                    <label>
                                                        <input type="radio" name="observation"
                                                            value="{{ encryptId(2) }}" class="validate-radio-required">
                                                        No
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="submit-button" style="text-align: right;">
                                            <x-button-submit class="submit"></x-button-submit>
                                            <x-button-reset class="submit"></x-button-reset>
                                            <x-button-cancel
                                                href="{{ admin_url('fire/sprinkler-inspection/list') }}"></x-button-cancel>
                                        </div>

                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>


    @stop

    @push('script')
        <script type="text/javascript" nonce="projectcab">
            // location based unit

            $(document).on('change', '#location_id', function() {
                var locationId = $(this).val();
                if (locationId) {
                    $.ajax({
                        url: "{{ admin_url('unit/ajax-list') }}/" + locationId + "/0",
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            $('#unit_id').empty().append(
                                '<option value="">Select unit</option>');
                            $.each(data, function(key, value) {
                                $('#unit_id').append('<option value="' + value
                                    .id + '">' + value.name + '</option>');
                            });
                            $('#unit_id').trigger('change.');
                        },
                        error: function(xhr) {
                            alert('Error fetching unit. Please try again.');
                        }
                    });
                } else {
                    $('#unit_id').empty().append('<option value="">Select unit</option>');
                    $('#unit_id').trigger('change.');
                }
            });
            $(document).ready(function() {
                $('#resetform').on('click', function(e) {
                    e.preventDefault();
                    location.reload();
                });

                var toDatepicker = flatpickr("#next_due", {
                    dateFormat: "d-m-Y",
                });

                var fromDatepicker = flatpickr("#inspection_date", {
                    dateFormat: "d-m-Y",
                    onChange: function(selectedDates) {
                        if (selectedDates.length > 0) {
                            var startDate = selectedDates[0];

                            var nextDay = new Date(startDate);
                            nextDay.setDate(startDate.getDate() + 1);

                            toDatepicker.set('minDate', nextDay);

                        }
                    }
                });
            });
            $(function() {
                $.validator.addMethod("noSpaces", function(value, element) {
                    return this.optional(element) || value.trim().length > 0;
                }, "This field cannot contain only spaces");

                $.validator.addMethod("uniqueItemCode", function(value, element) {
                    var itemCodes = [];

                    $("input[name^='resource_code']").each(function() {
                        var itemCodeValue = $(this).val();
                        if (itemCodeValue) {
                            itemCodes.push(itemCodeValue);
                        }
                    });

                    return itemCodes.indexOf(value) === itemCodes.lastIndexOf(value);
                }, "Resource code must be unique");

                $('#eyewashAdd').validate({
                    rules: {
                        doc_no: {
                            required: true,
                            minlength: 3,
                            maxlength: 100,
                            noSpaces: true,
                        },
                        issue_date: {
                            required: true,
                        },
                        inspection_date: {
                            required: true,
                        },
                        location_id: {
                            required: true,
                        },
                        shift_id: {
                            required: true,
                        },
                        next_due: {
                            required: true,
                        },
                        unit_id: {
                            required: true,
                        },
                        frequency_id: {
                            required: true,
                        },
                        identification_no: {
                            required: true,
                            minlength: 3,
                            maxlength: 100,
                            noSpaces: true,
                        },
                        forklift_type: {
                            required: true,
                        },
                        "department[1]": {
                            required: true,
                        },
                        "exact_location[1]": {
                            required: true,
                        },
                        "resource_code[1]": {
                            required: true,
                            minlength: 3,
                            maxlength: 30,
                        },
                        "quantity[1]": {
                            required: true,
                        },
                        "drain_condition[1]": {
                            required: true,
                            uniqueItemCode: true,
                        },
                        "main_isolation[1]": {
                            required: true,
                        },
                        "condition_of_flow_meter[1]": {
                            required: true,
                        },
                        "painting[1]": {
                            required: true,
                        },
                        "water_leakage[1]": {
                            required: true,
                        },
                        "remarks[1]": {
                            required: true,
                            minlength: 3,
                            maxlength: 600,
                        },
                        "qbd[1]": {
                            required: true,
                        },

                        device_image: {
                            required: true,
                            // extension: "jpg",
                            filesize: 15728640,
                        },
                        observation: {
                            required: true,
                        },
                        // signature_image: {
                        //     required: true,
                        //     filesize: 15728640,
                        // },

                    },
                    messages: {
                        doc_no: {
                            required: "Document Number is Required",
                            minlength: "Minimum Characters should be 3",
                            maxlength: "Maximum Characters should not exceed 100",
                        },
                        // signature_image: {
                        //     required: 'Please upload your signature',
                        //      filesize: "File size should not exceed 15MB",
                        // },
                        issue_date: {
                            required: "Date Of Audit is required",
                        },
                        rev_date: {
                            required: "Revision Date required",
                        },
                        inspection_date: {
                            required: "Inspeciton Date is required",
                        },
                        location_id: {
                            required: "Location is required",
                        },
                        shift_id: {
                            required: "Shift is required",
                        },
                        next_due: {
                            required: "Next due is required",
                        },
                        unit_id: {
                            required: "Unit is required",
                        },
                        frequency_id: {
                            required: "Frequency is required",
                        },
                        "department[1]": {
                            required: "Please add the Department",
                        },
                        "exact_location[1]": {
                            required: "Please enter the exact Location",
                        },
                        "resource_code[1]": {
                            required: "Please add the resource code",
                            minlength: "Minimum Characters should be 3",
                            maxlength: "Maximum Characters should not exceed 30",
                        },
                        "quantity[1]": {
                            required: "Please add the Quantity",
                        },
                        "water_leakage[1]": {
                            required: 'Please select the water leakage status',
                        },
                        "painting[1]": {
                            required: 'Please select the condition of painting',
                        },
                        "qbd[1]": {
                            required: 'Please select the Quality By Design condition',
                        },
                        "condition_of_flow_meter[1]": {
                            required: 'Please select the flow meter condition',
                        },
                        "main_isolation[1]": {
                            required: 'Please select the main isolation valve condition',
                        },
                        "drain_condition[1]": {
                            required: 'Please select the drain valve condition',
                        },
                        "remarks[1]": {
                            required: 'Please add the remarks',
                            minlength: "Minimum Characters should be 3",
                            maxlength: "Maximum Characters should not exceed 600",
                        },
                        device_image: {
                            required: "Please upload an image.",
                            // extension: "Only JPG files are allowed.",
                            filesize: "File size should not exceed 15MB",
                        },
                        observation: {
                            required: "Please add observation",
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
                    }
                });
            });

            let form_set_count = 2;
            let formIndex = 1;
            const minFormSets = 1;
            const maxFormSets = 200;
            let serial_number = 2;
            const maxObsSets = 5;

            $(document).ready(function() {
                $(document).on('click', '#add-row', function() {
                    let currentFormSets = $('.form-wrapper .form-set').length;



                    if (currentFormSets >= maxFormSets) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Maximum Sprinklar System Inspection CheckList Reached',
                            text: 'You can only add up to 200 Sprinklar System Inspection CheckList.',
                            confirmButtonColor: '#3085d6'
                        });
                        return;
                    }

                    let newSerialNumber = 'HTR-' + ('00000' + serial_number).slice(-5);

                    var newFormSet = `
                        <div class="row mt-4 form-set">


                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.sr_no') }}</label>
                                                        <input type="text" name="sr_no[${form_set_count}]" id = "sr_no-${form_set_count}"
                                                            class="form-control"
                                                            value="{{ FireSequence(SPRINKLAR_SYSTEM_INSPECTION) }}"
                                                            readonly>
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-2">
                                                            <div class="form-group form-input">
                                                                <label
                                                                    class="form-label require">{{ __('inspection.department') }}</label>
                                                                <select name="department[${form_set_count}]" id="department-${form_set_count}"
                                                                    class=" form-control single-select"
                                                                    style="width: 100%">
                                                                    <option value="">Select Department</option>

                                                                </select>
                                                            </div>
                                                        </div>

<div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.exact_location') }}</label>
                                                        <input type="text" name="exact_location[${form_set_count}]"
                                                            id = "exact_location[${form_set_count}]"
                                                            class="form-control exact_location"
                                                            placeholder="Exact Location"
                                                            >

                                                    </div>
                                                </div>project/list
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.resource_code') }}</label>
                                                        <input type="text" name="resource_code[${form_set_count}]"
                                                            id = "resource_code-${form_set_count}" class="form-control" value="">
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.quantity') }}</label>
                                                        <input type="number" name="quantity[${form_set_count}]" id = "quantity-${form_set_count}"
                                                            class="form-control" value="">
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.water_leakage') }}</label>
                                                        <select name="water_leakage[${form_set_count}]" id="water_leakage-${form_set_count}"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Of Water Leakage In Pipe</option>
                                                            <option value="{{ encryptId(OK) }}">
                                                                {{ __('inspection.ok') }}</option>
                                                            <option value="{{ encryptId(NOT_OK) }}">
                                                                {{ __('inspection.not_ok') }}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.painting') }}</label>
                                                        <select name="painting[${form_set_count}]" id="painting-${form_set_count}"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Of Condition Of Painting</option>
                                                            <option value="{{ encryptId(OK) }}">
                                                                {{ __('inspection.ok') }}</option>
                                                            <option value="{{ encryptId(NOT_OK) }}">
                                                                {{ __('inspection.not_ok') }}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.QBD') }}</label>
                                                        <select name="qbd[${form_set_count}]" id="qbd-${form_set_count}"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Of Quality By Design Condition
                                                            </option>
                                                            <option value="{{ encryptId(OK) }}">
                                                                {{ __('inspection.ok') }}</option>
                                                            <option value="{{ encryptId(NOT_OK) }}">
                                                                {{ __('inspection.not_ok') }}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.condition_of_flow_meter') }}</label>
                                                        <select name="condition_of_flow_meter[${form_set_count}]"
                                                            id="condition_of_flow_meter-${form_set_count}"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Status Of Flow Meter Condition</option>
                                                            <option value="{{ encryptId(OK) }}">
                                                                {{ __('inspection.ok') }}</option>
                                                            <option value="{{ encryptId(NOT_OK) }}">
                                                                {{ __('inspection.not_ok') }}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.main_isolation') }}</label>
                                                        <select name="main_isolation[${form_set_count}]" id="main_isolation-${form_set_count}"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Status Of Main Isolation Valve Condition
                                                            </option>
                                                            <option value="{{ encryptId(OK) }}">
                                                                {{ __('inspection.ok') }}</option>
                                                            <option value="{{ encryptId(NOT_OK) }}">
                                                                {{ __('inspection.not_ok') }}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.drain_condition') }}</label>
                                                        <select name="drain_condition[${form_set_count}]" id="drain_condition-${form_set_count}"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Status Of Drain Valve Condition</option>
                                                            <option value="{{ encryptId(OK) }}">
                                                                {{ __('inspection.ok') }}</option>
                                                            <option value="{{ encryptId(NOT_OK) }}">
                                                                {{ __('inspection.not_ok') }}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-8 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.remarks') }}</label>
                                                        <textarea name="remarks[${form_set_count}]" id="remarks-${form_set_count}" class="form-control" style="resize: none;"></textarea>

                                                    </div>
                                                </div>

                                                 <div class="col-md-2 text-right  mt-4">
                                                    <button class="btn btn-danger remove-row" type="button"
                                                        style="margin:10px;"><i class="fa fa-trash"></i></button>

                                                </div>
                                                <hr>
                                            </div>
                    `;

                    let newFormSetElement = $(newFormSet);

                    let departmentSelect = newFormSetElement.find('select[name^="department"]');
                    GetDepartment(departmentSelect);

                    let locationSelect = newFormSetElement.find('select[name^="location"]');
                    GetLocations(locationSelect);

                    $('.form-wrapper').append(newFormSetElement);

                    $("input[name='sr_no[" + form_set_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Serial number is required',
                        }
                    });

                    $("select[name='department[" + form_set_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Please select the department',
                        }
                    });

                    $("input[name='resource_code[" + form_set_count + "]']").rules('add', {
                        required: true,
                        minlength: 3,
                        maxlength: 30,
                        messages: {
                            required: 'Please enter the resource code',
                            minlength: "Minimum Characters should be 3",
                            maxlength: "Maximum Characters should not exceed 30",
                        }
                    });

                    $("input[name='quantity[" + form_set_count + "]']").rules('add', {
                        required: true,
                        number: true,
                        messages: {
                            required: 'Please specify the quantity',
                            number: 'Quantity must be a valid number',
                        }
                    });
                    $("input[name='exact_location[" + form_set_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Please enter the Exact Location',
                        }
                    });

                    $("select[name='water_leakage[" + form_set_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Please select the water leakage status',
                        }
                    });

                    $("select[name='painting[" + form_set_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Please select the condition of painting',
                        }
                    });

                    $("select[name='qbd[" + form_set_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Please select the Quality By Design condition',
                        }
                    });

                    $("select[name='condition_of_flow_meter[" + form_set_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Please select the flow meter condition',
                        }
                    });

                    $("select[name='main_isolation[" + form_set_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Please select the main isolation valve condition',
                        }
                    });

                    $("select[name='drain_condition[" + form_set_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Please select the drain valve condition',
                        }
                    });

                    $("textarea[name='remarks[" + form_set_count + "]']").rules('add', {
                        required: true,
                        minlength: 3,
                        maxlength: 300,
                        messages: {
                            required: 'Please enter remarks',
                            minlength: "Minimum Characters should be 3",
                            maxlength: "Maximum Characters should not exceed 300",
                        }
                    });



                    serial_number++;
                    form_set_count++;
                    updatePageIndices();

                });

                $(document).on('click', '#add-obs', function() {
                    let observationFormsets = $('.form-observation .form-obs').length;

                    if (currentFormSets >= maxObsSets) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Maximum Sprinklar System Inspection Observation Limit Reached',
                            text: 'You can only add up to 5 Sprinklar System Inspection Observation.',
                            confirmButtonColor: '#3085d6'
                        });
                        return;
                    }

                    var newObsSet = `
                        <div class="row mt-4 form-obs">
                                                <div class="card-header-inner p-2">
                                                    <h4 class="text-white">Sprinklar System Inspection Observation</h4>
                                                </div>

                                                <div class="d-flex justify-content-end align-items-center gap-2 m-2">
                                                    <button class="btn btn-primary add-row" type="button" id="add-row"
                                                        style="width: 120px;">
                                                        Add
                                                    </button>
                                                    <button class="btn btn-primary add-obs" type="button" id="add-obs"
                                                        style="width: 150px;">
                                                        Add Observation
                                                    </button>
                                                    <button type="button"
                                                        class="btn btn-danger remove-row d-flex align-items-center"
                                                        style="width: 120px;">
                                                        <i class="fa-solid fa-trash me-2"></i> Remove
                                                    </button>
                                                </div>

                                                <div class="col-md-12 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.obs') }}</label>
                                                        <textarea name="observation[1]" id="remarks" class="form-control" style="resize: none;"></textarea>

                                                    </div>
                                                </div>

                                            </div>
                    `;

                });
            });

            function GetDepartment(selectElement) {
                $.ajax({
                    type: "GET",
                    url: "{{ admin_url('fire/hooter-inspection/get/department') }}",
                    success: function(response) {
                        if (response.length > 0) {
                            let options = `<option value="">Select Department</option>`;
                            response.forEach(department => {
                                options +=
                                    `<option value="${department.id}">${department.department_name}</option>`;
                            });
                            $(selectElement).html(options).trigger('change');
                        }
                    }
                });
            }

            function GetLocations(selectElement) {
                $.ajax({
                    type: "GET",
                    url: "{{ admin_url('safety/eye-wash-inspection/monthly/get/locations') }}",
                    success: function(response) {
                        console.log(response);
                        if (response.length > 0) {
                            let options = `<option value="">Select Location</option>`;
                            response.forEach(location => {
                                options +=
                                    `<option value="${location.id}">${location.location_name}</option>`;
                            });
                            $(selectElement).html(options).trigger('change');
                        }
                    }
                });
            }

            function updatePageIndices() {
                $('.form-wrapper .form-set').each(function(index) {
                    let idx = index + 1;
                    let newSerialNumber = 'FEX-' + ('000000' + idx).slice(-6);
                    
                    $(this).find("input[name^='sr_no']").val(newSerialNumber);
                    $(this).find("input[name^='sr_no']").attr('name', 'sr_no[' + idx + ']');
                    $(this).find("input[name^='exact_location']").attr('name', 'exact_location[' + idx + ']');
                    $(this).find("select[name^='department']").attr('name', 'department[' + idx + ']');
                    $(this).find("input[name^='resource_code']").attr('name', 'resource_code[' + idx + ']');
                    $(this).find("input[name^='quantity']").attr('name', 'quantity[' + idx + ']');
                    $(this).find("select[name^='water_leakage']").attr('name', 'water_leakage[' + idx + ']');
                    $(this).find("select[name^='painting']").attr('name', 'painting[' + idx + ']');
                    $(this).find("select[name^='qbd']").attr('name', 'qbd[' + idx + ']');
                    $(this).find("select[name^='condition_of_flow_meter']").attr('name', 'condition_of_flow_meter[' +
                        idx + ']');
                    $(this).find("select[name^='main_isolation']").attr('name', 'main_isolation[' + idx + ']');
                    $(this).find("select[name^='drain_condition']").attr('name', 'drain_condition[' + idx + ']');
                    $(this).find("textarea[name^='remarks']").attr('name', 'remarks[' + idx + ']');

                    $(this).find('select').select2();
                });
            }



            $(document).on('click', '.remove-row', function() {
                let currentFormSets = $('.form-wrapper .form-set').length;

                if (currentFormSets <= minFormSets) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Minimum One CheckList Required',
                        text: 'At least One Sprinklar System Inspection Checklist is required.',
                        confirmButtonColor: '#3085d6'
                    });
                    return;
                }
                $(this).closest('.form-set').remove();
                updatePageIndices();

            });
        </script>
    @endpush
