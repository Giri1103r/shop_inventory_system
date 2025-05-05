@extends('admin.layouts.admin')
@section('title', 'Forklift Inspection Add')
@section('pageurl', admin_url('safety/forklift-inspection/list'))
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
                                        href="{{ admin_url('safety/forklift-inspection/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="basic-form mx-3">
                                    <form method="POST" id="safetyWalkAdd"
                                        action="{{ admin_url('safety/forklift-inspection/add/submit') }}" autocomplete="off"
                                        enctype="multipart/form-data">
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
                                                        class="form-control inspection_date"
                                                        value="{{ old('inspection_date') }}">
                                                </div>
                                                @error('inspection_date')
                                                    <div class="error">{{ $message }}</div>
                                                @enderror
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
                                            @error('signature_upload')
                                                <div class="error">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <hr>
                                        {{-- Current Month Observation --}}
                                        <div class="form-wrapper-current">
                                            <div class="row mt-4 form-set-current">
                                                <div class="card-header-inner p-2">
                                                    <h4 class="text-white">
                                                        {{ __('inspection.forklift_inspection_report') }}
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
                                                            class="form-label require">{{ __('inspection.department') }}</label>
                                                        <select name="department[1]" id="department[1]"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select department</option>
                                                            @foreach ($departments as $department)
                                                                <option value="{{ encryptId($department->id) }}"
                                                                    {{ old('department.1') == encryptId($department->id) ? 'selected' : '' }}>
                                                                    {{ $department->department_name }}</option>
                                                            @endforeach
                                                        </select>
                                                        @error('department.1')
                                                            <div class="error">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.unit') }}</label>
                                                        <select name="unit[1]" id="unit[1]"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Unit</option>
                                                            @foreach ($unit as $unit)
                                                                <option value="{{ encryptId($unit->id) }}"
                                                                    {{ old('unit.1') == encryptId($unit->id) ? 'selected' : '' }}>
                                                                    {{ $unit->unit_name }}</option>
                                                            @endforeach
                                                        </select>
                                                        @error('unit.1')
                                                            <div class="error">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label">{{ __('inspection.identification_no') }}</label>
                                                        <input type="text" name="identification_no[1]"
                                                            id = "identification_no"
                                                            class="form-control identification_no"
                                                            value="{{ forkliftInspection() }}" readonly>

                                                    </div>
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
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.corrective_action') }}</label>
                                                        <input type="text" name="corrective_action[1]"
                                                            id = "corrective_action" class="form-control"
                                                            value="{{ old('corrective_action.1') }}">
                                                        @error('corrective_action.1')
                                                            <div class="error">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.employee') }}</label>
                                                        <select name="emp_id[1]" id="emp_id[1]"
                                                            class="form-control single-select responsibility_id"
                                                            style="width: 100%">
                                                            <option value="">Select Employee Name</option>
                                                        </select>
                                                        @error('emp_id.1')
                                                            <div class="error">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.date_of_compliance') }}</label>
                                                        <input type="text" name="date_of_compliance[1]"
                                                            class="form-control date_of_compliance"
                                                            value="{{ old('date_of_compliance.1') }}">
                                                        @error('date_of_compliance.1')
                                                            <div class="error">{{ $message }}</div>
                                                        @enderror
                                                    </div>
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
                                                                Open</option>
                                                            <option value="{{ encryptId(0) }}"
                                                                {{ old('observation_status.1') == encryptId(0) ? 'selected' : '' }}>
                                                                Closed</option>
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
                                                href="{{ admin_url('safety/forklift-inspection/list') }}"></x-button-cancel>
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
                    department.reload();
                });

                flatpickr(".date_of_compliance", {
                    dateFormat: "d-m-Y",
                    minDate: new Date(),
                });
                flatpickr(".inspection_date", {
                    dateFormat: "d-m-Y",
                });
                flatpickr("#issue_date", {
                    dateFormat: "d-m-Y",
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
                            "department[1]": {
                                required: true,
                            },
                            "unit[1]": {
                                required: true,
                            },
                            // "identification_no[1]": {
                            //     required: true,
                            // },
                            "observation[1]": {
                                required: true,
                                minlength: 3,
                                maxlength: 100,
                            },
                            "corrective_action[1]": {
                                required: true,
                                minlength: 3,
                                maxlength: 100,
                            },

                            "emp_id[1]": {
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
                                minlength: 3,
                                maxlength: 100,
                            },
                        },
                        messages: {
                            doc_no: {
                                required: "Document Number is Required",
                                minlength: "Minimum Characters should be 3",
                                maxlength: "Maximum Characters should not exceed 100",
                            },
                            issue_date: {
                                required: "Issue date is required",
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
                            "department[1]": {
                                required: "Department is required",
                            },
                            "unit[1]": {
                                required: "Unit is required",
                            },
                            // "identification_no[1]": {
                            //     required: "Identification Number is required",
                            // },
                            "observation[1]": {
                                required: "Observation is required",
                                minlength: "Minimum Characters should be 3",
                                maxlength: "Maximum Characters should not exceed 100",
                            },
                            "corrective_action[1]": {
                                required: "Corrective Action and preventive Action is required",
                                minlength: "Minimum Characters should be 3",
                                maxlength: "Maximum Characters should not exceed 100",
                            },
                            "emp_id[1]": {
                                required: "Employee is required",
                            },
                            "date_of_compliance[1]": {
                                required: "Date of Compliance is required",

                            },
                            "observation_status[1]": {
                                required: "Observation Status is required",

                            },
                            "remarks[1]": {
                                required: "Remarks is required",
                                minlength: "Minimum Characters should be 3",
                                maxlength: "Maximum Characters should not exceed 300",

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

                $('.responsibility_id').select2({
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
                let newSerialNumber = 'FORKLIFT-INS-' + ('00000' + current_serial_number).slice(-5);

                var newCurrentFormSet = `
                        <div class="row mt-4 form-set-current">
                                                <div class="card-header-inner p-2">
                                                    <h4 class="text-white">{{ __('inspection.forklift_inspection_report') }}</h4>
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
                                                            class="form-label require">{{ __('inspection.department') }}</label>
                                                        <select name="department[${form_set_count}]" id="department[${form_set_count}]"
                                                            class=" form-control single-select department-select" style="width: 100%">
                                                            <option value="">Select department</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                  <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.unit') }}</label>
                                                        <select name="unit[${form_set_count}]" id="unit[${form_set_count}]"
                                                            class=" form-control single-select unit" style="width: 100%">
                                                            <option value="">Select Unit</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                 <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label">{{ __('inspection.identification_no') }}</label>
                                                        <input type="text" name="identification_no[${form_set_count}]"
                                                            id = "identification_no[${form_set_count}]" class="form-control identification_no" value="${newSerialNumber}">
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label">{{ __('inspection.observation') }}</label>
                                                        <input type="text" name="observation[${form_set_count}]" id = "observation[${form_set_count}]"
                                                            class="form-control " placeholder="Observation">
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.corrective_action') }}</label>
                                                        <input type="text" name="corrective_action[${form_set_count}]"
                                                            id = "corrective_action[${form_set_count}]" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.employee') }}</label>
                                                       <select name="emp_id[${form_set_count}]" id="emp_id[${form_set_count}]"
                                                            class="form-control single-select responsibility_id" style="width: 100%">
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
                                                            class=" form-control single-select observation_status" style="width: 100%">
                                                            <option value="">Select Observation Status</option>
                                                            <option value="{{ encryptId(1) }}">OPEN</option>
                                                            <option value="{{ encryptId(0) }}">CLOSED</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.remarks') }}</label>
                                                        <textarea name="remarks[${form_set_count}]" id="remarks" class="form-control remarks" style="resize: none;"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                    `;

                let newFormCurrentSetElement = $(newCurrentFormSet); // Convert string to jQuery object

                let departmentSelect = newFormCurrentSetElement.find('select[name^="department"]');
                Getdepartment(departmentSelect);

                let locationSelect = newFormCurrentSetElement.find('select[name^="unit"]');
                Getunit(locationSelect);

                $('.form-wrapper-current').append(newFormCurrentSetElement);

                $("select[name='department[" + form_set_current_count + "]']").rules('add', {
                    required: true,
                    messages: {
                        required: 'Please select the department',
                    }
                });

                $(".unit").rules('add', {
                    required: true,
                    messages: {
                        required: 'Please Select the Unit',
                    }
                });
                $("input[name='observation[" + form_set_current_count + "]']").rules('add', {
                    required: true,
                    minlength: 3,
                    maxlength: 100,
                    messages: {
                        required: 'Please Enter the Observation',
                        minlength: "Minimum Characters should be 3",
                        maxlength: "Maximum Characters should not exceed 100",
                    }
                });
                $("input[name='corrective_action[" + form_set_current_count + "]']").rules('add', {
                    required: true,
                    minlength: 3,
                    maxlength: 100,
                    messages: {
                        required: 'Please Enter the Corrective  and Preventive Action',
                        minlength: "Minimum Characters should be 3",
                        maxlength: "Maximum Characters should not exceed 100",
                    }
                });


                $(".responsibility_id").rules('add', {
                    required: true,
                    messages: {
                        required: 'Please Select the Employee',
                    }
                });

                $("input[name='date_of_compliance[" + form_set_current_count + "]']").rules('add', {
                    required: true,
                    messages: {
                        required: 'Please Select the Date of Compliance',
                    }
                });

                $(".observation_status").rules('add', {
                    required: true,
                    messages: {
                        required: 'Please Select the Observation',
                    }
                });

                $(".remarks").rules('add', {
                    required: true,
                    minlength: 3,
                    maxlength: 300,
                    messages: {
                        required: 'Please Enter the Remarks',
                        minlength: "Minimum Characters should be 3",
                        maxlength: "Maximum Characters should not exceed 300",
                    }
                });


                flatpickr(".date_of_compliance", {
                    dateFormat: "d-m-Y",
                    minDate: new Date(),
                });


                current_serial_number++;
                form_set_current_count++;
                updateCurrentPageIndices();

                $('.responsibility_id').select2({
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


            function Getdepartment(selectElement) {
                $.ajax({
                    type: "GET",
                    url: "{{ admin_url('safety/forklift-inspection/get/department') }}",
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

            function Getunit(selectElement) {
                $.ajax({
                    type: "GET",
                    url: "{{ admin_url('safety/forklift-inspection/get/unit') }}",
                    success: function(response) {
                        if (response.length > 0) {
                            let options = `<option value="">Select Unit</option>`;
                            response.forEach(unit => {
                                options +=
                                    `<option value="${unit.id}">${unit.unit_name}</option>`;
                            });
                            $(selectElement).html(options).trigger('change');
                        }
                    }
                });
            }

            function updateCurrentPageIndices() {
                $('.form-wrapper-current .form-set-current').each(function(index) {
                    let idx = index + 1;
                    let newSerialNumber = 'FORKLIFT-INS-' + ('000000' + idx).slice(-6);
                    $(this).find("input[name^='identification_no']").val(newSerialNumber);

                    $(this).find('input[name^="identification_no"]').attr('name', 'identification_no[' + idx + ']');
                    $(this).find('select[name^="department"]').attr('name', 'department[' + idx + ']');
                    $(this).find('input[name^="unit"]').attr('name', 'unit[' + idx + ']');
                    $(this).find('input[name^="observation"]').attr('name', 'observation[' + idx + ']');
                    $(this).find('select[name^="corrective_action"]').attr('name', 'corrective_action[' + idx + ']');
                    $(this).find('input[name^="date_of_compliance"]').attr('name', 'date_of_compliance[' + idx + ']');
                    $(this).find('input[name^="observation_status"]').attr('name', 'observation_status[' + idx + ']');
                    $(this).find('input[name^="emp_id"]').attr('name', 'emp_id[' + idx +
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
                        title: 'Minimum Observtion Required',
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
