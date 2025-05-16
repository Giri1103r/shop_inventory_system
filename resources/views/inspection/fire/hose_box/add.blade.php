@extends('admin.layouts.admin')
@section('title', 'Hose Box Inspection Add')
@section('pageurl', admin_url('fire/hose-box-inspection/list'))
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
                                    <x-button-back href="{{ admin_url('fire/hose-box-inspection/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form mx-3">
                                    <form method="POST" id="eyewashAdd"
                                        action="{{ admin_url('fire/hose-box-inspection/add/submit') }}" autocomplete="off"
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
                                                    <input type="text" name="issue_date" id = "issue_date"
                                                        class="form-control" placeholder="Issued Date"
                                                        value="{{ Displaydateformat($document_no->issue_date) }}" readonly>
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
                                                        class="form-control">
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
                                                        class="form-label require">{{ __('inspection.next_due') }}</label>
                                                    <input type="text" name="next_due" id = "next_due"
                                                        class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">{{ __('inspection.unit') }}</label>
                                                    <select name="unit_id" id="unit_id"
                                                        class=" form-control single-select" style="width: 100%">
                                                        <option value="">Select Unit</option>
                                                        @foreach ($units as $unit)
                                                            <option value="{{ encryptId($unit->id) }}">
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
                                        </div>
                                        <hr>
                                        <div class="form-wrapper">
                                            <div class="row mt-4 form-set">
                                                <div class="card-header-inner p-2">
                                                    <h4 class="text-white">Hose Box Inspection Checklist</h4>
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
                                                            class="form-label require">{{ __('inspection.sr_no') }}</label>
                                                        <input type="text" name="sr_no[1]" id = "sr_no"
                                                            class="form-control"
                                                            value="{{ FireSequence(HOSE_BOX_INSPECTION) }}" readonly>
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
                                                            @foreach ($locations as $location_one)
                                                                <option value="{{ encryptId($location_one->id) }}">
                                                                    {{ $location_one->location_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.hose_box_inspection') }}</label>
                                                        <input type="text" name="hose_box_no[1]" id = "hose_box_no"
                                                            class="form-control" value="">
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.hose_types') }}</label>
                                                        <select name="hose_types[1]" id="hose_types"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select
                                                                {{ __('inspection.hose_types') }}
                                                            </option>
                                                            @foreach ($types as $types)
                                                                <option value="{{ encryptId($types->id) }}">
                                                                    {{ $types->name }}</option>
                                                            @endforeach
                                                        </select>
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
                                                            class="form-label require">{{ __('inspection.branch_quantity') }}</label>
                                                        <input type="number" name="branch_quantity[1]"
                                                            id = "branch_quantity" class="form-control" value="">
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.hose_box_key') }}</label>
                                                        <select name="hose_box_key[1]" id="hose_box_key"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Status Of Hose Box Key</option>
                                                            <option value="{{ encryptId(PRESENT) }}">
                                                                {{ __('inspection.present') }}</option>
                                                            <option value="{{ encryptId(MISSING) }}">
                                                                {{ __('inspection.missing') }}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.condition') }}</label>
                                                        <select name="condition[1]" id="condition"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Condition Of The Hose Box
                                                            </option>
                                                            <option value="{{ encryptId(GOOD) }}">
                                                                Good</option>
                                                            <option value="{{ encryptId(FAIR) }}">
                                                                Fair</option>
                                                            <option value="{{ encryptId(POOR) }}">
                                                                Poor</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.approach') }}</label>
                                                        <textarea name="approach[1]" id="approach" class="form-control" style="resize: none;"></textarea>

                                                    </div>
                                                </div>
                                                <div class="col-md-6 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.remarks') }}</label>
                                                        <textarea name="remarks[1]" id="remarks" class="form-control" style="resize: none;"></textarea>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-observation">
                                            <div class="row mt-4 form-obs">
                                                <div class="card-header-inner p-2">
                                                    <h4 class="text-white">Hose Box Inspection Observation</h4>
                                                </div>

                                                <div class="col-md-12 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.obs') }}</label>


                                                        <div class="mb-2">
                                                            <label class="me-3">
                                                                <input type="radio" name="observation"
                                                                    value="{{ encryptId(1) }}"
                                                                    class="validate-radio-required"> Yes
                                                            </label>
                                                            <label>
                                                                <input type="radio" name="observation"
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
                                                href="{{ admin_url('fire/hose-box-inspection/list') }}"></x-button-cancel>
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

                    $("input[name^='hose_box_no']").each(function() {
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
                        location: {
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
                        "hose_box_no[1]": {
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
                        },
                        "quantity[1]": {
                            required: true,
                            number: true,
                        },
                        "branch_quantity[1]": {
                            required: true,
                            number: true,
                        },
                        "approach[1]": {
                            required: true,
                            minlength: 3,
                            maxlength: 300,
                        },
                        "remarks[1]": {
                            required: true,
                            minlength: 3,
                            maxlength: 300,
                        },
                        "hose_types[1]": {
                            required: true,
                        },
                        "hose_box_key[1]": {
                            required: true,
                        },
                        "condition[1]": {
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
                            filesize: 'File size should not exceed 15MB',
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
                        location: {
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
                        "hose_box_no[1]": {
                            required: "Please add the hose box code",
                            minlength: "Minimum Characters should be 3",
                            maxlength: "Maximum Characters should not exceed 30",
                        },
                        "type[1]": {
                            required: "Please select the valve type",
                        },
                        "location_isv[1]": {
                            required: "Please enter the location",
                        },
                        "quantity[1]": {
                            required: "Please specify the quantity",
                            number: "Quantity must be a valid number"
                        },
                        "branch_quantity[1]": {
                            required: "Please specify the branch quantity",
                            number: "Branch Quantity must be a valid number"
                        },
                        "approach[1]": {
                            required: "Please add approach",
                            minlength: "Minimum Characters should be 3",
                            maxlength: "Maximum Characters should not exceed 300",
                        },
                        "remarks[1]": {
                            required: "Please add remarks",
                            minlength: "Minimum Characters should be 3",
                            maxlength: "Maximum Characters should not exceed 300",
                        },
                        "hose_types[1]": {
                            required: "Please Select the hose type",
                        },
                        "hose_box_key[1]": {
                            required: "Please select the status of hose box key"
                        },
                        "condition[1]": {
                            required: "Please select the condition"
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
                            title: 'Maximum Hose Box Inspection CheckList Reached',
                            text: 'You can only add up to 200 Hose Box Inspection CheckList.',
                            confirmButtonColor: '#3085d6'
                        });
                        return;
                    }

                    let newSerialNumber = 'HBI-' + ('00000' + serial_number).slice(-5);

                    var newFormSet = `
                        <div class="row mt-4 form-set">
                                                <div class="card-header-inner p-2">
                                                    <h4 class="text-white">Hose Box Inspection Checklist</h4>
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
                                                            class="form-label require">{{ __('inspection.sr_no') }}</label>
                                                        <input type="text" name="sr_no[${form_set_count}]" id = "sr_no-${form_set_count}"
                                                            class="form-control"
                                                            value="{{ FireSequence(HOSE_BOX_INSPECTION) }}" readonly>
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
                                                                <option value="{{ encryptId($location->id) }}">
                                                                    {{ $location->location_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.hose_box_inspection') }}</label>
                                                        <input type="text" name="hose_box_no[${form_set_count}]"
                                                            id = "hose_box_no-${form_set_count}" class="form-control" value="">
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.hose_types') }}</label>
                                                        <select name="hose_types[${form_set_count}]" id="hose_types-${form_set_count}"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select {{ __('inspection.hose_types') }}
                                                            </option>
                                                            @foreach ($types as $type)
                                                                <option value="{{ encryptId($types->id) }}">
                                                                    {{ $types->name }}</option>
                                                            @endforeach
                                                        </select>
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
                                                            class="form-label require">{{ __('inspection.branch_quantity') }}</label>
                                                        <input type="number" name="branch_quantity[${form_set_count}]" id = "branch_quantity-${form_set_count}"
                                                            class="form-control" value="">
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.hose_box_key') }}</label>
                                                        <select name="hose_box_key[${form_set_count}]" id="hose_box_key-${form_set_count}"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Status Of Hose Box Key</option>
                                                            <option value="{{ encryptId(PRESENT) }}">
                                                                {{ __('inspection.present') }}</option>
                                                            <option value="{{ encryptId(MISSING) }}">
                                                                {{ __('inspection.missing') }}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.condition') }}</label>
                                                        <select name="condition[${form_set_count}]" id="condition-${form_set_count}"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Condition Of The Hose Box
                                                            </option>
                                                            <option value="{{ encryptId(GOOD) }}">
                                                                Good</option>
                                                            <option value="{{ encryptId(FAIR) }}">
                                                                Fair</option>
                                                            <option value="{{ encryptId(POOR) }}">
                                                                Poor</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.approach') }}</label>
                                                        <textarea name="approach[${form_set_count}]" id="approach-${form_set_count}" class="form-control" style="resize: none;"></textarea>

                                                    </div>
                                                </div>
                                                <div class="col-md-6 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.remarks') }}</label>
                                                        <textarea name="remarks[${form_set_count}]" id="remarks-${form_set_count}" class="form-control" style="resize: none;"></textarea>

                                                    </div>
                                                </div>
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

                    $("select[name='location[" + form_set_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Please select the location',
                        }
                    });

                    $("input[name='hose_box_no[" + form_set_count + "]']").rules('add', {
                        required: true,
                        uniqueItemCode: true,
                        minlength: 3,
                        maxlength: 30,
                        messages: {
                            required: 'Please enter the hose box no',
                            uniqueItemCode: 'Hose Box No must be unique',
                            minlength: "Minimum Characters should be 3",
                            maxlength: "Maximum Characters should not exceed 30",
                        }
                    });

                    $("select[name='hose_types[" + form_set_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Please select the hose type',
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

                    $("input[name='branch_quantity[" + form_set_count + "]']").rules('add', {
                        required: true,
                        number: true,
                        messages: {
                            required: 'Please specify the branch quantity',
                            number: 'Branch Quantity must be a valid number',
                        }
                    });

                    $("select[name='hose_box_key[" + form_set_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Please select the status of the hose box key',
                        }
                    });

                    $("select[name='condition[" + form_set_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Please select the condition of the hose box',
                        }
                    });

                    $("textarea[name='approach[" + form_set_count + "]']").rules('add', {
                        required: true,
                        minlength: 3,
                        maxlength: 300,
                        messages: {
                            required: 'Please enter the approach detail',
                            minlength: "Minimum Characters should be 3",
                            maxlength: "Maximum Characters should not exceed 300",
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
                            title: 'Maximum Hose Box Inspection Observation Limit Reached',
                            text: 'You can only add up to 5 Hose Box Inspection Observation.',
                            confirmButtonColor: '#3085d6'
                        });
                        return;
                    }

                    var newObsSet = `
                        <div class="row mt-4 form-obs">
                                                <div class="card-header-inner p-2">
                                                    <h4 class="text-white">Hose Box Inspection Observation</h4>
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
                    let newSerialNumber = 'HBI-' + ('000000' + idx).slice(-6);

                    $(this).find("input[name^='sr_no']").val(newSerialNumber);
                    $(this).find("input[name^='sr_no']").attr('name', 'sr_no[' + idx + ']');
                    $(this).find("select[name^='location']").attr('name', 'location[' + idx + ']');
                    $(this).find("input[name^='hose_box_no']").attr('name', 'hose_box_no[' + idx + ']');
                    $(this).find("select[name^='hose_types']").attr('name', 'hose_types[' + idx + ']');
                    $(this).find("input[name^='quantity']").attr('name', 'quantity[' + idx + ']');
                    $(this).find("input[name^='branch_quantity']").attr('name', 'branch_quantity[' + idx + ']');
                    $(this).find("select[name^='hose_box_key']").attr('name', 'hose_box_key[' + idx + ']');
                    $(this).find("select[name^='condition']").attr('name', 'condition[' + idx + ']');
                    $(this).find("textarea[name^='approach']").attr('name', 'approach[' + idx + ']');
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
                        text: 'At least One Hose Box Inspection Checklist is required.',
                        confirmButtonColor: '#3085d6'
                    });
                    return;
                }
                $(this).closest('.form-set').remove();
                updatePageIndices();

            });
        </script>
    @endpush
