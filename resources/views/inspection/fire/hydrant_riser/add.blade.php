@extends('admin.layouts.admin')
@section('title', 'Hydrant and Riser')
@section('pageurl', admin_url('fire/hydrant-riser-inspection/list'))
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
                                        href="{{ admin_url('fire/hydrant-riser-inspection/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form mx-3">
                                    <form method="POST" id="eyewashAdd"
                                        action="{{ admin_url('fire/hydrant-riser-inspection/add/submit') }}"
                                        autocomplete="off" enctype="multipart/form-data">
                                        @csrf

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
                                                        <input type="text" name="issue_date" id = ""
                                                            class="form-control" placeholder="Issued Date"
                                                            value="{{ displaydateformat($document_no->issue_date) }}"
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
                                                    @error('inspection_date')
                                                        <div class="error">{{ $message }}</div>
                                                    @enderror
                                                </div>
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
                                                                {{ old('location_id') == encryptId($location->id) ? 'selected' : '' }}>
                                                                {{ $location->location_name }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('location_id')
                                                        <div class="error">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label require">{{ __('inspection.exact_location') }}</label>
                                                    <div class="input-group date form-input custom-height">
                                                        <input type="text" name="exact_location" id = "exact_location"
                                                            class="form-control" value="{{ old('exact_location') }}">
                                                    </div>
                                                </div>
                                                @error('next_due')
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
                                                                {{ old('shift_id') == encryptId($shift->id) ? 'selected' : '' }}>
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
                                                                {{ old('frequency_id') == encryptId($frequency->id) ? 'selected' : '' }}>
                                                                {{ $frequency->frequency_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                @error('frequency_id')
                                                    <div class="error">{{ $message }}</div>
                                                @enderror
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
                                            <input type="hidden" name="document_reference_id"
                                                value="{{ encryptId($document_no->id) }}">
                                        </div>
                                        <hr>
                                        <div class="form-wrapper">
                                            <div class="card-header-inner d-flex justify-content-between">
                                                <h4 class="text-white ms-3">Hydrant And Riser Inspection Checklist</h4>
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
                                                            class="form-label require">{{ __('inspection.location') }}</label>
                                                        <select name="location_check_id[1]" id="location_check_id[1]"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select {{ __('inspection.location') }}
                                                            </option>
                                                            @foreach ($locations as $location)
                                                                <option value="{{ encryptId($location->id) }}"
                                                                    {{ old('location_check_id') == encryptId($location->id) ? 'selected' : '' }}>
                                                                    {{ $location->location_name }}</option>
                                                            @endforeach
                                                        </select>
                                                        @error('location_check_id')
                                                            <div class="error">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label require">Hydrant No</label>
                                                        <input type="text" name="hydrant_no[1]" id = "hydrant_no"
                                                            class="form-control" value="{{ old('hydrant_no.1') }}">
                                                    </div>
                                                    @error('hydrant_no.1')
                                                        <div class="error">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label require">Lugs</label>
                                                        <select name="lugs[1]" id="lugs[1]"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Lugs</option>
                                                            <option value="{{ encryptId(1) }}">
                                                                Present</option>
                                                            <option value="{{ encryptId(0) }}">
                                                                Missing</option>

                                                        </select>
                                                    </div>

                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label require">Rubber Washer</label>
                                                        <select name="rubber_washer[1]" id="rubber_washer[1]"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Lugs</option>
                                                            <option value="{{ encryptId(1) }}">
                                                                Intact</option>
                                                            <option value="{{ encryptId(0) }}">
                                                                Damaged</option>

                                                        </select>
                                                    </div>

                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label require">Check Nut</label>
                                                        <select name="check_nut[1]" id="check_nut[1]"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Check Nut </option>
                                                            <option value="{{ encryptId(1) }}">
                                                                Present</option>
                                                            <option value="{{ encryptId(0) }}">
                                                                Missing</option>

                                                        </select>
                                                    </div>

                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label require">Spindle Wheel</label>
                                                        <select name="spindle_wheel[1]" id="spindle_wheel[1]"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Spindle Wheel </option>
                                                            <option value="{{ encryptId(1) }}">
                                                                Functional</option>
                                                            <option value="{{ encryptId(0) }}">
                                                                Non-functional</option>

                                                        </select>
                                                    </div>

                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label require">Blank Cap</label>
                                                        <select name="blank_cap[1]" id="blank_cap[1]"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Blank Cap </option>
                                                            <option value="{{ encryptId(1) }}">
                                                                Present</option>
                                                            <option value="{{ encryptId(0) }}">
                                                                Missing</option>

                                                        </select>
                                                    </div>

                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label require">Female Coupling</label>
                                                        <select name="female_coupling[1]" id="female_coupling[1]"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Female Coupling</option>
                                                            <option value="{{ encryptId(1) }}">
                                                                Functional</option>
                                                            <option value="{{ encryptId(0) }}">
                                                                Non-functional</option>

                                                        </select>
                                                    </div>

                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label require">Lever</label>
                                                        <select name="lever[1]" id="lever[1]"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Lever</option>
                                                            <option value="{{ encryptId(1) }}">
                                                                Functional</option>
                                                            <option value="{{ encryptId(0) }}">
                                                                Non-functional</option>

                                                        </select>
                                                    </div>

                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label require">Flow Test</label>
                                                        <select name="flow_test[1]" id="flow_test[1]"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Flow Test</option>
                                                            <option value="{{ encryptId(GOOD) }}"
                                                                {{ old('flow_test.1') == encryptId(GOOD) ? 'selected' : '' }}>
                                                                Good</option>
                                                            <option value="{{ encryptId(BAD) }}"
                                                                {{ old('flow_test.1') == encryptId(BAD) ? 'selected' : '' }}>
                                                                Bad</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.physical_condition') }}</label>
                                                        <select name="physical_condition[1]" id="physical_condition[1]"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Physical Condition</option>
                                                            <option value="{{ encryptId(GOOD) }}"
                                                                {{ old('physical_condition.1') == encryptId(GOOD) ? 'selected' : '' }}>
                                                                Good</option>
                                                            <option value="{{ encryptId(FAIR) }}"
                                                                {{ old('physical_condition.1') == encryptId(FAIR) ? 'selected' : '' }}>
                                                                Fair</option>
                                                            <option value="{{ encryptId(POOR) }}"
                                                                {{ old('physical_condition.1') == encryptId(POOR) ? 'selected' : '' }}>
                                                                Poor</option>
                                                        </select>
                                                    </div>
                                                    @error('physical_condition.1')
                                                        <div class="error">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label require">Condition of ISV</label>
                                                        <select name="condition_of_ivs[1]" id="condition_of_ivs[1]"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Condition of ISV </option>
                                                            <option value="{{ encryptId(1) }}">
                                                                Functional</option>
                                                            <option value="{{ encryptId(0) }}">
                                                                Non-functional</option>

                                                        </select>
                                                    </div>

                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label require">Approach</label>
                                                        <select name="approach[1]" id="approach[1]"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Approach</option>
                                                            <option value="{{ encryptId(1) }}">
                                                                Ok</option>
                                                            <option value="{{ encryptId(0) }}">
                                                                Not Ok</option>

                                                        </select>
                                                    </div>

                                                </div>

                                                <div class="col-md-8 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.remarks') }}</label>
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
                                        <div class="form-observation">
                                            <div class="row mt-4 form-obs">
                                                <div class="card-header-inner p-2">
                                                    <h4 class="text-white">Hydrant And Riser Observation</h4>
                                                </div>



                                                <div class="col-md-12 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.obs') }}</label>

                                                        <!-- Radio Buttons for Observation Needed -->
                                                        <div class="mb-2">
                                                            <label class="me-3">
                                                                <input type="radio" name="observation_needed"
                                                                    value="{{ encryptId(1) }}"> Yes
                                                            </label>
                                                            <label>
                                                                <input type="radio" name="observation_needed"
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
                                                href="{{ admin_url('fire/hydrant-riser-inspection/list') }}"></x-button-cancel>
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

                    $("input[name^='hydrant_no']").each(function() {
                        var itemCodeValue = $(this).val();
                        if (itemCodeValue) {
                            itemCodes.push(itemCodeValue);
                        }
                    });

                    return itemCodes.indexOf(value) === itemCodes.lastIndexOf(value);
                }, "Hydrant No must be unique");

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
                        exact_location: {
                            required: true,
                            maxlength: 255,
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
                        "location_check_id[1]": {
                            required: true,
                        },
                        "hydrant_no[1]": {
                            required: true,
                        },
                        "lugs[1]": {
                            required: true,
                        },
                        "rubber_washer[1]": {
                            required: true,
                        },
                        "check_nut[1]": {
                            required: true,
                        },
                        "spindle_wheel[1]": {
                            required: true,
                            uniqueItemCode: true,
                        },
                        "blank_cap[1]": {
                            required: true,
                        },
                        "female_coupling[1]": {
                            required: true,
                        },
                        "lever[1]": {
                            required: true,
                        },
                        "flow_test[1]": {
                            required: true,
                        },
                        "physical_condition[1]": {
                            required: true,
                        },
                        "condition_of_ivs[1]": {
                            required: true,
                        },
                        "approach[1]": {
                            required: true,

                        },
                        "remarks[1]": {
                            required: true,
                            minlength: 3,
                            maxlength: 300,
                        },
                        device_image: {
                            required: true,
                            // extension: "jpg",
                            filesize: 15728640,
                        },
                        observation_needed: {
                            required: true,
                        },
                        signature_image: {
                            required: true,
                            filesize: 15728640,
                        },

                    },
                    messages: {
                        doc_no: {
                            required: "Document Number is Required",
                            minlength: "Minimum Characters should be 3",
                            maxlength: "Maximum Characters should not exceed 100",
                        },
                        signature_image: {
                            required: 'Please upload your signature',
                            filesize: "File size should not exceed 15MB",
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
                        exact_location: {
                            required: "Exact Location is required",
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
                        'location_check_id[1]': {
                            required: "Please Select the Location",
                        },
                        "hydrant_no[1]": {
                            required: "Please Add the hydrant No",
                        },
                        "lugs[1]": {
                            required: "Please Select the lugs",
                        },
                        "rubber_washer[1]": {
                            required: "Please Enter the Rubber Washer",
                        },
                        "check_nut[1]": {
                            required: "Please Select The Check Nut",
                        },
                        "spindle_wheel[1]": {
                            required: "Please Select the Spindle Wheel",
                        },
                        "blank_cap[1]": {
                            required: "Please select the Blank Cap",
                        },
                        "female_coupling[1]": {
                            required: "Please add the Female Coupling",
                        },
                        "lever[1]": {
                            required: "Please select the Lever",
                        },
                        "flow_test[1]": {
                            required: "Please add the Flow Test",
                        },
                        "physical_condition[1]": {
                            required: "Please Select The Physical Condition",
                        },
                        "condition_of_ivs[1]": {
                            required: "Please Select The Condition of IVS",
                        },
                        "approach[1]": {
                            required: "Please select Approach",

                        },
                        "remarks[1]": {
                            required: "Please add remarks",
                            minlength: "Minimum Characters should be 3",
                            maxlength: "Maximum Characters should not exceed 300",
                        },
                        device_image: {
                            required: "Please upload an image.",
                            extension: "Only JPG files are allowed.",
                            filesize: "File size should not exceed 15MB",
                        },
                        observation_needed: {
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
                            title: 'Maximum Hydrant and Riser  CheckList Reached',
                            text: 'You can only add up to 200 Hydrant and Riser CheckList.',
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
                                                            class="form-label require">{{ __('inspection.location') }}</label>
                                                        <select name="location_check_id[${form_set_count}]" id="location_check_id[${form_set_count}]"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select {{ __('inspection.location') }}
                                                            </option>
                                                            @foreach ($locations as $location)
                                                                <option value="{{ encryptId($location->id) }}"
                                                                    {{ old('location_check_id') == encryptId($location->id) ? 'selected' : '' }}>
                                                                    {{ $location->location_name }}</option>
                                                            @endforeach
                                                        </select>
                                                        @error('location_check_id')
                                                            <div class="error">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">Hydrant No</label>
                                                        <input type="text" name="hydrant_no[${form_set_count}]"
                                                            id = "hydrant_no[${form_set_count}]" class="form-control"
                                                            value="{{ old('hydrant_no.1') }}">
                                                    </div>
                                                    @error('hydrant_no.1')
                                                        <div class="error">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">Lugs</label>
                                                        <select name="lugs[${form_set_count}]" id="lugs[${form_set_count}]"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Lugs</option>
                                                            <option value="{{ encryptId(1) }}">
                                                                Present</option>
                                                            <option value="{{ encryptId(0) }}">
                                                                Missing</option>

                                                        </select>
                                                    </div>

                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">Rubber Washer</label>
                                                        <select name="rubber_washer[${form_set_count}]" id="rubber_washer[${form_set_count}]"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Rubber Washer</option>
                                                            <option value="{{ encryptId(1) }}">
                                                                Intact</option>
                                                            <option value="{{ encryptId(0) }}">
                                                                Damaged</option>

                                                        </select>
                                                    </div>

                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">Check Nut</label>
                                                        <select name="check_nut[${form_set_count}]" id="check_nut[${form_set_count}]"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Check Nut </option>
                                                            <option value="{{ encryptId(1) }}">
                                                                Present</option>
                                                            <option value="{{ encryptId(0) }}">
                                                                Missing</option>

                                                        </select>
                                                    </div>

                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">Spindle Wheel</label>
                                                        <select name="spindle_wheel[${form_set_count}]" id="spindle_wheel[${form_set_count}]"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Spindle Wheel </option>
                                                            <option value="{{ encryptId(1) }}">
                                                                Functional</option>
                                                            <option value="{{ encryptId(0) }}">
                                                                Non-functional</option>

                                                        </select>
                                                    </div>

                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">Blank Cap</label>
                                                        <select name="blank_cap[${form_set_count}]" id="blank_cap[${form_set_count}]"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Blank Cap </option>
                                                            <option value="{{ encryptId(1) }}">
                                                                Present</option>
                                                            <option value="{{ encryptId(0) }}">
                                                                Missing</option>

                                                        </select>
                                                    </div>

                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">Female Coupling</label>
                                                        <select name="female_coupling[${form_set_count}]" id="female_coupling[${form_set_count}]"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Female Coupling</option>
                                                            <option value="{{ encryptId(1) }}">
                                                                Functional</option>
                                                            <option value="{{ encryptId(0) }}">
                                                                Non-functional</option>

                                                        </select>
                                                    </div>

                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">Lever</label>
                                                        <select name="lever[${form_set_count}]" id="lever[${form_set_count}]"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Lever</option>
                                                            <option value="{{ encryptId(1) }}">
                                                                Functional</option>
                                                            <option value="{{ encryptId(0) }}">
                                                                Non-functional</option>

                                                        </select>
                                                    </div>

                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label require">Flow Test</label>
                                                        <select name="flow_test[${form_set_count}]" id="flow_test[${form_set_count}]"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Flow Test</option>
                                                            <option value="{{ encryptId(GOOD) }}"
                                                                {{ old('flow_test.1') == encryptId(GOOD) ? 'selected' : '' }}>
                                                                Good</option>
                                                            <option value="{{ encryptId(BAD) }}"
                                                                {{ old('flow_test.1') == encryptId(BAD) ? 'selected' : '' }}>
                                                                Bad</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.physical_condition') }}</label>
                                                        <select name="physical_condition[${form_set_count}]" id="physical_condition[${form_set_count}]"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Physical Condition</option>
                                                            <option value="{{ encryptId(GOOD) }}"
                                                                {{ old('physical_condition.1') == encryptId(GOOD) ? 'selected' : '' }}>
                                                                Good</option>
                                                            <option value="{{ encryptId(FAIR) }}"
                                                                {{ old('physical_condition.1') == encryptId(FAIR) ? 'selected' : '' }}>
                                                                Fair</option>
                                                            <option value="{{ encryptId(POOR) }}"
                                                                {{ old('physical_condition.1') == encryptId(POOR) ? 'selected' : '' }}>
                                                                Poor</option>
                                                        </select>
                                                    </div>
                                                    @error('physical_condition.1')
                                                        <div class="error">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">Condition of ISV</label>
                                                        <select name="condition_of_ivs[${form_set_count}]" id="condition_of_ivs[${form_set_count}]"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Condition of ISV </option>
                                                            <option value="{{ encryptId(1) }}">
                                                                Functional</option>
                                                            <option value="{{ encryptId(0) }}">
                                                                Non-functional</option>

                                                        </select>
                                                    </div>

                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">Approach</label>
                                                              <select name="approach[${form_set_count}]" id="approach[${form_set_count}]"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Approach</option>
                                                            <option value="{{ encryptId(1) }}">
                                                                Ok</option>
                                                            <option value="{{ encryptId(0) }}">
                                                                Not Ok</option>

                                                        </select>


                                                    </div>

                                                </div>

                                                <div class="col-md-8 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.remarks') }}</label>
                                                        <textarea name="remarks[${form_set_count}]" id="remarks[${form_set_count}]" class="form-control" style="resize: none;">{{ old('remarks.1') }}</textarea>

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
                    `;



                    let newFormSetElement = $(newFormSet);

                    let locationSelect = newFormSetElement.find('select[name^="department"]');

                    $('.form-wrapper').append(newFormSetElement);

                    $("input[name='hydrant_no[" + form_set_count + "]']").rules('add', {
                        required: true,
                        uniqueItemCode: true,
                        messages: {
                            required: 'Please add the hydrant No',
                        }
                    });

                    $("select[name='location_check_id[" + form_set_count + "]']").rules('add', {
                        required: true,
                        uniqueItemCode: true,
                        messages: {
                            required: 'Please Select the Location',
                        }
                    });

                    $("select[name='lugs[" + form_set_count + "]']").rules('add', {
                        required: true,
                        uniqueItemCode: true,
                        messages: {
                            required: 'Please Select the Lugs',
                        }
                    });

                    $("select[name='rubber_washer[" + form_set_count + "]']").rules('add', {
                        required: true,
                        uniqueItemCode: true,
                        messages: {
                            required: 'Please Select the Rubber Washer',
                        }
                    });

                    $("select[name='check_nut[" + form_set_count + "]']").rules('add', {
                        required: true,
                        uniqueItemCode: true,
                        messages: {
                            required: 'Please Select the Check Nut',
                        }
                    });

                    $("select[name='spindle_wheel[" + form_set_count + "]']").rules('add', {
                        required: true,
                        uniqueItemCode: true,
                        messages: {
                            required: 'Please Select the Spindle Wheel',
                        }
                    });

                    $("select[name='blank_cap[" + form_set_count + "]']").rules('add', {
                        required: true,
                        uniqueItemCode: true,
                        messages: {
                            required: 'Please Select the Blank Cap',
                        }
                    });

                    $("select[name='female_coupling[" + form_set_count + "]']").rules('add', {
                        required: true,
                        uniqueItemCode: true,
                        messages: {
                            required: 'Please Select the Female Coupling',
                        }
                    });

                    $("select[name='lever[" + form_set_count + "]']").rules('add', {
                        required: true,
                        uniqueItemCode: true,
                        messages: {
                            required: 'Please Select the Female Lever',
                        }
                    });

                    $("select[name='flow_test[" + form_set_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: "Please Select the Flow Test result.",
                        }
                    });


                    $("select[name='physical_condition[" + form_set_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Please Select the Physical Condition',
                        }
                    });

                    $("select[name='condition_of_ivs[" + form_set_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Please Select the Condition of IVS',
                        }
                    });

                    $("textarea[name='remarks[" + form_set_count + "]']").rules('add', {
                        required: true,
                        minlength: 3,
                        maxlength: 300,
                        messages: {
                            required: 'Please add the remarks',
                            minlength: "Minimum Characters should be 3",
                            maxlength: "Maximum Characters should not exceed 300",
                        }
                    });

                    $("select[name='approach[" + form_set_count + "]']").rules('add', {
                        required: true,

                        messages: {
                            required: 'Please select the Approach',

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
                            title: 'Maximum Hydrant and Riser Inspection Observation Limit Reached',
                            text: 'You can only add up to 5 Hydrant and Riser Inspection Observation.',
                            confirmButtonColor: '#3085d6'
                        });
                        return;
                    }

                    var newObsSet = `
                        <div class="row mt-4 form-obs">
                                                <div class="card-header-inner p-2">
                                                    <h4 class="text-white">Hydrant and Riser Inspection Observation</h4>
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



            function updatePageIndices() {
                $('.form-wrapper .form-set').each(function(index) {
                    let idx = index + 1;

                    $(this).find('select[name^="location_check_id"]').attr('name', 'location_check_id[' + idx + ']');
                    $(this).find('input[name^="hydrant_no"]').attr('name', 'hydrant_no[' + idx + ']');
                    $(this).find('select[name^="lugs"]').attr('name', 'lugs[' + idx + ']');
                    $(this).find('select[name^="rubber_washer"]').attr('name', 'rubber_washer[' + idx + ']');
                    $(this).find('select[name^="check_nut"]').attr('name', 'check_nut[' + idx + ']');
                    $(this).find('select[name^="spindle_wheel"]').attr('name', 'spindle_wheel[' + idx + ']');
                    $(this).find('select[name^="blank_cap"]').attr('name', 'blank_cap[' + idx + ']');
                    $(this).find('select[name^="female_coupling"]').attr('name', 'female_coupling[' + idx + ']');
                    $(this).find('select[name^="lever"]').attr('name', 'lever[' + idx + ']');
                    $(this).find('select[name^="flow_test"]').attr('name', 'flow_test[' + idx + ']');
                    $(this).find('select[name^="physical_condition"]').attr('name', 'physical_condition[' + idx + ']');
                    $(this).find('select[name^="condition_of_ivs"]').attr('name', 'condition_of_ivs[' + idx + ']');
                    $(this).find('textarea[name^="approach"]').attr('name', 'approach[' + idx + ']');
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
                        text: 'At least One Hydrant and Riser Inspection Checklist is required.',
                        confirmButtonColor: '#3085d6'
                    });
                    return;
                }
                $(this).closest('.form-set').remove();
                updatePageIndices();

            });
        </script>
    @endpush
