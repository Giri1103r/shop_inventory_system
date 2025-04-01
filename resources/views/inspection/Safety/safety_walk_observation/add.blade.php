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
                                                        placeholder="Enter the Document Number" value="{{ old('doc_no') }}">
                                                </div>
                                                @error('doc_no')
                                                    <div class="error">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label require">{{ __('inspection.issue_date') }}</label>
                                                    <input type="text" name="issue_date" id = "issue_date"
                                                        class="form-control" placeholder="Issued Date"
                                                        value="{{ old('issue_date') }}">
                                                    @error('issue_date')
                                                        <div class="error">{{ $message }}</div>
                                                    @enderror
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
                                                        class="form-control" value="{{ old('inspection_date') }}">
                                                    @error('inspection_date')
                                                        <div class="error">{{ $message }}</div>
                                                    @enderror
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
                                                            <option value="{{ encryptId($shift->id) }}"
                                                                {{ old('shift_id') == encryptId($shift->id) ? 'selected' : '' }}>
                                                                {{ $shift->shift }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('shift_id')
                                                        <div class="error">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-3 mb-3 form-input">
                                                <label for="emp_name" class="form-label ">Month</label>
                                                <div class="input-group date form-input  custom-height">
                                                    <input type="text" class="form-control " name="month"
                                                        id="month" autocomplete="off" value="{{ old('month') }}">
                                                    <div class="input-group-addon input-group-text">
                                                        <span class="fa fa-calendar"></span>
                                                    </div>
                                                </div>
                                                @error('month')
                                                    <div class="error">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">{{ __('inspection.unit') }}</label>
                                                    <select name="unit" id="unit"
                                                        class=" form-control single-select" style="width: 100%">
                                                        <option value="">Select Unit</option>
                                                        @foreach ($unit as $unit)
                                                            <option value="{{ encryptId($unit->id) }}"
                                                                {{ old('unit') == encryptId($unit->id) ? 'selected' : '' }}>
                                                                {{ $unit->unit_name }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('unit')
                                                        <div class="error">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label require">{{ __('inspection.safety_walk_taken_by') }}</label>
                                                    <input type="text" name="safety_walk_taken_by"
                                                        id = "safety_walk_taken_by" class="form-control"
                                                        value="{{ old('safety_walk_taken_by') }}">
                                                    @error('safety_walk_taken_by')
                                                        <div class="error">{{ $message }}</div>
                                                    @enderror
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
                                                            class="form-label require">{{ __('inspection.location') }}</label>
                                                        <select name="location[1]" id="location[1]"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Location</option>
                                                            @foreach ($locations as $location)
                                                                <option value="{{ encryptId($location->id) }}"
                                                                    {{ old('location.1') == encryptId($location->id) ? 'selected' : '' }}>
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
                                                            class="form-label require">{{ __('inspection.date_of_observation') }}</label>
                                                        <input type="text" name="date_of_observation[1]"
                                                            id = "date_of_observation"
                                                            class="form-control date_of_observation"
                                                            value="{{ old('date_of_observation.1') }}">
                                                    </div>
                                                    @error('date_of_observation.1')
                                                        <div class="error">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.observation') }}</label>
                                                        <input type="text" name="observation[1]" id = "observation"
                                                            class="form-control " placeholder="Observation"
                                                            value="{{ old('observation.1') }}">
                                                        @error('observation.1')
                                                            <div class="error">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="form-input col-md-4 mb-2">
                                                    <label class="form-label require">Image</label>
                                                    <input type="hidden" name="checklist_file[1]" value="">
                                                    <input type="file" name="checklist_file[1]" id="checklist_file"
                                                        class="form-control form-control-sm"
                                                        accept="image/jpeg, image/png" placeholder="Upload a new image">
                                                    <small>Allowed file types: .jpg, .jpeg, .png</small>

                                                    @error('checklist_file.1')
                                                        <div class="error">{{ $message }}</div>
                                                    @enderror
                                                </div>


                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.recomended_action') }}</label>
                                                        <input type="text" name="recomended_action[1]"
                                                            id = "unit_of_measurement" class="form-control"
                                                            value="{{ old('recomended_action.1') }}">
                                                        @error('recomended_action.1')
                                                            <div class="error">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.employee') }}</label>
                                                        <select name="emp_id[1]" id="emp_id[1]"
                                                            class="form-control single-select emp_id" style="width: 100%">
                                                            <option value="">Select Employee Name</option>
                                                        </select>
                                                    </div>
                                                    @error('emp_id.1')
                                                        <div class="error">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.date_of_compliance') }}</label>
                                                        <input type="text" name="date_of_compliance[1]"
                                                            class="form-control date_of_compliance"
                                                            value="{{ old('date_of_compliance.1') }}">
                                                    </div>
                                                    @error('date_of_compliance.1')
                                                        <div class="error">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.observation_status') }}</label>
                                                        <select name="observation_status[1]" id="observation_status[1]"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Observation Status</option>
                                                            <option value="{{ encryptId(1) }}"
                                                                {{ old('observation_status.1') == encryptId(1) ? 'selected' : '' }}>
                                                                Active</option>
                                                            <option value="{{ encryptId(0) }}"
                                                                {{ old('observation_status.1') == encryptId(0) ? 'selected' : '' }}>
                                                                Deactive</option>
                                                        </select>
                                                    </div>
                                                    @error('observation_status.1')
                                                        <div class="error">{{ $message }}</div>
                                                    @enderror
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
                flatpickr(".date_of_observation", {
                    dateFormat: "d-m-Y",
                });

                flatpickr(".date_of_compliance", {
                    dateFormat: "d-m-Y",
                    minDate: new Date(),
                });


                $('#month').datepicker({
                    format: 'MM',
                    minViewMode: 1,
                    autoclose: true
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
                            signature_image: {
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
                            "checklist_file[1]": {
                                required: true,
                            },




                            "location[1]": {
                                required: true,
                            },

                            "observation[1]": {
                                required: true,
                            },
                            "date_of_observation[1]": {
                                required: true,
                            },

                            "recomended_action[1]": {
                                required: true,
                            },

                            "date_of_compliance[1]": {
                                required: true,
                            },

                            "observation_status[1]": {
                                required: true,
                            },

                            "remarks[1]": {
                                required: true,
                            },

                            "emp_id[1]": {
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
                            signature_image: {
                                required: "Signature is required",
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




                            "location[1]": {
                                required: "Location is required",

                            },

                            "observation[1]": {
                                required: "Observation is required",

                            },
                            "date_of_observation[1]": {
                                required: "Date of observation is required",

                            },

                            "recomended_action[1]": {
                                required: "Recomended Action is required",

                            },

                            "date_of_compliance[1]": {
                                required: "Date of Compliance is required",

                            },

                            "observation_status[1]": {
                                required: "Observation Status is required",

                            },

                            "remarks[1]": {
                                required: "Remarks is required",
                            },
                            "checklist_file[1]": {
                                required: "Image is Required",
                            },

                            "emp_id[1]": {
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

                $('.emp_id').select2({
                    ajax: {
                        url: '{{ admin_url('ohc/safety-petty-logbook/employeeid') }}',
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return {
                                search: params.term
                            };
                        },
                        processResults: function(data) {
                            return {
                                results: $.map(data, function(item) {
                                    return {
                                        id: item.id,
                                        text: item.text
                                    };
                                })
                            };
                        }
                    },
                    minimumInputLength: 1,
                    dropdownCssClass: 'form-control',
                    selectionCssClass: 'form-control'
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
                                                            class="form-label require">{{ __('inspection.location') }}</label>
                                                        <select name="location[${form_set_count}]" id="location[${form_set_count}]"
                                                            class=" form-control single-select location-select" style="width: 100%">
                                                            <option value="">Select location</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                 <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label">{{ __('inspection.date_of_observation') }}</label>
                                                        <input type="text" name="date_of_observation[${form_set_count}]"
                                                            class="form-control date_of_observation" >
                                                    </div>
                                                </div>
                                                 <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.observation') }}</label>
                                                        <input type="text" name="observation[${form_set_count}]" id = "observation"
                                                            class="form-control" placeholder="Observation">
                                                    </div>
                                                </div>
                                                <div class="form-input col-md-4 mb-2">
                                                    <label class="form-label">Image</label>
                                                    <input type="file" name="checklist_file[${form_set_count}]" id="checklist_file"
                                                        class="form-control form-control-sm"  accept="image/jpeg, image/png"
                                                        placeholder="Enter the image">
                                                    <small>Allowed file types: jpg</small>
                                                    <div id="checklist_file_error" class="text-danger"></div>

                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.recomended_action') }}</label>
                                                        <input type="text" name="recomended_action[${form_set_count}]"
                                                            id = "unit_of_measurement" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.employee') }}</label>
                                                       <select name="emp_id[${form_set_count}]" id="emp_id[${form_set_count}]"
                                                            class="form-control single-select emp_id" style="width: 100%">
                                                            <option value="">Select Employee Name</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.date_of_compliance') }}</label>
                                                        <input type="text" name="date_of_compliance[${form_set_count}]"
                                                             class="form-control date_of_compliance">
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

                let newFormCurrentSetElement = $(newCurrentFormSet); // Convert string to jQuery object

                let locationSelect = newFormCurrentSetElement.find('select[name^="location"]');
                GetLocation(locationSelect);

                $('.form-wrapper-current').append(newFormCurrentSetElement);

                $("select[name='location[" + form_set_current_count + "]']").rules('add', {
                    required: true,
                    messages: {
                        required: 'Please select the Location',
                    }
                });

                $("input[name='observation[" + form_set_current_count + "]']").rules('add', {
                    required: true,
                    uniqueItemCode: true,
                    messages: {
                        required: 'Please Enter the Observation',
                    }
                });
                $("input[name='date_of_observation[" + form_set_current_count + "]']").rules('add', {
                    required: true,
                    uniqueItemCode: true,
                    messages: {
                        required: 'Please Select the Observation Date',
                    }
                });
                $("input[name='recomended_action[" + form_set_current_count + "]']").rules('add', {
                    required: true,
                    messages: {
                        required: 'Please Enter the Recomended Action',
                    }
                });


                $("input[name='date_of_compliance[" + form_set_current_count + "]']").rules('add', {
                    required: true,
                    messages: {
                        required: 'Please Select the Date of Compliance',
                    }
                });

                $("input[name='observation_status[" + form_set_current_count + "]']").rules('add', {
                    required: true,
                    messages: {
                        required: 'Please Select the Observation Status',
                    }
                });

                $("input[name='remarks[" + form_set_current_count + "]']").rules('add', {
                    required: true,
                    messages: {
                        required: 'Please Enter the Remarks',
                    }
                });

                flatpickr(".date_of_compliance", {
                    dateFormat: "d-m-Y",
                    minDate: new Date(),
                });

                flatpickr(".date_of_observation", {
                    dateFormat: "d-m-Y",
                });


                current_serial_number++;
                form_set_current_count++;
                updateCurrentPageIndices();

                $('.emp_id').select2({
                    ajax: {
                        url: '{{ admin_url('ohc/safety-petty-logbook/employeeid') }}',
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return {
                                search: params.term
                            };
                        },
                        processResults: function(data) {
                            return {
                                results: $.map(data, function(item) {
                                    return {
                                        id: item.id,
                                        text: item.text
                                    };
                                })
                            };
                        }
                    },
                    minimumInputLength: 1,
                    dropdownCssClass: 'form-control',
                    selectionCssClass: 'form-control'
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

                    $(this).find('input[name^="sr_no"]').attr('name', 'sr_no[' + idx + ']');
                    $(this).find('select[name^="location"]').attr('name', 'location[' + idx + ']');
                    $(this).find('input[name^="date_of_observation"]').attr('name', 'date_of_observation[' + idx + ']');
                    $(this).find('input[name^="observation"]').attr('name', 'observation[' + idx + ']');
                    $(this).find('select[name^="recomended_action"]').attr('name', 'recomended_action[' + idx + ']');
                    $(this).find('input[name^="date_of_compliance"]').attr('name', 'date_of_compliance[' + idx +
                        ']');
                    $(this).find('input[name^="observation_status"]').attr('name', 'observation_status[' + idx +
                        ']');
                    $(this).find('input[name^="remarks"]').attr('name', 'remarks[' + idx + ']');

                    $(this).find('select').select2();
                });
            }




            $(document).on('click', '.remove-row-current', function() {
                let currentFormSets = $('.form-wrapper-current .form-set-current').length;

                if (currentFormSets <= minFormCurrentSets) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Minimum  Observtion Required',
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
