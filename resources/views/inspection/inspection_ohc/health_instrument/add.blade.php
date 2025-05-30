@extends('admin.layouts.admin')
@section('title', 'Health Instrument Calibration')
@section('pageurl', admin_url('ohc/first-aider/list'))

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
                                        href="{{ admin_url('ohc/health-instrument/calibration-track-sheet/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <form method="POST" id="HealthInstrumentAdd" enctype="multipart/form-data"
                                    action="{{ admin_url('ohc/health-instrument/calibration-track-sheet/add/submit') }}">
                                    @csrf

                                    <div class="basic-form">

                                        <div class="row">
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label ">Health Instrument Calibration ID</label>
                                                    <input type="text" name="audit_id" id = "audit_id"
                                                        class="form-control" readonly
                                                        value="{{ getSequence('HealthInstrument') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label">Document No </label>
                                                    <input type="text" name="document_no" id="document_no"
                                                        class="form-control" value="{{ $document_no->doc_no }}" readonly
                                                        placeholder=" Enter Document Number ">
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label for="rate" class="form-label">Issued
                                                        Date</label>
                                                    <div class="input-group date form-input custom-height">
                                                        <input type="text" name="issue_date" id="issue_date"
                                                            value="{{ displaydateformat($document_no->issue_date) }}"
                                                            readonly class="form-control"autocomplete="off">
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
                                                        <input type="text" name="review_date" id="review_date"
                                                            class="form-control" value="{{ $document_no->rev_dt }}" readonly
                                                            autocomplete="off">

                                                        <div class="input-group-addon input-group-text">
                                                            <span class="fa fa-calendar"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label for="unit_id" class="form-label">
                                                        Unit</label>
                                                    <select name="unit_id" id="unit_id_1"
                                                        class=" form-control single-select" style="width: 100%">
                                                        <option value="">Select Unit</option>
                                                        @foreach ($unitList as $unit)
                                                            <option value="{{ encryptId($unit->id) }}">
                                                                {{ $unit->unit_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <input type="hidden" name="document_reference_id"
                                                value="{{ encryptId($document_no->id) }}">

                                        </div>
                                    </div>
                                    <div class="card-body">

                                        <div class="row">
                                            <div class="card-header-inner d-flex justify-content-between">
                                                <h4 class="text-white"> Health Calibration Details</h4>
                                                <button class="btn btn-primary mb-2 addmorebutton"
                                                    data-block='lesson_learned_block' data-row='lesson_learned_row'
                                                    type="button" id="dynamic-add-more"
                                                    style="margin-left: 10px;   width: 84px;">
                                                    Add
                                                </button>
                                            </div>
                                        </div>
                                        <div id="lesson_learned_block">
                                            <div class="row lesson_learned_row" style="margin-top: 20px;">


                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label"> Instrument Name </label>
                                                        <input type="text" name="health_instrument[1][instrument_name]"
                                                            id="instrument_name_1" class="form-control"
                                                            placeholder=" Enter Instrument Name">
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label">Resource Code</label>
                                                        <input type="text" name="health_instrument[1][resource_code]"
                                                            id="resource_code_1" class="form-control"
                                                            placeholder=" Enter Resource Code">
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label">Exact Location</label>
                                                        <input type="text" name="health_instrument[1][exact_location]"
                                                            id="exact_location_1" class="form-control"
                                                            placeholder="Enter Exact Location">
                                                    </div>
                                                </div>



                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label">Instrument Serial Number</label>
                                                        <input type="text"
                                                            name="health_instrument[1][instrument_serial_no]"
                                                            id="instrument_serial_no_1" class="form-control"
                                                            placeholder="Enter Instrument Serial Number">
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label">Make</label>
                                                        <input type="text" name="health_instrument[1][make]"
                                                            id="make_1" class="form-control"
                                                            placeholder="Enter Instrument Make">
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label">Model</label>
                                                        <input type="text" name="health_instrument[1][model]"
                                                            id="model_1" class="form-control"
                                                            placeholder="Enter Instrument Model">
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label">Instrument Range</label>
                                                        <input type="text"
                                                            name="health_instrument[1][instrument_range]"
                                                            id="instrument_range_1" class="form-control"
                                                            placeholder="Enter Instrument Range">
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label require">Frequency</label>
                                                        <select name="health_instrument[1][frequency_id]"
                                                            id="frequency_id[1]" class="form-control single-select"
                                                            style="width: 100%">
                                                            <option value="">Select the Frequency</option>
                                                            @foreach ($frequency as $freq)
                                                                <option value="{{ encryptId($freq->id) }}">
                                                                    {{ $freq->frequency_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label">Date of Calibration</label>
                                                        <input type="text"
                                                            name="health_instrument[1][date_of_calibration]"
                                                            id="date_of_calibration_1" class="form-control">
                                                    </div>
                                                </div>


                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label">Next Due Date</label>
                                                        <input type="text"
                                                            name="health_instrument[1][due_date_of_calibration]"
                                                            id="due_date_of_calibration_1" class="form-control">
                                                    </div>
                                                </div>


                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label">Remark</label>
                                                        <textarea class="form-control" name="health_instrument[1][instrument_remarks]" id="instrument_remarks_1"></textarea>

                                                    </div>
                                                </div>

                                                <div class="col-md-2 text-right mt-2">
                                                    <button class="btn btn-danger removerowdata" type="button"
                                                        style="margin:10px;"><i class="fa fa-trash"></i> Remove</button>

                                                </div>

                                                <hr class="mt-4">
                                            </div>

                                        </div>

                                        <div class="submit-button" style="text-align: right;">
                                            <x-button-submit class="submit"></x-button-submit>
                                            <x-button-reset class=""></x-button-reset>
                                            <x-button-cancel
                                                href="{{ admin_url('ohc/health-instrument/calibration-track-sheet/list') }}"></x-button-cancel>
                                        </div>
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


        document.addEventListener("DOMContentLoaded", function() {
            function initializeFlatpickr() {
                
                flatpickr("input[id^='date_of_calibration_']", {
                    dateFormat: "d-m-Y"
                });
                flatpickr("input[id^='due_date_of_calibration_']", {
                    dateFormat: "d-m-Y"
                });

            }

            function updateRowIndexes() {

                $("#lesson_learned_block .lesson_learned_row").each(function(index) {
                    let newIndex = index + 1;
                    $(this).find("input, select, textarea").each(function() {
                        let oldName = $(this).attr("name");
                        let oldId = $(this).attr("id");

                        if (oldName) {
                            let newName = oldName.replace(/\[\d+\]/, "[" + newIndex + "]");
                            $(this).attr("name", newName);
                        }

                        if (oldId) {
                            let newId = oldId.replace(/\d+$/, newIndex);
                            $(this).attr("id", newId);
                        }
                    });
                });
                initializeFlatpickr();
            }


            $("#dynamic-add-more").on("click", function() {
                let rowCount = $("#lesson_learned_block .lesson_learned_row").length;
                if (rowCount >= 200) {
                    Swal.fire({
                        icon: "error",
                        title: "Sorry!",
                        text: "Maximum 200 records only."
                    });
                    return;
                }

                let firstRow = $(".lesson_learned_row").first();

                firstRow.find(".single-select").select2('destroy');

                let newRow = firstRow.clone();
                let newRowNumber = rowCount + 1;

                firstRow.find(".single-select").select2();

                newRow.find("input, select, textarea, button").each(function() {
                    let oldName = $(this).attr("name");
                    let oldId = $(this).attr("id");

                    if (oldName) {
                        let newName = oldName.replace(/\[\d+\]/, "[" + newRowNumber + "]");
                        $(this).attr("name", newName);
                    }
                    if (oldId) {
                        let newId = oldId.replace(/_\d+$/, "_" + newRowNumber);
                        $(this).attr("id", newId);
                    }

                    if ($(this).is("input[type='text'], textarea, input[type='number']")) {
                        $(this).val("");
                    }
                    if ($(this).is("select")) {
                        $(this).val("").trigger("change");
                    }
                });
                newRow.find("input[name$='[sr_no]']").val("AMBIENT-" + String(rowCount + 1).padStart(4,
                    '0'));

                newRow.find(".invalid-feedback").remove();
                newRow.find(".is-invalid").removeClass("is-invalid");

                newRow.find(".single-select").select2();
                $("#lesson_learned_block").append(newRow);

                newRow.find("input[name$='[instrument_name]']").rules("add", {
                    minlength: 3,
                    maxlength: 2000,
                    required: true,
                    pattern: /^[a-zA-Z0-9\s\-_"'()]+$/,
                    messages: {
                        minlength: "Instrument name must be at least 3 characters.",
                        maxlength: "Instrument name must not exceed 200 characters.",
                        maxlength: "Instrument name is required.",
                        pattern: "Only letters, numbers, spaces, and -_'() are allowed."
                    }
                });

                newRow.find("input[name$='[resource_code]']").rules("add", {
                    minlength: 3,
                    maxlength: 2000,
                    required: true,
                    pattern: /^[a-zA-Z0-9\s\-_"'()]+$/,
                    messages: {
                        minlength: "resource code must be at least 3 characters.",
                        maxlength: "resource code must not exceed 200 characters.",
                        required: "resource code is required.",
                        pattern: "Only letters, numbers, spaces, and -_'() are allowed."
                    }
                });
                newRow.find("input[name$='[exact_location]']").rules("add", {
                    minlength: 3,
                    maxlength: 200,
                    required: true,
                    pattern: /^[a-zA-Z0-9\s\-_"'()]+$/,
                    messages: {
                        minlength: "Excat Location must be at least 3 characters.",
                        maxlength: "Excat Location must not exceed 200 characters.",
                        required: "Excat Location is required.",
                        pattern: "Only letters, numbers, spaces, and -_'() are allowed."
                    }
                });

                newRow.find("input[name$='[instrument_serial_no]']").rules("add", {
                    minlength: 3,
                    required: true,
                    maxlength: 200,
                    pattern: /^[a-zA-Z0-9\s\-_"'()]+$/,
                    messages: {
                        minlength: "Instrument Serial Number must be at least 3 characters.",
                        maxlength: "Instrument Serial Number must not exceed 200 characters.",
                        required: "Instrument Serial Number is required.",
                        pattern: "Only letters, numbers, spaces, and -_'() are allowed."
                    }
                });


                newRow.find("input[name$='[make]']").rules("add", {
                    minlength: 3,
                    required: true,
                    maxlength: 200,
                    pattern: /^[a-zA-Z0-9\s\-_"'()]+$/,
                    messages: {
                        minlength: "Make must be at least 3 characters.",
                        maxlength: "Make must not exceed 200 characters.",
                        required: "Make is required.",
                        pattern: "Only letters, numbers, spaces, and -_'() are allowed."
                    }
                });
                newRow.find("input[name$='[model]']").rules("add", {
                    minlength: 3,
                    maxlength: 200,
                    required: true,
                    pattern: /^[a-zA-Z0-9\s\-_"'()]+$/,
                    messages: {
                        minlength: "Model must be at least 3 characters.",
                        maxlength: "Model must not exceed 200 characters.",
                        required: "Model is required.",
                        pattern: "Only letters, numbers, spaces, and -_'() are allowed."
                    }
                });
                newRow.find("input[name$='[instrument_range]']").rules("add", {
                    minlength: 3,
                    required: true,
                    maxlength: 200,
                    pattern: /^[a-zA-Z0-9\s\-_"'()]+$/,
                    messages: {
                        minlength: "Instrument range must be at least 3 characters.",
                        maxlength: "Instrument range must not exceed 200 characters.",
                        required: "Instrument range is required.",
                        pattern: "Only letters, numbers, spaces, and -_'() are allowed."
                    }
                });
                newRow.find("select[name$='[frequency_id]']").rules("add", {
                    required: true,
                    messages: {
                        minlength: "Frequency is required.",

                    }
                });
                newRow.find("input[name$='[date_of_calibration]']").rules("add", {
                    required: true,
                    messages: {
                        minlength: "Date of calibration is required.",

                    }
                });
                newRow.find("input[name$='[due_date_of_calibration]']").rules("add", {
                    required: true,
                    messages: {
                        minlength: "Date of calibration is required.",

                    }
                });
                initializeFlatpickr();
                $('.single-select').select2();
            });

            $(document).on("click", ".removerowdata", function() {
                let rowCount = $("#lesson_learned_block .lesson_learned_row").length;
                if (rowCount > 1) {
                    $(this).closest(".lesson_learned_row").remove();
                    updateRowIndexes();


                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Sorry!",
                        text: "At least one record is required."
                    });
                }
            });

            initializeFlatpickr();
        });

        function addValidationRules(newRow) {
            newRow.find("input[name$='[instrument_name]']").rules("add", {
                minlength: 3,
                maxlength: 2000,
                required: true,
                pattern: /^[a-zA-Z0-9\s\-_"'()]+$/,
                messages: {
                    minlength: "Instrument name must be at least 3 characters.",
                    maxlength: "Instrument name must not exceed 200 characters.",
                    maxlength: "Instrument name is required.",
                    pattern: "Only letters, numbers, spaces, and -_'() are allowed."
                }
            });

            newRow.find("input[name$='[resource_code]']").rules("add", {
                minlength: 3,
                maxlength: 2000,
                required: true,
                pattern: /^[a-zA-Z0-9\s\-_"'()]+$/,
                messages: {
                    minlength: "resource code must be at least 3 characters.",
                    maxlength: "resource code must not exceed 200 characters.",
                    required: "resource code is required.",
                    pattern: "Only letters, numbers, spaces, and -_'() are allowed."
                }
            });
            newRow.find("input[name$='[exact_location]']").rules("add", {
                minlength: 3,
                maxlength: 200,
                required: true,
                pattern: /^[a-zA-Z0-9\s\-_"'()]+$/,
                messages: {
                    minlength: "Excat Location must be at least 3 characters.",
                    maxlength: "Excat Location must not exceed 200 characters.",
                    required: "Excat Location is required.",
                    pattern: "Only letters, numbers, spaces, and -_'() are allowed."
                }
            });

            newRow.find("input[name$='[instrument_serial_no]']").rules("add", {
                minlength: 3,
                required: true,
                maxlength: 200,
                pattern: /^[a-zA-Z0-9\s\-_"'()]+$/,
                messages: {
                    minlength: "Instrument Serial Number must be at least 3 characters.",
                    maxlength: "Instrument Serial Number must not exceed 200 characters.",
                    required: "Instrument Serial Number is required.",
                    pattern: "Only letters, numbers, spaces, and -_'() are allowed."
                }
            });


            newRow.find("input[name$='[make]']").rules("add", {
                minlength: 3,
                required: true,
                maxlength: 200,
                pattern: /^[a-zA-Z0-9\s\-_"'()]+$/,
                messages: {
                    minlength: "Make must be at least 3 characters.",
                    maxlength: "Make must not exceed 200 characters.",
                    required: "Make is required.",
                    pattern: "Only letters, numbers, spaces, and -_'() are allowed."
                }
            });
            newRow.find("input[name$='[model]']").rules("add", {
                minlength: 3,
                maxlength: 200,
                required: true,
                pattern: /^[a-zA-Z0-9\s\-_"'()]+$/,
                messages: {
                    minlength: "Model must be at least 3 characters.",
                    maxlength: "Model must not exceed 200 characters.",
                    required: "Model is required.",
                    pattern: "Only letters, numbers, spaces, and -_'() are allowed."
                }
            });
            newRow.find("input[name$='[instrument_range]']").rules("add", {
                minlength: 3,
                required: true,
                maxlength: 200,
                pattern: /^[a-zA-Z0-9\s\-_"'()]+$/,
                messages: {
                    minlength: "Instrument range must be at least 3 characters.",
                    maxlength: "Instrument range must not exceed 200 characters.",
                    required: "Instrument range is required.",
                    pattern: "Only letters, numbers, spaces, and -_'() are allowed."
                }
            });
            newRow.find("select[name$='[frequency_id]']").rules("add", {
                required: true,
                messages: {
                    minlength: "Frequency name is required.",

                }
            });
            newRow.find("input[name$='[date_of_calibration]']").rules("add", {
                required: true,
                messages: {
                    minlength: "Date of calibration is required.",

                }
            });
            newRow.find("input[name$='[due_date_of_calibration]']").rules("add", {
                required: true,
                messages: {
                    minlength: "Date of calibration is required.",

                }
            });
        }

        $(function() {
            $.validator.setDefaults({
                highlight: function(element) {
                    $(element).addClass('is-invalid');
                    if ($(element).hasClass('single-select')) {
                        $(element).next('.select2-container').find('.select2-selection')
                            .addClass('is-invalid');
                    }
                },
                unhighlight: function(element) {
                    $(element).removeClass('is-invalid');
                    if ($(element).hasClass('single-select')) {
                        $(element).next('.select2-container').find('.select2-selection')
                            .removeClass('is-invalid');
                    }
                },
                errorPlacement: function(error, element) {
                    if (element.hasClass('single-select')) {
                        error.addClass('invalid-feedback').insertAfter(element.next(
                            '.select2-container'));
                    } else {
                        error.addClass('invalid-feedback').insertAfter(element);
                    }
                }
            });

            $("#HealthInstrumentAdd").validate({
                rules: {
                    unit_id: {
                        required: true,
                    },
                    "document_no": {
                        required: true,
                        minlength: 3,
                        maxlength: 200,
                        pattern: /^[a-zA-Z0-9\s\-_"'()/]+$/
                    },
                    "health_instrument[1][instrument_name]": {
                        required: true,
                        minlength: 3,
                        maxlength: 200,
                        pattern: /^[a-zA-Z0-9\s\-_"'()]+$/
                    },
                    "health_instrument[1][resource_code]": {
                        required: true,
                        minlength: 3,
                        maxlength: 200,
                        pattern: /^[a-zA-Z0-9\s\-_"'()/]+$/
                    },
                    "health_instrument[1][exact_location]": {
                        required: true,
                        minlength: 3,
                        maxlength: 200,
                        pattern: /^[a-zA-Z0-9\s\-_"'()]+$/
                    },
                    "health_instrument[1][instrument_serial_no]": {
                        required: true,
                        minlength: 3,
                        maxlength: 200,
                        pattern: /^[a-zA-Z0-9\s\-_"'()/]+$/
                    },
                    "health_instrument[1][make]": {
                        required: true,
                        minlength: 3,
                        maxlength: 200,
                        pattern: /^[a-zA-Z0-9\s\-_"'()]+$/
                    },
                    "health_instrument[1][model]": {
                        required: true,
                        minlength: 3,
                        maxlength: 200,
                        pattern: /^[a-zA-Z0-9\s\-_"'()]+$/
                    },
                    "health_instrument[1][instrument_range]": {
                        required: true,
                        minlength: 3,
                        maxlength: 200,
                        pattern: /^[a-zA-Z0-9\s\-_"'*()/]+$/
                    },
                    "health_instrument[1][calibration_frequency]": {
                        required: true,
                        minlength: 3,
                        maxlength: 200,
                        pattern: /^[a-zA-Z0-9\s\-_"'()]+$/
                    },
                    "health_instrument[1][frequency_id]": {
                        required: true,

                    },
                    "health_instrument[1][date_of_calibration]": {
                        required: true,

                    },
                    "health_instrument[1][due_date_of_calibration]": {
                        required: true,

                    },

                },
                messages: {
                    unit_id: {
                        required: "Please select the unit",
                    },
                    "document_no": {
                        required: "Document number is required.",
                        minlength: "Document number must be at least 3 characters long.",
                        maxlength: "Document number must not exceed 200 characters.",
                        pattern: "Only letters, numbers, spaces, and -_'()/ are allowed."
                    },
                    "health_instrument[1][instrument_name]": {
                        required: "Instrument name is required.",
                        minlength: "Instrument name must be at least 3 characters.",
                        maxlength: "Instrument name must not exceed 200 characters.",
                        pattern: "Only letters, numbers, spaces, and -_'() are allowed."
                    },
                    "health_instrument[1][resource_code]": {
                        required: "Resource code is required.",
                        minlength: "Resource code must be at least 3 characters.",
                        maxlength: "Resource code must not exceed 200 characters.",
                        pattern: "Only letters, numbers, spaces, and -_'()/ are allowed."
                    },
                    "health_instrument[1][exact_location]": {
                        required: "Exact location is required.",
                        minlength: "Exact location must be at least 3 characters.",
                        maxlength: "Exact location must not exceed 200 characters.",
                        pattern: "Only letters, numbers, spaces, and -_'() are allowed."
                    },
                    "health_instrument[1][instrument_serial_no]": {
                        required: "Serial number is required.",
                        minlength: "Serial number must be at least 3 characters.",
                        maxlength: "Serial number must not exceed 200 characters.",
                        pattern: "Only letters, numbers, spaces, and -_'()/ are allowed."
                    },
                    "health_instrument[1][make]": {
                        required: "Make is required.",
                        minlength: "Make must be at least 3 characters.",
                        maxlength: "Make must not exceed 200 characters.",
                        pattern: "Only letters, numbers, spaces, and -_'() are allowed."
                    },
                    "health_instrument[1][model]": {
                        required: "Model is required.",
                        minlength: "Model must be at least 3 characters.",
                        maxlength: "Model must not exceed 200 characters.",
                        pattern: "Only letters, numbers, spaces, and -_'() are allowed."
                    },
                    "health_instrument[1][instrument_range]": {
                        required: "Instrument range is required.",
                        minlength: "Instrument range must be at least 3 characters.",
                        maxlength: "Instrument range must not exceed 200 characters.",
                        pattern: "Only letters, numbers, spaces, and -_'*/() are allowed."
                    },
                    "health_instrument[1][calibration_frequency]": {
                        required: "Calibration frequency is required.",
                        minlength: "Calibration frequency must be at least 3 characters.",
                        maxlength: "Calibration frequency must not exceed 200 characters.",
                        pattern: "Only letters, numbers, spaces, and -_'() are allowed."
                    },
                    "health_instrument[1][frequency_id]": {
                        required: "Please Select the option.",

                    },
                    "health_instrument[1][date_of_calibration]": {
                        required: "Please Select the date.",

                    },
                    "health_instrument[1][due_date_of_calibration]": {
                        required: "Please Select the date.",

                    },

                },
                submitHandler: function(form) {
                    form.submit();
                }
            });

            $("#lesson_learned_block .lesson_learned_row").each(function() {
                addValidationRules($(this));
            });
        });
    </script>
@endpush
