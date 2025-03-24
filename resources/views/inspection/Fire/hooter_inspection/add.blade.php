@extends('admin.layouts.admin')
@section('title', 'Hooter Inspection Add')
@section('pageurl', admin_url('fire/hooter-inspection/list'))
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
                                    <x-button-back href="{{ admin_url('fire/hooter-inspection/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form mx-3">
                                    <form method="POST" id="eyewashAdd"
                                        action="{{ admin_url('fire/hooter-inspection/add/submit') }}"
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
                                                        class="form-control" value="{{ getDocumentReviewDate('SAF-0') }}"
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
                                            {{-- <div class="col-md-4 mb-2 d-flex flex-column align-items-center">
                                                <div class="form-group form-input text-center">
                                                    <label
                                                        class="form-label require">{{ __('inspection.observation_required') }}</label>
                                                    <div class="d-flex gap-3">
                                                        <div class="form-check">
                                                            <input type="radio" name="obs_choice"
                                                                id="yes_option" value="{{ YES }}" class="form-check-input"
                                                                required>
                                                            <label for="yes_option" class="form-check-label">Yes</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input type="radio" name="obs_choice" id="no_option"
                                                                value="{{ NO }}" class="form-check-input" required>
                                                            <label for="no_option" class="form-check-label">No</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div> --}}
                                        </div>
                                        <hr>
                                        <div class="form-wrapper">
                                            <div class="row mt-4 form-set">
                                                <div class="card-header-inner p-2">
                                                    <h4 class="text-white">Hooter Inspection Checklist</h4>
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
                                                            class="form-control" value="{{ HooterSequence() }}" readonly>
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.resource_code') }}</label>
                                                        <input type="text" name="resource_code[1]"
                                                            id = "resource_code" class="form-control">
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
                                                <div class="col-md-12 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.check_items') }}</label>
                                                        <textarea name="check_items[1]" id="check_items" class="form-control" style="resize: none;" rows="4"></textarea>
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
                                                <div class="col-md-8 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.observations') }}</label>
                                                        <div class="mt-1">
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="blinking_light[1]" id="blinking_light"
                                                                    value="YES">
                                                                <label class="form-check-label"
                                                                    for="blinking_light">Blinking Light</label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="connection[1]" id="connection" value="YES">
                                                                <label class="form-check-label"
                                                                    for="connection">Connection</label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="auditbility[1]" id="auditbility" value="YES">
                                                                <label class="form-check-label"
                                                                    for="auditbility">Audibility</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 mb-2">
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
                                                    <h4 class="text-white">Hooter Inspection Observation</h4>
                                                </div>

                                                {{-- <div class="d-flex justify-content-end align-items-center gap-2 m-2">
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
                                                </div> --}}

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
                        "check_items[1]": {
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
                            extension: "jpg",
                            filesize: 2097152
                        },
                        observation:{
                            required : true,
                        },
                
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
                        "check_items[1]": {
                            required: "Please add the checkitems",
                        },
                        "resource_code[1]": {
                            required: "Please add the resource code",
                        },
                        "remarks[1]": {
                            required: "Please add remarks",
                        },
                        device_image: {
                            required: "Please upload an image.",
                            extension: "Only JPG files are allowed.",
                            filesize: "Image must be under 2MB."
                        },
                        observation:{
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
                            title: 'Maximum Hooter Inspection CheckList Reached',
                            text: 'You can only add up to 200 Hooter Inspection CheckList.',
                            confirmButtonColor: '#3085d6'
                        });
                        return;
                    }

                    let newSerialNumber = 'HTR-' + ('00000' + serial_number).slice(-5);

                    var newFormSet = `
                        <div class="row mt-4 form-set">
                                                <div class="card-header-inner p-2">
                                                    <h4 class="text-white">Hooter Inspection Checklist</h4>
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
                                                            class="form-control" value="{{ HooterSequence() }}" readonly>
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.resource_code') }}</label>
                                                        <input type="text" name="resource_code[${form_set_count}]"
                                                            id = "resource_code" class="form-control">
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
                                                <div class="col-md-12 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.check_items') }}</label>
                                                        <textarea name="check_items[${form_set_count}]" id="check_items" class="form-control" style="resize: none;" rows="4"></textarea>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.quantity') }}</label>
                                                        <input type="number" name="quantity[${form_set_count}]" id = "quantity"
                                                            class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-md-8 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.observations') }}</label>
                                                         <!-- Checkboxes -->
                                                        <div class="mt-1">
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="blinking_light[${form_set_count}]" id="blinking_light[${form_set_count}]"
                                                                    value="YES">
                                                                <label class="form-check-label" for="blinking_light">Blinking Light</label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="connection[${form_set_count}]" id="connection[${form_set_count}]"
                                                                    value="YES">
                                                                <label class="form-check-label" for="connection">Connection</label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="auditbility[${form_set_count}]" id="auditbility[${form_set_count}]"
                                                                    value="YES">
                                                                <label class="form-check-label" for="auditbility">Audibility</label>
                                                            </div>
                                                        </div>
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

                    $("select[name='department[" + form_set_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Please select the department',
                        }
                    });

                    $("input[name='resource_code[" + form_set_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Please add the resource code',
                        }
                    });

                    $("input[name='quantity[" + form_set_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Please add the quantity',
                        }
                    });

                    $("textarea[name='check_items[" + form_set_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Please add the check items',
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

                $(document).on('click', '#add-obs', function() {
                    let observationFormsets = $('.form-observation .form-obs').length;

                    if (currentFormSets >= maxObsSets) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Maximum Hooter Inspection Observation Limit Reached',
                            text: 'You can only add up to 5 Hooter Inspection Observation.',
                            confirmButtonColor: '#3085d6'
                        });
                        return;
                    }

                    var newObsSet = `
                        <div class="row mt-4 form-obs">
                                                <div class="card-header-inner p-2">
                                                    <h4 class="text-white">Hooter Inspection Observation</h4>
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

            function updatePageIndices() {
                $('.form-wrapper .form-set').each(function(index) {
                    let idx = index + 1;
                    let newSerialNumber = 'HTR-' + ('000000' + idx).slice(-6);
                    $(this).find("input[name^='sr_no']").val(newSerialNumber);

                    $(this).find('input[name^="sr_no"]').attr('name', 'sr_no[' + idx + ']');
                    $(this).find('select[name^="department"]').attr('name', 'department[' + idx + ']');
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
                        text: 'At least One Hooter Inspection Checklist is required.',
                        confirmButtonColor: '#3085d6'
                    });
                    return;
                }
                $(this).closest('.form-set').remove();
                updatePageIndices();

            });
        </script>
    @endpush
