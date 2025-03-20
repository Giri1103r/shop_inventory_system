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
                                        action="{{ admin_url('safety/eye-wash-inspection/monthly/add/submit') }}"
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
                                        </div>
                                        <hr>
                                        <div class="form-wrapper">
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
                                                        <!-- Checkboxes -->
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
                                                                    name="auditbility[]" id="auditbility" value="YES">
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
                        "temperature[1]": {
                            required: true,
                        },
                        "pressure[1]": {
                            required: true,
                        },
                        "quality[1]": {
                            required: true,
                        },
                        "water[1]": {
                            required: true,
                        },
                        "receptacle[1]": {
                            required: true,
                        },
                        "eyewash_heads[1]": {
                            required: true,
                        },
                        "foot_pedal[1]": {
                            required: true,
                        },
                        "hfsov[1]": {
                            required: true,
                        },
                        "value[1]": {
                            required: true,
                        },
                        "condition[1]": {
                            required: true,
                        },
                        "location[1]": {
                            required: true,
                        },
                        "resource_code[1]": {
                            required: true,
                        },
                        "remarks[1]": {
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
                        "temperature[1]": {
                            required: "Please add the temperature of the water",
                        },
                        "location[1]": {
                            required: "Please Select The Location",
                        },
                        "condition[1]": {
                            required: "Please add the condition of the Eye wash inspection",
                        },
                        "value[1]": {
                            required: "Please add the value",
                        },
                        "hfsov[1]": {
                            required: "Please add the value of Hand free stay open",
                        },
                        "foot_pedal[1]": {
                            required: "Please add foot pedal value",
                        },
                        "eyewash_heads[1]": {
                            required: "Please add the name of Eyewash heads",
                        },
                        "receptacle[1]": {
                            required: "Please add the name of receptable used",
                        },
                        "water[1]": {
                            required: "Please select the water quality",
                        },
                        "quality[1]": {
                            required: "Please select the quality of water",
                        },
                        "pressure[1]": {
                            required: "Please add the pressure of the water",
                        },
                        "resource_code[1]": {
                            required: "Please add the resource code",
                        },
                        "remarks[1]": {
                            required: "Please add remarks",
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
                                                        <select name="department[${form_set_count}]" id="department"
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
                                                                    name="blinking_light[${form_set_count}]" id="blinking_light"
                                                                    value="YES">
                                                                <label class="form-check-label" for="blinking_light">Blinking Light</label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="connection[${form_set_count}]" id="connection"
                                                                    value="YES">
                                                                <label class="form-check-label" for="connection">Connection</label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="auditbility[${form_set_count}]" id="auditbility"
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

                    $("select[name='condition[" + form_set_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Please add the condition of the Eye wash inspection',
                        }
                    });

                    $("textarea[name='remarks[" + form_set_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Please add the temperature details for the Eye wash inspection',
                        }
                    });

                    $("input[name='value[" + form_set_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Please add the valve',
                        }
                    });

                    $("input[name='hfsov[" + form_set_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Please add the value of Hand free stay open',
                        }
                    });

                    $("input[name='foot_pedal[" + form_set_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Please add foot pedal value',
                        }
                    });

                    $("input[name='eyewash_heads[" + form_set_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Please add the name of Eyewash heads',
                        }
                    });

                    $("input[name='receptacle[" + form_set_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Please add the name of receptable used',
                        }
                    });

                    $("select[name='water[" + form_set_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Please select the water quality',
                        }
                    });

                    $("input[name='quality[" + form_set_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Please select the quality of water',
                        }
                    });

                    $("input[name='pressure[" + form_set_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Please add the pressure of the water',
                        }
                    });

                    $("input[name='temperature[" + form_set_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Please add the temperature of the water',
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
                    $(this).find('input[name^="eyewash_heads"]').attr('name', 'eyewash_heads[' + idx + ']');
                    $(this).find('input[name^="receptacle"]').attr('name', 'receptacle[' + idx + ']');
                    $(this).find('select[name^="water"]').attr('name', 'water[' + idx + ']');
                    $(this).find('input[name^="quality"]').attr('name', 'quality[' + idx + ']');
                    $(this).find('input[name^="pressure"]').attr('name', 'pressure[' + idx + ']');
                    $(this).find('input[name^="temperature"]').attr('name', 'temperature[' + idx + ']');
                    $(this).find('textarea[name^="remarks"]').attr('name', 'temperature[' + idx + ']');

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
