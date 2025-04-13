@extends('admin.layouts.admin')
@section('title', 'Sand Bucket Inspection Add')
@section('pageurl', admin_url('fire/fire-sand-bucket-inspection/list'))
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
                                        href="{{ admin_url('fire/fire-sand-bucket-inspection/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form mx-3">
                                    <form method="POST" id="eyewashAdd"
                                        action="{{ admin_url('fire/fire-sand-bucket-inspection/add/submit') }}"
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
                                                    <input type="text" name="issue_date" id = ""
                                                        class="form-control" placeholder="Issued Date"
                                                        value="{{ displaydateformat($document_no->issue_date) }}" readonly>
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
                                                    <input type="text" name="inspection_date" id = "inspection_date"
                                                        class="form-control" value="{{ old('inspection_date') }}">
                                                    @error('inspection_date')
                                                        <div class="error">{{ $message }}</div>
                                                    @enderror
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
                                                        class="form-label require">{{ __('inspection.next_due') }}</label>
                                                    <input type="text" name="next_due" id = "next_due"
                                                        class="form-control" value="{{ old('next_due') }}">
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
                                                        @foreach ($units as $unit)
                                                            <option value="{{ encryptId($unit->id) }}"
                                                                {{ old('unit_id') == encryptId($unit->id) ? 'selected' : '' }}>
                                                                {{ $unit->unit_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                @error('unit_id')
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
                                            <div class="col-md-4 form-group form-input mb-2">
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
                                            </div>
                                            <input type="hidden" name="document_reference_id"
                                                value="{{ encryptId($document_no->id) }}">
                                        </div>
                                        <hr>
                                        <div class="form-wrapper">
                                            <div class="row mt-4 form-set">
                                                <div class="card-header-inner p-2">
                                                    <h4 class="text-white">Sand Bucket Inspection</h4>
                                                </div>

                                                <div class="d-flex justify-content-end align-items-center gap-2 m-2">
                                                    <button class="btn btn-primary add-row" type="button" id="add-row"
                                                        style="min-width: 130px;">
                                                        Add
                                                    </button>
                                                    {{-- <button class="btn btn-primary add-obs" type="button" id="add-obs"
                                                        style="min-width: 160px;">
                                                        Add Observation
                                                    </button> --}}
                                                    <button type="button"
                                                        class="btn btn-danger remove-row d-flex align-items-center"
                                                        style="min-width: 130px;">
                                                        <i class="fa-solid fa-trash me-2"></i> Remove
                                                    </button>
                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.location') }}</label>
                                                        <select name="location[1]" id="location"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select {{ __('inspection.location') }}
                                                            </option>
                                                            @foreach ($locations as $location)
                                                                <option value="{{ encryptId($location->id) }}"
                                                                    {{ old('location') == encryptId($location->id) ? 'selected' : '' }}>
                                                                    {{ $location->location_name }}</option>
                                                            @endforeach
                                                        </select>
                                                        @error('location.1')
                                                            <div class="error">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.fire_sand_bucket_stand_no') }}</label>
                                                        <input type="text" name="fire_sand_bucket_stand_no[1]"
                                                            id = "fire_sand_bucket_stand_no" class="form-control"
                                                            value="{{ old('fire_sand_bucket_stand_no.1') }}">
                                                    </div>
                                                    @error('fire_sand_bucket_stand_no.1')
                                                        <div class="error">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.fire_sand_bucket_no') }}</label>
                                                        <input type="text" name="fire_sand_bucket_no[1]"
                                                            id = "fire_sand_bucket_no" class="form-control"
                                                            value="{{ old('fire_sand_bucket_no.1') }}">
                                                    </div>
                                                    @error('fire_sand_bucket_no.1')
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
                                                            class="form-label require">{{ __('inspection.fire_bucket_condition') }}</label>
                                                        <select name="fire_bucket_condition[1]" id="fire_bucket_condition"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Condition</option>
                                                            <option value="{{ encryptId(GOOD) }}"
                                                                {{ old('fire_bucket_condition.1') == encryptId(GOOD) ? 'selected' : '' }}>
                                                                Good</option>
                                                            <option value="{{ encryptId(FAIR) }}"
                                                                {{ old('fire_bucket_condition.1') == encryptId(FAIR) ? 'selected' : '' }}>
                                                                Fair</option>
                                                            <option value="{{ encryptId(POOR) }}"
                                                                {{ old('fire_bucket_condition.1') == encryptId(POOR) ? 'selected' : '' }}>
                                                                Poor</option>
                                                        </select>
                                                    </div>
                                                    @error('fire_bucket_condition.1')
                                                        <div class="error">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.paint_condition') }}</label>
                                                        <select name="paint_condition[1]" id="paint_condition"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Condition</option>
                                                            <option value="{{ encryptId(GOOD) }}"
                                                                {{ old('paint_condition.1') == encryptId(GOOD) ? 'selected' : '' }}>
                                                                Good</option>
                                                            <option value="{{ encryptId(FAIR) }}"
                                                                {{ old('paint_condition.1') == encryptId(FAIR) ? 'selected' : '' }}>
                                                                Fair</option>
                                                            <option value="{{ encryptId(POOR) }}"
                                                                {{ old('paint_condition.1') == encryptId(POOR) ? 'selected' : '' }}>
                                                                Poor</option>
                                                        </select>
                                                    </div>
                                                    @error('paint_condition.1')
                                                        <div class="error">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.qualtiy_quantity_sand') }}</label>
                                                        <select name="qualtiy_quantity_sand[1]" id="qualtiy_quantity_sand"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Quantity and Quality of Sand
                                                            </option>
                                                            <option value="{{ encryptId(GOOD) }}"
                                                                {{ old('qualtiy_quantity_sand.1') == encryptId(ADEQUATE) ? 'selected' : '' }}>
                                                                Adequate</option>
                                                            <option value="{{ encryptId(FAIR) }}"
                                                                {{ old('qualtiy_quantity_sand.1') == encryptId(INADEQUATE) ? 'selected' : '' }}>
                                                                Inadequate</option>

                                                        </select>
                                                    </div>
                                                    @error('qualtiy_quantity_sand.1')
                                                        <div class="error">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.approach') }}</label>
                                                        <input type="text" name="approach[1]" id = "approach"
                                                            class="form-control" value="{{ old('approach.1') }}">
                                                    </div>
                                                    @error('approach.1')
                                                        <div class="error">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="col-md-12 mb-2">
                                                        <div class="form-group form-input">
                                                            <label
                                                                class="form-label require">{{ __('inspection.remarks') }}</label>
                                                            <textarea name="remarks" id="remarks" class="form-control" style="resize: none;"></textarea>

                                                        </div>
                                                    </div>
                                                    @error('remarks.1')
                                                        <div class="error">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-observation">
                                            <div class="row mt-4 form-obs">
                                                <div class="card-header-inner p-2">
                                                    <h4 class="text-white">Sand Bucket Inspection Observation</h4>
                                                </div>

                                                <div class="col-md-12 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.obs') }}</label>

                                                        <div class="mb-2">
                                                            <label class="me-3">
                                                                <input type="radio" name="observation_needed"
                                                                    value="{{ encryptId(1) }}"
                                                                    class="validate-radio-required"> Yes
                                                            </label>
                                                            <label>
                                                                <input type="radio" name="observation_needed"
                                                                    value="{{ encryptId(2) }}"
                                                                    class="validate-radio-required"> No
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
                                                href="{{ admin_url('safety/forklift-inspection/monthly/list') }}"></x-button-cancel>
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
                flatpickr("#issue_date", {
                    dateFormat: "d-m-Y",
                });
                flatpickr("#inspection_date", {
                    dateFormat: "d-m-Y",
                });
                flatpickr("#next_due", {
                    dateFormat: "d-m-Y",
                    minDate: new Date(),
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
                }, "Resource Code must be unique");

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
                        "fire_sand_bucket_stand_no[1]": {
                            required: true,
                        },
                        "fire_sand_bucket_no[1]": {
                            required: true,
                        },
                        "condition[1]": {
                            required: true,
                        },
                        "fire_bucket_condition[1]": {
                            required: true,
                        },
                        "paint_condition[1]": {
                            required: true,
                        },
                        "approach[1]": {
                            required: true,
                        },
                        "location[1]": {
                            required: true,
                        },
                        "qualtiy_quantity_sand[1]": {
                            required: true,
                        },
                        "remarks[1]": {
                            required: true,
                        },

                        observation: {
                            required: true,
                        },
                        signature_image: {
                            required: true,
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
                        'location[1]': {
                            required: "Please Select the Location",
                        },
                        "fire_sand_bucket_stand_no[1]": {
                            required: "Please Select the Fire Sand Bucket Stand Number",
                        },
                        "fire_sand_bucket_no[1]": {
                            required: "Please Select the Fire Sand Bucket Number",
                        },
                        "condition[1]": {
                            required: "Please Enter the Condition",
                        },
                        "fire_bucket_condition[1]": {
                            required: "Please Enter the Fire Bucket Condition",
                        },
                        "paint_condition[1]": {
                            required: "Please Enter the Paint Condition",
                        },
                        "remarks[1]": {
                            required: "Please Enter the Remarks",
                        },

                        "approach[1]": {
                            required: "Please Enter the Approach",
                        },
                        "qualtiy_quantity_sand[1]": {
                            required: "Please select the Quality and Quantity of Sand",
                        },
                        device_image: {
                            required: "Please upload an image.",
                            extension: "Only JPG files are allowed.",
                            filesize: "Image must be under 2MB."
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
                            title: 'Maximum Sand Bucket Inspection CheckList Reached',
                            text: 'You can only add up to 200 Sand Bucket Inspection CheckList.',
                            confirmButtonColor: '#3085d6'
                        });
                        return;
                    }

                    let newSerialNumber = 'HTR-' + ('00000' + serial_number).slice(-5);

                    var newFormSet = `
                        <div class="row mt-4 form-set">
                                                <div class="card-header-inner p-2">
                                                    <h4 class="text-white">Sand Bucket Inspection</h4>
                                                </div>

                                                <div class="d-flex justify-content-end gap-0 m-2">
                                                    <button class="btn btn-primary add-row me-3" type="button"
                                                        id="add-row" style="width: 84px;">
                                                        Add
                                                    </button>
                                                    <button type="button" class="btn btn-danger remove-row">
                                                        <i class="fa-solid fa-trash"></i> Remove
                                                    </button>

                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.location') }}</label>
                                                        <select name="location[${form_set_count}]" id="location"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select {{ __('inspection.location') }}
                                                            </option>
                                                            @foreach ($locations as $location)
                                                                <option value="{{ encryptId($location->id) }}"
                                                                    {{ old('location') == encryptId($location->id) ? 'selected' : '' }}>
                                                                    {{ $location->location_name }}</option>
                                                            @endforeach
                                                        </select>

                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.fire_sand_bucket_stand_no') }}</label>
                                                        <input type="text" name="fire_sand_bucket_stand_no[${form_set_count}]"
                                                            id = "fire_sand_bucket_stand_no" class="form-control"
                                                           >
                                                    </div>

                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.fire_sand_bucket_no') }}</label>
                                                        <input type="text" name="fire_sand_bucket_no[${form_set_count}]"
                                                            id = "fire_sand_bucket_no" class="form-control"
                                                            >
                                                    </div>

                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.condition') }}</label>
                                                        <select name="condition[${form_set_count}]" id="condition"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Condition</option>
                                                            <option value="{{ encryptId(GOOD) }}"
                                                                >
                                                                Good</option>
                                                            <option value="{{ encryptId(FAIR) }}"
                                                                >
                                                                Fair</option>
                                                            <option value="{{ encryptId(POOR) }}"
                                                                >
                                                                Poor</option>
                                                        </select>
                                                    </div>

                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.fire_bucket_condition') }}</label>
                                                        <select name="fire_bucket_condition[${form_set_count}]" id="condition"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Condition</option>
                                                            <option value="{{ encryptId(GOOD) }}"
                                                                >
                                                                Good</option>
                                                            <option value="{{ encryptId(FAIR) }}"
                                                                >
                                                                Fair</option>
                                                            <option value="{{ encryptId(POOR) }}"
                                                                >
                                                                Poor</option>
                                                        </select>
                                                    </div>

                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.paint_condition') }}</label>
                                                        <select name="paint_condition[${form_set_count}]" id="condition"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Condition</option>
                                                            <option value="{{ encryptId(GOOD) }}"
                                                                >
                                                                Good</option>
                                                            <option value="{{ encryptId(FAIR) }}"
                                                                >
                                                                Fair</option>
                                                            <option value="{{ encryptId(POOR) }}"
                                                                >
                                                                Poor</option>
                                                        </select>
                                                    </div>

                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.qualtiy_quantity_sand') }}</label>
                                                        <select name="qualtiy_quantity_sand[${form_set_count}]" id="qualtiy_quantity_sand"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Quantity and Quality of Sand
                                                            </option>
                                                            <option value="{{ encryptId(GOOD) }}"
                                                              >
                                                                Adequate</option>
                                                            <option value="{{ encryptId(FAIR) }}"
                                                               >
                                                                Inadequate</option>

                                                        </select>
                                                    </div>

                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.approach') }}</label>
                                                        <input type="text" name="approach[${form_set_count}]" id = "approach"
                                                            class="form-control" value="{{ old('approach.1') }}">
                                                    </div>

                                                </div>
                                                 <div class="col-md-4 mb-2">
                                                    <div class="col-md-12 mb-2">
                                                        <div class="form-group form-input">
                                                            <label
                                                                class="form-label require">{{ __('inspection.remarks') }}</label>
                                                            <textarea name="remarks[${form_set_count}]" id="remarks" class="form-control" style="resize: none;"></textarea>

                                                        </div>
                                                    </div>

                                                </div>


                                            </div>
                    `;

                    let newFormSetElement = $(newFormSet);

                    let locationSelect = newFormSetElement.find('select[name^="department"]');
                    GetDepartment(locationSelect);

                    $('.form-wrapper').append(newFormSetElement);

                    $("select[name='location[" + form_set_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Please select the location',
                        }
                    });

                    $("input[name='fire_sand_bucket_stand_no[" + form_set_count + "]']").rules('add', {
                        required: true,
                        uniqueItemCode: true,
                        messages: {
                            required: 'Please add the Fire Sand Bucket Stand Number',
                        }
                    });

                    $("input[name='fire_sand_bucket_no[" + form_set_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Please Select the Fire Sand Bucket Number',
                        }
                    });

                    $("select[name='condition[" + form_set_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Please Select the  Condition',
                        }
                    });
                    $("select[name='fire_bucket_condition[" + form_set_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Please Select the  Fire Bucket Condition',
                        }
                    });
                    $("select[name='paint_condition[" + form_set_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Please Select the  Paint Condition',
                        }
                    });
                    $("select[name='qualtiy_quantity_sand[" + form_set_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Please Select the Quality and Quantiy of Sand',
                        }
                    });
                    $("input[name='approach[" + form_set_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Please Enter the Approach',
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
                            title: 'Maximum Sand Bucket Inspection Observation Limit Reached',
                            text: 'You can only add up to 5 Sand Bucket Inspection Observation.',
                            confirmButtonColor: '#3085d6'
                        });
                        return;
                    }

                    var newObsSet = `
                        <div class="row mt-4 form-obs">
                                                <div class="card-header-inner p-2">
                                                    <h4 class="text-white">Sand Bucket Inspection Observation</h4>
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
                    url: "{{ admin_url('fire/fire-sand-bucket-inspection/get/department') }}",
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

            function updatePageIndices() {
                $('.form-wrapper .form-set').each(function(index) {
                    let idx = index + 1;

                    $(this).find('select[name^="location"]').attr('name', 'location[' + idx + ']');
                    $(this).find('input[name^="fire_sand_bucket_stand_no"]').attr('name', 'fire_sand_bucket_stand_no[' +
                        idx + ']');
                    $(this).find('input[name^="fire_sand_bucket_no"]').attr('name', 'fire_sand_bucket_no[' + idx + ']');
                    $(this).find('select[name^="condition"]').attr('name', 'condition[' + idx + ']');
                    $(this).find('select[name^="qualtiy_quantity_sand"]').attr('name', 'qualtiy_quantity_sand[' + idx +
                        ']');
                    $(this).find('input[name^="approach"]').attr('name', 'approach[' + idx + ']');

                    $(this).find('select').select2();
                });
            }


            $(document).on('click', '.remove-row', function() {
                let currentFormSets = $('.form-wrapper .form-set').length;

                if (currentFormSets <= minFormSets) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Minimum One CheckList Required',
                        text: 'At least One Sand Bucket Inspection Checklist is required.',
                        confirmButtonColor: '#3085d6'
                    });
                    return;
                }
                $(this).closest('.form-set').remove();
                updatePageIndices();

            });
        </script>
    @endpush
