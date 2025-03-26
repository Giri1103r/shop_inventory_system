@extends('admin.layouts.admin')
@section('title', 'Weekly First Aid')
@section('pageurl', admin_url('ohc/first-aid-box/weekly-inspection/list'))


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
                                        href="{{ admin_url('ohc/first-aid-box/weekly-inspection/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <form method="POST" id="HealthInstrumentAdd" enctype="multipart/form-data"
                                    action="{{ admin_url('ohc/first-aid-box/weekly-inspection/add/submit') }}">
                                    @csrf

                                    <div class="basic-form">

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
                                                    <label for="rate" class="form-label require ">Date Of
                                                        Inspection</label>
                                                    <div class="input-group date form-input custom-height">
                                                        <input type="text" name="date_of_inspection"
                                                            id="date_of_inspection" class="form-control"autocomplete="off">
                                                        <div class="input-group-addon input-group-text">
                                                            <span class="fa fa-calendar"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">First Aid Box Number</label>
                                                    <input type="text" name="first_aid_box_no" id = "first_aid_box_no"
                                                        class="form-control">
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

                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label for="location_id" class="form-label">
                                                        Location</label>
                                                    <select name="location_id" id="location_id"
                                                        class=" form-control single-select" style="width: 100%">
                                                        <option value="">Select Location</option>
                                                        @foreach ($location as $loc)
                                                            <option value="{{ encryptId($loc->id) }}">
                                                                {{ $loc->location_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Unit</label>
                                                    <select name="unit_id" id="unit_id" class="form-control single-select"
                                                        style="width: 100%">
                                                        <option value="">Select the unit</option>
                                                        @foreach ($unit as $list)
                                                            <option value="{{ encryptId($list->id) }}">
                                                                {{ $list->unit_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">First Aider Name</label>
                                                    <select name="first_aider" id="first_aider"
                                                        class="form-control single-select" style="width: 100%">
                                                        <option value="">Select the option</option>
                                                        @foreach ($First_aid as $list)
                                                            <option value="{{ encryptId($list->id) }}">
                                                                {{ $list->certifier_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>


                                        </div>
                                    </div>

                                    <div class="card-body">
                                        <div class="row">
                                            <div class="card-header-inner">
                                                <h4 class="text-white">Weekly First Aid</h4>
                                            </div>
                                        </div>
        
                                        <div class="d-flex justify-content-end align-items-center mb-3 button-container">
        
                                            <button class="btn btn-primary addmorebutton" data-block='lesson_learned_block'
                                                data-row='lesson_learned_row' type="button" id="dynamic-add-more"
                                                style="margin-left: 10px; width: 84px;">
                                                Add
                                            </button>
        
                                            
                                        </div>

                                        <div class="table-responsive">
                                            <table class="table table-bordered" id="medicine-table">
                                                <thead class="bg-secondary text-white">
                                                    <tr>
                                                        <th width="25%">Medicine</th>
                                                        <th width="15%">Available Quantity</th>
                                                        <th width="15%">Freeze Quantity</th>
                                                        <th width="15%">Material Expiry</th>
                                                        <th width="20%">Remarks</th>
                                                        <th width="10%">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="lesson_learned_block">
                                                    <tr class="lesson_learned_row">
                                                        <td>
                                                            <div class="form-group">
                                                                <select name="weekly_first_aid[1][medicine_id]"
                                                                    class="form-control  single-select">
                                                                    <option value="">Select Medicine</option>
                                                                    @foreach ($medicine as $medi)
                                                                    <option value="{{ encryptId($medi->id) }}">
                                                                        {{ $medi->medicine }}</option>
                                                                @endforeach
                                                                </select>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="form-group">
                                                                <input type="number" name="weekly_first_aid[1][available_quantity]"
                                                                    class="form-control" min="0">
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="form-group">
                                                                <input type="number" name="weekly_first_aid[1][freeze_quantity]"
                                                                    class="form-control" min="0">
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="form-group">
                                                                <input type="text" name="weekly_first_aid[1][material_expiry]"
                                                                    class="form-control datepicker">
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="form-group">
                                                                <textarea name="weekly_first_aid[1][remarks]" class="form-control" rows="1"></textarea>
                                                            </div>
                                                        </td>
                                                        <td><button class="btn btn-danger removerowdata"
                                                            type="button" style="margin:10px;"><i
                                                                class="fa fa-trash"></i></button>
                                                    </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="submit-button" style="text-align: right;">
                                            <x-button-submit class="submit"></x-button-submit>
                                            <x-button-reset class=""></x-button-reset>
                                            <x-button-cancel
                                                href="{{ admin_url('ohc/first-aid-box/weekly-inspection/list') }}"></x-button-cancel>
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
                flatpickr("input[id^='issue_date']", {
                    dateFormat: "d-m-Y",
                    minDate: new Date()
                });
                flatpickr("input[id^='date_of_inspection']", {
                    dateFormat: "d-m-Y",
                    minDate: new Date()
                });
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

                let newRow = $(".lesson_learned_row").first().clone();
                newRow.find("input, select, textarea").each(function() {
                    let oldName = $(this).attr("name");
                    let oldId = $(this).attr("id");

                    if (oldName) {
                        let newName = oldName.replace(/\[\d+\]/, "[" + (rowCount + 1) + "]");
                        $(this).attr("name", newName);
                    }
                    if (oldId) {
                        let newId = oldId.replace(/\d+$/, rowCount + 1);
                        $(this).attr("id", newId);
                    }
                    if ($(this).is("input[type='text'], textarea")) {
                        $(this).val("");
                    }
                    if ($(this).is("select")) {
                        $(this).val("").trigger("change");
                    }
                });
                newRow.find("input[name*='[sr_no]']").val("HEALTH-" + String(rowCount + 1).padStart(4,
                    '0'));

                newRow.find(".invalid-feedback").remove();
                newRow.find(".is-invalid").removeClass("is-invalid");
                newRow.find(".select2-container").remove();
                newRow.find(".single-select").select2();

                $("#lesson_learned_block").append(newRow);

                newRow.find("input[name*='[instrument_name]']").rules("add", {
                    minlength: 3,
                    maxlength: 2000,
                    pattern: /^[a-zA-Z0-9\s\-_"'()]+$/,
                    messages: {
                        minlength: "Instrument name must be at least 3 characters.",
                        maxlength: "Instrument name must not exceed 200 characters.",
                        pattern: "Only letters, numbers, spaces, and -_'() are allowed."
                    }
                });

                newRow.find("input[name*='[resource_code]']").rules("add", {
                    minlength: 3,
                    maxlength: 2000,
                    pattern: /^[a-zA-Z0-9\s\-_"'()]+$/,
                    messages: {
                        minlength: "Instrument name must be at least 3 characters.",
                        maxlength: "Instrument name must not exceed 200 characters.",
                        pattern: "Only letters, numbers, spaces, and -_'() are allowed."
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

       

        
    </script>
@endpush



