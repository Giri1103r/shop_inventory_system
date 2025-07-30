@extends('admin.layouts.admin')
@section('title', 'Water Quality')
@section('pageurl', admin_url('safety/eye-wash-inspection/monthly/list'))
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
                                    <x-button-back
                                        href="{{ admin_url('safety/eye-wash-inspection/monthly/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form mx-3">
                                    <form method="POST" id="eyewashAdd"
                                        action="{{ admin_url('safety/eye-wash-inspection/monthly/add/submit') }}"
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
                                                            class="form-control" value="{{ old('inspection_date') }}">
                                                        <div class="input-group-addon input-group-text">
                                                            <span class="fa fa-calendar"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                @error('inspection_date')
                                                    <div class="error">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label require">{{ __('inspection.next_due') }}</label>


                                                    <div class="input-group date form-input custom-height">
                                                        <input type="text" name="next_due" id = "next_due"
                                                            class="form-control" value="{{ old('next_due') }}">
                                                        <div class="input-group-addon input-group-text">
                                                            <span class="fa fa-calendar"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                @error('next_due')
                                                    <div class="error">{{ $message }}</div>
                                                @enderror
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
                                                            <option value="{{ encryptId($location->id) }}"
                                                                {{ old('location_id.1') == encryptId($location->id) ? 'selected' : '' }}>
                                                                {{ $location->location_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                @error('location_id')
                                                    <div class="error">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">{{ __('inspection.unit') }}</label>
                                                    <select name="unit_id" id="unit_id"
                                                        class=" form-control single-select" style="width: 100%">
                                                        <option value="">Select Unit</option>

                                                    </select>
                                                </div>
                                                @error('unit_id')
                                                    <div class="error">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Shift</label>
                                                    <select name="shift_id" id="shift_id"
                                                        class=" form-control single-select" style="width: 100%">
                                                        <option value="">Select Shift</option>
                                                        @foreach ($shifts as $shift)
                                                            <option value="{{ encryptId($shift->id) }}"
                                                                {{ old('shift_id.1') == encryptId($shift->id) ? 'selected' : '' }}>
                                                                {{ $shift->shift }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                @error('shift_id')
                                                    <div class="error">{{ $message }}</div>
                                                @enderror
                                            </div>


                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label require">{{ __('inspection.frequency') }}</label>
                                                    <select name="frequency_id" id="frequency_id"
                                                        class=" form-control single-select" style="width: 100%">
                                                        <option value="">Select Frequency</option>
                                                        @foreach ($frequency as $frequency)
                                                            <option value="{{ encryptId($frequency->id) }}"
                                                                {{ old('frequency_id.1') == encryptId($frequency->id) ? 'selected' : '' }}>
                                                                {{ $frequency->frequency_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                @error('frequency_id')
                                                    <div class="error">{{ $message }}</div>
                                                @enderror
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
                                            {{-- @error('signature_image')
                                                <div class="error">{{ $message }}</div>
                                            @enderror --}}
                                        </div>
                                        <hr>
                                        <div class="form-wrapper">
                                            <div class="card-header-inner d-flex justify-content-between">
                                                <h4 class="text-white ms-2">Water Quality Checklist</h4>
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
                                                            class="form-control" value="{{ MEWSequence() }}" readonly>
                                                    </div>
                                                    @error('sr_no.1')
                                                        <div class="error">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.location') }}</label>
                                                        <select name="location[1]" id="location"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Location</option>
                                                            @foreach ($locations as $location)
                                                                <option value="{{ encryptId($location->id) }}"
                                                                    {{ old('location.1') == encryptId($location->id) ? 'selected' : '' }}>
                                                                    {{ $location->location_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    @error('location.1')
                                                        <div class="error">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.resource_code') }}</label>
                                                        <input type="text" name="resource_code[1]"
                                                            id = "resource_code" class="form-control"
                                                            value="{{ old('resource_code.1') }}">
                                                    </div>
                                                    @error('resource_code.1')
                                                        <div class="error">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.condition') }}</label>
                                                        <select name="condition[1]" id="condition"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Condition</option>
                                                            <option value="{{ encryptId(GOOD) }}"
                                                                {{ old('condition.1') == encryptId(GOOD) ? 'selected' : '' }}>
                                                                Good</option>
                                                            <option value="{{ encryptId(FAIR) }}"
                                                                {{ old('condition.1') == encryptId(FAIR) ? 'selected' : '' }}>
                                                                Fair</option>
                                                            <option value="{{ encryptId(POOR) }}"
                                                                {{ old('condition.1') == encryptId(POOR) ? 'selected' : '' }}>
                                                                Poor</option>
                                                        </select>
                                                    </div>
                                                    @error('condition.1')
                                                        <div class="error">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.value') }}</label>
                                                        <input type="text" name="value[1]" id = "value"
                                                            class="form-control" value="{{ old('value.1') }}">
                                                    </div>
                                                    @error('value.1')
                                                        <div class="error">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.hfsov') }}</label>
                                                        <input type="text" name="hfsov[1]" id = "hfsov"
                                                            class="form-control" value="{{ old('hfsov.1') }}">
                                                    </div>
                                                    @error('hfsov.1')
                                                        <div class="error">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.foot_pedal_value') }}</label>
                                                        <input type="text" name="foot_pedal[1]" id = "foot_pedal"
                                                            class="form-control" value="{{ old('foot_pedal.1') }}">
                                                    </div>
                                                    @error('foot_pedal.1')
                                                        <div class="error">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.eyewash_heads') }}</label>

                                                        <select name="eyewash_heads[1]" id="eyewash_heads"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select
                                                                {{ __('inspection.eyewash_heads') }}</option>
                                                            <option value="{{ encryptId(OK) }}"
                                                                {{ old('eyewash_heads.1') == encryptId(OK) ? 'selected' : '' }}>
                                                                Ok</option>
                                                            <option value="{{ encryptId(NOT_OK) }}"
                                                                {{ old('eyewash_heads.1') == encryptId(NOT_OK) ? 'selected' : '' }}>
                                                                Not Ok</option>
                                                        </select>
                                                    </div>
                                                    @error('eyewash_heads.1')
                                                        <div class="error">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.receptacle') }}</label>
                                                        <input type="text" name="receptacle[1]" id = "receptacle"
                                                            class="form-control" value="{{ old('receptacle.1') }}">
                                                    </div>
                                                    @error('receptacle.1')
                                                        <div class="error">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.water') }}</label>
                                                        <select name="water[1]" id="water"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Water</option>
                                                            <option value="{{ encryptId(GOOD) }}"
                                                                {{ old('condition.1') == encryptId(GOOD) ? 'selected' : '' }}>
                                                                Good</option>
                                                            <option value="{{ encryptId(FAIR) }}"
                                                                {{ old('condition.1') == encryptId(FAIR) ? 'selected' : '' }}>
                                                                Fair</option>
                                                            <option value="{{ encryptId(POOR) }}"
                                                                {{ old('condition.1') == encryptId(POOR) ? 'selected' : '' }}>
                                                                Poor</option>
                                                        </select>
                                                    </div>
                                                    @error('water.1')
                                                        <div class="error">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.pressure') }}</label>
                                                        <input type="text" name="pressure[1]" id = "pressure"
                                                            class="form-control" value="{{ old('pressure.1') }}">
                                                    </div>
                                                    @error('pressure.1')
                                                        <div class="error">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.temperature') }}</label>
                                                        <select name="temperature[1]" id="temperature"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select
                                                                {{ __('inspection.temperature') }}</option>
                                                            <option value="{{ encryptId(OK) }}"
                                                                {{ old('temperature.1') == encryptId(NORMAL) ? 'selected' : '' }}>
                                                                Normal</option>
                                                            <option value="{{ encryptId(NOT_OK) }}"
                                                                {{ old('temperature.1') == encryptId(ABNORMAL) ? 'selected' : '' }}>
                                                                Abnormal</option>
                                                        </select>
                                                    </div>
                                                    @error('temperature.1')
                                                        <div class="error">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-12 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label ">{{ __('inspection.remarks') }}</label>
                                                        <textarea name="remarks[1]" id="remarks" class="form-control" style="resize: none;">{{ old('remarks.1') }}</textarea>

                                                    </div>
                                                    @error('remarks.1')
                                                        <div class="error">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-2 text-right  mt-4">
                                                    <button class="btn btn-danger remove-row" type="button"
                                                        style="margin:10px;"><i class="fa fa-trash"></i></button>

                                                </div>
                                                <hr>
                                            </div>
                                        </div>

                                        <div class="submit-button" style="text-align: right;">
                                            <x-button-submit class="submit"></x-button-submit>
                                            <x-button-reset class="submit"></x-button-reset>
                                            <x-button-cancel
                                                href="{{ admin_url('safety/eye-wash-inspection/monthly/list') }}"></x-button-cancel>
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
            $(function() {
                $.validator.addMethod("noSpaces", function(value, element) {
                    return this.optional(element) || value.trim().length > 0;
                }, "This field cannot contain only spaces");

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
                        // signature_image: {
                        //     required: true,
                        //     filesize: 15728640,
                        // },
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
                        "temperature[1]": {
                            required: true,
                        },
                        "pressure[1]": {
                            required: true,
                            minlength: 3,
                            maxlength: 30
                        },
                        "water[1]": {
                            required: true,
                        },
                        "receptacle[1]": {
                            required: true,
                            minlength: 3,
                            maxlength: 30
                        },
                        "eyewash_heads[1]": {
                            required: true,
                        },
                        "foot_pedal[1]": {
                            required: true,
                            minlength: 3,
                            maxlength: 30
                        },
                        "hfsov[1]": {
                            required: true,
                            minlength: 3,
                            maxlength: 30
                        },
                        "value[1]": {
                            required: true,
                            minlength: 3,
                            maxlength: 30
                        },
                        "condition[1]": {
                            required: true,
                        },
                        "location[1]": {
                            required: true,
                        },
                        "resource_code[1]": {
                            required: true,
                            minlength: 3,
                            maxlength: 30
                        },
                        "remarks[1]": {

                            minlength: 3,
                            maxlength: 300,

                        }

                    },
                    messages: {
                        doc_no: {
                            required: "Document Number is Required",
                            minlength: "Minimum Characters should be 3",
                            maxlength: "Maximum Characters should not exceed 100",
                        },
                        issue_date: {
                            required: "Date Of Audit is required",
                        },
                        rev_date: {
                            required: "Revision Date required",
                        },
                        inspection_date: {
                            required: "Inspeciton Date is required",
                        },
                        // signature_image: {
                        //     required: "Signature is required",
                        //     filesize: "File size must be less than 15MB."
                        // },
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
                        "temperature[1]": {
                            required: "Please Select the Temperature",
                        },
                        "location[1]": {
                            required: "Please Select The Location",
                        },
                        "condition[1]": {
                            required: "Please add the condition of the Eye wash inspection",
                        },
                        "value[1]": {
                            required: "Please add the value",
                            minlength: "Please enter at least 3 characters.",
                            maxlength: "Please enter no more than 30 characters."
                        },
                        "hfsov[1]": {
                            required: "Please add the value of Hand free stay open",
                            minlength: "Please enter at least 3 characters.",
                            maxlength: "Please enter no more than 30 characters."
                        },
                        "foot_pedal[1]": {
                            required: "Please add foot pedal value",
                            minlength: "Please enter at least 3 characters.",
                            maxlength: "Please enter no more than 30 characters."
                        },
                        "eyewash_heads[1]": {
                            required: "Please add the name of Eye Wash & Head Shower",
                        },
                        "receptacle[1]": {
                            required: "Please add the name of receptable used",
                            minlength: "Please enter at least 3 characters.",
                            maxlength: "Please enter no more than 30 characters."
                        },
                        "water[1]": {
                            required: "Please select the water quality",
                        },
                        "pressure[1]": {
                            required: "Please add the pressure of the water",
                            minlength: "Please enter at least 3 characters.",
                            maxlength: "Please enter no more than 30 characters."
                        },
                        "resource_code[1]": {
                            required: "Please add the resource code",
                            minlength: "Please enter at least 3 characters.",
                            maxlength: "Please enter no more than 30 characters."
                        },
                        "remarks[1]": {
                            required: "Please add remarks",
                            minlength: "Minimum Characters should be 3",
                            maxlength: "Maximum Characters should not exceed 300",
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
                        console.log('test');
                        form.submit();

                    },
                    invalidHandler: function(event, validator) {
                        var errors = validator.numberOfInvalids();
                        console.log(errors + " field(s) are invalid");
                        validator.errorList.forEach(function(error) {
                            console.log("Field: " + error.element.name + ", Error: " +
                                error
                                .message);
                        });
                    }
                });
            });

            let form_set_count = 2;
            let formIndex = 1;
            const minFormSets = 1;
            const maxFormSets = 200;
            let serial_number = 2;

            $(document).ready(function() {
                $(document).on('click', '#add-row', function() {
                    let currentFormSets = $('.form-wrapper .form-set').length;



                    if (currentFormSets >= maxFormSets) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Maximum Monthly Eye Wash CheckList Reached',
                            text: 'You can only add up to 200 Monthly Eye Wash CheckList.',
                            confirmButtonColor: '#3085d6'
                        });
                        return;
                    }

                    let newSerialNumber = 'MEW-' + ('00000' + serial_number).slice(-5);

                    var newFormSet = `
                        <div class="row mt-4 form-set">

                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.sr_no') }}</label>
                                                        <input type="text" name="sr_no[${form_set_count}]" id = "sr_no"
                                                            class="form-control" value="${newSerialNumber}" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.location') }}</label>
                                                        <select name="location[${form_set_count}]" id="location[${form_set_count}]"
                                                            class=" form-control single-select location-select" style="width: 100%">
                                                            <option value="">Select Location</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.resource_code') }}</label>
                                                        <input type="text" name="resource_code[${form_set_count}]"
                                                            id = "resource_code[${form_set_count}]" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.condition') }}</label>
                                                        <select name="condition[${form_set_count}]" id="condition[${form_set_count}]"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Condition</option>
                                                            <option value="{{ encryptId(GOOD) }}">Good</option>
                                                            <option value="{{ encryptId(FAIR) }}">Fair</option>
                                                            <option value="{{ encryptId(POOR) }}">Poor</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.value') }}</label>
                                                        <input type="text" name="value[${form_set_count}]" id = "value[${form_set_count}]"
                                                            class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.hfsov') }}</label>
                                                        <input type="text" name="hfsov[${form_set_count}]" id = "hfsov[${form_set_count}]"
                                                            class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.foot_pedal_value') }}</label>
                                                        <input type="text" name="foot_pedal[${form_set_count}]" id = "foot_pedal[${form_set_count}]"
                                                            class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.eyewash_heads') }}</label>
                                                       <select name="eyewash_heads[${form_set_count}]" id="eyewash_heads[$${form_set_count}]"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select {{ __('inspection.eyewash_heads') }}</option>
                                                            <option value="{{ encryptId(OK) }}"
                                                                {{ old('eyewash_heads.1') == encryptId(OK) ? 'selected' : '' }}>
                                                                OK</option>
                                                            <option value="{{ encryptId(NOT_OK) }}"
                                                                {{ old('eyewash_heads.1') == encryptId(NOT_OK) ? 'selected' : '' }}>
                                                                NOT OK</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.receptacle') }}</label>
                                                        <input type="text" name="receptacle[${form_set_count}]" id = "receptacle[${form_set_count}]"
                                                            class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.water') }}</label>
                                                        <select name="water[${form_set_count}]" id="water[${form_set_count}]"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Water</option>
                                                            <option value="{{ encryptId(GOOD) }}">Good</option>
                                                            <option value="{{ encryptId(FAIR) }}">Fair</option>
                                                            <option value="{{ encryptId(POOR) }}">Poor</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.pressure') }}</label>
                                                        <input type="text" name="pressure[${form_set_count}]" id = "pressure[${form_set_count}]"
                                                            class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.temperature') }}</label>
                                                         <select name="temperature[${form_set_count}]" id="temperature[${form_set_count}]"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select
                                                                {{ __('inspection.temperature') }}</option>
                                                            <option value="{{ encryptId(OK) }}"
                                                                {{ old('temperature.1') == encryptId(NORMAL) ? 'selected' : '' }}>
                                                                Normal</option>
                                                            <option value="{{ encryptId(NOT_OK) }}"
                                                                {{ old('temperature.1') == encryptId(ABNORMAL) ? 'selected' : '' }}>
                                                                                                                                Abnormal</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label ">{{ __('inspection.remarks') }}</label>
                                                        <textarea name="remarks[${form_set_count}]" id="remarks[${form_set_count}]" class="form-control" style="resize: none;"></textarea>

                                                    </div>
                                                </div>
  <div class="col-md-2 text-right  mt-4">
                                                    <button class="btn btn-danger remove-row" type="button"
                                                        style="margin:10px;"><i class="fa fa-trash"></i></button>

                                                </div>
                                                <hr>
                                            </div>
                    `;

                    let newFormSetElement = $(newFormSet); // Convert string to jQuery object

                    let locationSelect = newFormSetElement.find('select[name^="location"]');
                    GetLocations(locationSelect); // Now this will work correctly

                    $('.form-wrapper').append(newFormSetElement);

                    $("select[name='location[" + form_set_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Please select the location',
                        }
                    });

                    $("input[name='resource_code[" + form_set_count + "]']").rules('add', {
                        required: true,
                        minlength: 3,
                        maxlength: 30,
                        messages: {
                            required: 'Please add the resource code',
                            minlength: "Please enter at least 3 characters.",
                            maxlength: "Please enter no more than 30 characters."
                        }
                    });

                    $("select[name='condition[" + form_set_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Please add the condition of the Eye wash inspection',
                        }
                    });

                    $("textarea[name='remarks[" + form_set_count + "]']").rules('add', {

                        minlength: 3,
                        maxlength: 300,
                        messages: {
                            required: 'Please add the remarks',
                            minlength: "Minimum Characters should be 3",
                            maxlength: "Maximum Characters should not exceed 100",
                        }
                    });

                    $("input[name='value[" + form_set_count + "]']").rules('add', {
                        required: true,
                        minlength: 3,
                        maxlength: 30,
                        messages: {
                            required: 'Please add the valve',
                            minlength: "Please enter at least 3 characters.",
                            maxlength: "Please enter no more than 30 characters."
                        }
                    });

                    $("input[name='hfsov[" + form_set_count + "]']").rules('add', {
                        required: true,
                        minlength: 3,
                        maxlength: 30,
                        messages: {
                            required: 'Please add the value of Hand free stay open',
                            minlength: "Please enter at least 3 characters.",
                            maxlength: "Please enter no more than 30 characters."
                        }
                    });

                    $("input[name='foot_pedal[" + form_set_count + "]']").rules('add', {
                        required: true,
                        minlength: 3,
                        maxlength: 30,
                        messages: {
                            required: 'Please add foot pedal value',
                            minlength: "Please enter at least 3 characters.",
                            maxlength: "Please enter no more than 30 characters."
                        }
                    });

                    $("select[name='eyewash_heads[" + form_set_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Please add the name of Eye Wash & Head Shower',
                        }
                    });

                    $("input[name='receptacle[" + form_set_count + "]']").rules('add', {
                        required: true,
                        minlength: 3,
                        maxlength: 30,
                        messages: {
                            required: 'Please add the name of receptable used',
                            minlength: "Please enter at least 3 characters.",
                            maxlength: "Please enter no more than 30 characters."
                        }
                    });

                    $("select[name='water[" + form_set_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Please select the water quality',
                        }
                    });

                    $("input[name='pressure[" + form_set_count + "]']").rules('add', {
                        required: true,
                        minlength: 3,
                        maxlength: 30,
                        messages: {
                            required: 'Please add the pressure of the water',
                            minlength: "Please enter at least 3 characters.",
                            maxlength: "Please enter no more than 30 characters."
                        }
                    });

                    $("select[name='temperature[" + form_set_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Please Select the Temperature',
                        }
                    });

                    serial_number++;
                    form_set_count++;
                    updatePageIndices();

                });
            });

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
                    let newSerialNumber = 'MEW-' + ('000000' + idx).slice(-6);
                    $(this).find("input[name^='sr_no']").val(newSerialNumber);

                    $(this).find('input[name^="sr_no"]').attr('name', 'sr_no[' + idx + ']');
                    $(this).find('select[name^="location"]').attr('name', 'location[' + idx + ']');
                    $(this).find('input[name^="resource_code"]').attr('name', 'resource_code[' + idx + ']');
                    $(this).find('select[name^="condition"]').attr('name', 'condition[' + idx + ']');
                    $(this).find('input[name^="value"]').attr('name', 'value[' + idx + ']');
                    $(this).find('input[name^="hfsov"]').attr('name', 'hfsov[' + idx + ']');
                    $(this).find('input[name^="foot_pedal"]').attr('name', 'foot_pedal[' + idx + ']');
                    $(this).find('select[name^="eyewash_heads"]').attr('name', 'eyewash_heads[' + idx + ']');
                    $(this).find('input[name^="receptacle"]').attr('name', 'receptacle[' + idx + ']');
                    $(this).find('select[name^="water"]').attr('name', 'water[' + idx + ']');
                    $(this).find('input[name^="pressure"]').attr('name', 'pressure[' + idx + ']');
                    $(this).find('select[name^="temperature"]').attr('name', 'temperature[' + idx + ']');
                    $(this).find('textarea[name^="remarks"]').attr('name', 'remarks[' + idx + ']');

                    $(this).find('select').select2();
                });
            }


            $(document).on('click', '.remove-row', function() {
                let currentFormSets = $('.form-wrapper .form-set').length;

                if (currentFormSets <= minFormSets) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Minimum One CheckList Required',
                        text: 'At least One Monthly Eyewash CheckList is required.',
                        confirmButtonColor: '#3085d6'
                    });
                    return;
                }
                $(this).closest('.form-set').remove();
                updatePageIndices();

            });
        </script>
    @endpush
