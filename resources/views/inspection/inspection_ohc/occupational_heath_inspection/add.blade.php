@extends('admin.layouts.admin')
@section('title', 'Occupational Health Center Inspection Checklist')
@section('pageurl', admin_url('ohc/inspection/list'))


@section('content')
    <div class="clearfix"></div>
    <div class="page-titles">
        <div class="d-flex align-items-center">


        </div>

    </div>

    <div class="content-body  default-height">
        <div class="container-fluid main-content">
            <div class="row">

                <div class="col-12">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="align-back-btc">
                                    <x-button-back href="{{ admin_url('ohc/inspection/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form">
                                    <form method="POST" id="OccupationalHealth"
                                        action="{{ admin_url('ohc/inspection/add/submit') }}" autocomplete="off"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" value="{{ encryptId(1) }}" name="ohc_type">
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
                                                    <label class="form-label require">Shift</label>
                                                    <select name="shift" id="shift" style="width: 100%"
                                                        class="form-control single-select">
                                                        <option value="">Select the option</option>
                                                        @foreach ($shift as $list)
                                                            <option value="{{ encryptId($list->id) }}">{{ $list->shift }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require" for="frequency_id">Frequency</label>
                                                    <select name="frequency_id" id="frequency_id" style="width: 100%"
                                                        class="form-control single-select">
                                                        <option value="">Select the option</option>
                                                        @foreach ($frequency as $list)
                                                            <option value="{{ encryptId($list->id) }}">
                                                                {{ $list->frequency_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Unit</label>
                                                    <select name="unit_id" id="unit_id" style="width: 100%"
                                                        class="form-control single-select">
                                                        <option value="">Select the option</option>
                                                        @foreach ($unit as $list)
                                                            <option value="{{ encryptId($list->id) }}">
                                                                {{ $list->unit_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Location</label>
                                                    <select name="location_id" id="location_id" style="width: 100%"
                                                        class="form-control single-select">
                                                        <option value="">Select the option</option>
                                                        @foreach ($location as $list)
                                                            <option value="{{ encryptId($list->id) }}">
                                                                {{ $list->location_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label for="rate" class="form-label require ">Next Due On</label>
                                                    <div class="input-group date form-input custom-height">
                                                        <input type="text" name="next_due_on" id="next_due_on"
                                                            class="form-control"autocomplete="off">
                                                        <div class="input-group-addon input-group-text">
                                                            <span class="fa fa-calendar"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label for="rate" class="form-label require ">Date of
                                                        inspection</label>
                                                    <div class="input-group date form-input custom-height">
                                                        <input type="text" name="date_of_inspection"
                                                            id="date_of_inspection"
                                                            class="form-control"autocomplete="off">
                                                        <div class="input-group-addon input-group-text">
                                                            <span class="fa fa-calendar"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                        </div>

                                        <div class="row mt-2">
                                            <div class="card-header-inner">
                                                <h4 class="text-white">Occupational Heath Inspection Checklist</h4>
                                            </div>
                                        </div>

                                        <div class="table-responsive">
                                            <div class="col-md-12">
                                                <table class="table table-bordered ">

                                                    <thead class="bg-secondary" style="color: #ffff">

                                                        <tr>
                                                            <th colspan="2">
                                                                Check Points
                                                            </th>

                                                            @foreach ($getoption as $option)
                                                                <th>
                                                                    {{ $option }}
                                                                </th>
                                                            @endforeach
                                                            <th>
                                                                Quantity
                                                            </th>
                                                            <th colspan="3" \>
                                                                Remarks
                                                            </th>
                                                        </tr>

                                                    </thead>
                                                    <tbody>

                                                        @foreach ($checklist_details as $key => $details)
                                                            @php
                                                                $rowCount = count($details);
                                                            @endphp
                                                            @foreach ($details as $index => $checklist)
                                                                <tr>
                                                                    <input type="hidden"
                                                                        name="sub_type_id[{{ $checklist->checklist_sub_type_id }}][]"
                                                                        value="{{ $checklist->checklist_id }}">

                                                                    {{-- @if ($index == 0)
                                                                        <td rowspan="{{ $rowCount }}"
                                                                            style="border: 1px solid black; padding: 8px; background-color: #f5f5f5; font-weight: bold;">
                                                                            {{ $checklist->subcategory_name }}
                                                                        </td>
                                                                    @endif --}}
                                                                    <td colspan="2"
                                                                        style="border: 1px solid black; padding: 8px; text-align: center; vertical-align: middle;">
                                                                        {{ $checklist->checklist_name }}
                                                                    </td>

                                                                    @php
                                                                        $options = explode(',', $checklist->type);
                                                                    @endphp

                                                                    @foreach ($getoption as $option)
                                                                        <td
                                                                            style="border: 1px solid black; padding: 8px; text-align: center; vertical-align: middle;">
                                                                            <label class="radio-label">
                                                                                <input type="radio"
                                                                                    name="checklist_type_status[{{ $checklist->checklist_id }}]"
                                                                                    value="{{ $option }}">
                                                                                {{ $option }}
                                                                            </label>
                                                                        </td>
                                                                    @endforeach
                                                                    <td
                                                                        style="border: 1px solid black; padding: 8px; text-align: center; vertical-align: middle;">
                                                                        <input type="number"
                                                                            name="quantity[{{ $checklist->checklist_id ?? '' }}]"
                                                                            min="0" class="form-control">
                                                                    </td>

                                                                    <td
                                                                        style="border: 1px solid black; padding: 8px; text-align: center;">
                                                                        <textarea name="remarks[{{ $checklist->checklist_id ?? '' }}]" cols="5" rows="3" class="form-control"></textarea>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        @endforeach
                                                    </tbody>



                                                </table>
                                            </div>
                                        </div>


                                        {{-- @if ($signature_upload->signature_upload != '')
                                            <label class="form-label view_label">Requestor Signature</label>

                                            <p>
                                                <a href="{{ asset($signature_upload->signature_upload) }}"
                                                    target="_blank">
                                                    <img src="{{ asset($signature_upload->signature_upload) }}"
                                                        style="width: 100px" alt="image">
                                                </a>
                                            </p>
                                        @else
                                            <div class="col-md-4 mb-3">
                                                <div class="form-group form-input">
                                                    <label for="signature_image"
                                                        class="form-label fw-bold require">Requestor
                                                        Signature</label>
                                                    <input type="file" class="form-control "
                                                        accept="image/png, image/jpeg, image/jpg" name="signature_image"
                                                        id="signature_image">
                                                    <div class="text-danger"></div>
                                                    <small>Allowed file types: png, jpeg, jpg</small>

                                                    <!-- Preview Container -->
                                                    <div id="imagePreviewContainer" class="mt-2"
                                                        style="display: none;">
                                                        <img id="imagePreview" src="#" alt="Signature Preview"
                                                            class="img-thumbnail" width="200">
                                                    </div>
                                                </div>
                                            </div>
                                        @endif --}}
                                </div>
                                <hr>
                                <div class="submit-button" style="text-align: right;">
                                    <x-button-submit class="submit"></x-button-submit>
                                    <x-button-reset class="submit"></x-button-reset>
                                    <x-button-cancel href="{{ admin_url('ohc/inspection/list') }}"></x-button-cancel>
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
        });
        var Datepicker = flatpickr("#date_of_inspection", {
            dateFormat: "d-m-Y",
            minDate: new Date()

        });
        var dueDate = flatpickr("#next_due_on", {
            dateFormat: "d-m-Y",
            minDate: new Date()

        });


        $(document).ready(function() {
            $('#signature_image').on('change', function() {



                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#imagePreview').attr('src', e.target.result);
                    $('#imagePreviewContainer').show();
                };
                reader.readAsDataURL(file);
            });

        });

        $(function() {
            // Initialize validator
            var validator = $('#OccupationalHealth').validate({
                rules: {
                    shift: {
                        required: true
                    },
                    review_date: {
                        required: true
                    },
                    unit_id: {
                        required: true
                    },
                    signature_image: {
                        required: true,
                        extension: "png|jpeg|jpg",
                        filesize: 15728640,
                    },
                    date_of_inspection: {
                        required: true
                    },
                    location_id: {
                        required: true
                    },
                    next_due_on: {
                        required: true
                    },
                    frequency_id: {
                        required: true
                    }
                },
                messages: {
                    shift: {
                        required: "Shift is required"
                    },
                    frequency_id: {
                        required: "Frequency Name is required"
                    },
                    signature_image: {
                        required: "Please upload your signature image.",
                        extension: "Allowed file types: PNG, JPEG, JPG.",
                        filesize: "File must be less than 15MB."
                    },

                    date_of_inspection: {
                        required: "Date Of Inspection is required"
                    },
                    review_date: {
                        required: "Review Date is required"
                    },
                    next_due_on: {
                        required: "Next Due date is required"
                    },
                    location_id: {
                        required: "Location is required"
                    },
                    unit_id: {
                        required: "Unit is required"
                    }
                },
                errorElement: 'span',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    if (element.is(':radio') || element.is('textarea') || element.is(
                            'input[type="number"]')) {
                        element.closest('td').append(error);
                    } else {
                        element.closest('.form-input').append(error);
                    }
                },
                highlight: function(element) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element) {
                    $(element).removeClass('is-invalid');
                },
                submitHandler: function(form) {
                    console.log('Form submitted');
                    form.submit();
                },
                invalidHandler: function(event, validator) {
                    var errors = validator.numberOfInvalids();
                    console.log(errors + " field(s) are invalid");
                    validator.errorList.forEach(function(error) {
                        console.log("Field: " + error.element.name + ", Error: " + error
                            .message);
                    });
                }
            });

            // Custom method for radio button requirement
            $.validator.addMethod("radioRequired", function(value, element, param) {
                return $('input[name="' + param + '"]:checked').length > 0;
            }, "Please select an option");

            // Custom method for quantity
            $.validator.addMethod("quantityRequired", function(value, element) {
                var checklistId = $(element).attr('name').match(/\d+/);
                if (checklistId && checklistId[0]) {
                    var selectedValue = $('input[name="checklist_type_status[' + checklistId[0] +
                        ']"]:checked').val();

                    if (selectedValue === "YES") {
                        return $.trim(value).length > 0 && parseInt(value, 10) >= 1;
                    } else if (selectedValue === "NO" || selectedValue === "N/A") {
                        return $.trim(value).length > 0 && parseInt(value, 10) === 0;
                    }
                }
                return true;
            }, "");


            // Custom method for remarks field
            $.validator.addMethod("remarksRequired", function(value, element) {
                var checklistId = $(element).attr('name').match(/\d+/);
                if (checklistId && checklistId[0]) {
                    return $('input[name="checklist_type_status[' + checklistId[0] + ']"]:checked').length >
                        0 ?
                        $.trim(value).length > 0 :
                        true;
                }
                return true;
            }, "Please provide remarks");

            // Add rules for radio buttons
            $('input[type="radio"]').each(function() {
                var name = $(this).attr("name");
                validator.settings.rules[name] = {
                    radioRequired: name
                };
            });

            // Add rules for remarks textarea
            $('textarea[name^="remarks"]').each(function() {
                var name = $(this).attr("name");
                validator.settings.rules[name] = {
                    remarksRequired: true,
                    minlength: 3,
                    maxlength: 600
                };
                validator.settings.messages[name] = {
                    remarksRequired: "Remarks are required if an option is selected",
                    minlength: "Remarks must be at least 3 characters",
                    maxlength: "Remarks must not exceed 600 characters"
                };
            });
            // Add rules for quantity textarea
            $('input[name^="quantity"]').each(function() {
                var input = $(this);
                var checklistId = input.attr('name').match(/\d+/)[0];

                input.rules("add", {
                    quantityRequired: true,
                    messages: {
                        quantityRequired: function() {
                            var selectedValue = $('input[name="checklist_type_status[' +
                                checklistId + ']"]:checked').val();
                            if (selectedValue === "YES") {
                                return "Please enter quantity greater than or equal to 1.";
                            } else if (selectedValue === "NO" || selectedValue === "N/A") {
                                return "Quantity must be 0 when status is NO or N/A.";
                            }
                            return "Please enter a valid quantity.";
                        }
                    }
                });
            });


        });
    </script>
@endpush
