@extends('admin.layouts.admin')
@section('title', 'Isolation Valve Inspection ')
@section('pageurl', admin_url('fire/isolating-valve-inspection/list'))
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
                                    <x-button-back
                                        href="{{ admin_url('fire/isolating-valve-inspection/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form mx-3">
                                    <form method="POST" id="eyewashAdd"
                                        action="{{ admin_url('fire/isolating-valve-inspection/add/submit') }}"
                                        autocomplete="off" enctype="multipart/form-data">
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
                                                        <option value="">Select
                                                            {{ __('inspection.location') }}
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
                                                <h4 class="text-white ms-3">Isolation Valve Inspection Checklist
                                                </h4>
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
                                                            value="{{ FireSequence(ISOLATION_VALVE_INSPECTION) }}"
                                                            readonly>
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.location_isv') }}</label>
                                                        <input type="text" name="location_isv[1]" id = "location_isv"
                                                            class="form-control" value="">
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
                                                            class="form-label require">{{ __('inspection.size_isv') }}</label>
                                                        <input type="number" name="size_isv[1]" id = "size_isv"
                                                            class="form-control" value="">
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.isv_status') }}</label>
                                                        <select name="isv_status[1]" id="isv_status"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Status Of Valve</option>
                                                            <option value="{{ encryptId(FUNCTIONAL) }}">
                                                                {{ __('inspection.functional') }}</option>
                                                            <option value="{{ encryptId(NON_FUNCTIONAL) }}">
                                                                {{ __('inspection.non_functional') }}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.wheel_operation') }}</label>
                                                        <select name="wheel_operation[1]" id="wheel_operation"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Status Of ISV Operation
                                                                Wheel
                                                            </option>
                                                            <option value="{{ encryptId(FUNCTIONAL) }}">
                                                                {{ __('inspection.functional') }}</option>
                                                            <option value="{{ encryptId(NON_FUNCTIONAL) }}">
                                                                {{ __('inspection.non_functional') }}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.leakage') }}</label>
                                                        <select name="leakage[1]" id="leakage"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Status Of ISV Operation
                                                                Wheel
                                                            </option>
                                                            <option value="{{ encryptId(YES) }}">
                                                                {{ __('inspection.no') }}</option>
                                                            <option value="{{ encryptId(NO) }}">
                                                                {{ __('inspection.yes') }}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.valve_type') }}</label>
                                                        <select name="type[1]" id="type"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Valve Type</option>
                                                            @foreach ($types as $type)
                                                                <option value="{{ $type->id }}">
                                                                    {{ $type->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2 .valve_type_others d-none">
                                                    <div class="form-group form-input">
                                                        <label class="form-label require">Other</label>
                                                        <input type="text" name="valve_type_others[1]"
                                                            id="valve_type_others" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.open') }}</label>
                                                        <select name="open[1]" id="open"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Status</option>
                                                            <option value="{{ encryptId(OPEN) }}">
                                                                Opened</option>
                                                            <option value="{{ encryptId(CLOSE) }}">
                                                                Closed</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.close') }}</label>
                                                        <select name="close[1]" id="close"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Status</option>
                                                            <option value="{{ encryptId(OPEN) }}">
                                                                Opened</option>
                                                            <option value="{{ encryptId(CLOSE) }}">
                                                                Closed</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 mb-2">
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
                                            <div class="row mt-4 form-obs">
                                                <div class="card-header-inner p-2">
                                                    <h4 class="text-white">Isolation Valve Inspection Observation</h4>
                                                </div>
                                                <div class="col-md-12 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.obs') }}</label>

                                                        <!-- Radio Buttons for Observation Needed -->
                                                        <div class="mb-2">
                                                            <label class="me-3">
                                                                <input type="radio" name="observation"
                                                                    value="{{ encryptId(1) }}"> Yes
                                                            </label>
                                                            <label>
                                                                <input type="radio" name="observation"
                                                                    value="{{ encryptId(2) }}"> No
                                                            </label>
                                                        </div>


                                                    </div>
                                                </div>

                                            </div>
                                        </div>

                                        <div class="submit-button" style="text-align: right;">
                                            <x-button-submit class="submit"></x-button-submit>
                                            <x-button-reset class="submit"></x-button-reset>
                                            <x-button-cancel
                                                href="{{ admin_url('fire/isolating-valve-inspection/list') }}"></x-button-cancel>
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
            // display the valve types

            $('#type').change(function() {
                var selectedValue = $(this).val();

                if (selectedValue == '5') {
                    $('#valve_type_others').closest('.col-md-4').removeClass('d-none');
                } else {
                    $('#valve_type_others').closest('.col-md-4').addClass('d-none');

                }
            });
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
                        "resource_code[1]": {
                            required: true,
                            uniqueItemCode: true,
                            minlength: 3,
                            maxlength: 30,
                        },
                        "type[1]": {
                            required: true,
                        },
                        "location_isv[1]": {
                            required: true,
                            minlength: 3,
                            maxlength: 30,
                        },
                        "size_isv[1]": {
                            required: true,
                            number: true,
                        },
                        "isv_status[1]": {
                            required: true,
                        },
                        "wheel_operation[1]": {
                            required: true,
                        },
                        "leakage[1]": {
                            required: true,
                        },
                        "open[1]": {
                            required: true,
                        },
                        "close[1]": {
                            required: true,
                        },
                        "remarks[1]": {
                            required: true,
                            minlength: 3,
                            maxlength: 600,
                        },

                        "valve_type_others[1]": {
                            required: function() {
                                return $('#type').val() ==
                                    '5';
                            },
                            minlength: 3,
                            maxlength: 100,
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
                        //      filesize: 15728640,
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
                        //     filesize: 'File size should not exceed 15MB',
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
                        "resource_code[1]": {
                            required: "Please add the resource code",
                            minlength: "Minimum Characters should be 3",
                            maxlength: "Maximum Characters should not exceed 30",
                        },
                        "type[1]": {
                            required: "Please select the valve type",
                        },
                        "location_isv[1]": {
                            required: "Please enter the location",
                            minlength: "Minimum Characters should be 3",
                            maxlength: "Maximum Characters should not exceed 30",
                        },
                        "size_isv[1]": {
                            required: "Please specify the size",
                            number: "Size must be a valid number"
                        },
                        "isv_status[1]": {
                            required: "Please select the status of the valve",
                        },
                        "wheel_operation[1]": {
                            required: "Please select the status of ISV operation wheel",
                        },
                        "leakage[1]": {
                            required: "Please select the leakage status",
                        },
                        "open[1]": {
                            required: "Please select the open status"
                        },
                        "close[1]": {
                            required: "Please select the close status"
                        },
                        "remarks[1]": {
                            required: "Please add remarks",
                            minlength: "Minimum Characters should be 3",
                            maxlength: "Maximum Characters should not exceed 600",
                        },
                        "valve_type_others[1]": {
                            required: "This field is required",
                            minlength: "Minimum Characters should be 3",
                            maxlength: "Maximum Characters should not exceed 100",
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
                            title: 'Maximum Fire Extinguisher Inspection CheckList Reached',
                            text: 'You can only add up to 200 Fire Extinguisher Inspection CheckList.',
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
                                                            value="{{ FireSequence(ISOLATION_VALVE_INSPECTION) }}"
                                                            readonly>
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.location_isv') }}</label>
                                                        <input type="text" name="location_isv[${form_set_count}]" id = "location_isv-${form_set_count}"
                                                            class="form-control" value="">
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.resource_code') }}</label>
                                                        <input type="text" name="resource_code[${form_set_count}]" id = "resource_code-${form_set_count}"
                                                            class="form-control" value="">
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.size_isv') }}</label>
                                                        <input type="number" name="size_isv[${form_set_count}]" id = "size_isv-${form_set_count}"
                                                            class="form-control" value="">
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.isv_status') }}</label>
                                                        <select name="isv_status[${form_set_count}]" id="isv_status-${form_set_count}"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Status Of Valve</option>
                                                            <option value="{{ encryptId(FUNCTIONAL) }}">
                                                                {{ __('inspection.functional') }}</option>
                                                            <option value="{{ encryptId(NON_FUNCTIONAL) }}">
                                                                {{ __('inspection.non_functional') }}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.wheel_operation') }}</label>
                                                        <select name="wheel_operation[${form_set_count}]" id="wheel_operation-${form_set_count}"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Status Of ISV Operation Wheel</option>
                                                            <option value="{{ encryptId(FUNCTIONAL) }}">
                                                                {{ __('inspection.functional') }}</option>
                                                            <option value="{{ encryptId(NON_FUNCTIONAL) }}">
                                                                {{ __('inspection.non_functional') }}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.leakage') }}</label>
                                                        <select name="leakage[${form_set_count}]" id="leakage-${form_set_count}"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Status Of ISV Operation Wheel</option>
                                                            <option value="{{ encryptId(YES) }}">
                                                                {{ __('inspection.no') }}</option>
                                                            <option value="{{ encryptId(NO) }}">
                                                                {{ __('inspection.yes') }}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                               <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label require">{{ __('inspection.valve_type') }}</label>
                                                        <select name="type[${form_set_count}]" id="type-${form_set_count}" class="form-control single-select valve-type" data-count="${form_set_count}" style="width: 100%">
                                                            <option value="">Select Valve Type</option>
                                                            @foreach ($types as $type)
                                                                <option value="{{ $type->id }}">
                                                                    {{ $type->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                    <div class="col-md-4 mb-2 valve_type_others d-none" data-count="${form_set_count}">
                                                        <div class="form-group form-input">
                                                            <label class="form-label require">Other</label>
                                                            <input type="text" name="valve_type_others[${form_set_count}]" id="valve_type_others-${form_set_count}" class="form-control others">
                                                        </div>
                                                    </div>

                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.open') }}</label>
                                                        <select name="open[${form_set_count}]" id="open-${form_set_count}"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Status</option>
                                                            <option value="{{ encryptId(OPEN) }}">
                                                                Opened</option>
                                                            <option value="{{ encryptId(CLOSE) }}">
                                                                Closed</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.close') }}</label>
                                                        <select name="close[${form_set_count}]" id="close-${form_set_count}"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Status</option>
                                                            <option value="{{ encryptId(OPEN) }}">
                                                                Opened</option>
                                                            <option value="{{ encryptId(CLOSE) }}">
                                                                Closed</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-6 mb-2">
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

                    $(document).on('change', '.valve-type', function() {
                        var selectedValue = $(this).val();
                        var count = $(this).data('count');


                        var otherFieldBlock = $('.valve_type_others[data-count="' + count + '"]');

                        if (selectedValue == '5') {
                            otherFieldBlock.removeClass('d-none');
                        } else {
                            otherFieldBlock.addClass('d-none');
                        }
                    });


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

                    $("input[name='location_isv[" + form_set_count + "]']").rules('add', {
                        required: true,
                        minlength: 3,
                        maxlength: 30,
                        messages: {
                            required: 'Please enter the location',
                            minlength: "Minimum Characters should be 3",
                            maxlength: "Maximum Characters should not exceed 30",
                        }
                    });

                    $("input[name='resource_code[" + form_set_count + "]']").rules('add', {
                        required: true,
                        uniqueItemCode: true,
                        minlength: 3,
                        maxlength: 30,
                        messages: {
                            required: 'Please enter the resource code',
                            uniqueItemCode: 'Resource code must be unique',
                            minlength: "Minimum Characters should be 3",
                            maxlength: "Maximum Characters should not exceed 30",
                        }
                    });

                    $("input[name='size_isv[" + form_set_count + "]']").rules('add', {
                        required: true,
                        number: true,
                        messages: {
                            required: 'Please specify the size',
                            number: 'Size must be a valid number',
                        }
                    });
                    $("input[name='valve_type_others[" + form_set_count + "]']").rules('add', {
                        required: true,
                        minlength: 3,
                        maxlength: 100,

                        messages: {
                            required: 'This field is required',
                            minlength: "Minimum Characters should be 3",
                            maxlength: "Maximum Characters should not exceed 100",
                        }
                    });

                    $("select[name='isv_status[" + form_set_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Please select the status of the valve',
                        }
                    });

                    $("select[name='wheel_operation[" + form_set_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Please select the status of ISV operation wheel',
                        }
                    });

                    $("select[name='leakage[" + form_set_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Please select the leakage status',
                        }
                    });

                    $("select[name='type[" + form_set_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Please select the valve type',
                        }
                    });

                    $("select[name='open[" + form_set_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Please select the open status',
                        }
                    });

                    $("select[name='close[" + form_set_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Please select the close status',
                        }
                    });

                    $("textarea[name='remarks[" + form_set_count + "]']").rules('add', {
                        required: true,
                        minlength: 3,
                        maxlength: 600,
                        messages: {
                            required: 'Please enter remarks',
                            minlength: "Minimum Characters should be 3",
                            maxlength: "Maximum Characters should not exceed 600",
                        }
                    });


                    serial_number++;
                    form_set_count++;
                    updatePageIndices();

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
                    $(this).find("input[name^='location_isv']").attr('name', 'location_isv[' + idx + ']');
                    $(this).find("input[name^='resource_code']").attr('name', 'resource_code[' + idx + ']');
                    $(this).find("input[name^='size_isv']").attr('name', 'size_isv[' + idx + ']');
                    $(this).find("select[name^='isv_status']").attr('name', 'isv_status[' + idx + ']');
                    $(this).find("select[name^='wheel_operation']").attr('name', 'wheel_operation[' + idx + ']');
                    $(this).find("select[name^='leakage']").attr('name', 'leakage[' + idx + ']');
                    $(this).find("select[name^='type']").attr('name', 'type[' + idx + ']');
                    $(this).find("select[name^='open']").attr('name', 'open[' + idx + ']');
                    $(this).find("select[name^='close']").attr('name', 'close[' + idx + ']');
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
                        text: 'At least One Isolation Valve Inspection is required.',
                        confirmButtonColor: '#3085d6'
                    });
                    return;
                }
                $(this).closest('.form-set').remove();
                updatePageIndices();

            });
        </script>
    @endpush
