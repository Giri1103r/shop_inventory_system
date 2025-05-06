@extends('admin.layouts.admin')
@section('title', 'Emergency Light Inspection Add')
@section('pageurl', admin_url('fire/emergency-light-inspection/list'))
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
                                        href="{{ admin_url('fire/emergency-light-inspection/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form mx-3">
                                    <form method="POST" id="emergencyLight"
                                        action="{{ admin_url('fire/emergency-light-inspection/add/submit') }}"
                                        autocomplete="off" enctype="multipart/form-data">
                                        @csrf

                                        <div class="row">
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">{{ __('inspection.doc_no') }}</label>
                                                    <input type="text" name="document_no" id = "document_no"
                                                        class="form-control" placeholder="Enter the Document Number"
                                                        value="{{ $document_no->doc_no }}" readonly>
                                                    @error('doc_no')
                                                        <div class="error">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label require">{{ __('inspection.issue_date') }}</label>
                                                    <div class="input-group date form-input custom-height">
                                                        <input type="text" name="issue_date" id="issue_date"
                                                            class="form-control"autocomplete="off"
                                                            value="{{ displaydateformat($document_no->issue_date) }}"
                                                            readonly>
                                                        <div class="input-group-addon input-group-text">
                                                            <span class="fa fa-calendar"></span>
                                                        </div>
                                                    </div>
                                                </div>

                                                @error('issue_date')
                                                    <div class="error">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label require">{{ __('inspection.rev_date') }}</label>
                                                    <input type="text" name="review_date" id = "review_date"
                                                        class="form-control" value="{{ $document_no->rev_dt }}" readonly>
                                                </div>
                                            </div>
                                            <input type="hidden" name="document_reference_id"
                                                value="{{ encryptId($document_no->id) }}">
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
                                                        <input type="file" name="signature_image" id="signature_upload"
                                                            class="form-control form-control-sm" accept="image/*"
                                                            placeholder="Enter the image">
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
                                                    <h4 class="text-white">Emergency Light Inspection</h4>
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
                                                            value="{{ FireSequence(EMERGENCY_LIGHT_INSPECTION) }}"
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
                                                            @foreach ($department as $list)
                                                                <option value="{{ encryptId($list->id) }}">
                                                                    {{ $list->department_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.location') }}</label>
                                                        <input type="text" name="location[1]" id = "location"
                                                            class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.emergency_light_number') }}</label>
                                                        <input type="text" name="emergency_light_number[1]"
                                                            id = "emergency_light_number" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.condition_of_light') }}</label>
                                                        <select name="condition_of_light[1]" id="condition_of_light"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select the option</option>

                                                            @foreach ($conditionLight as $list)
                                                                <option value="{{ encryptId($list->id) }}">
                                                                    {{ $list->condition }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.type_of_light') }}</label>
                                                        <select name="type_of_light[1]" id="type_of_light"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select the option</option>
                                                            @foreach ($lightType as $list)
                                                                <option value="{{ encryptId($list->id) }}">
                                                                    {{ $list->type }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.capacity') }}</label>
                                                        <input type="number" name="capacity[1]" id = "capacity"
                                                            class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.quantity') }}</label>
                                                        <input type="number" name="quantity[1]" id = "quantity"
                                                            class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.power_supply') }}</label>
                                                        <select name="power_supply[1]" id="power_supply"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select power supply</option>
                                                            @foreach ($powerSupply as $list)
                                                                <option value="{{ encryptId($list->id) }}">
                                                                    {{ $list->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.light_condition') }}</label>
                                                        <select name="light_condition[1]" id="light_condition"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select the option</option>
                                                            <option value="{{ encryptId(1) }}">Good</option>
                                                            <option value="{{ encryptId(2) }}">Fair</option>
                                                            <option value="{{ encryptId(3) }}">Poor</option>

                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.switch_condition') }}</label>
                                                        <select name="switch_condition[1]" id="switch_condition"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select the option</option>
                                                            <option value="{{ encryptId(1) }}">Good</option>
                                                            <option value="{{ encryptId(2) }}">Fair</option>
                                                            <option value="{{ encryptId(3) }}">Poor</option>

                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.status') }}</label>
                                                        <select name="status[1]" id="status"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Status</option>
                                                            @foreach ($fireStatus as $list)
                                                                <option value="{{ encryptId($list->id) }}">
                                                                    {{ $list->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.remarks') }}</label>
                                                        <textarea name="remarks[1]" id="remarks" class="form-control" style="resize: none;" rows="4"></textarea>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                        <div class="form-observation">
                                            <div class="row mt-4 form-obs">
                                                <div class="card-header-inner p-2">
                                                    <h4 class="text-white">Emergency Light Inspection Observation</h4>
                                                </div>
                                                <div class="col-md-12 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.obs') }}</label>


                                                        <div class="mb-2">
                                                            <label class="me-3">
                                                                <input type="radio" name="observation_needed"
                                                                    value="{{encryptId(1)}}" class="validate-radio-required"> Yes
                                                            </label>
                                                            <label>
                                                                <input type="radio" name="observation_needed"
                                                                    value="{{encryptId(2)}}" class="validate-radio-required"> No
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
                                                href="{{ admin_url('fire/emergency-light-inspection/list') }}"></x-button-cancel>
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

                $('#emergencyLight').validate({
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
                        "check_items[1]": {
                            required: true,
                        },
                        "location[1]": {
                            required: true,
                        },
                        "quantity[1]": {
                            required: true,
                        },
                        "department[1]": {
                            required: true,
                        },
                        "resource_code[1]": {
                            required: true,
                        },
                        "remarks[1]": {
                            required: true,
                        },
                        device_image: {
                            required: true,
                            // extension: "jpg",
                            // filesize: 2097152
                        },
                        observation: {
                            required: true,
                        },
                        signature_image: {
                            required: true,
                        },
                        "capacity[1]": {
                            required: true,
                        },
                        "emergency_light_number[1]": {
                            required: true,
                        },
                        "condition_of_light[1]": {
                            required: true,
                        },
                        "type_of_light[1]": {
                            required: true,
                        },
                        "power_supply[1]": {
                            required: true,
                        },
                        "switch_condition[1]": {
                            required: true,
                        },
                        "light_condition[1]": {
                            required: true,
                        },
                        "status[1]": {
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
                        "quantity[1]": {
                            required: "Please add the quantity",
                        },
                        "department[1]": {
                            required: "Please Select The Department",
                        },
                        "location[1]": {
                            required: "Please Enter the Location",
                        },
                        "capacity[1]": {
                            required: "Please add the capacity",
                        },
                        "emergency_light_number[1]": {
                            required: "Please add the emergency of the light",
                        },
                        "condition_of_light[1]": {
                            required: "Please select the condition of light",
                        },
                        "type_of_light[1]": {
                            required: "Please select the condition of light",
                        },
                        "power_supply[1]": {
                            required: "Please select the condition of light",
                        },
                        "switch_condition[1]": {
                            required: "Please select the  swith condition of light",
                        },
                        "light_condition[1]": {
                            required: "Please select the  light condition",
                        },
                        "status[1]": {
                            required: "Please select the Status",
                        },
                        "remarks[1]": {
                            required: "Please add remarks",
                        },
                        device_image: {
                            required: "Please upload an image.",
                            // extension: "Only JPG files are allowed.",
                            // filesize: "Image must be under 2MB."
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
                            title: 'Emergency Light Inspection CheckList Reached',
                            text: 'You can only add up to 200 Emergency Light Inspection.',
                            confirmButtonColor: '#3085d6'
                        });
                        return;
                    }

                    let newSerialNumber = 'HTR-' + ('00000' + serial_number).slice(-5);

                    var newFormSet = `
                        <div class="row mt-4 form-set">
                                                <div class="card-header-inner p-2">
                                                    <h4 class="text-white">Emergency Light Inspection</h4>
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
                                                        <input type="text" name="sr_no[${form_set_count}]" id = "sr_no_${form_set_count}"
                                                            class="form-control"
                                                            value="{{ FireSequence(EMERGENCY_LIGHT_INSPECTION) }}"
                                                            readonly>
                                                    </div>
                                                </div>


                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.department') }}</label>
                                                        <select name="department[${form_set_count}]" id="department_${form_set_count}"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Department</option>

                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.location') }}</label>
                                                        <input type="text" name="location[${form_set_count}]" id = "location_${form_set_count}"
                                                            class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.emergency_light_number') }}</label>
                                                        <input type="text" name="emergency_light_number[${form_set_count}]"
                                                            id = "emergency_light_number_${form_set_count}" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.condition_of_light') }}</label>
                                                        <select name="condition_of_light[${form_set_count}]" id="condition_of_light_${form_set_count}"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select the option</option>

                                                            @foreach ($conditionLight as $list)
                                                                <option value="{{ encryptId($list->id) }}">
                                                                    {{ $list->condition }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.type_of_light') }}</label>
                                                        <select name="type_of_light[${form_set_count}]" id="type_of_light_${form_set_count}"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select the option</option>
                                                            @foreach ($lightType as $list)
                                                                <option value="{{ encryptId($list->id) }}">
                                                                    {{ $list->type }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.capacity') }}</label>
                                                        <input type="number" name="capacity[${form_set_count}]" id = "capacity_${form_set_count}"
                                                            class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.quantity') }}</label>
                                                        <input type="number" name="quantity[${form_set_count}]" id = "quantity_${form_set_count}"
                                                            class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.power_supply') }}</label>
                                                        <select name="power_supply[${form_set_count}]" id="power_supply_${form_set_count}"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select power supply</option>
                                                            @foreach ($powerSupply as $list)
                                                                <option value="{{ encryptId($list->id) }}">
                                                                    {{ $list->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.light_condition') }}</label>
                                                        <select name="light_condition[${form_set_count}]" id="light_condition_${form_set_count}"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select the option</option>
                                                            <option value="{{ encryptId(1) }}">Good</option>
                                                            <option value="{{ encryptId(2) }}">Fair</option>
                                                            <option value="{{ encryptId(3) }}">Poor</option>

                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.switch_condition') }}</label>
                                                        <select name="switch_condition[${form_set_count}]" id="switch_condition_${form_set_count}"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select the option</option>
                                                            <option value="{{ encryptId(1) }}">Good</option>
                                                            <option value="{{ encryptId(2) }}">Fair</option>
                                                            <option value="{{ encryptId(3) }}">Poor</option>

                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.status') }}</label>
                                                        <select name="status[${form_set_count}]" id="status_${form_set_count}"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Status</option>
                                                            @foreach ($fireStatus as $list)
                                                                <option value="{{ encryptId($list->id) }}">
                                                                    {{ $list->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.remarks') }}</label>
                                                        <textarea name="remarks[${form_set_count}]" id="remarks_${form_set_count}" class="form-control" style="resize: none;" rows="4"></textarea>
                                                    </div>
                                                </div>

                                            </div>
                    `;

                    let newFormSetElement = $(newFormSet);

                    let locationSelect = newFormSetElement.find('select[name^="department"]');
                    GetDepartment(locationSelect);

                    $('.form-wrapper').append(newFormSetElement);
                    newFormSetElement.find('select.single-select').select2({
                        width: '100%'
                    });
                    $("select[name='department[" + form_set_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Please select the department',
                        }
                    });
                    $("select[name='status[" + form_set_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Please select the status',
                        }
                    });
                    $("select[name='switch_condition[" + form_set_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Please select the Switch Condition',
                        }
                    });
                    $("select[name='light_condition[" + form_set_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Please select the light Condition',
                        }
                    });
                    $("select[name='power_supply[" + form_set_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Please select the Power Suply',
                        }
                    });
                    $("select[name='condition_of_light[" + form_set_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Please select the Condition of Light',
                        }
                    });
                    $("select[name='type_of_light[" + form_set_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Please select the Type of Light',
                        }
                    });
                    $("input[name='location[" + form_set_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Please add the location',
                        }
                    });
                    $("input[name='emergency_light_number[" + form_set_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Please add the Emergency of Light',
                        }
                    });
                    $("input[name='quantity[" + form_set_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Please add the quantity',
                        }
                    });

                    $("input[name='capacity[" + form_set_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Please add the capacity',
                        }
                    });

                    $("textarea[name='remarks[" + form_set_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Please add the remarks',
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
                    url: "{{ admin_url('fire/emergency-light-inspection/get/department') }}",
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
                    let newSerialNumber = 'EML-' + ('000000' + idx).slice(-6);
                    $(this).find("input[name^='sr_no']").val(newSerialNumber);

                    $(this).find('input[name^="sr_no"]').attr('name', 'sr_no[' + idx + ']');
                    $(this).find('select[name^="department"]').attr('name', 'department[' + idx + ']');
                    $(this).find('select[name^="condition_of_light"]').attr('name', 'condition_of_light[' + idx + ']');
                    $(this).find('select[name^="type_of_light"]').attr('name', 'type_of_light[' + idx + ']');
                    $(this).find('select[name^="power_supply"]').attr('name', 'power_supply[' + idx + ']');
                    $(this).find('select[name^="switch_condition"]').attr('name', 'switch_condition[' + idx + ']');
                    $(this).find('select[name^="light_condition"]').attr('name', 'light_condition[' + idx + ']');
                    $(this).find('select[name^="status"]').attr('name', 'status[' + idx + ']');
                    $(this).find('input[name^="resource_code"]').attr('name', 'resource_code[' + idx + ']');
                    $(this).find('input[name^="quantity"]').attr('name', 'quantity[' + idx + ']');
                    $(this).find('textarea[name^="check_items"]').attr('name', 'check_items[' + idx + ']');
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
                        text: 'At least One Emergency Light Inspection is required.',
                        confirmButtonColor: '#3085d6'
                    });
                    return;
                }
                $(this).closest('.form-set').remove();
                updatePageIndices();

            });
        </script>
    @endpush
