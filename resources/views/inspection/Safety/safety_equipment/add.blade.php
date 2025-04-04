@extends('admin.layouts.admin')
@section('title', 'Safety Equipment Add')
@section('pageurl', admin_url('fire-safety-equipment/list'))
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
                                        href="{{ admin_url('safety/fire-safety-equipment/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form mx-3">
                                    <form method="POST" id="eyewashAdd"
                                        action="{{ admin_url('safety/fire-safety-equipment/add/submit') }}"
                                        autocomplete="off" enctype="multipart/form-data">
                                        @csrf

                                        <div class="row">
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">{{ __('inspection.doc_no') }}</label>
                                                    <input type="text" name="doc_no" id = "doc_no" class="form-control"
                                                        placeholder="Enter the Document Number"
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
                                                    <input type="text" name="issue_date" id = "issue_date"
                                                        class="form-control" placeholder="Issued Date"
                                                        value="{{ displaydateformat($document_no->issue_date) }}" readonly>
                                                </div>
                                                @error('issue_date')
                                                    <div class="error">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label require">{{ __('inspection.rev_date') }}</label>
                                                    <input type="text" name="rev_date" id = "rev_date"
                                                        class="form-control" value="{{ $document_no->rev_dt }}" readonly>
                                                </div>
                                            </div>
                                            <input type="hidden" name="document_reference_id"
                                                value="{{ encryptId($document_no->id) }}">
                                        </div>
                                        <hr>
                                        <div class="form-wrapper">
                                            <div class="row mt-4 form-set">
                                                <div class="card-header-inner p-2">
                                                    <h4 class="text-white">{{ __('inspection.fire_safety_equipment') }}
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
                                                            class="form-label require">{{ __('inspection.equipment_name') }}</label>
                                                        <select name="equipment_name[1]" id="equipment_name"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Equipment</option>
                                                            @foreach ($equipment as $equipment)
                                                                <option value="{{ encryptId($equipment->id) }}"
                                                                    {{ old('equipment_name.1') == encryptId($equipment->id) ? 'selected' : '' }}>
                                                                    {{ $equipment->equipment_name }}</option>
                                                            @endforeach
                                                        </select>
                                                        @error('equipment_name.1')
                                                            <div class="error">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.item_code') }}</label>
                                                        <input type="text" name="item_code[1]" id = "item_code"
                                                            class="form-control">
                                                        @error('item_code.1')
                                                            <div class="error">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.standard_norms') }}</label>
                                                        <select name="standard_norms[1]" id="standard_norms"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Standard/Norms</option>
                                                            <option value="{{ encryptId(STANDARD) }}"
                                                                {{ old('standard_norms.1') == encryptId(STANDARD) ? 'selected' : '' }}>
                                                                STANDARD</option>
                                                            <option value="{{ encryptId(NORMS) }}"
                                                                {{ old('standard_norms.1') == encryptId(NORMS) ? 'selected' : '' }}>
                                                                NORMS</option>
                                                        </select>
                                                        @error('standard_norms.1')
                                                            <div class="error">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.equipment_category') }}</label>
                                                        <input type="text" name="equipment_category[1]"
                                                            id = "equipment_category" class="form-control">
                                                        @error('equipment_category.1')
                                                            <div class="error">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.unit_of_measurement') }}</label>
                                                        <input type="text" name="unit_of_measurement[1]"
                                                            id = "unit_of_measurement" class="form-control">
                                                        @error('unit_of_measurement.1')
                                                            <div class="error">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.minimum_order_value') }}</label>
                                                        <input type="text" name="minimum_order_value[1]"
                                                            id = "minimum_order_value" class="form-control">
                                                        @error('minimum_order_value.1')
                                                            <div class="error">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.economic_order_quantity') }}</label>
                                                        <input type="text" name="economic_order_quantity[1]"
                                                            id = "economic_order_quantity" class="form-control">
                                                        @error('economic_order_quantity.1')
                                                            <div class="error">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.observation_status') }}</label>
                                                        <select name="observation_status[1]" id="observation_status"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Observation Status</option>
                                                            <option value="{{ encryptId(1) }}"
                                                                {{ old('observation_status.1') == encryptId(1) ? 'selected' : '' }}>
                                                                Active</option>
                                                            <option value="{{ encryptId(0) }}"
                                                                {{ old('observation_status.1') == encryptId(0) ? 'selected' : '' }}>
                                                                DeActive</option>
                                                            @error('observation_status.1')
                                                                <div class="error">{{ $message }}</div>
                                                            @enderror
                                                        </select>
                                                        @error('observation_status.1')
                                                            <div class="error">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-12 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.remarks') }}</label>
                                                        <textarea name="remarks[1]" id="remarks" class="form-control" style="resize: none;">{{ old('remarks.1') }}</textarea>

                                                    </div>
                                                    @error('remarks.1')
                                                        <div class="error">{{ $message }}</div>
                                                    @enderror

                                                </div>

                                            </div>
                                        </div>

                                        <div class="submit-button" style="text-align: right;">
                                            <x-button-submit class="submit"></x-button-submit>
                                            <x-button-reset class="submit"></x-button-reset>
                                            <x-button-cancel
                                                href="{{ admin_url('safety/fire-safety-equipment/list') }}"></x-button-cancel>
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

                    $("input[name^='item_code']").each(function() {
                        var itemCodeValue = $(this).val();
                        if (itemCodeValue) {
                            itemCodes.push(itemCodeValue);
                        }
                    });

                    return itemCodes.indexOf(value) === itemCodes.lastIndexOf(value);
                }, "Item Code must be unique");

                $.validator.addMethod("uniqueEquipmentName", function(value, element) {
                    var isDuplicate = false;
                    $("input[name^='equipment_name']").each(function() {
                        if ($(this).val() === value && this !== element) {
                            isDuplicate = true;
                            return false;
                        }
                    });
                    return !isDuplicate;
                }, "This Equipment Name has already been selected.");



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
                        rev_date: {
                            required: true,
                        },
                        "equipment_name[1]": {
                            required: true,
                            uniqueEquipmentName: true,
                            remote: {
                                url: '{{ admin_url('safety/fire-safety-equipment/Equipmentunique') }}',
                                type: "post",
                                data: {
                                    equipment_name: function() {
                                        return $('#equipment_name').val();
                                    },
                                }
                            }
                        },
                        "item_code[1]": {
                            required: true,
                            uniqueItemCode: true,
                            remote: {
                                url: '{{ admin_url('safety/fire-safety-equipment/unique') }}',
                                type: "post",
                                data: {
                                    equipment_name: function() {
                                        return $('#equipment_name').val();
                                    },
                                    item_code: function() {
                                        return $('#item_code').val();
                                    }
                                }
                            }
                        },
                        "standard_norms[1]": {
                            required: true,
                        },
                        "equipment_category[1]": {
                            required: true,
                        },
                        "unit_of_measurement[1]": {
                            required: true,
                        },
                        "economic_order_quantity[1]": {
                            required: true,
                        },
                        "minimum_order_value[1]": {
                            required: true,
                        },
                        "observation_status[1]": {
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
                        "equipment_name[1]": {
                            required: "Please Enter the Equipment Name",
                            remote: "This Equipment Name is already Exists"
                        },
                        "item_code[1]": {
                            required: "Please Enter the Item Code",
                            uniqueItemCode: 'Item Code Must be Unique',
                        },
                        "standard_norms[1]": {
                            required: "Please Select the Standart/Norms",
                        },
                        "equipment_category[1]": {
                            required: "Please Enter the Equipment Category",
                        },
                        "unit_of_measurement[1]": {
                            required: "Please Enter the Unit Of Measurement",
                        },
                        "minimum_order_value[1]": {
                            required: "Please Enter the Minimum Order Value",
                        },
                        "economic_order_quantity[1]": {
                            required: "Please Enter the Economic Order Quantity",
                        },
                        "observation_status[1]": {
                            required: "Please Select Observation Status",
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

                $('#equipment_name').on('change', function() {
                    $(this).valid(); // Re-trigger validation
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



                    var newFormSet = `
                        <div class="row mt-4 form-set">
                                                <div class="card-header-inner p-2">
                                                    <h4 class="text-white">{{ __('inspection.fire_safety_equipment') }}</h4>
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
                                                            class="form-label require">{{ __('inspection.equipment_name') }}</label>
                                                        <select name="equipment_name[${form_set_count}]" id="equipment_name[${form_set_count}]"
                                                            class=" form-control single-select location-select" style="width: 100%">
                                                            <option value="">Select Equipment Name</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.item_code') }}</label>
                                                        <input type="text" name="item_code[${form_set_count}]"
                                                            id = "item_code_${form_set_count}" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.standard_norms') }}</label>
                                                        <select name="standard_norms[${form_set_count}]" id="standard_norms[${form_set_count}]"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Standard/Norms</option>
                                                            <option value="{{ encryptId(STANDARD) }}">STANDARD</option>
                                                            <option value="{{ encryptId(NORMS) }}">NORMS</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.equipment_category') }}</label>
                                                        <input type="text" name="equipment_category[${form_set_count}]" id = "equipment_category"
                                                            class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.unit_of_measurement') }}</label>
                                                        <input type="text" name="unit_of_measurement[${form_set_count}]" id = "unit_of_measurement"
                                                            class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.minimum_order_value') }}</label>
                                                        <input type="text" name="minimum_order_value[${form_set_count}]" id = "minimum_order_value"
                                                            class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.economic_order_quantity') }}</label>
                                                        <input type="text" name="economic_order_quantity[${form_set_count}]"
                                                            id = "economic_order_quantity" class="form-control">
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.observation_status') }}</label>
                                                        <select name="observation_status[${form_set_count}]" id="observation_status[${form_set_count}]"
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
                                                        <textarea name="remarks[${form_set_count}]" id="remarks" class="form-control" style="resize: none;"></textarea>

                                                    </div>
                                                </div>
                                            </div>
                    `;

                    let newFormSetElement = $(newFormSet); // Convert string to jQuery object

                    let locationSelect = newFormSetElement.find('select[name^="equipment_name"]');
                    GetEquipment(locationSelect);

                    $('.form-wrapper').append(newFormSetElement);

                    $("select[name='equipment_name[" + form_set_count + "]']").rules('add', {
                        required: true,
                        uniqueEquipmentName: true,
                        remote: {
                            url: '{{ admin_url('safety/fire-safety-equipment/Equipmentunique') }}',
                            type: "post",
                            data: {
                                equipment_name: function() {
                                    return $('#equipment_name_' + form_set_count).val();
                                },
                            }
                        },
                        messages: {
                            required: 'Please select the Equipment',
                            remote: 'Equipment already exists',
                        }
                    });

                    $("input[name='item_code[" + form_set_count + "]']").rules('add', {
                        required: true,
                        uniqueItemCode: true,
                        remote: {
                            url: '{{ admin_url('safety/fire-safety-equipment/unique') }}',
                            type: "post",
                            data: {
                                equipment_name: function() {
                                    return $('#equipment_name_' + form_set_count).val();
                                },
                                item_code: function() {
                                    return $('#item_code_' + form_set_count).val();
                                }
                            }
                        },
                        messages: {
                            required: 'Please add the Item code',
                            uniqueItemCode: 'Equipment name and Item Code Already Exists',
                            remote: 'Equipment name and Item Code Already Exists'
                        }
                    });

                    $("select[name='standard_norms[" + form_set_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Please Select the Standard Norms',
                        }
                    });


                    $("input[name='equipment_category[" + form_set_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Please Enter the Equipment Category',
                        }
                    });


                    $("input[name='unit_of_measurement[" + form_set_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Please Select the Unit of Measurement',
                        }
                    });

                    $("input[name='minimum_order_value[" + form_set_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Please Select the Minimum Order Value',
                        }
                    });

                    $("input[name='economic_order_quantity[" + form_set_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Please Enter the Economic order Quantity',
                        }
                    });

                    $("select[name='observation_status[" + form_set_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Please select the Observation Status',
                        }
                    });

                    $("input[name='remarks[" + form_set_count + "]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Please Enter the remarks',
                        }
                    });

                    serial_number++;
                    form_set_count++;
                    updatePageIndices();

                });
            });

            function GetEquipment(selectElement) {
                $.ajax({
                    type: "GET",
                    url: "{{ admin_url('safety/fire-safety-equipment/get/equipment') }}",
                    success: function(response) {

                        if (response.length > 0) {
                            let options = `<option value="">Select Equipment</option>`;
                            response.forEach(location => {
                                options +=
                                    `<option value="${location.id}">${location.equipment_name}</option>`;
                            });
                            $(selectElement).html(options).trigger('change');
                        }
                    }
                });
            }

            function updatePageIndices() {
                $('.form-wrapper .form-set').each(function(index) {
                    let idx = index + 1;

                    $(this).find('input[name^="sr_no"]').attr('name', 'sr_no[' + idx + ']');
                    $(this).find('select[name^="equipment_name"]').attr('name', 'equipment_name[' + idx + ']');
                    $(this).find('input[name^="item_code"]').attr('name', 'item_code[' + idx + ']');
                    $(this).find('select[name^="standard_norms"]').attr('name', 'standard_norms[' + idx + ']');
                    $(this).find('input[name^="equipment_category"]').attr('name', 'equipment_category[' + idx + ']');
                    $(this).find('input[name^="unit_of_measurement"]').attr('name', 'unit_of_measurement[' + idx + ']');
                    $(this).find('input[name^="minimum_order_value"]').attr('name', 'minimum_order_value[' + idx + ']');
                    $(this).find('input[name^="economic_order_quantity"]').attr('name', 'economic_order_quantity[' +
                        idx + ']');
                    $(this).find('input[name^="observation_status"]').attr('name', 'observation_status[' + idx + ']');
                    $(this).find('textarea[name^="remarks"]').attr('name', 'remarks[' + idx + ']');

                    $(this).find('select').select2();
                });
            }


            $(document).on('click', '.remove-row', function() {
                let currentFormSets = $('.form-wrapper .form-set').length;

                if (currentFormSets <= minFormSets) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Minimum One Equipment Required',
                        text: 'At least Equipment Name is required.',
                        confirmButtonColor: '#3085d6'
                    });
                    return;
                }
                $(this).closest('.form-set').remove();
                updatePageIndices();

            });
        </script>
    @endpush
