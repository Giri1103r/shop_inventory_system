@extends('admin.layouts.admin')
@section('title', 'Safety Walk Observation Add')
@section('pageurl', admin_url('safety-walk-observation/list'))
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
                                        href="{{ admin_url('safety/safety-walk-observation/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="basic-form mx-3">
                                    <form method="POST" id="safetyWalkAdd"
                                        action="{{ admin_url('safety/safety-walk-observation/add/submit') }}"
                                        autocomplete="off" enctype="multipart/form-data">
                                        @csrf

                                        <div class="row">
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">{{ __('inspection.doc_no') }}</label>
                                                    <input type="text" name="doc_no" id = "doc_no" class="form-control"
                                                        placeholder="Enter the Document Number">
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
                                                        class="form-control" value="{{ getDocumentReviewDate('FSE-0') }}"
                                                        readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label require">{{ __('inspection.inspection_date') }}</label>
                                                    <input type="text" name="inspection_date" id = "inspection_date"
                                                        class="form-control" value="">
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">{{ __('inspection.shifts') }}</label>
                                                    <select name="shift_id" id="shift_id"
                                                        class=" form-control single-select" style="width: 100%">
                                                        <option value="">Select Shift
                                                        </option>
                                                        @foreach ($shift as $shift)
                                                            <option value="{{ encryptId($shift->id) }}">
                                                                {{ $shift->shift }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3 mb-3 form-input">
                                                <label for="emp_name" class="form-label ">Month</label>
                                                <div class="input-group date form-input  custom-height">
                                                    <input type="text" class="form-control " name="month"
                                                        id="month" autocomplete="off">
                                                    <div class="input-group-addon input-group-text">
                                                        <span class="fa fa-calendar"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">{{ __('inspection.unit') }}</label>
                                                    <select name="unit" id="unit"
                                                        class=" form-control single-select" style="width: 100%">
                                                        <option value="">Select Unit</option>
                                                        @foreach ($unit as $unit)
                                                            <option value="{{ encryptId($unit->id) }}">
                                                                {{ $unit->unit_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label require">{{ __('inspection.safety_walk_taken_by') }}</label>
                                                    <input type="text" name="safety_walk_taken_by"
                                                        id = "safety_walk_taken_by" class="form-control" value="">
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="form-wrapper">

                                            <div class="row mt-4 form-set">
                                                <div class="card-header-inner p-2">
                                                    <h4 class="text-white">
                                                        {{ __('inspection.previous_month_observation') }}
                                                    </h4>
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
                                                        <input type="text" name="sr_no[1][1]" id = "sr_no"
                                                            class="form-control"
                                                            value="{{ SafetyWalkPreviousObservation() }}" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.location') }}</label>
                                                        <select name="location[1][1]" id="location"
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
                                                        <label
                                                            class="form-label require">{{ __('inspection.observation') }}</label>
                                                        <input type="text" name="observation[1][1]" id = "observation"
                                                            class="form-control" placeholder="Observation">
                                                    </div>
                                                </div>
                                                <div class="form-input col-md-4 mb-2">
                                                    <label class="form-label">Image</label>
                                                    <input type="file" name="checklist_file[1][1]" id="checklist_file"
                                                        class="form-control form-control-sm" accept="image/.*"
                                                        placeholder="Enter the image">
                                                    <small>Allowed file types: jpg</small>
                                                    <div id="checklist_file_error" class="text-danger"></div>
                                                    {{-- @error('checklist_file')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror --}}
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.recomended_action') }}</label>
                                                        <input type="text" name="recomended_action[1][1]"
                                                            id = "unit_of_measurement" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.employee') }}</label>
                                                        <input type="text" name="emp_id[1][1]" id = "emp_id"
                                                            class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.date_of_compliance') }}</label>
                                                        <input type="text" name="date_of_compliance[1][1]"
                                                            id = "date_of_compliance" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.observation_status') }}</label>
                                                        <select name="observation_status[1][1]" id="observation_status"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Observation Status</option>
                                                            <option value="{{ encryptId(1) }}">Active</option>
                                                            <option value="{{ encryptId(0) }}">DeActive</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.remarks') }}</label>
                                                        <textarea name="remarks[1][1]" id="remarks" class="form-control" style="resize: none;"></textarea>

                                                    </div>
                                                </div>

                                            </div>
                                        </div>

                                        {{-- Current Month Observation --}}
                                        <div class="form-wrapper-current">
                                            <div class="row mt-4 form-set-current">
                                                <div class="card-header-inner p-2">
                                                    <h4 class="text-white">
                                                        {{ __('inspection.current_month_observation') }}
                                                    </h4>
                                                </div>

                                                <div class="d-flex justify-content-end gap-0 m-2">
                                                    <button class="btn btn-primary add-row me-3" type="button"
                                                        id="add-row-current" style="width: 84px;">
                                                        Add
                                                    </button>
                                                    <button type="button" class="btn btn-danger remove-row-current">
                                                        <i class="fa-solid fa-trash"></i> Remove
                                                    </button>

                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.sr_no') }}</label>
                                                        <input type="text" name="sr_no[2][1]" id = "sr_no"
                                                            class="form-control"
                                                            value="{{ SafetyWalkCurrentObservation() }}" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.location') }}</label>
                                                        <select name="location[2][1]" id="location[2][1]"
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
                                                        <label
                                                            class="form-label">{{ __('inspection.observation') }}</label>
                                                        <input type="text" name="observation[2][1]" id = "observation"
                                                            class="form-control" placeholder="Observation">
                                                    </div>
                                                </div>
                                                <div class="form-input col-md-4 mb-2">
                                                    <label class="form-label">Image</label>
                                                    <input type="file" name="checklist_file[2][1]" id="checklist_file"
                                                        class="form-control form-control-sm" accept="image/.*"
                                                        placeholder="Enter the image">
                                                    <small>Allowed file types: jpg</small>
                                                    <div id="checklist_file_error" class="text-danger"></div>
                                                    {{-- @error('checklist_file')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror --}}
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.recomended_action') }}</label>
                                                        <input type="text" name="recomended_action[2][1]"
                                                            id = "unit_of_measurement" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.employee') }}</label>
                                                        <input type="text" name="emp_id[2][1]" id = "emp_id"
                                                            class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.date_of_compliance') }}</label>
                                                        <input type="text" name="date_of_compliance[2][1]"
                                                            id = "date_of_compliance" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.observation_status') }}</label>
                                                        <select name="observation_status[2][1]"
                                                            id="observation_status[2][1]"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Observation Status</option>
                                                            <option value="{{ encryptId(1) }}">Active</option>
                                                            <option value="{{ encryptId(0) }}">DeActive</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.remarks') }}</label>
                                                        <textarea name="remarks[2][1]" id="remarks" class="form-control" style="resize: none;"></textarea>

                                                    </div>
                                                </div>

                                            </div>
                                        </div>


                                        <div class="submit-button" style="text-align: right;">
                                            <x-button-submit class="submit"></x-button-submit>
                                            <x-button-reset class="submit"></x-button-reset>
                                            <x-button-cancel
                                                href="{{ admin_url('safety/safety-walk-observation/list') }}"></x-button-cancel>
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
                flatpickr("#observation_date", {
                    dateFormat: "d-m-Y",
                    minDate: new Date(),
                });

                flatpickr("#date_of_compliance[1][1]", {
                        dateFormat: "d-m-Y",
                        minDate: new Date(),
                    });
                flatpickr("#date_of_compliance[2][1]", {
                        dateFormat: "d-m-Y",
                        minDate: new Date(),
                    });

                $('#month').datepicker({
                    format: 'MM',
                    minViewMode: 1,
                    autoclose: true
                });
            });
            $(function() {
                $.validator.addMethod("noSpaces", function(value, element) {
                    return this.optional(element) || value.trim().length > 0;
                }, "This field cannot contain only spaces");

                $.validator.addMethod("uniqueItemCode", function(value, element) {
                    var itemCodes = [];

                    $("input[name^='item_code']").each(function() {
                        var itemCodeValue = $(this).val();
                        if (itemCodeValue) {
                            itemCodes.push(itemCodeValue);
                        }
                    });

                    return itemCodes.indexOf(value) === itemCodes.lastIndexOf(value);
                }, "Item Code must be unique");


                $('#safetyWalkAdd').validate({
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
                        rev_date: {
                            required: true,
                        },
                        "inspection_date": {
                            required: true,
                        },
                        "shift_id": {
                            required: true,
                        },
                        "month": {
                            required: true,
                        },
                        "unit": {
                            required: true,
                        },
                        "safety_walk_taken_by": {
                            required: true,
                        },
                        "sr_no[1][1]": {
                            required: true,
                        },
                        "sr_no[2][1]": {
                            required: true,
                        },

                        "location[1][1]": {
                            required: true,
                        },
                        "location[2][1]": {
                            required: true,
                        },
                        "observation[1][1]": {
                            required: true,
                        },
                        "observation[2][1]": {
                            required: true,
                        },
                        "recomended_action[1][1]": {
                            required: true,
                        },
                        "recomended_action[2][1]": {
                            required: true,
                        },
                        "date_of_compliance[1][1]": {
                            required: true,
                        },
                        "date_of_compliance[2][1]": {
                            required: true,
                        },
                        "observation_status[1][1]": {
                            required: true,
                        },
                        "observation_status[2][1]": {
                            required: true,
                        },
                        "remarks[1][1]": {
                            required: true,
                        },
                        "remarks[2][1]": {
                            required: true,
                        },
                        "emp_id[1][1]": {
                            required: true,
                        },
                        "emp_id[2][1]": {
                            required: true,
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
                        "inspection_date": {
                            required: "Inspection Date is required",
                        },
                        "shift_id": {
                            required: "Shift is required",
                        },
                        "month": {
                            required: "Month is required",
                        },
                        "unit": {
                            required: "Unit is required",
                        },
                        "safety_walk_taken_by": {
                            required: "Safety Walk Taken By is required",
                        },
                        "sr_no[1][1]": {
                            required: "Serial Number is required",
                        },
                        "sr_no[2][1]": {
                            required: "Serial Number is required",
                        },

                        "location[1][1]": {
                            required: "Location is required",

                        },
                        "location[2][1]": {
                            required: "Location is required",

                        },
                        "observation[1][1]": {
                            required: "Observation is required",

                        },
                        "observation[2][1]": {
                            required: "Observation is required",

                        },
                        "recomended_action[1][1]": {
                            required: "Recomended Action is required",

                        },
                        "recomended_action[2][1]": {
                            required: "Recomended Action is required",

                        },
                        "date_of_compliance[1][1]": {
                            required: "Date of Compliance is required",

                        },
                        "date_of_compliance[2][1]": {
                            required: "Date of Compliance is required",

                        },
                        "observation_status[1][1]": {
                            required: "Observation Status is required",

                        },
                        "observation_status[2][1]": {
                            required: "Observation Status is required",

                        },
                        "remarks[1][1]": {
                            required: "Remarks is required",

                        },
                        "remarks[2][1]": {
                            required: "Remarks is required",
                        },
                        "emp_id[1][1]": {
                            required: "Employee is required",

                        },
                        "emp_id[2][1]": {
                            required: "Employee is required",
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
                        validator.errorList.forEach(function(error) {

                        });
                    }
                });
            });

            let form_set_count = 2;
            let formIndex = 1;
            const minFormSets = 1;
            const maxFormSets = 200;
            let serial_number = 2;

            let form_set_current_count = 2;
            let currentformIndex = 1;
            const minFormCurrentSets = 1;
            const maxFormCurrentSets = 200;
            let current_serial_number = 2;

            $(document).ready(function() {
                $(document).on('click', '#add-row', function() {
                    let currentFormSets = $('.form-wrapper .form-set').length;
                    if (currentFormSets >= maxFormSets) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Maximum observation Reached',
                            text: 'You can only add up to 200 Previous Month Observation',
                            confirmButtonColor: '#3085d6'
                        });
                        return;
                    }
                    let newSerialNumber = 'PREVIOUS-OBS-' + ('00000' + serial_number).slice(-5);

                    var newFormSet = `
                        <div class="row mt-4 form-set">
                                                <div class="card-header-inner p-2">
                                                    <h4 class="text-white">{{ __('inspection.previous_month_observation') }}</h4>
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
                                                        <input type="text" name="sr_no[1][${form_set_count}]" id = "sr_no"
                                                            class="form-control" value="${newSerialNumber}" readonly>
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.location') }}</label>
                                                        <select name="location[1][${form_set_count}]" id="location[1][${form_set_count}]"
                                                            class=" form-control single-select location-select" style="width: 100%">
                                                            <option value="">Select location</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                 <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.observation') }}</label>
                                                        <input type="text" name="observation[1][${form_set_count}]" id = "observation"
                                                            class="form-control" placeholder="Observation">
                                                    </div>
                                                </div>
                                                <div class="form-input col-md-4 mb-2">
                                                    <label class="form-label">Image</label>
                                                    <input type="file" name="checklist_file[1][${form_set_count}]" id="checklist_file"
                                                        class="form-control form-control-sm" accept="image/.*"
                                                        placeholder="Enter the image">
                                                    <small>Allowed file types: jpg</small>
                                                    <div id="checklist_file_error" class="text-danger"></div>
                                                    {{-- @error('checklist_file')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror --}}
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.recomended_action') }}</label>
                                                        <input type="text" name="recomended_action[1][${form_set_count}]"
                                                            id = "unit_of_measurement" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.employee') }}</label>
                                                        <input type="text" name="emp_id[1][${form_set_count}]" id = "emp_id[2][${form_set_count}]"
                                                            class="form-control">
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.date_of_compliance') }}</label>
                                                        <input type="text" name="date_of_compliance[1][${form_set_count}]"
                                                            id = "date_of_compliance[1][${form_set_count}]" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.observation_status') }}</label>
                                                        <select name="observation_status[1][${form_set_count}]" id="observation_status[1][${form_set_count}]"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Observation Status</option>
                                                            <option value="{{ encryptId(1) }}">Active</option>
                                                            <option value="{{ encryptId(0) }}">DeActive</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.remarks') }}</label>
                                                        <textarea name="remarks[1][${form_set_count}]" id="remarks" class="form-control" style="resize: none;"></textarea>

                                                    </div>
                                                </div>
                                            </div>
                    `;

                    let newFormSetElement = $(newFormSet); // Convert string to jQuery object

                    let locationSelect = newFormSetElement.find('select[name^="location[1]"]');
                    GetLocation(locationSelect);

                    $('.form-wrapper').append(newFormSetElement);

                    $("select[name='location[1][" + form_set_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Please select the Location',
                        }
                    });

                    $("input[name='observation[1][" + form_set_count + "]']").rules('add', {
                        required: true,
                        uniqueItemCode: true,
                        messages: {
                            required: 'Please Enter the Observation',
                        }
                    });
                    $("input[name='recomended_action[1][" + form_set_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Please Enter the Recomended Action',
                        }
                    });


                    $("input[name='date_of_compliance[1][" + form_set_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Please Select the Date of Compliance',
                        }
                    });

                    $("input[name='observation_status[1][" + form_set_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Please Select the Observation Status',
                        }
                    });

                    $("input[name='remarks[1][" + form_set_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Please Enter the Remarks',
                        }
                    });
                    serial_number++;

                    flatpickr("#date_of_compliance", {
                        dateFormat: "d-m-Y",
                        minDate: new Date(),
                    });

                    form_set_count++;
                    updatePageIndices();

                });


                $(document).on('click', '#add-row-current', function() {

                    let currentFormSets = $('.form-wrapper-current .form-set-current').length;
                    if (currentFormSets >= maxFormCurrentSets) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Maximum observation Reached',
                            text: 'You can only add up to 200 Current Month Observation',
                            confirmButtonColor: '#3085d6'
                        });
                        return;
                    }
                    let newSerialNumber = 'CURRENT-OBS-' + ('00000' + current_serial_number).slice(-5);

                    var newCurrentFormSet = `
                        <div class="row mt-4 form-set-current">
                                                <div class="card-header-inner p-2">
                                                    <h4 class="text-white">{{ __('inspection.current_month_observation') }}</h4>
                                                </div>

                                                <div class="d-flex justify-content-end gap-0 m-2">
                                                    <button class="btn btn-primary add-row-current me-3" type="button"
                                                        id="add-row-current" style="width: 84px;">
                                                        Add
                                                    </button>
                                                    <button type="button" class="btn btn-danger remove-row-current">
                                                        <i class="fa-solid fa-trash"></i> Remove
                                                    </button>

                                                </div>

                                                   <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.sr_no') }}</label>
                                                        <input type="text" name="sr_no[2][${form_set_current_count}]" id = "sr_no"
                                                            class="form-control" value="${newSerialNumber}" readonly>
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.location') }}</label>
                                                        <select name="location[2][${form_set_current_count}]" id="location[2][${form_set_current_count}]"
                                                            class=" form-control single-select location-select" style="width: 100%">
                                                            <option value="">Select location</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                 <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.observation') }}</label>
                                                        <input type="text" name="observation[2][${form_set_current_count}]" id = "observation"
                                                            class="form-control" placeholder="Observation">
                                                    </div>
                                                </div>
                                                <div class="form-input col-md-4 mb-2">
                                                    <label class="form-label">Image</label>
                                                    <input type="file" name="checklist_file[2][${form_set_current_count}]" id="checklist_file"
                                                        class="form-control form-control-sm" accept="image/.*"
                                                        placeholder="Enter the image">
                                                    <small>Allowed file types: jpg</small>
                                                    <div id="checklist_file_error" class="text-danger"></div>
                                                    {{-- @error('checklist_file')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror --}}
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.recomended_action') }}</label>
                                                        <input type="text" name="recomended_action[2][${form_set_current_count}]"
                                                            id = "unit_of_measurement" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.employee') }}</label>
                                                        <input type="text" name="emp_id[2][${form_set_current_count}]" id = "emp_id[2][${form_set_current_count}]"
                                                            class="form-control">
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.date_of_compliance') }}</label>
                                                        <input type="text" name="date_of_compliance[2][${form_set_current_count}]"
                                                            id = "date_of_compliance" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.observation_status') }}</label>
                                                        <select name="observation_status[2][${form_set_current_count}]" id="observation_status[2][${form_set_current_count}]"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Observation Status</option>
                                                            <option value="{{ encryptId(1) }}">Active</option>
                                                            <option value="{{ encryptId(0) }}">DeActive</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.remarks') }}</label>
                                                        <textarea name="remarks[2][${form_set_current_count}]" id="remarks" class="form-control" style="resize: none;"></textarea>

                                                    </div>
                                                </div>
                                            </div>
                    `;

                    let newFormCurrentSetElement = $(newCurrentFormSet); // Convert string to jQuery object

                    let locationSelect = newFormCurrentSetElement.find('select[name^="location[2]"]');
                    GetLocation(locationSelect);

                    $('.form-wrapper-current').append(newFormCurrentSetElement);

                    $("select[name='location[2][" + form_set_current_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Please select the Location',
                        }
                    });

                    $("input[name='observation[2][" + form_set_current_count + "]']").rules('add', {
                        required: true,
                        uniqueItemCode: true,
                        messages: {
                            required: 'Please Enter the Observation',
                        }
                    });
                    $("input[name='recomended_action[2][" + form_set_current_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Please Enter the Recomended Action',
                        }
                    });


                    $("input[name='date_of_compliance[2][" + form_set_current_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Please Select the Date of Compliance',
                        }
                    });

                    $("input[name='observation_status[2][" + form_set_current_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Please Select the Observation Status',
                        }
                    });

                    $("input[name='remarks[2][" + form_set_current_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Please Enter the Remarks',
                        }
                    });

                    flatpickr("#date_of_compliance", {
                        dateFormat: "d-m-Y",
                        minDate: new Date(),
                    });
                    current_serial_number++;
                    form_set_current_count++;
                    updateCurrentPageIndices();

                });
            });

            function GetLocation(selectElement) {
                $.ajax({
                    type: "GET",
                    url: "{{ admin_url('safety/eye-wash-inspection/monthly/get/locations') }}",
                    success: function(response) {

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

            function updateCurrentPageIndices() {
                $('.form-wrapper-current .form-set-current').each(function(index) {
                    let idx = index + 1;
                    let newSerialNumber = 'CURRENT-OBS-' + ('000000' + idx).slice(-6);
                    $(this).find("input[name^='sr_no']").val(newSerialNumber);

                    $(this).find('input[name^="sr_no"]').attr('name', 'sr_no[2][' + idx + ']');
                    $(this).find('select[name^="location"]').attr('name', 'location[2][' + idx + ']');
                    $(this).find('input[name^="observation"]').attr('name', 'observation[2][' + idx + ']');
                    $(this).find('select[name^="recomended_action"]').attr('name', 'recomended_action[2][' + idx + ']');
                    $(this).find('input[name^="date_of_compliance"]').attr('name', 'date_of_compliance[2][' + idx +
                        ']');
                    $(this).find('input[name^="observation_status"]').attr('name', 'observation_status[2][' + idx +
                        ']');
                    $(this).find('input[name^="remarks"]').attr('name', 'remarks[2][' + idx + ']');

                    $(this).find('select').select2();
                });
            }

            function updatePageIndices() {
                $('.form-wrapper .form-set').each(function(index) {
                    let idx = index + 1;
                    let newSerialNumber = 'PREVIOUS-OBS-' + ('000000' + idx).slice(-6);
                    $(this).find("input[name^='sr_no']").val(newSerialNumber);

                    $(this).find('input[name^="sr_no"]').attr('name', 'sr_no[1][' + idx + ']');
                    $(this).find('select[name^="location"]').attr('name', 'location[1][' + idx + ']');
                    $(this).find('input[name^="observation"]').attr('name', 'observation[1][' + idx + ']');
                    $(this).find('select[name^="recomended_action"]').attr('name', 'recomended_action[1][' + idx + ']');
                    $(this).find('input[name^="date_of_compliance"]').attr('name', 'date_of_compliance[1][' + idx +
                        ']');
                    $(this).find('input[name^="observation_status"]').attr('name', 'observation_status[1][' + idx +
                        ']');
                    $(this).find('input[name^="remarks"]').attr('name', 'remarks[1][' + idx + ']');

                    $(this).find('select').select2();
                });
            }


            $(document).on('click', '.remove-row', function() {
                let previousFormSets = $('.form-wrapper .form-set').length;

                if (previousFormSets <= minFormSets) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Minimum  Previous Month Observtion Required',
                        text: 'At least one Observation is required.',
                        confirmButtonColor: '#3085d6'
                    });
                    return;
                }
                $(this).closest('.form-set').remove();
                updatePageIndices();

            });

            $(document).on('click', '.remove-row-current', function() {
                let currentFormSets = $('.form-wrapper-current .form-set-current').length;

                if (currentFormSets <= minFormCurrentSets) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Minimum  Current Month Observtion Required',
                        text: 'At least one Observation is required.',
                        confirmButtonColor: '#3085d6'
                    });
                    return;
                }
                $(this).closest('.form-set-current').remove();
                updateCurrentPageIndices();

            });
        </script>
    @endpush
