@extends('admin.layouts.admin')
@section('title', 'Monthly First Aid Box Audit Checklist')
@section('pageurl', admin_url('ohc/first-aid-box/monthly-audit/list'))


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
                                    <x-button-back
                                        href="{{ admin_url('ohc/first-aid-box/monthly-audit/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form">
                                    <form method="POST" id="monthlyfirstaidchecklist"
                                        action="{{ admin_url('ohc/first-aid-box/monthly-audit/add/submit') }}"
                                        autocomplete="off" enctype="multipart/form-data">
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
                                                    <label class="form-label require">Frequency</label>
                                                    <select name="frequency" id="frequency" style="width: 100%"
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
                                                    <label for="rate" class="form-label require ">Date of
                                                        inspection</label>
                                                    <div class="input-group date form-input custom-height">
                                                        <input type="text" name="date" id="date"
                                                            class="form-control"autocomplete="off">
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
                                                        <img src="{{ asset($signature_upload->signature_upload) }}"
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
                                                    <div id="imagePreviewContainer" class="mt-2" style="display:None">
                                                        <img id="imagePreview" src="#" alt="Signature Preview"
                                                            class="img-thumbnail" width="200">
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="row mt-2">
                                            <div class="card-header-inner">
                                                <h4 class="text-white">Monthly First Aid Box Audit Checklist</h4>
                                            </div>
                                        </div>

                                        <div class="table-responsive">
                                            <div class="col-md-12">
                                                <table class="table table-bordered ">

                                                    <thead class="bg-secondary" style="color: #ffff">
                                                        <tr>
                                                            <th>Unit</th>
                                                            <th>Department</th>
                                                            <th>First Aid Box Number</th>
                                                            <th>Does the first-aid register is being properly maintened as &
                                                                when require.</th>
                                                            <th>Does the first-aid register is being properly maintened as &
                                                                when require.</th>
                                                            <th>Does the first-aid register is being properly maintened as &
                                                                when require.</th>
                                                            <th>Does the First-aid box is being maintained as per the freeze
                                                                quantity.</th>
                                                            <th>Does the medical requisition slip record is being
                                                                maintained.</th>
                                                            <th>Does the first-aid box is clean.</th>
                                                            <th>Does the first-aid box sticker available.</th>
                                                            <th>Does the First aid material index is available.</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>

                                                    <tbody id="medicine-tbody">
                                                        <tr class="medicine-row">
                                                            <td>
                                                                <div class="form-group">
                                                                    <select name="unit_id[0]"
                                                                        class="form-control single-select unit_id"
                                                                        style="width: 100%">
                                                                        <option value="">Select the Unit Name
                                                                        </option>
                                                                        @foreach ($unit as $list)
                                                                            <option value="{{ encryptId($list->id) }}">
                                                                                {{ $list->unit_name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="form-group">
                                                                    <select name="department_id[0]"
                                                                        class="form-control single-select department_id"
                                                                        style="width: 100%">
                                                                        <option value="">Select the Department Name
                                                                        </option>
                                                                    </select>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <input type="text" name="first_aid_box[0]"
                                                                    class="form-control first_aid_box" readonly>
                                                            </td>
                                                            <td>
                                                                <div class="form-group">
                                                                    <input type="radio"
                                                                        name="first_aid_register_maintained[0]"
                                                                        id="first_aid_register_maintained_yes[0]"
                                                                        value="{{ encryptId(1) }}">
                                                                    <label for="first_aid_register_maintained_yes[0]"
                                                                        class="fw-bold">Yes</label>
                                                                    <br>
                                                                    <input type="radio"
                                                                        name="first_aid_register_maintained[0]"
                                                                        id="first_aid_register_maintained_no[0]"
                                                                        value="{{ encryptId(0) }}">
                                                                    <label for="first_aid_register_maintained_no[0]"
                                                                        class="fw-bold">No</label>
                                                                </div>
                                                            </td>

                                                            <td>
                                                                <div class="form-group">
                                                                    <input type="radio"
                                                                        name="first_aid_box_inspect_periodicity[0]"
                                                                        id="first_aid_box_inspect_periodicity_yes[0]"
                                                                        value="{{ encryptId(1) }}">
                                                                    <label for="first_aid_box_inspect_periodicity_yes[0]"
                                                                        class="fw-bold">Yes</label>
                                                                    <br>
                                                                    <input type="radio"
                                                                        name="first_aid_box_inspect_periodicity[0]"
                                                                        id="first_aid_box_inspect_periodicity_no[0]"
                                                                        value="{{ encryptId(0) }}">
                                                                    <label for="first_aid_box_inspect_periodicity_no[0]"
                                                                        class="fw-bold">No</label>
                                                                </div>
                                                            </td>

                                                            <td>
                                                                <div class="form-group">

                                                                    <input type="radio"
                                                                        name="first_aid_box_checklist_periodicity[0]"class="fw-bold"
                                                                        id="first_aid_box_checklist_periodicity_yes[0]"
                                                                        value="{{ encryptId(1) }}"> <label
                                                                        for="first_aid_box_checklist_periodicity_yes[0]"
                                                                        class="fw-bold">Yes</label> <br>
                                                                    <input type="radio"
                                                                        name="first_aid_box_checklist_periodicity[0]"class="fw-bold"
                                                                        id="first_aid_box_checklist_periodicity_no[0]"
                                                                        value="{{ encryptId(0) }}"> <label
                                                                        for="first_aid_box_checklist_periodicity_no[0]"
                                                                        class="fw-bold">No</label>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="form-group">

                                                                    <input type="radio"
                                                                        name="first_aid_box_freeze_quantity[0]"class="fw-bold"
                                                                        id="first_aid_box_freeze_quantity_yes[0]"
                                                                        value="{{ encryptId(1) }}"> <label
                                                                        for="first_aid_box_freeze_quantity_yes[0]"
                                                                        class="fw-bold">Yes</label> <br>
                                                                    <input type="radio"
                                                                        name="first_aid_box_freeze_quantity[0]"class="fw-bold"
                                                                        id="first_aid_box_freeze_quantity_no[0]"
                                                                        value="{{ encryptId(0) }}"> <label
                                                                        for="first_aid_box_freeze_quantity_no[0]"
                                                                        class="fw-bold">No</label>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="form-group">

                                                                    <input type="radio"
                                                                        name="medicine_requisition_slip_record[0]"class="fw-bold"
                                                                        id="medicine_requisition_slip_record_yes[0]"
                                                                        value="{{ encryptId(1) }}"> <label
                                                                        for="medicine_requisition_slip_record_yes[0]"
                                                                        class="fw-bold">Yes</label> <br>
                                                                    <input type="radio"
                                                                        name="medicine_requisition_slip_record[0]"class="fw-bold"
                                                                        id="medicine_requisition_slip_record_no[0]"
                                                                        value="{{ encryptId(0) }}"> <label
                                                                        for="medicine_requisition_slip_record_no[0]"
                                                                        class="fw-bold">No</label>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="form-group">

                                                                    <input type="radio"
                                                                        name="first_aid_box_clean[0]"class="fw-bold"
                                                                        id="first_aid_box_clean_yes[0]"
                                                                        value="{{ encryptId(1) }}"> <label
                                                                        for="first_aid_box_clean_yes[0]"
                                                                        class="fw-bold">Yes</label> <br>
                                                                    <input type="radio"
                                                                        name="first_aid_box_clean[0]"class="fw-bold"
                                                                        id="first_aid_box_clean_no[0]"
                                                                        value="{{ encryptId(0) }}"> <label
                                                                        for="first_aid_box_clean_no[0]"
                                                                        class="fw-bold">No</label>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="form-group">

                                                                    <input type="radio"
                                                                        name="first_aid_box_sticker[0]"class="fw-bold"
                                                                        id="first_aid_box_sticker_yes[0]"
                                                                        value="{{ encryptId(1) }}"> <label
                                                                        for="first_aid_box_sticker_yes[0]"
                                                                        class="fw-bold">Yes</label> <br>
                                                                    <input type="radio"
                                                                        name="first_aid_box_sticker[0]"class="fw-bold"
                                                                        id="first_aid_box_sticker_no[0]"
                                                                        value="{{ encryptId(0) }}"> <label
                                                                        for="first_aid_box_sticker_no[0]"
                                                                        class="fw-bold">No</label>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="form-group">

                                                                    <input type="radio"
                                                                        name="first_aid_material_index[0]" class="fw-bold"
                                                                        id="first_aid_material_index_yes[0]"
                                                                        value="{{ encryptId(1) }}"> <label
                                                                        for="first_aid_material_index_yes[0]"
                                                                        class="fw-bold">Yes</label> <br>
                                                                    <input type="radio"
                                                                        name="first_aid_material_index[0]"class="fw-bold"
                                                                        id="first_aid_material_index_no[0]"
                                                                        value="{{ encryptId(0) }}"> <label
                                                                        for="first_aid_material_index_no[0]"
                                                                        class="fw-bold">No</label>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="row gap-2">
                                                                    <div class="d-flex justify-content-center align-items-center bg-primary mt-2 ml-2 text-white rounded add-row"
                                                                        style="width: 30px; height: 30px;">
                                                                        <i class="fa-solid fa-plus"></i>
                                                                    </div>
                                                                    <div class="d-flex justify-content-center align-items-center bg-danger mt-2 ml-2 text-white rounded delete-row"
                                                                        style="width: 30px; height: 30px;">
                                                                        <i class="fa-solid fa-trash"></i>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
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
                                        href="{{ admin_url('ohc/first-aid-box/monthly-audit/checklist') }}"></x-button-cancel>
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
    <script>
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
        var Datepicker = flatpickr("#date", {
            dateFormat: "d-m-Y",
            minDate: new Date()

        });
        var dueDate = flatpickr("#next_due_on", {
            dateFormat: "d-m-Y",
            minDate: new Date()

        });


        // getDepartment

        $(document).on('change', '.unit_id', function() {
            var unitId = $(this).val();
            var row = $(this).closest('tr');
            var departmentDropdown = row.find('.department_id');

            if (unitId) {
                $.ajax({
                    url: "{{ admin_url('department/ajax-list') }}/" + unitId + "/0",
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        departmentDropdown.empty().append(
                            '<option value="">Select Department</option>');
                        $.each(data, function(key, value) {
                            departmentDropdown.append('<option value="' + value.id + '">' +
                                value.name + '</option>');
                        });
                        departmentDropdown.trigger('change'); // Ensure Select2 updates properly
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: "warning",
                            title: "Warning!",
                            text: "Error in fetching the Department.",
                            confirmButtonColor: "#d33",
                            confirmButtonText: "OK"
                        });
                    }
                });
            } else {
                departmentDropdown.empty().append('<option value="">Select Department</option>');
                departmentDropdown.trigger('change');
            }
        });

        // First Aid Box Number Fetching
        $(document).on('change', '.unit_id, .department_id', function() {
            var row = $(this).closest('tr');
            var unitId = row.find('.unit_id').val();
            var departmentId = row.find('.department_id').val();
            var firstAidBox = row.find('input[name^="first_aid_box"]');

            if (unitId && departmentId) {
                $.ajax({
                    url: "{{ admin_url('ohc/first-aid-record/first-aid-location/details') }}",
                    type: 'GET',
                    data: {
                        unit_id: unitId,
                        department_id: departmentId
                    },
                    dataType: 'json',
                    success: function(response) {
                        firstAidBox.val(response.first_aid_box_no);
                    },
                    error: function(xhr) {
                        alert('Error fetching first aid details. Please try again.');
                    }
                });
            }
        });

        // Add More Details - Cloning Rows
        $(document).ready(function() {
            // Initialize Select2
            $(".single-select").select2();


            $("#monthlyfirstaidchecklist").validate({
                rules: {
                    "unit_id[0]": {
                        required: true
                    },
                    "department_id[0]": {
                        required: true
                    },
                    "first_aid_register_maintained[0]": {
                        required: true
                    },
                    "first_aid_box_inspect_periodicity[0]": {
                        required: true
                    },
                    "first_aid_box_checklist_periodicity[0]": {
                        required: true
                    },
                    "first_aid_box_freeze_quantity[0]": {
                        required: true
                    },
                    "medicine_requisition_slip_record[0]": {
                        required: true
                    },
                    "first_aid_box_clean[0]": {
                        required: true
                    },
                    "first_aid_box_sticker[0]": {
                        required: true
                    },
                    "first_aid_material_index[0]": {
                        required: true
                    }
                },
                messages: {
                    "unit_id[0]": "Please select a unit",
                    "department_id[0]": "Please select a department",
                    "first_aid_register_maintained[0]": "This field is required",
                    "first_aid_box_inspect_periodicity[0]": "This field is required",
                    "first_aid_box_checklist_periodicity[0]": "This field is required",
                    "first_aid_box_freeze_quantity[0]": "This field is required",
                    "medicine_requisition_slip_record[0]": "This field is required",
                    "first_aid_box_clean[0]": "This field is required",
                    "first_aid_box_sticker[0]": "This field is required",
                    "first_aid_material_index[0]": "This field is required"
                }
            });


            $(document).on("click", ".add-row", function() {
                var rowCount = $("#medicine-tbody tr").length;
                var newRow = $(".medicine-row:first").clone();

                newRow.find("select, input").each(function() {
                    var name = $(this).attr("name");
                    if (name) {
                        name = name.replace(/\[\d+\]/, "[" + rowCount + "]");
                        $(this).attr("name", name);
                    }

                    var id = $(this).attr("id");
                    if (id) {
                        var newId = id.replace(/\[\d+\]/, "[" + rowCount + "]");
                        $(this).attr("id", newId);
                    }

                    var label = newRow.find("label[for='" + id + "']");
                    if (label.length) {
                        label.attr("for", newId);
                    }

                    if ($(this).is("input[type='radio'], input[type='checkbox']")) {
                        $(this).prop("checked", false);
                    }

                    if ($(this).is("select")) {
                        $(this).val(null).trigger("change"); // Reset value
                    }

                    $(this).removeClass("error");
                    $(this).next("label.error").remove();
                });


                $("#medicine-tbody").append(newRow);


                newRow.find(".single-select").each(function() {
                    $(this).removeClass("select2-hidden-accessible");
                    $(this).next(".select2-container").remove();
                    $(this).select2();
                });

                // Validate dynamically added fields
                newRow.find("input, select").each(function() {
                    $(this).rules("add", {
                        required: true,
                        messages: {
                            required: "This field is required"
                        }
                    });
                });
            });



        });




        // deleted the row
        $(document).on("click", ".delete-row", function() {
            if ($("#medicine-tbody tr").length > 1) {
                $(this).closest("tr").remove();
            } else {
                Swal.fire({
                    icon: "warning",
                    title: "Warning!",
                    text: "At least one row must be present.",
                    confirmButtonColor: "#d33",
                    confirmButtonText: "OK"
                });
            }
        });

        $(function () {
    $.validator.addMethod(
        "regex",
        function (value, element, regex) {
            return this.optional(element) || new RegExp(regex).test(value);
        },
        "Invalid format."
    );

    $('#monthlyfirstaidchecklist').validate({
        rules: {
            shift: {
                required: true,
            },
            frequency: {
                required: true,
            },
            issue_date: {
                required: true,
            },
            document_no: {
                required: true,
                minlength: 3,
                maxlength: 30,
            },
            review_date: {
                required: true,
            },
            date: {
                required: true,
            },
            signature_image: {
                required: true,
                extension: "png|jpeg|jpg",
            }
        },
        messages: {
            shift: {
                required: "Please select the Shift name.",
            },
            frequency: {
                required: "Please select the Frequency Name.",
            },
            issue_date: {
                required: "Please select the issue date.",
            },
            date: {
                required: "Please select the date.",
            },
            document_no: {
                required: "Document Number is Required",
                minlength: "Minimum Characters should be 3",
                maxlength: "Maximum Characters should not exceed 30",
            },
            review_date: {
                required: "Please select the review date.",
            },
            signature_image: {
                required: "Please upload a signature.",
                extension: "Only PNG, JPEG, and JPG formats are allowed.",
            },
        },
        errorElement: 'span',
        errorPlacement: function (error, element) {
            error.addClass('invalid-feedback');

            if (element.hasClass("single-select") || element.hasClass("form-control")) {
                // Append error message for select elements and form-controls properly
                element.closest('.form-input').append(error);
            } else if (element.is(":file")) {
                // Append error message for file input
                element.closest('.form-group').append(error);
            } else {
                error.insertAfter(element);
            }
        },
        highlight: function (element, errorClass, validClass) {
            $(element).addClass('is-invalid');
        },
        unhighlight: function (element, errorClass, validClass) {
            $(element).removeClass('is-invalid');
        },
        submitHandler: function (form) {
            form.submit();
        },
        invalidHandler: function (event, validator) {
            var errors = validator.numberOfInvalids();
            console.log("Form has " + errors + " invalid fields.");
        },
    });
});

    </script>
@endpush
