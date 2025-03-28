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
                                        <div
                                            class="d-flex justify-content-end align-items-center me-2 mb-3 button-container">
                                            <button class="btn btn-primary add-row me-3" type="button" id="add-row"
                                                style="width: 84px;">
                                                Add
                                            </button>

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
                                                            <th>Does the first-aid box is bieng inspect as per periodicity.
                                                            </th>
                                                            <th>Does the First- aid box inspection Checklist is being filled
                                                                as per periodicity.
                                                            </th>
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
                                                                    <select name="unit_id[]"
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
                                                                    <select name="department_id[]"
                                                                        class="form-control single-select department_id"
                                                                        style="width: 100%">
                                                                        <option value="">Select the Department Name
                                                                        </option>
                                                                    </select>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <input type="text" name="first_aid_box[]"
                                                                    class="form-control first_aid_box" readonly>
                                                            </td>
                                                            <td>
                                                                <div class="form-group">
                                                                    <input type="radio"
                                                                        name="first_aid_register_maintained[]"
                                                                        id="first_aid_register_maintained_yes[]"
                                                                        value="{{ encryptId(1) }}">
                                                                    <label for="first_aid_register_maintained_yes[]"
                                                                        class="fw-bold">Yes</label>
                                                                    <br>
                                                                    <input type="radio"
                                                                        name="first_aid_register_maintained[]"
                                                                        id="first_aid_register_maintained_no[]"
                                                                        value="{{ encryptId(0) }}">
                                                                    <label for="first_aid_register_maintained_no[]"
                                                                        class="fw-bold">No</label>
                                                                </div>
                                                            </td>

                                                            <td>
                                                                <div class="form-group">
                                                                    <input type="radio"
                                                                        name="first_aid_inspect_periodicity[]"
                                                                        id="first_aid_inspect_periodicity_yes[]"
                                                                        value="{{ encryptId(1) }}">
                                                                    <label for="first_aid_inspect_periodicity_yes[]"
                                                                        class="fw-bold">Yes</label>
                                                                    <br>
                                                                    <input type="radio"
                                                                        name="first_aid_inspect_periodicity[]"
                                                                        id="first_aid_inspect_periodicity_no[]"
                                                                        value="{{ encryptId(0) }}">
                                                                    <label for="first_aid_inspect_periodicity_no[]"
                                                                        class="fw-bold">No</label>
                                                                </div>
                                                            </td>

                                                            <td>
                                                                <div class="form-group">

                                                                    <input type="radio"
                                                                        name="first_aid_checklist_periodicity[]"class="fw-bold"
                                                                        id="first_aid_checklist_periodicity_yes[]"
                                                                        value="{{ encryptId(1) }}"> <label
                                                                        for="first_aid_checklist_periodicity_yes[]"
                                                                        class="fw-bold">Yes</label> <br>
                                                                    <input type="radio"
                                                                        name="first_aid_checklist_periodicity[]"class="fw-bold"
                                                                        id="first_aid_checklist_periodicity_no[]"
                                                                        value="{{ encryptId(0) }}"> <label
                                                                        for="first_aid_checklist_periodicity_no[]"
                                                                        class="fw-bold">No</label>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="form-group">

                                                                    <input type="radio"
                                                                        name="first_aid_freeze_quantity[]"class="fw-bold"
                                                                        id="first_aid_freeze_quantity_yes[]"
                                                                        value="{{ encryptId(1) }}"> <label
                                                                        for="first_aid_freeze_quantity_yes[]"
                                                                        class="fw-bold">Yes</label> <br>
                                                                    <input type="radio"
                                                                        name="first_aid_freeze_quantity[]"class="fw-bold"
                                                                        id="first_aid_freeze_quantity_no[]"
                                                                        value="{{ encryptId(0) }}"> <label
                                                                        for="first_aid_freeze_quantity_no[]"
                                                                        class="fw-bold">No</label>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="form-group">

                                                                    <input type="radio"
                                                                        name="medicine_requisition_slip_record[]"class="fw-bold"
                                                                        id="medicine_requisition_slip_record_yes[]"
                                                                        value="{{ encryptId(1) }}"> <label
                                                                        for="medicine_requisition_slip_record_yes[]"
                                                                        class="fw-bold">Yes</label> <br>
                                                                    <input type="radio"
                                                                        name="medicine_requisition_slip_record[]"class="fw-bold"
                                                                        id="medicine_requisition_slip_record_no[]"
                                                                        value="{{ encryptId(0) }}"> <label
                                                                        for="medicine_requisition_slip_record_no[]"
                                                                        class="fw-bold">No</label>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="form-group">

                                                                    <input type="radio"
                                                                        name="first_aid_clean[]"class="fw-bold"
                                                                        id="first_aid_clean_yes[]"
                                                                        value="{{ encryptId(1) }}"> <label
                                                                        for="first_aid_clean_yes[]"
                                                                        class="fw-bold">Yes</label> <br>
                                                                    <input type="radio"
                                                                        name="first_aid_clean[]"class="fw-bold"
                                                                        id="first_aid_clean_no[]"
                                                                        value="{{ encryptId(0) }}"> <label
                                                                        for="first_aid_clean_no[]"
                                                                        class="fw-bold">No</label>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="form-group">

                                                                    <input type="radio"
                                                                        name="first_aid_sticker[]"class="fw-bold"
                                                                        id="first_aid_sticker_yes[]"
                                                                        value="{{ encryptId(1) }}"> <label
                                                                        for="first_aid_sticker_yes[]"
                                                                        class="fw-bold">Yes</label> <br>
                                                                    <input type="radio"
                                                                        name="first_aid_sticker[]"class="fw-bold"
                                                                        id="first_aid_sticker_no[]"
                                                                        value="{{ encryptId(0) }}"> <label
                                                                        for="first_aid_sticker_no[]"
                                                                        class="fw-bold">No</label>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="form-group">

                                                                    <input type="radio"
                                                                        name="first_aid_material_index[]" class="fw-bold"
                                                                        id="first_aid_material_index_yes[]"
                                                                        value="{{ encryptId(1) }}"> <label
                                                                        for="first_aid_material_index_yes[]"
                                                                        class="fw-bold">Yes</label> <br>
                                                                    <input type="radio"
                                                                        name="first_aid_material_index[]"class="fw-bold"
                                                                        id="first_aid_material_index_no[]"
                                                                        value="{{ encryptId(0) }}"> <label
                                                                        for="first_aid_material_index_no[]"
                                                                        class="fw-bold">No</label>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="row gap-2">

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

        $(document).ready(function() {
            let rowCount = 1; // Initialize row count

            $(".add-row").click(function() {
                var newRow = `
        <tr class="medicine-row">
            <td>
                <div class="form-group">
                    <select name="unit_id[${rowCount}]" class="form-control single-select unit_id" style="width: 100%">
                        <option value="">Select the Unit Name</option>
                        @foreach ($unit as $list)
                            <option value="{{ encryptId($list->id) }}">{{ $list->unit_name }}</option>
                        @endforeach
                    </select>
                </div>
            </td>
            <td>
                <div class="form-group">
                    <select name="department_id[${rowCount}]" class="form-control single-select department_id" style="width: 100%">
                        <option value="">Select the Department Name</option>
                    </select>
                </div>
            </td>
            <td>
                <input type="text" name="first_aid_box[${rowCount}]" class="form-control first_aid_box" readonly>
            </td>
            </td>
                                                            <td>
                                                                <div class="form-group">
                                                                    <input type="radio"
                                                                        name="first_aid_register_maintained[${rowCount}]"
                                                                        id="first_aid_register_maintained_yes[${rowCount}]"
                                                                        value="{{ encryptId(1) }}">
                                                                    <label for="first_aid_register_maintained_yes[${rowCount}]"
                                                                        class="fw-bold">Yes</label>
                                                                    <br>
                                                                    <input type="radio"
                                                                        name="first_aid_register_maintained[${rowCount}]"
                                                                        id="first_aid_register_maintained_no[${rowCount}]"
                                                                        value="{{ encryptId(0) }}">
                                                                    <label for="first_aid_register_maintained_no[${rowCount}]"
                                                                        class="fw-bold">No</label>
                                                                         <br>
                                                                     <span class="error-message text-danger"></span>
                                                                </div>
                                                            </td>

                                                            <td>
                                                                <div class="form-group">
                                                                    <input type="radio"
                                                                        name="first_aid_inspect_periodicity[${rowCount}]"
                                                                        id="first_aid_inspect_periodicity_yes[${rowCount}]"
                                                                        value="{{ encryptId(1) }}">
                                                                    <label for="first_aid_inspect_periodicity_yes[${rowCount}]"
                                                                        class="fw-bold">Yes</label>
                                                                    <br>
                                                                    <input type="radio"
                                                                        name="first_aid_inspect_periodicity[${rowCount}]"
                                                                        id="first_aid_inspect_periodicity_no[${rowCount}]"
                                                                        value="{{ encryptId(0) }}">
                                                                    <label for="first_aid_inspect_periodicity_no[${rowCount}]"
                                                                        class="fw-bold">No</label>
                                                                         <br>
                                                                         <span class="error-message text-danger"></span>
                                                                </div>
                                                            </td>

                                                            <td>
                                                                <div class="form-group">

                                                                    <input type="radio"
                                                                        name="first_aid_checklist_periodicity[${rowCount}]"class="fw-bold"
                                                                        id="first_aid_checklist_periodicity_yes[${rowCount}]"
                                                                        value="{{ encryptId(1) }}"> <label
                                                                        for="first_aid_checklist_periodicity_yes[${rowCount}]"
                                                                        class="fw-bold">Yes</label> <br>
                                                                    <input type="radio"
                                                                        name="first_aid_checklist_periodicity[${rowCount}]"class="fw-bold"
                                                                        id="first_aid_checklist_periodicity_no[${rowCount}]"
                                                                        value="{{ encryptId(0) }}"> <label
                                                                        for="first_aid_checklist_periodicity_no[${rowCount}]"
                                                                        class="fw-bold">No</label>
                                                                         <br>
                                                                         <span class="error-message text-danger"></span>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="form-group">

                                                                    <input type="radio"
                                                                        name="first_aid_freeze_quantity[${rowCount}]"class="fw-bold"
                                                                        id="first_aid_freeze_quantity_yes[${rowCount}]"
                                                                        value="{{ encryptId(1) }}"> <label
                                                                        for="first_aid_freeze_quantity_yes[${rowCount}]"
                                                                        class="fw-bold">Yes</label> <br>
                                                                    <input type="radio"
                                                                        name="first_aid_freeze_quantity[${rowCount}]"class="fw-bold"
                                                                        id="first_aid_freeze_quantity_no[${rowCount}]"
                                                                        value="{{ encryptId(0) }}"> <label
                                                                        for="first_aid_freeze_quantity_no[${rowCount}]"
                                                                        class="fw-bold">No</label>
                                                                         <br>
                                                                        <span class="error-message text-danger"></span>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="form-group">

                                                                    <input type="radio"
                                                                        name="medicine_requisition_slip_record[${rowCount}]"class="fw-bold"
                                                                        id="medicine_requisition_slip_record_yes[${rowCount}]"
                                                                        value="{{ encryptId(1) }}"> <label
                                                                        for="medicine_requisition_slip_record_yes[${rowCount}]"
                                                                        class="fw-bold">Yes</label> <br>
                                                                    <input type="radio"
                                                                        name="medicine_requisition_slip_record[${rowCount}]"class="fw-bold"
                                                                        id="medicine_requisition_slip_record_no[${rowCount}]"
                                                                        value="{{ encryptId(0) }}"> <label
                                                                        for="medicine_requisition_slip_record_no[${rowCount}]"
                                                                        class="fw-bold">No</label>
                                                                         <br>
                                                                        <span class="error-message text-danger"></span>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="form-group">

                                                                    <input type="radio"
                                                                        name="first_aid_clean[${rowCount}]"class="fw-bold"
                                                                        id="first_aid_clean_yes[${rowCount}]"
                                                                        value="{{ encryptId(1) }}"> <label
                                                                        for="first_aid_clean_yes[${rowCount}]"
                                                                        class="fw-bold">Yes</label> <br>
                                                                    <input type="radio"
                                                                        name="first_aid_clean[${rowCount}]"class="fw-bold"
                                                                        id="first_aid_clean_no[${rowCount}]"
                                                                        value="{{ encryptId(0) }}"> <label
                                                                        for="first_aid_clean_no[${rowCount}]"
                                                                        class="fw-bold">No</label>
                                                                         <br>
                                                                        <span class="error-message text-danger"></span>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="form-group">

                                                                    <input type="radio"
                                                                        name="first_aid_sticker[${rowCount}]"class="fw-bold"
                                                                        id="first_aid_sticker_yes[${rowCount}]"
                                                                        value="{{ encryptId(1) }}"> <label
                                                                        for="first_aid_sticker_yes[${rowCount}]"
                                                                        class="fw-bold">Yes</label> <br>
                                                                    <input type="radio"
                                                                        name="first_aid_sticker[${rowCount}]"class="fw-bold"
                                                                        id="first_aid_sticker_no[${rowCount}]"
                                                                        value="{{ encryptId(0) }}"> <label
                                                                        for="first_aid_sticker_no[${rowCount}]"
                                                                        class="fw-bold">No</label>
                                                                         <br>
                                                                       <span class="error-message text-danger"></span>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="form-group">

                                                                    <input type="radio"
                                                                        name="first_aid_material_index[${rowCount}]" class="fw-bold"
                                                                        id="first_aid_material_index_yes[${rowCount}]"
                                                                        value="{{ encryptId(1) }}"> <label
                                                                        for="first_aid_material_index_yes[${rowCount}]"
                                                                        class="fw-bold">Yes</label> <br>
                                                                    <input type="radio"
                                                                        name="first_aid_material_index[${rowCount}]"class="fw-bold"
                                                                        id="first_aid_material_index_no[${rowCount}]"
                                                                        value="{{ encryptId(0) }}"> <label
                                                                        for="first_aid_material_index_no[${rowCount}]"
                                                                        class="fw-bold">No</label>
                                                                         <br>
                                                                      <span class="error-message text-danger"></span>
                                                                </div>
                                                            </td>
                                                              <td>
                                                                        <div class="row gap-2">

                                                                            <div class="d-flex justify-content-center align-items-center bg-danger mt-2 ml-2 text-white rounded delete-row"
                                                                                style="width: 30px; height: 30px;">
                                                                                <i class="fa-solid fa-trash"></i>
                                                                            </div>
                                                                        </div>
                                                            </td>
                                                    </tr>`;


                $("#medicine-tbody").append(newRow);

                rowCount++;
                $('.single-select').select2({
                    placeholder: "Select an option",
                    width: '100%'
                });



                $(`select[name="unit_id[\${rowCount}]"]`).rules('add', {
                    required: true,
                    messages: {
                        required: 'Unit Name is required'
                    }
                });

                $(`select[name="department_id[\${rowCount}]"]`).rules('add', {
                    required: true,
                    messages: {
                        required: 'Department Name is required'
                    }
                });

                $(`input[name="first_aid_box[\${rowCount}]"]`).rules('add', {
                    required: true,
                    messages: {
                        required: 'First Aid Box Number is required'
                    }
                });

                $("#monthlyfirstaidchecklist").validate({
                    rules: {
                        [`first_aid_register_maintained[\${rowCount}]`]: {
                            required: true
                        },
                        [`first_aid_inspect_periodicity[\${rowCount}]`]: {
                            required: true
                        },
                        [`first_aid_checklist_periodicity[\${rowCount}]`]: {
                            required: true
                        },
                        [`first_aid_freeze_quantity[\${rowCount}]`]: {
                            required: true
                        },
                        [`medicine_requisition_slip_record[\${rowCount}]`]: {
                            required: true
                        },
                        [`first_aid_clean[\${rowCount}]`]: {
                            required: true
                        },
                        [`first_aid_sticker[\${rowCount}]`]: {
                            required: true
                        },
                        [`first_aid_material_index[\${rowCount}]`]: {
                            required: true
                        },
                    },
                    messages: {
                        [`first_aid_register_maintained[\${rowCount}]`]: {
                            required: "Please select an option"
                        },
                        [`first_aid_inspect_periodicity[\${rowCount}]`]: {
                            required: "Please select an option"
                        },
                        [`first_aid_checklist_periodicity[\${rowCount}]`]: {
                            required: "Please select an option"
                        },
                        [`first_aid_freeze_quantity[\${rowCount}]`]: {
                            required: "Please select an option"
                        },
                        [`medicine_requisition_slip_record[\${rowCount}]`]: {
                            required: "Please select an option"
                        },
                        [`first_aid_clean[\${rowCount}]`]: {
                            required: "Please select an option"
                        },
                        [`first_aid_sticker[\${rowCount}]`]: {
                            required: "Please select an option"
                        },
                        [`first_aid_material_index[\${rowCount}]`]: {
                            required: "Please select an option"
                        },
                    }
                });

            });

        });





        $(function() {
            $.validator.addMethod(
                "regex",
                function(value, element, regex) {
                    return this.optional(element) || new RegExp(regex).test(value);
                },
                "Invalid format."
            );

            $('#monthlyfirstaidchecklist').validate({
                rules: {
                    shift: {
                        required: true
                    },
                    frequency: {
                        required: true
                    },
                    issue_date: {
                        required: true
                    },
                    document_no: {
                        required: true,
                        minlength: 3,
                        maxlength: 30
                    },
                    review_date: {
                        required: true
                    },
                    date: {
                        required: true
                    },
                    signature_image: {
                        required: true,
                        extension: "png|jpeg|jpg"
                    },

                    "unit_id[]": {
                        required: true
                    },
                    "department_id[]": {
                        required: true
                    },
                    "first_aid_box[]": {
                        required: true
                    },
                    "first_aid_register_maintained[]": {
                        required: true
                    },
                    "first_aid_inspect_periodicity[]": {
                        required: true
                    },
                    "first_aid_freeze_quantity[]": {
                        required: true
                    },
                    "first_aid_checklist_periodicity[]": {
                        required: true
                    },
                    "first_aid_material_index[]": {
                        required: true
                    },
                    "first_aid_sticker[]": {
                        required: true
                    },
                    "first_aid_clean[]": {
                        required: true
                    },
                    "medicine_requisition_slip_record[]": {
                        required: true
                    },
                },

                messages: {
                    shift: {
                        required: "Please select the Shift name."
                    },
                    frequency: {
                        required: "Please select the Frequency Name."
                    },
                    issue_date: {
                        required: "Please select the issue date."
                    },
                    date: {
                        required: "Please select the date."
                    },
                    document_no: {
                        required: "Document Number is Required",
                        minlength: "Minimum Characters should be 3",
                        maxlength: "Maximum Characters should not exceed 30",
                    },
                    review_date: {
                        required: "Please select the review date."
                    },
                    signature_image: {
                        required: "Please upload a signature.",
                        extension: "Only PNG, JPEG, and JPG formats are allowed.",
                    },
                    "unit_id[]": {
                        required: "Please select the Unit Name."
                    },
                    "department_id[]": {
                        required: "Please select the Department Name."
                    },
                    "first_aid_box[]": {
                        required: "Please enter First Aid Box details."
                    },
                    "first_aid_register_maintained[]": {
                        required: "Please select an option."
                    },
                    "first_aid_inspect_periodicity[]": {
                        required: "Please select an option."
                    },
                    "first_aid_freeze_quantity[]": {
                        required: "Please select an option."
                    },
                    "first_aid_checklist_periodicity[]": {
                        required: "Please select an option."
                    },
                    "first_aid_material_index[]": {
                        required: "Please select an option."
                    },
                    "first_aid_sticker[]": {
                        required: "Please select an option."
                    },
                    "first_aid_clean[]": {
                        required: "Please select an option."
                    },
                    "medicine_requisition_slip_record[]": {
                        required: "Please select an option."
                    },
                },

                errorElement: 'span',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');

                    if (element.hasClass("single-select") || element.hasClass("form-control")) {
                        element.closest('.form-group').append(error);
                    } else if (element.is(":file")) {
                        element.closest('.form-group').append(error);
                    } else if (element.is(":radio")) {
                        element.closest('.form-group').append(error);
                    } else {
                        error.insertAfter(element);
                    }
                },

                highlight: function(element) {
                    $(element).addClass('is-invalid');
                },

                unhighlight: function(element) {
                    $(element).removeClass('is-invalid');
                },

                submitHandler: function(form) {
                    form.submit();
                },

                invalidHandler: function(event, validator) {
                    var errors = validator.numberOfInvalids();
                    console.log("Form has " + errors + " invalid fields.");
                },
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
    </script>
@endpush
