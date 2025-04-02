@extends('admin.layouts.admin')
@section('title', 'Fire PA System Add')
@section('pageurl', admin_url('fire/pa-system-inspection/list'))
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
                                        href="{{ admin_url('fire/pa-system-inspection/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form mx-3">
                                    <form method="POST" id="eyewashAdd"
                                        action="{{ admin_url('fire/pa-system-inspection/add/submit') }}"
                                        autocomplete="off" enctype="multipart/form-data">
                                        @csrf

                                        <div class="row">
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">{{ __('inspection.doc_no') }}</label>
                                                    <input type="text" name="doc_no" id = "doc_no" class="form-control"
                                                        placeholder="Document Number">
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label require">{{ __('inspection.issue_date') }}</label>
                                                    <input type="text" name="issue_date" id = "issue_date"
                                                        class="form-control" placeholder="Issued Date">
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label require">{{ __('inspection.rev_date') }}</label>
                                                    <input type="text" name="rev_date" id = "rev_date"
                                                        class="form-control" value="{{ getDocumentReviewDate('PA-0') }}"
                                                        readonly>
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
                                                    <h4 class="text-white">Fire PA System Checklist</h4>
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
                                                            value="{{ FireSequence(FIRE_PA_SYSTEM_INSPECTION) }}"
                                                            readonly>
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.location') }}</label>
                                                        <select name="location[1]" id="location"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Location</option>
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
                                                        <select name="unit[1]" id="unit_id_1"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Unit</option>
                                                            @foreach ($units as $unit)
                                                                <option value="{{ encryptId($unit->id) }}">
                                                                    {{ $unit->unit_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">Remarks</label>
                                                        <textarea name="remark[1]" id="remarks" class="form-control" style="resize: none;" rows="4"></textarea>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">Audio Quality</label>
                                                        <select name="audio_quality[1]" id="audio_quality"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Audio Quality</option>
                                                            <option value="{{ encryptId(1) }}">Good</option>
                                                            <option value="{{ encryptId(2) }}">Fair</option>
                                                            <option value="{{ encryptId(3) }}">Poor</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">Mic Condition</label>
                                                        <select name="mic_condition[1]" id="mic_condition"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Mic Condition</option>
                                                            <option value="{{ encryptId(1) }}">Good</option>
                                                            <option value="{{ encryptId(2) }}">Fair</option>
                                                            <option value="{{ encryptId(3) }}">Poor</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">Mic Quantity</label>
                                                        <input type="text" name="mic_quantity[1]" id = "mic_quantity"
                                                            class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label require">Physical Condition</label>
                                                        <select name="physical_condition[1]" id="Physical Condition"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Physical Condition</option>
                                                            <option value="{{ encryptId(1) }}">Good</option>
                                                            <option value="{{ encryptId(2) }}">Fair</option>
                                                            <option value="{{ encryptId(3) }}">Poor</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label require">Cable
                                                            Condition</label>
                                                        <select name="cable_condition[1]" id="cable_condition"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Cable Condition</option>
                                                            <option value="{{ encryptId(1) }}">Good</option>
                                                            <option value="{{ encryptId(2) }}">Fair</option>
                                                            <option value="{{ encryptId(3) }}">Poor</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label require">Operation </label>
                                                        <select name="operation[1]" id="operation"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Operation</option>
                                                            <option value="{{ encryptId(1) }}">Functional</option>
                                                            <option value="{{ encryptId(2) }}">Non-functional</option>
                                                        </select>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                        <div class="form-observation">
                                            <div class="row mt-4 form-obs">
                                                <div class="card-header-inner p-2">
                                                    <h4 class="text-white">Fire PA System Observation</h4>
                                                </div>
                                                <div class="col-md-12 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.obs') }}</label>
                                                        <textarea name="observation" id="remarks" class="form-control" style="resize: none;"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="submit-button" style="text-align: right;">
                                            <x-button-submit class="submit"></x-button-submit>
                                            <x-button-reset class="submit"></x-button-reset>
                                            <x-button-cancel
                                                href="{{ admin_url('fire/pa-system-inspection/list') }}"></x-button-cancel>
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
                        "audio_quality[1]": {
                            required: true,
                        },
                        "mic_condition[1]": {
                            required: true,
                        },
                        "remark[1]": {
                            required: true,
                        },
                        "mic_quantity[1]": {
                            required: true,
                            number: true,
                            min: 1,
                        },
                        "physical_condition[1]": {
                            required: true,
                        },
                        "cable_condition[1]": {
                            required: true,
                        },
                        "operation[1]": {
                            required: true,
                        },
                        "location[1]": {
                            required: true,
                        },
                        "unit[1]": {
                            required: true,
                        },
                        device_image: {
                            required: true,
                            filesize: 2097152
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
                        frequency_id: {
                            required: "Frequency is required",
                        },
                        "audio_quality[1]": {
                            required: "Please select the Audio Quality",
                        },
                        "mic_condition[1]": {
                            required: "Please select the Mic Condition",
                        },
                        "mic_quantity[1]": {
                            required: "Please Enter the Mic Quantity",
                            number: 'Mic Quantity must be a valid number',
                            min: 'Quantity must be at least 1',
                        },
                        "remark[1]": {
                            required: "Please add remarks",
                        },
                        "location[1]": {
                            required: "Please select the location",
                        },
                        "physical_condition[1]": {
                            required: "Please select the Physical Condition",
                        },
                        "cable_condition[1]": {
                            required: "Please Select the Cable Condition"
                        },
                        "operation[1]": {
                            required: "Please select the Operation",
                        },
                        "unit[1]": {
                            required: "Please select the Unit",
                        },
                        device_image: {
                            required: "Please upload an image.",
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
                            title: 'Maximum Fire PA System CheckList Reached',
                            text: 'You can only add up to 200 Fire PA System CheckList.',
                            confirmButtonColor: '#3085d6'
                        });
                        return;
                    }

                    let newSerialNumber = 'PA-' + ('00000' + serial_number).slice(-5);

                    var newFormSet = `
                        <div class="row mt-4 form-set">
                            <div class="card-header-inner p-2">
                                <h4 class="text-white">Fire PA System Checklist</h4>
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
                                    <input type="text" name="sr_no[${form_set_count}]" id = "sr_no"
                                        class="form-control" value="{{ FireSequence(FIRE_PA_SYSTEM_INSPECTION) }}" readonly>
                                </div>
                            </div>

                            <div class="col-md-4 mb-2">
                                <div class="form-group form-input">
                                    <label
                                        class="form-label require">{{ __('inspection.location') }}</label>
                                    <select name="location[${form_set_count}]" id="location-${form_set_count}"
                                        class=" form-control single-select" style="width: 100%">
                                        <option value="">Select Location</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 mb-2">
                                <div class="form-group form-input">
                                    <label class="form-label require">{{ __('inspection.unit') }}</label>
                                    <select name="unit[${form_set_count}]" id="unit_id-${form_set_count}"
                                        class=" form-control single-select" style="width: 100%">
                                        <option value="">Select Unit</option>
                                        @foreach ($units as $unit)
                                            <option value="{{ encryptId($unit->id) }}">
                                                {{ $unit->unit_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12 mb-2">
                                <div class="form-group form-input">
                                    <label
                                        class="form-label require">Remarks</label>
                                    <textarea name="remark[${form_set_count}]" id="remarks" class="form-control" style="resize: none;" rows="4"></textarea>
                                </div>
                            </div>
                             <div class="col-md-4 mb-2">
                                <div class="form-group form-input">
                                    <label
                                        class="form-label require">Audio Quality</label>
                                    <select name="audio_quality[${form_set_count}]" id="audio_quality-${form_set_count}"
                                        class=" form-control single-select" style="width: 100%">
                                        <option value="">Select Audio Quality</option>
                                       <option value="{{ encryptId(1) }}">Good</option>
                                        <option value="{{ encryptId(2) }}">Fair</option>
                                        <option value="{{ encryptId(3) }}">Poor</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 mb-2">
                                <div class="form-group form-input">
                                    <label
                                        class="form-label require">Mic Condition</label>
                                    <select name="mic_condition[${form_set_count}]" id="mic_condition-${form_set_count}"
                                        class=" form-control single-select" style="width: 100%">
                                        <option value="">Select Mic Condition</option>
                                       <option value="{{ encryptId(1) }}">Good</option>
                                        <option value="{{ encryptId(2) }}">Fair</option>
                                        <option value="{{ encryptId(3) }}">Poor</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 mb-2">
                                <div class="form-group form-input">
                                    <label
                                        class="form-label require">Mic Quantity</label>
                                    <input type="text" name="mic_quantity[${form_set_count}]" id = "mic_quantity"
                                        class="form-control">
                                </div>
                            </div>
                            <div class="col-md-4 mb-2">
                                <div class="form-group form-input">
                                    <label class="form-label require">Physical Condition</label>
                                    <select name="physical_condition[${form_set_count}]" id="Physical Condition-${form_set_count}"
                                        class=" form-control single-select" style="width: 100%">
                                        <option value="">Select Physical Condition</option>
                                        <option value="{{ encryptId(1) }}">Good</option>
                                        <option value="{{ encryptId(2) }}">Fair</option>
                                        <option value="{{ encryptId(3) }}">Poor</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 mb-2">
                                <div class="form-group form-input">
                                    <label class="form-label require">Cable
                                        Condition</label>
                                    <select name="cable_condition[${form_set_count}]" id="cable_condition-${form_set_count}"
                                        class=" form-control single-select" style="width: 100%">
                                        <option value="">Select Cable Condition</option>
                                        <option value="{{ encryptId(1) }}">Good</option>
                                        <option value="{{ encryptId(2) }}">Fair</option>
                                        <option value="{{ encryptId(3) }}">Poor</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 mb-2">
                                <div class="form-group form-input">
                                    <label class="form-label require">Operation </label>
                                    <select name="operation[${form_set_count}]" id="operation-${form_set_count}"
                                        class=" form-control single-select" style="width: 100%">
                                        <option value="">Select Operation</option>
                                        <option value="{{ encryptId(1) }}">Functional</option>
                                        <option value="{{ encryptId(2) }}">Non-functional</option>
                                    </select>
                                </div>
                            </div>

                        </div>
                    `;

                    let newFormSetElement = $(newFormSet);

                    let locationSelect = newFormSetElement.find('select[name^="location"]');
                    GetLocations(locationSelect);

                    $('.form-wrapper').append(newFormSetElement);

                    $('select[name^="unit["]').each(function() {
                        $(this).select2({
                            placeholder: "Select Unit",
                            width: '100%'
                        });
                    });

                    $('select[name^="audio_quality["]').each(function() {
                        $(this).select2({
                            placeholder: "Select Audio Quality",
                            width: '100%'
                        });
                    });

                    $('select[name^="mic_condition["]').each(function() {
                        $(this).select2({
                            placeholder: "Select Mic Condition",
                            width: '100%'
                        });
                    });

                    $('select[name^="physical_condition["]').each(function() {
                        $(this).select2({
                            placeholder: "Select Physical Condition",
                            width: '100%'
                        });
                    });

                    $('select[name^="cable_condition["]').each(function() {
                        $(this).select2({
                            placeholder: "Select Cable Condition",
                            width: '100%'
                        });
                    });

                    $('select[name^="operation["]').each(function() {
                        $(this).select2({
                            placeholder: "Select OPeration",
                            width: '100%'
                        });
                    });

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

                    $("select[name='unit[" + form_set_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Please select the Unit',
                        }
                    });

                    $("select[name='audio_quality[" + form_set_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Please select the Audio Quality',
                        }
                    });

                    $("select[name='mic_condition[" + form_set_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Please select the Mic Condition',
                        }
                    });

                    $("input[name='mic_quantity[" + form_set_count + "]']").rules('add', {
                        required: true,
                        number: true,
                        min: 1,
                        messages: {
                            required: 'Please Enter the Mic Quantity',
                            number: 'Mic Quantity must be a valid number',
                            min: 'Quantity must be at least 1',
                        }
                    });

                    $("select[name='physical_condition[" + form_set_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Please select the Physical Condition',
                        }
                    });

                    $("select[name='cable_condition[" + form_set_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Please select the Cable Condition',
                        }
                    });

                    $("select[name='operation[" + form_set_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Please select the Operation',
                        }
                    });

                    $("textarea[name='remark[" + form_set_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Please add remarks',
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
                            title: 'Maximum Fire PA System Observation Limit Reached',
                            text: 'You can only add up to 5 Fire PA System Observation.',
                            confirmButtonColor: '#3085d6'
                        });
                        return;
                    }

                    var newObsSet = `
                        <div class="row mt-4 form-obs">
                            <div class="card-header-inner p-2">
                                <h4 class="text-white">Fire PA System Observation</h4>
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

            function GetLocations(selectElement) {
                $.ajax({
                    type: "GET",
                    url: "{{ admin_url('fire/pa-system-inspection/get/locations') }}",
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
                    let newSerialNumber = 'PA-' + ('000000' + idx).slice(-6);
                    $(this).find("input[name^='sr_no']").val(newSerialNumber);

                    $(this).find('input[name^="sr_no"]').attr('name', 'sr_no[' + idx + ']');
                    $(this).find('select[name^="location"]').attr('name', 'location[' + idx + ']');
                    $(this).find('select[name^="unit"]').attr('name', 'unit[' + idx + ']');
                    $(this).find('select[name^="audio_quality"]').attr('name', 'audio_quality[' + idx + ']');
                    $(this).find('select[name^="mic_condition"]').attr('name', 'mic_condition[' + idx + ']');
                    $(this).find('input[name^="mic_quantity"]').attr('name', 'mic_quantity[' + idx + ']');
                    $(this).find('select[name^="physical_condition"]').attr('name', 'physical_condition[' + idx + ']');
                    $(this).find('select[name^="cable_condition"]').attr('name', 'cable_condition[' + idx + ']');
                    $(this).find('select[name^="operation"]').attr('name', 'operation[' + idx + ']');
                    $(this).find('textarea[name^="remarks"]').attr('name', 'remark[' + idx + ']');

                    $(this).find('select').select2();
                });
            }


            $(document).on('click', '.remove-row', function() {
                let currentFormSets = $('.form-wrapper .form-set').length;

                if (currentFormSets <= minFormSets) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Minimum One CheckList Required',
                        text: 'At least One Fire PA System Checklist is required.',
                        confirmButtonColor: '#3085d6'
                    });
                    return;
                }
                $(this).closest('.form-set').remove();
                updatePageIndices();

            });
        </script>
    @endpush
