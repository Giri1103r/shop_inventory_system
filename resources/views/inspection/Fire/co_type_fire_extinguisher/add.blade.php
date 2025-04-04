@extends('admin.layouts.admin')
@section('title', 'Co2 Type Fire Extinguisher Inspection Add')
@section('pageurl', admin_url('fire/fire-extinguisher/co2/list'))
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
                                    <x-button-back href="{{ admin_url('fire/fire-extinguisher/co2/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form mx-3">
                                    <form method="POST" id="cotypefireAdd"
                                        action="{{ admin_url('fire/fire-extinguisher/co2/add/submit') }}" autocomplete="off"
                                        enctype="multipart/form-data">
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
                                                        class="form-control" value="{{old('inspection_date')}}">
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
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label require">{{ __('inspection.next_due') }}</label>
                                                    <input type="text" name="next_due" id = "next_due"
                                                        class="form-control" value="{{ old('next_due') }}">
                                                </div>
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
                                                    <h4 class="text-white">Co2 Type Fire Extinguisher Inspection Checklist</h4>
                                                </div>

                                                <div class="d-flex justify-content-end align-items-center gap-2 m-2">
                                                    <button class="btn btn-primary add-row" type="button" id="add-row"
                                                        style="min-width: 130px;">
                                                        Add
                                                    </button>
                                                    <button type="button"
                                                        class="btn btn-danger remove-row d-flex align-items-center"
                                                        style="min-width: 130px;">
                                                        <i class="fa-solid fa-trash me-2"></i> Remove
                                                    </button>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.sr_no') }}</label>
                                                        <input type="text" name="sr_no[1]" id = "sr_no"
                                                            class="form-control"
                                                            value="{{ FireSequence(CO_TYPE_FIRE_EXTINGUISHER_INSPECTION) }}"
                                                            readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">FIRE POINT NO</label>
                                                       <input type="text" name="fire_point_no[1]" id="fire_point_no" class="form-control">
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
                                                                <option value="{{ encryptId($department->id) }}"
                                                                    {{ old('department.1') == encryptId($department->id) ? 'selected' : '' }}>
                                                                    {{ $department->department_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
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
                                                                    {{ old('location_id') == encryptId($location->id) ? 'selected' : '' }}>
                                                                    {{ $location->location_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.type') }}</label>
                                                        <select name="type[1]" id="type"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Type</option>
                                                            @foreach ($types as $type)
                                                                <option value="{{ encryptId($type->id) }}">
                                                                    {{ $type->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">Capacity</label>
                                                       <input type="text" name="capacity[1]" id="capacity" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">Quantity</label>
                                                       <input type="text" name="quantity[1]" id="quantity" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.discharge_tube') }}</label>
                                                        <select name="discharge_tube[1]" id="discharge_tube"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Status Of Discharge Tube</option>
                                                            <option value="{{ encryptId(FUNCTIONAL) }}"
                                                                {{ old('discharge_tube.1') == encryptId(FUNCTIONAL) ? 'selected' : '' }}>
                                                                {{ __('inspection.functional') }}</option>
                                                            <option value="{{ encryptId(NON_FUNCTIONAL) }}"
                                                                {{ old('discharge_tube.1') == encryptId(NON_FUNCTIONAL) ? 'selected' : '' }}>
                                                                {{ __('inspection.non_functional') }}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">Discharge Horn</label>
                                                        <select name="discharge_horn[1]" id="discharge_horn"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Discharge Horn</option>
                                                            <option value="{{ encryptId(FUNCTIONAL) }}"
                                                                {{ old('discharge_horn.1') == encryptId(FUNCTIONAL) ? 'selected' : '' }}>
                                                                {{ __('inspection.functional') }}</option>
                                                            <option value="{{ encryptId(NON_FUNCTIONAL) }}"
                                                                {{ old('discharge_horn.1') == encryptId(NON_FUNCTIONAL) ? 'selected' : '' }}>
                                                                {{ __('inspection.non_functional') }}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">Weight of CO2 in FE</label>
                                                       <input type="text" name="weight_of_co2_in_fe[1]" id="weight_of_co2_in_fe" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.safety_pin') }}</label>
                                                        <select name="safety_pin[1]" id="safety_pin"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Status Of Safety Pin</option>
                                                            <option value="{{ encryptId(PRESENT) }}"
                                                                {{ old('safety_pin.1') == encryptId(PRESENT) ? 'selected' : '' }}>
                                                                {{ __('inspection.present') }}</option>
                                                            <option value="{{ encryptId(MISSING) }}"
                                                                {{ old('safety_pin.1') == encryptId(MISSING) ? 'selected' : '' }}>
                                                                {{ __('inspection.missing') }}</option>
                                                        </select>
                                                    </div>
                                                    @error('safety_pin.1')
                                                        <div class="error">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">Approach</label>
                                                        <input type="text" name="approach[1]" id="approach" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-md-12 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.remarks') }}</label>
                                                        <textarea name="remarks[1]" id="remarks" class="form-control" style="resize: none;">{{ old('remarks.1') }}</textarea>

                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                        <div class="form-observation">
                                            <div class="row mt-4 form-obs">
                                                <div class="card-header-inner p-2">
                                                    <h4 class="text-white">Co2 Type Fire Extinguisher Inspection Observation</h4>
                                                </div>

                                                <div class="col-md-12 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.obs') }}</label>
                                                        <textarea name="observation" id="observation" class="form-control" style="resize: none;"></textarea>

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

                    $("input[name^='fire_point_no']").each(function() {
                        var itemCodeValue = $(this).val();
                        if (itemCodeValue) {
                            itemCodes.push(itemCodeValue);
                        }
                    });

                    return itemCodes.indexOf(value) === itemCodes.lastIndexOf(value);
                }, "Fire Point No must be unique");

                $('#cotypefireAdd').validate({
                    rules: {
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
                        "fire_point_no[1]": {
                            required: true,
                            noSpaces: true,
                            number: true,
                            uniqueItemCode: true,
                        },
                        "department[1]": {
                            required: true,
                        },
                        "location[1]": {
                            required: true,
                        },
                        "type[1]": {
                            required: true,
                        },
                        "capacity[1]": {
                            required: true,
                            noSpaces: true,
                        },
                        "quantity[1]": {
                            required: true,
                            noSpaces: true,
                            number: true,
                            min: 1,
                        },
                        "discharge_tube[1]": {
                            required: true,
                        },
                        "discharge_horn[1]": {
                            required: true,
                        },
                        "weight_of_co2_in_fe[1]": {
                            required: true,
                            noSpaces: true,
                        },
                        "safety_pin[1]": {
                            required: true,
                        },
                        "approach[1]": {
                            required: true,
                            noSpaces: true,
                        },
                        "remarks[1]": {
                            required: true,
                            noSpaces: true,
                        },
                        device_image: {
                            required: true,
                            // extension: "jpg",
                            filesize: 2097152
                        },
                        observation: {
                            required: true,
                            noSpaces: true,
                        },
                        signature_image: {
                            required: true,
                        },
                    },
                    messages: {
                        signature_image: {
                            required: 'Please upload your signature',
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
                        'fire_point_no[1]': {
                            required: "Please Enter the Fire Point Number",
                            uniqueItemCode: 'Fire Point Number must be unique',
                            number: 'Quantity Must be valid Number',
                        },
                        "department[1]": {
                            required: "Please Select the Department",
                        },
                        "location[1]": {
                            required: "Please Select the Location",
                        },
                        "type[1]": {
                            required: "Please Select the Fire Type",
                        },
                        "capacity[1]": {
                            required: "Please Enter Capacity",
                        },
                        "quantity[1]": {
                            required: "Please Enter the Quantity",
                        },
                        "discharge_tube[1]": {
                            required: "Please Select the Discharge Tube",
                        },
                        "discharge_horn[1]": {
                            required: "Please Select the Discharge Horn",
                        },
                        "weight_of_co2_in_fe[1]": {
                            required: "Please Enter the Weight of CO2 in FE",
                        },
                        "safety_pin[1]": {
                            required: "Please Select the Safety Pin",
                        },
                        "approach[1]": {
                            required: "Please Enter the Approach",
                        },
                        "remarks[1]": {
                            required: "Please Enter the Remarks",
                        },
                        device_image: {
                            required: "Please upload an image.",
                            // extension: "Only JPG files are allowed.",
                            filesize: "Image must be under 2MB."
                        },
                        observation: {
                            required: "Please Enter the Observation",
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
                            title: 'Maximum Co2 Type Fire Extinguisher Inspection CheckList Reached',
                            text: 'You can only add up to 200 Co2 Type Fire Extinguisher Inspection CheckList.',
                            confirmButtonColor: '#3085d6'
                        });
                        return;
                    }

                    let newSerialNumber = 'HTR-' + ('00000' + serial_number).slice(-5);

                    var newFormSet = `
                        <div class="row mt-4 form-set">
                            <div class="card-header-inner p-2">
                                <h4 class="text-white">Co2 Type Fire Extinguisher Inspection Checklist</h4>
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
                                        class="form-label require">{{ __('inspection.sr_no') }}</label>
                                    <input type="text" name="sr_no[${form_set_count}]" id = "sr_no"
                                        class="form-control" value="{{ FireSequence(CO_TYPE_FIRE_EXTINGUISHER_INSPECTION) }}" readonly>
                                </div>
                            </div>

                            <div class="col-md-4 mb-2">
                                <div class="form-group form-input">
                                    <label
                                        class="form-label require">FIRE POINT NO</label>
                                    <input type="text" name="fire_point_no[${form_set_count}]" id="fire_point_no-${form_set_count}" class="form-control">
                                </div>
                            </div>

                            <div class="col-md-4 mb-2">
                                <div class="form-group form-input">
                                    <label
                                        class="form-label require">{{ __('inspection.department') }}</label>
                                    <select name="department[${form_set_count}]" id="department-${form_set_count}"
                                        class=" form-control single-select" style="width: 100%">
                                    </select>
                                </div>
                            </div>

                             <div class="col-md-4 mb-2">
                                <div class="form-group form-input">
                                    <label
                                        class="form-label require">{{ __('inspection.location') }}</label>
                                    <select name="location[${form_set_count}]" id="location-${form_set_count}"
                                        class=" form-control single-select" style="width: 100%">
                                        <option value="">Select {{ __('inspection.location') }}
                                        </option>
                                        @foreach ($locations as $location)
                                            <option value="{{ encryptId($location->id) }}"
                                                {{ old('location_id') == encryptId($location->id) ? 'selected' : '' }}>
                                                {{ $location->location_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 mb-2">
                                <div class="form-group form-input">
                                    <label
                                        class="form-label require">{{ __('inspection.type') }}</label>
                                    <select name="type[${form_set_count}]" id="type-${form_set_count}"
                                        class=" form-control single-select" style="width: 100%">
                                        <option value="">Select Type</option>
                                        @foreach ($types as $type)
                                            <option value="{{ encryptId($type->id) }}">
                                                {{ $type->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 mb-2">
                                <div class="form-group form-input">
                                    <label
                                        class="form-label require">Capacity</label>
                                    <input type="text" name="capacity[${form_set_count}]" id="capacity-${form_set_count}" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-4 mb-2">
                                <div class="form-group form-input">
                                    <label
                                        class="form-label require">Quantity</label>
                                    <input type="text" name="quantity[${form_set_count}]" id="quantity-${form_set_count}" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-4 mb-2">
                                <div class="form-group form-input">
                                    <label
                                        class="form-label require">{{ __('inspection.discharge_tube') }}</label>
                                    <select name="discharge_tube[${form_set_count}]" id="discharge_tube-${form_set_count}"
                                        class=" form-control single-select" style="width: 100%">
                                        <option value="">Select Status Of Discharge Tube</option>
                                        <option value="{{ encryptId(FUNCTIONAL) }}"
                                            {{ old('discharge_tube.1') == encryptId(FUNCTIONAL) ? 'selected' : '' }}>
                                            {{ __('inspection.functional') }}</option>
                                        <option value="{{ encryptId(NON_FUNCTIONAL) }}"
                                            {{ old('discharge_tube.1') == encryptId(NON_FUNCTIONAL) ? 'selected' : '' }}>
                                            {{ __('inspection.non_functional') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 mb-2">
                                <div class="form-group form-input">
                                    <label
                                        class="form-label require">Discharge Horn</label>
                                    <select name="discharge_horn[${form_set_count}]" id="discharge_horn-${form_set_count}"
                                        class=" form-control single-select" style="width: 100%">
                                        <option value="">Select Discharge Horn</option>
                                        <option value="{{ encryptId(FUNCTIONAL) }}"
                                            {{ old('discharge_horn.1') == encryptId(FUNCTIONAL) ? 'selected' : '' }}>
                                            {{ __('inspection.functional') }}</option>
                                        <option value="{{ encryptId(NON_FUNCTIONAL) }}"
                                            {{ old('discharge_horn.1') == encryptId(NON_FUNCTIONAL) ? 'selected' : '' }}>
                                            {{ __('inspection.non_functional') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 mb-2">
                                <div class="form-group form-input">
                                    <label
                                        class="form-label require">Weight of CO2 in FE</label>
                                    <input type="text" name="weight_of_co2_in_fe[${form_set_count}]" id="weight_of_co2_in_fe-${form_set_count}" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-4 mb-2">
                                <div class="form-group form-input">
                                    <label
                                        class="form-label require">{{ __('inspection.safety_pin') }}</label>
                                    <select name="safety_pin[${form_set_count}]" id="safety_pin-${form_set_count}"
                                        class=" form-control single-select" style="width: 100%">
                                        <option value="">Select Status Of Safety Pin</option>
                                        <option value="{{ encryptId(PRESENT) }}"
                                            {{ old('safety_pin.1') == encryptId(PRESENT) ? 'selected' : '' }}>
                                            {{ __('inspection.present') }}</option>
                                        <option value="{{ encryptId(MISSING) }}"
                                            {{ old('safety_pin.1') == encryptId(MISSING) ? 'selected' : '' }}>
                                            {{ __('inspection.missing') }}</option>
                                    </select>
                                </div>
                                @error('safety_pin.1')
                                    <div class="error">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-2">
                                <div class="form-group form-input">
                                    <label
                                        class="form-label require">Approach</label>
                                    <input type="text" name="approach[${form_set_count}]" id="approach-${form_set_count}" class="form-control">
                                </div>
                            </div>

                            <div class="col-md-12 mb-2">
                                <div class="form-group form-input">
                                    <label
                                        class="form-label require">{{ __('inspection.remarks') }}</label>
                                    <textarea name="remarks[${form_set_count}]" id="remarks" class="form-control" style="resize: none;"></textarea>

                                </div>
                            </div>

                        </div>
                    `;

                    let newFormSetElement = $(newFormSet);

                    let locationSelect = newFormSetElement.find('select[name^="department"]');
                    GetDepartment(locationSelect);

                    $('.form-wrapper').append(newFormSetElement);

                    $("input[name='sr_no[" + form_set_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Serial number is required',
                        }
                    });

                    $("input[name='fire_point_no[" + form_set_count + "]']").rules('add', {
                        required: true,
                        uniqueItemCode: true,
                        noSpaces: true,
                        number: true,
                        messages: {
                            required: 'Please Enter the Fire Point Number',
                            uniqueItemCode: 'Fire Point Number must be unique',
                            noSpaces: 'Fire Point Number cannot be empty or only spaces',
                            number: 'Quantity Must be valid Number',
                        }
                    });

                    $("select[name='department[" + form_set_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Please select the department',
                        }
                    });

                    $("select[name='location[" + form_set_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Please Select the Location',
                        }
                    });

                    $("select[name='type[" + form_set_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Please Select the Type',
                        }
                    });

                    $("input[name='capacity[" + form_set_count + "]']").rules('add', {
                        required: true,
                        noSpaces: true,
                        messages: {
                            required: 'Please Enter the Capacity',
                            noSpaces: 'Capacity cannot be empty or only spaces'
                        }
                    });

                    $("input[name='quantity[" + form_set_count + "]']").rules('add', {
                        required: true,
                        noSpaces: true,
                        number:true,
                        messages: {
                            required: 'Please Enter the Quantity',
                            noSpaces: 'Quantity cannot be empty or only spaces',
                            number: 'Quantity Must be valid Number',
                        }
                    });

                    $("select[name='discharge_tube[" + form_set_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Please Select the Discharge Tube',
                        }
                    });

                    $("select[name='discharge_horn[" + form_set_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Please Select the Discharge Horn',
                        }
                    });

                    $("input[name='weight_of_co2_in_fe[" + form_set_count + "]']").rules('add', {
                        required: true,
                        noSpaces: true,
                        messages: {
                            required: 'Please Enter the Weight of CO2 in FE',
                            noSpaces: 'Weight of CO2 in FE cannot be empty or only spaces',
                        }
                    });

                    $("select[name='safety_pin[" + form_set_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Please Select the Safety Pin',
                        }
                    });

                    $("input[name='approach[" + form_set_count + "]']").rules('add', {
                        required: true,
                        noSpaces: true,
                        messages: {
                            required: 'Please Enter the Approach',
                            noSpaces: 'Approach cannot be empty or only spaces',
                        }
                    });

                    $("textarea[name='remarks[" + form_set_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Please Enter the Remarks',
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
                            title: 'Maximum Co2 Type Fire Extinguisher Inspection Observation Limit Reached',
                            text: 'You can only add up to 5 Co2 Type Fire Extinguisher Inspection Observation.',
                            confirmButtonColor: '#3085d6'
                        });
                        return;
                    }

                    var newObsSet = `
                        <div class="row mt-4 form-obs">
                            <div class="card-header-inner p-2">
                                <h4 class="text-white">Co2 Type Fire Extinguisher Inspection Observation</h4>
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
                                    <textarea name="observation[1]" id="observation" class="form-control" style="resize: none;"></textarea>

                                </div>
                            </div>

                        </div>
                    `;

                });
            });

            function GetDepartment(selectElement) {
                $.ajax({
                    type: "GET",
                    url: "{{ admin_url('fire/fire-extinguisher/co2/get/department') }}",
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
                    let newSerialNumber = 'CTFE-' + ('000000' + idx).slice(-6);
                    $(this).find("input[name^='sr_no']").val(newSerialNumber);
                    $(this).find('input[name^="sr_no"]').attr('name', 'sr_no[' + idx + ']');
                    $(this).find('input[name^="fire_point_no"]').attr('name', 'fire_point_no[' + idx + ']');
                    $(this).find('select[name^="department"]').attr('name', 'department[' + idx + ']');
                    $(this).find('select[name^="location"]').attr('name', 'location[' + idx + ']');
                    $(this).find('select[name^="type"]').attr('name', 'type[' + idx + ']');
                    $(this).find('input[name^="capacity"]').attr('name', 'capacity[' + idx + ']');
                    $(this).find('input[name^="quantity"]').attr('name', 'quantity[' + idx + ']');
                    $(this).find('select[name^="discharge_tube"]').attr('name', 'discharge_tube[' + idx + ']');
                    $(this).find('select[name^="discharge_horn"]').attr('name', 'discharge_horn[' + idx + ']');
                    $(this).find('input[name^="weight_of_co2_in_fe"]').attr('name', 'weight_of_co2_in_fe[' + idx + ']');
                    $(this).find('select[name^="safety_pin"]').attr('name', 'safety_pin[' + idx + ']');
                    $(this).find('input[name^="approach"]').attr('name', 'approach[' + idx + ']');
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
                        text: 'At least One Co2 Type Fire Extinguisher Inspection Checklist is required.',
                        confirmButtonColor: '#3085d6'
                    });
                    return;
                }
                $(this).closest('.form-set').remove();
                updatePageIndices();

            });
        </script>
    @endpush
