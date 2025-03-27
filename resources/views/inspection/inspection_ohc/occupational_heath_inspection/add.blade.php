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
                                    <form method="POST" id="weeklyambulance"
                                        action="{{ admin_url('ohc/inspection/add/submit') }}" autocomplete="off"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" value="{{ encryptId(1) }}" name="ohc_type">
                                        <div class="row">
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Document Number</label>
                                                    <input type="text" name="document_no" id = "document_no"
                                                        class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label for="rate" class="form-label require ">Issued
                                                        Date</label>
                                                    <div class="input-group date form-input custom-height">
                                                        <input type="text" name="issue_date" id="issue_date"
                                                            class="form-control"autocomplete="off">
                                                        <div class="input-group-addon input-group-text">
                                                            <span class="fa fa-calendar"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label for="rate" class="form-label require ">Review
                                                        Date</label>
                                                    <div class="input-group date form-input custom-height">
                                                        <input type="text" value="{{ getDocumentReviewDate('0') }}"
                                                            name="review_date" id="review_date" class="form-control"
                                                            autocomplete="off" readonly>

                                                        <div class="input-group-addon input-group-text">
                                                            <span class="fa fa-calendar"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

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
                                                            id="date_of_inspection" class="form-control"autocomplete="off">
                                                        <div class="input-group-addon input-group-text">
                                                            <span class="fa fa-calendar"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            @if ($signature_upload->signature_upload != '')
                                            <label class="form-label view_label">Requestor Signature</label>

                                            <p>
                                                <a href="{{ asset($signature_upload->signature_upload) }}"
                                                    target="_blank">
                                                    <img src="{{ asset( $signature_upload->signature_upload) }}"
                                                        style="width: 100px" alt="image">
                                                </a>
                                            </p>
                                        @else
                                            <div class="col-md-4 mb-3">
                                                <label for="signature_image"
                                                    class="form-label fw-bold require">Requestor Signature</label>
                                                <input type="file"
                                                    class="form-control validate-file-accept validate-file-required"
                                                    accept="image/png, image/jpeg, image/jpg" name="signature_image"
                                                    id="signature_image">
                                                <div class="text-danger"></div>
                                                <small>Allowed file types: png, jpeg, jpg</small>

                                                <!-- Preview Container -->
                                                <div id="imagePreviewContainer" class="mt-2" style="display: none;">
                                                    <img id="imagePreview" src="#" alt="Signature Preview"
                                                        class="img-thumbnail" width="200">
                                                </div>
                                            </div>
                                        @endif
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
                                                            <th colspan="3">
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

                                                                    @if ($index == 0)
                                                                        <td rowspan="{{ $rowCount }}"
                                                                            style="border: 1px solid black; padding: 8px; background-color: #f5f5f5; font-weight: bold;">
                                                                            {{ $checklist->subcategory_name }}
                                                                        </td>
                                                                    @endif
                                                                    <td colspan="2"
                                                                        style="border: 1px solid black; padding: 8px;">
                                                                        {{ $checklist->checklist_name }}

                                                                    </td>
                                                                    @php
                                                                        $options = explode(',', $checklist->type);
                                                                    @endphp

                                                                    @foreach ($getoption as $option)
                                                                        <td
                                                                            style="border: 1px solid black; padding: 8px; text-align: center;">
                                                                            <label class="radio-label">
                                                                                <input type="radio"
                                                                                    name="checklist_type_status[{{ $checklist->checklist_id }}]"
                                                                                    value="{{ $option }}">
                                                                                {{ $option }}
                                                                            </label>
                                                                        </td>
                                                                    @endforeach
                                                                    <td
                                                                        style="border: 1px solid black; padding: 8px; text-align: center;">
                                                                        <input type="number"
                                                                            name="quantity[{{ $checklist->checklist_id ?? '' }}]" min="1"
                                                                            class="form-control">
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
                                </div>
                                <hr>
                                <div class="submit-button" style="text-align: right;">
                                    <x-button-submit class="submit"></x-button-submit>
                                    <x-button-reset class="submit"></x-button-reset>
                                    <x-button-cancel
                                        href="{{ admin_url('ohc/weekly-ambulance/inspection/checklist') }}"></x-button-cancel>
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

        var IssueDatepicker = flatpickr("#issue_date", {
            dateFormat: "d-m-Y",
            minDate: new Date()

        });
        var Datepicker = flatpickr("#date_of_inspection", {
            dateFormat: "d-m-Y",
            minDate: new Date()

        });
        var dueDate = flatpickr("#next_due_on", {
            dateFormat: "d-m-Y",
            minDate: new Date()

        });
        $(function() {
            $('#weeklyambulance').validate({
                rules: {
                    document_no: {
                        required: true,
                        minlength: 3,
                        maxlength: 100,
                    },
                    issue_date: {
                        required: true,
                    },
                    shift: {
                        required: true,
                    },
                    review_date: {
                        required: true,
                    },
                    unit_id: {
                        required: true,
                    },
                    date_of_inspection: {
                        required: true,
                    },
                    location_id: {
                        required: true,
                    },
                    next_due_on: {
                        required: true,
                    }
                },
                messages: {
                    document_no: {
                        required: "Document Number is Required",
                        minlength: "Minimum Characters should be 3",
                        maxlength: "Maximum Characters should not exceed 100",
                    },
                    issue_date: {
                        required: "Issue date is required",
                    },
                    shift: {
                        required: "Shift is required",
                    },
                    date_of_inspection: {
                        required: "Date Of Inspection is required",
                    },
                    review_date: {
                        required: "Review Date is required",
                    },
                    next_due_on: {
                        required: "Next Due date is required",
                    },
                    location_id: {
                        required: "Location is required",
                    },
                    unit_id: {
                        required: "Unit is required",
                    }
                },
                errorElement: 'span',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    if (element.is(':radio')) {
                        element.closest('td').append(error);
                    } else if (element.is('textarea')) {
                        element.closest('td').append(error);
                    }
                    else if (element.is('input[type="number"]')) {
                        element.closest('td').append(error);
                    }
                   else {
                        element.closest('.form-input').append(error);
                    }
                },
                highlight: function(element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element, errorClass, validClass) {
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


            // $.validator.addMethod("radioRequired", function(value, element, param) {
            //     return $('input[name="' + param + '"]:checked').length > 0;
            // }, "Please select an option");

            // $.validator.addMethod("remarksRequired", function(value, element) {
            //     var checklistId = $(element).attr('name').match(/\d+/)[
            //         0];
            //     return $('input[name="checklist_type_status[' + checklistId + ']"]:checked').length > 0 ? $
            //         .trim(value).length > 0 : true;
            // }, "Please provide remarks ");

            $.validator.addMethod("quantityRequired", function(value, element) {
                var checklistId = $(element).attr('name').match(/\d+/)[
                    0];
                return $('input[name="checklist_type_status[' + checklistId + ']"]:checked').length > 0 ? $
                    .trim(value).length > 0 : true;
            }, "Please provide remarks ");




            $('input[type="radio"]').each(function() {
                var name = $(this).attr("name");
                $('#weeklyambulance').validate().settings.rules[name] = {
                    radioRequired: name
                };
            });

            // Apply validation dynamically to remarks fields
            $('textarea[name^="remarks"]').each(function() {
                var name = $(this).attr("name");
                $('#weeklyambulance').validate().settings.rules[name] = {
                    remarksRequired: true,
                    minlength: 3,
                    maxlength: 600
                };
                $('#weeklyambulance').validate().settings.messages[name] = {
                    remarksRequired: "Remarks are required if an option is selected",
                    minlength: "Remarks must be at least 3 characters",
                    maxlength: "Remarks must not exceed 600 characters"
                };
            });

            // Apply validation dynamically to quantity fields
            $('input[name^="quantity"]').each(function() {
                var name = $(this).attr("name");
                $('#weeklyambulance').validate().settings.rules[name] = {
                    quantityRequired: true,
                    digits: true,
                };
                $('#weeklyambulance').validate().settings.messages[name] = {
                    quantityRequired: "Quantity is required if an option is selected",
                    digits: "Quantity should be numeric",
                };
            });
        });
    </script>
@endpush
