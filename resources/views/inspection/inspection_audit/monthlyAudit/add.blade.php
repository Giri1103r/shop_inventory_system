@extends('admin.layouts.admin')
@section('title', 'Monthly Audit Plan')
@section('pageurl', admin_url('audit/monthly-audit/audit-plan/list'))

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
                                        href="{{ admin_url('audit/monthly-audit/audit-plan/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <form method="POST" id="monthlyAuditTask" enctype="multipart/form-data"
                                    action="{{ admin_url('audit/monthly-audit/audit-plan/add/submit') }}">
                                    @csrf

                                    <div class="basic-form">
                                        <div class="card-body">

                                            <div class="row">
                                                <div class="card-header-inner d-flex justify-content-between">
                                                    <h4 class="text-white"> Monthly Audit Plan</h4>
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

                                                    <div class="col-md-4 mb-3">
                                                        <div class="form-group form-input">
                                                            <label class="form-label require">Auditee Name</label>
                                                            <input type="text" name="monthly_audit[1][auditee_name]"
                                                                id = "auditee_name_1" class="form-control"
                                                                placeholder="Enter Auditee Name">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4 mb-3">
                                                        <div class="form-group form-input">
                                                            <label for="unit_id" class="form-label require">
                                                                Unit</label>
                                                            <select name="monthly_audit[1][unit_id]" id="unit_id_1"
                                                                class=" form-control single-select" style="width: 100%">
                                                                <option value="">Select Unit</option>
                                                                @foreach ($unitList as $unit)
                                                                    <option value="{{ encryptId($unit->id) }}">
                                                                        {{ $unit->unit_name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4 mb-3">
                                                        <div class="form-group form-input">
                                                            <label for="unit_id" class="form-label require">
                                                                Task Name</label>
                                                            <select name="monthly_audit[1][task_name]" id="task_name_1"
                                                                class=" form-control single-select" style="width: 100%">
                                                                <option value="">Select Unit</option>
                                                                @foreach ($audit_task as $task)
                                                                    <option value="{{ encryptId($task->id) }}">
                                                                        {{ $task->task_name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <div class="form-group form-input">
                                                            <label for="unit_id" class="form-label require">Compliance
                                                                Category
                                                            </label>
                                                            <select name="monthly_audit[1][compliance_category]"
                                                                id="compliance_category_1"
                                                                class=" form-control single-select" style="width: 100%">
                                                                <option value="">Select Category</option>
                                                                <option value="{{ encryptId(FIRE) }}">Fire</option>
                                                                <option value="{{ encryptId(HEALTH) }}">Health</option>
                                                                <option value="{{ encryptId(SAFETY) }}">Saftey</option>
                                                                <option value="{{ encryptId(MIS) }}">MIS</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4 mb-2">
                                                        <div class="form-group form-input">
                                                            <label class="form-label require">Reference Doc.No
                                                            </label>
                                                            <input type="text" name="monthly_audit[1][reference_doc_no]"
                                                                id = "reference_doc_no_1" class="form-control"
                                                                placeholder="Enter Reference Doc.No">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4 mb-2">
                                                        <div class="form-group form-input">
                                                            <label class="form-label require">Frequency</label>
                                                            <select name="monthly_audit[1][frequency_id]"
                                                                id="frequency_id_1" class="form-control single-select"
                                                                style="width: 100%">
                                                                <option value="">Select the Frequency</option>
                                                                @foreach ($frequency as $frequency)
                                                                    <option value="{{ encryptId($frequency->id) }}">
                                                                        {{ $frequency->frequency_name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4 mb-2">
                                                        <div class="form-group form-input">
                                                            <label class="form-label require"> Direct / In-Direct
                                                            </label>
                                                            <select name="monthly_audit[1][direct_in_direct]"
                                                                id="direct_in_direct_1" class=" form-control single-select"
                                                                style="width: 100%">
                                                                <option value="">Select Category</option>
                                                                <option value="1">Direct</option>
                                                                <option value="0">In-Direct</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4 mb-2">
                                                        <label class="form-label ">Status (Yes/No)</label>
                                                        <div class="mb-3 form-input">
                                                            <input type="radio" name="monthly_audit[1][status]"
                                                                value="1">
                                                            <label for="yes">YES</label>

                                                            <input type="radio" name="monthly_audit[1][status]"
                                                                value="0">
                                                            <label for="no">NO</label>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4 mb-2">
                                                        <div class="form-group form-input">
                                                            <label class="form-label require">Points
                                                            </label>
                                                            <input type="number" name="monthly_audit[1][points]"
                                                                id = "points_1" class="form-control"
                                                                placeholder="Enter Points">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4 mt-2">
                                                        <div class="form-group form-input require">
                                                            <label class="form-label">Remark</label>
                                                            <textarea class="form-control" name="monthly_audit[1][remark]" id="remark_1"></textarea>

                                                        </div>
                                                    </div>


                                                    <div class="col-md-2 text-right mt-3">
                                                        <button class="btn btn-danger removerowdata" type="button"
                                                            style="margin:10px;"><i class="fa fa-trash"></i></button>

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
                    if ($(this).is("input[type='text'], textarea, input[type='number']")) {
                        $(this).val("");
                    }
                    if ($(this).is("select")) {
                        $(this).val("").trigger("change");
                    }
                });

                newRow.find(".invalid-feedback").remove();
                newRow.find(".is-invalid").removeClass("is-invalid");
                newRow.find(".select2-container").remove();
                newRow.find(".single-select").select2();
                $("#lesson_learned_block").append(newRow);
                

                // Add validation rules dynamically
                newRow.find("input[name*='[auditee_name]']").rules("add", {
                    required: true,
                    messages: {
                        required: "Auditee Name is required."
                    }
                });

                newRow.find("select[name*='[unit_id]']").rules("add", {
                    required: true,
                    messages: {
                        required: "Unit is required."
                    }
                });

                newRow.find("select[name*='[task_name]']").rules("add", {
                    required: true,
                    messages: {
                        required: "Task Name is required."
                    }
                });

                newRow.find("select[name*='[compliance_category]']").rules("add", {
                    required: true,
                    messages: {
                        required: "Compliance Category is required."
                    }
                });

                newRow.find("input[name*='[reference_doc_no]']").rules("add", {
                    required: true,
                    messages: {
                        required: "Reference Doc No is required."
                    }
                });

                newRow.find("input[name*='[points]']").rules("add", {
                    required: true,
                    number: true,
                    messages: {
                        required: "Points are required.",
                        number: "Only numeric values are allowed."
                    }
                });

                newRow.find("textarea[name*='[remark]']").rules("add", {
                    required: true,
                    messages: {
                        required: "Remark is required."
                    }
                });

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
        });


        $(document).ready(function() {
            $("#monthlyAuditTask").validate({
                rules: {
                    "monthly_audit[1][auditee_name]": {
                        required: true
                    },
                    "monthly_audit[1][unit_id]": {
                        required: true
                    },
                    "monthly_audit[1][task_name]": {
                        required: true
                    },
                    "monthly_audit[1][compliance_category]": {
                        required: true
                    },
                    "monthly_audit[1][reference_doc_no]": {
                        required: true
                    },
                    "monthly_audit[1][frequency_id]": {
                        required: true
                    },
                    "monthly_audit[1][direct_in_direct]": {
                        required: true
                    },
                    "monthly_audit[1][status]": {
                        required: true
                    },
                    "monthly_audit[1][points]": {
                        required: true,
                        number: true,
                        min: 0
                    },
                    "monthly_audit[1][remark]": {
                        required: true
                    }
                },
                messages: {
                    "monthly_audit[1][auditee_name]": "Please enter the Auditee Name",
                    "monthly_audit[1][unit_id]": "Please select a Unit",
                    "monthly_audit[1][task_name]": "Please select a Task",
                    "monthly_audit[1][compliance_category]": "Please select a Compliance Category",
                    "monthly_audit[1][reference_doc_no]": "Please enter the Reference Doc. No",
                    "monthly_audit[1][frequency_id]": "Please select the Frequency",
                    "monthly_audit[1][direct_in_direct]": "Please select Direct/In-Direct",
                    "monthly_audit[1][status]": "Please select a Status",
                    "monthly_audit[1][points]": {
                        required: "Please enter Points",
                        number: "Points must be a number",
                        min: "Points cannot be negative"
                    },
                    "monthly_audit[1][remark]": "Please enter a Remark"
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
                        console.log("Field: " + error.element.name + ", Error: " + error
                            .message);
                    });
                }
            });
        });
    </script>
@endpush


