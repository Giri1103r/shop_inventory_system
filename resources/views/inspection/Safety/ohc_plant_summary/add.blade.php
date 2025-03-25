@extends('admin.layouts.admin')
@section('title', 'Safety Walk Observation Add')
@section('pageurl', admin_url('ohc-plant-summary/list'))
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
                                    <x-button-back href="{{ admin_url('safety/ohc-plant-summary/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="basic-form mx-3">
                                    <form method="POST" id="safetyWalkAdd"
                                        action="{{ admin_url('safety/ohc-plant-summary/add/submit') }}" autocomplete="off"
                                        enctype="multipart/form-data">
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
                                                        class="form-control" value="{{ getDocumentReviewDate('OHC-0') }}"
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
                                                    <label
                                                        class="form-label require">{{ __('inspection.updated_frequency') }}</label>
                                                    <input type="text" name="updated_frequency" id = "updated_frequency"
                                                        class="form-control" value="">
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
                                                    <h4 class="text-white">
                                                        {{ __('inspection.ohc_report') }}
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
                                                            class="form-label require">{{ __('inspection.description') }}</label>
                                                        <textarea name="description[1][1]" id="description" class="form-control" style="resize: none;"></textarea>
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.unit1') }}</label>
                                                        <select name="unit_1[1][1]" id="unit1[1][1]"
                                                            class="form-control single-select" style="width: 100%">
                                                            <option value="">Select Unit</option>
                                                            @foreach ($units as $unit)
                                                                <!-- Changed $units to $unit -->
                                                                <option value="{{ encryptId($unit->id) }}">
                                                                    {{ $unit->unit_name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.unit2') }}</label>
                                                        <select name="unit_2[1][1]" id="unit2[1][1]"
                                                            class="form-control single-select" style="width: 100%">
                                                            <option value="">Select Unit</option>
                                                            @foreach ($units as $unit)
                                                                <!-- Changed $units to $unit -->
                                                                <option value="{{ encryptId($unit->id) }}">
                                                                    {{ $unit->unit_name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.unit3') }}</label>
                                                        <select name="unit_3[1][1]" id="unit3[1][1]"
                                                            class="form-control single-select" style="width: 100%">
                                                            <option value="">Select Unit</option>
                                                            @foreach ($units as $unit)
                                                                <!-- Changed $units to $unit -->
                                                                <option value="{{ encryptId($unit->id) }}">
                                                                    {{ $unit->unit_name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.unit4') }}</label>
                                                        <select name="unit_4[1][1]" id="unit4[1][1]"
                                                            class="form-control single-select" style="width: 100%">
                                                            <option value="">Select Unit</option>
                                                            @foreach ($units as $unit)
                                                                <!-- Changed $units to $unit -->
                                                                <option value="{{ encryptId($unit->id) }}">
                                                                    {{ $unit->unit_name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.total_quantity') }}</label>
                                                        <input name="total_quantity[1][1]" id="total_quantity"
                                                            class="form-control" style="resize: none;" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Current Month Observation --}}
                                        <div class="form-wrapper-current">
                                            <div class="row mt-4 form-set-current">
                                                <div class="card-header-inner p-2">
                                                    <h4 class="text-white">
                                                        {{ __('inspection.fire_water_pump_house_details') }}
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
                                                            class="form-label require">{{ __('inspection.water_pump_storage_tank') }}</label>
                                                        <input name="water_pump_storage_tank[2][1]"
                                                            id="water_pump_storage_tank" class="form-control"
                                                            style="resize: none;" />
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.unit1') }}</label>
                                                        <select name="unit_1[2][1]" id="unit1[2][1]"
                                                            class="form-control single-select" style="width: 100%">
                                                            <option value="">Select Unit</option>
                                                            @foreach ($units as $unit)
                                                                <!-- Changed $units to $unit -->
                                                                <option value="{{ encryptId($unit->id) }}">
                                                                    {{ $unit->unit_name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.unit2') }}</label>
                                                        <select name="unit_2[2][1]" id="unit2[2][1]"
                                                            class="form-control single-select" style="width: 100%">
                                                            <option value="">Select Unit</option>
                                                            @foreach ($units as $unit)
                                                                <!-- Changed $units to $unit -->
                                                                <option value="{{ encryptId($unit->id) }}">
                                                                    {{ $unit->unit_name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.unit3') }}</label>
                                                        <select name="unit_3[2][1]" id="unit3[2][1]"
                                                            class="form-control single-select" style="width: 100%">
                                                            <option value="">Select Unit</option>
                                                            @foreach ($units as $unit)
                                                                <!-- Changed $units to $unit -->
                                                                <option value="{{ encryptId($unit->id) }}">
                                                                    {{ $unit->unit_name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.unit4') }}</label>
                                                        <select name="unit_4[2][1]" id="unit4[2][1]"
                                                            class="form-control single-select" style="width: 100%">
                                                            <option value="">Select Unit</option>
                                                            @foreach ($units as $unit)
                                                                <!-- Changed $units to $unit -->
                                                                <option value="{{ encryptId($unit->id) }}">
                                                                    {{ $unit->unit_name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>

                                        <div class="submit-button" style="text-align: right;">
                                            <x-button-submit class="submit"></x-button-submit>
                                            <x-button-reset class="submit"></x-button-reset>
                                            <x-button-cancel
                                                href="{{ admin_url('safety/ohc-plant-summary/list') }}"></x-button-cancel>
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

            });
            $(function() {
                $.validator.addMethod("noSpaces", function(value, element) {
                    return this.optional(element) || value.trim().length > 0;
                }, "This field cannot contain only spaces");


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
                        'signature_image': {
                            required: true,
                        },
                        updated_frequency: {
                            required: true,
                        },
                        "description[1][1]": {
                            required: true,
                        },
                        "unit_1[1][1]": {
                            required: true,
                        },
                        "unit_2[1][1]": {
                            required: true,
                        },
                        "unit_3[1][1]": {
                            required: true,
                        },
                        "unit_4[1][1]": {
                            required: true,
                        },
                        "water_pump_storage_tank[2][1]": {
                            required: true,
                        },
                        "unit_1[2][1]": {
                            required: true,
                        },
                        "unit_2[2][1]": {
                            required: true,
                        },
                        "unit_3[2][1]": {
                            required: true,
                        },
                        "unit_4[2][1]": {
                            required: true,
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
                        "inspection_date": {
                            required: "Inspection Date is required",
                        },
                        "description[1][1]": {
                            required: "Description is required",
                        },
                        "unit_1[1][1]": {
                            required: "Unit - 1 is required",
                        },
                        "unit_2[1][1]": {
                            required: "Unit - 2 is required",
                        },
                        "unit_3[1][1]": {
                            required: "Unit - 3 is required",
                        },
                        "unit_4[2][1]": {
                            required: "Unit - 4 is required",
                        },
                        "unit_1[2][1]": {
                            required: "Unit - 1 is required",
                        },
                        "unit_2[2][1]": {
                            required: "Unit - 2 is required",
                        },
                        "unit_3[2][1]": {
                            required: "Unit - 3 is required",
                        },
                        "unit_4[2][1]": {
                            required: "Unit - 4 is required",
                        },
                        "water_pump_storage_tank[2][1]": {
                            required: "Water Pump and Storage Tank is required",
                        },
                        updated_frequency: {
                            required: "Updated Frequency is required",
                        },
                        'signature_image': {
                            required: "Signature is required",
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
                            title: 'Maximum Limit Reached',
                            text: 'You can only add up to 200 Record',
                            confirmButtonColor: '#3085d6'
                        });
                        return;
                    }

                    var newFormSet = `
                            <div class="row mt-4 form-set">
                                <div class="card-header-inner p-2">
                                    <h4 class="text-white">{{ __('inspection.ohc_report') }}</h4>
                                </div>

                                <div class="d-flex justify-content-end gap-0 m-2">
                                    <button class="btn btn-primary add-row me-3" type="button" id="add-row" style="width: 84px;">
                                        Add
                                    </button>
                                    <button type="button" class="btn btn-danger remove-row">
                                        <i class="fa-solid fa-trash"></i> Remove
                                    </button>
                                </div>

                                <div class="col-md-4 mb-2">
                                    <div class="form-group form-input">
                                        <label class="form-label require">{{ __('inspection.description') }}</label>
                                        <textarea name="description[1][${form_set_count}]" id="description[1][${form_set_count}]" class="form-control description" style="resize: none;"></textarea>
                                    </div>
                                </div>

                                <div class="col-md-4 mb-2">
                                    <div class="form-group form-input">
                                        <label class="form-label require">{{ __('inspection.unit1') }}</label>
                                        <select name="unit_1[1][${form_set_count}]" id="unit1[1][${form_set_count}]" class="form-control single-select unit_1" style="width: 100%">
                                            <option value="">Select Unit</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4 mb-2">
                                    <div class="form-group form-input">
                                        <label class="form-label require">{{ __('inspection.unit2') }}</label>
                                        <select name="unit_2[1][${form_set_count}]" id="unit2[1][${form_set_count}]" class="form-control single-select unit_2" style="width: 100%">
                                            <option value="">Select Unit</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4 mb-2">
                                    <div class="form-group form-input">
                                        <label class="form-label require">{{ __('inspection.unit3') }}</label>
                                        <select name="unit_3[1][${form_set_count}]" id="unit3[1][${form_set_count}]" class="form-control single-select unit_3" style="width: 100%">
                                            <option value="">Select Unit</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4 mb-2">
                                    <div class="form-group form-input">
                                        <label class="form-label require">{{ __('inspection.unit4') }}</label>
                                        <select name="unit_4[1][${form_set_count}]" id="unit4[1][${form_set_count}]" class="form-control single-select unit_4" style="width: 100%">
                                            <option value="">Select Unit</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4 mb-2">
                                    <div class="form-group form-input">
                                        <label class="form-label require">{{ __('inspection.total_quantity') }}</label>
                                        <input name="total_quantity[1][${form_set_count}]" id="total_quantity[1][${form_set_count}]" class="form-control total_quantity" style="resize: none;" />
                                    </div>
                                </div>
                            </div>
                        `;

                    let newFormSetElement = $(newFormSet);

                    for (let i = 1; i <= 4; i++) {
                        let unitSelect = newFormSetElement.find(`select[name^="unit_${i}[1]"]`);
                        GetUnit(unitSelect);
                    }

                    $('.form-wrapper').append(newFormSetElement);

                    newFormSetElement.find(".description").rules('add', {
                        required: true,
                        messages: {
                            required: 'Description is Required',
                        }
                    });

                    for (let i = 1; i <= 4; i++) {
                        newFormSetElement.find(`.unit_${i}`).rules('add', {
                            required: true,
                            messages: {
                                required: 'Unit - ' + i + ' is Required',
                            }
                        });
                    }
                    form_set_count++;
                    updatePageIndices();
                });



                $(document).on('click', '#add-row-current', function() {
                    let currentFormSets = $('.form-wrapper-current .form-set-current').length;
                    if (currentFormSets >= maxFormCurrentSets) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Maximum Limit Reached',
                            text: 'You can only add up to 200 Record',
                            confirmButtonColor: '#3085d6'
                        });
                        return;
                    }

                    var newCurrentFormSet = `
                        <div class="row mt-4 form-set-current">
                            <div class="card-header-inner p-2">
                                <h4 class="text-white">{{ __('inspection.fire_water_pump_house_details') }}</h4>
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
                                    <label class="form-label require">{{ __('inspection.water_pump_storage_tank') }}</label>
                                    <input name="water_pump_storage_tank[2][${form_set_count}]"
                                        id="water_pump_storage_tank" class="form-control water_pump_storage_tank"
                                        style="resize: none;" />
                                </div>
                            </div>

                            <div class="col-md-4 mb-2">
                                <div class="form-group form-input">
                                    <label class="form-label require">{{ __('inspection.unit1') }}</label>
                                    <select name="unit_1[2][${form_set_count}]" id="unit1[2][${form_set_count}]"
                                        class="form-control single-select unit_1" style="width: 100%">
                                        <option value="">Select Unit</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4 mb-2">
                                <div class="form-group form-input">
                                    <label class="form-label require">{{ __('inspection.unit2') }}</label>
                                    <select name="unit_2[2][${form_set_count}]" id="unit2[2][${form_set_count}]"
                                        class="form-control single-select unit_2" style="width: 100%">
                                        <option value="">Select Unit</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4 mb-2">
                                <div class="form-group form-input">
                                    <label class="form-label require">{{ __('inspection.unit3') }}</label>
                                    <select name="unit_3[2][${form_set_count}]" id="unit3[2][${form_set_count}]"
                                        class="form-control single-select unit_3" style="width: 100%">
                                        <option value="">Select Unit</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4 mb-2">
                                <div class="form-group form-input">
                                    <label class="form-label require">{{ __('inspection.unit4') }}</label>
                                    <select name="unit_4[2][${form_set_count}]" id="unit4[2][${form_set_count}]"
                                        class="form-control single-select unit_4" style="width: 100%">
                                        <option value="">Select Unit</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    `;

                    let newFormCurrentSetElement = $(newCurrentFormSet); // Convert string to jQuery object

                    // Log the new element for debugging
                    console.log('New form set:', newFormCurrentSetElement);

                    // Append the form set to the DOM
                    $('.form-wrapper-current').append(newFormCurrentSetElement);

                    // Add validation rules for the Water Pump Storage Tank input using the dynamic name attribute
                    $("input[name='water_pump_storage_tank[2][" + form_set_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Water Pump and Storage Tank is required',
                        }
                    });

                    // Add validation rules for unit selects (unit 1 to unit 4)
                    for (let i = 1; i <= 4; i++) {
                        $("select[name='unit_" + i + "[2][" + form_set_count + "]']").rules('add', {
                            required: true,
                            messages: {
                                required: 'Unit - ' + i + ' is Required',
                            }
                        });
                    }

                    // Increment form set count
                    form_set_current_count++;

                    // Update the page indices if necessary
                    updateCurrentPageIndices();
                });


            });

            function GetUnit(selectElement) {
                $.ajax({
                    type: "GET",
                    url: "{{ admin_url('safety/forklift-inspection/get/unit') }}",
                    success: function(response) {

                        if (response.length > 0) {
                            let options = `<option value="">Select Unit</option>`;
                            response.forEach(location => {
                                options +=
                                    `<option value="${location.id}">${location.unit_name}</option>`;
                            });
                            $(selectElement).html(options).trigger('change');
                        }
                    }
                });
            }

            function updateCurrentPageIndices() {
                $('.form-wrapper-current .form-set-current').each(function(index) {
                    let idx = index + 1;

                    $(this).find('input[name^="water_pump_storage_tank"]').attr('name', 'water_pump_storage_tank[2][' +
                        idx + ']');
                    $(this).find('select[name^="unit_1"]').attr('name', 'unit_1[2][' + idx + ']');
                    $(this).find('input[name^="unit_2"]').attr('name', 'unit_2[2][' + idx + ']');
                    $(this).find('select[name^="unit_3"]').attr('name', 'unit_3[2][' + idx + ']');
                    $(this).find('input[name^="unit_4"]').attr('name', 'unit_4[2][' + idx +
                        ']');
                    $(this).find('select').select2();
                });
            }

            function updatePageIndices() {
                $('.form-wrapper .form-set').each(function(index) {
                    let idx = index + 1;

                    $(this).find('input[name^="description"]').attr('name', 'description[1][' + idx + ']');
                    $(this).find('select[name^="unit_1"]').attr('name', 'unit_1[1][' + idx + ']');
                    $(this).find('input[name^="unit_2"]').attr('name', 'unit_2[1][' + idx + ']');
                    $(this).find('select[name^="unit_3"]').attr('name', 'unit_3[1][' + idx + ']');
                    $(this).find('input[name^="unit_4"]').attr('name', 'unit_4[1][' + idx +
                        ']');
                    $(this).find('input[name^="total_quantity"]').attr('name', 'total_quantity[1][' + idx +
                        ']');

                    $(this).find('select').select2();
                });
            }


            $(document).on('click', '.remove-row', function() {
                let previousFormSets = $('.form-wrapper .form-set').length;

                if (previousFormSets <= minFormSets) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Minimum Limit',
                        text: 'At least one Record is required.',
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
                        title: 'Minimum  Limit',
                        text: 'At least one Record is required.',
                        confirmButtonColor: '#3085d6'
                    });
                    return;
                }
                $(this).closest('.form-set-current').remove();
                updateCurrentPageIndices();

            });
        </script>
    @endpush
