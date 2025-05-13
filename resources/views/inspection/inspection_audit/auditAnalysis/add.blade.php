@extends('admin.layouts.admin')
@section('title', '6S Audit Analysis')
@section('pageurl', admin_url('audit/6s-analysis/list'))

@section('content')

    <style>
        .card-header-inner {
            padding: 11px;
        }
    </style>

    <div class="clearfix"></div>
    <div class="page-titles">
        <div class="d-flex align-items-center">
            {{-- <h4 class="text-black">{{ __('6S Audit Analysis Add') }}</h4> --}}
        </div>

    </div>

    <div class="content-body  default-height">
        <div class="container-fluid main-content">
            <div class="row">
                <div class="col-12">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title"></h4>
                                <div class="align-back-btc">
                                    <x-button-back href="{{ admin_url('audit/6s-analysis/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form">
                                    <form method="POST" id="auditAnalysisAdd"
                                        action="{{ admin_url('audit/6s-analysis/add/submit') }}">
                                        @csrf

                                        <div class="row">
                                            <div class="card-header-inner">
                                                <h4 class="text-white">6S Audit Analysis</h4>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">6S Audit Analysis ID </label>
                                                    <input type="text" name="audit_analysis_id" class="form-control"
                                                        value="{{ getsequence('audit_analysis') }}" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">6S Audit Analysis Report </label>
                                                    <input type="text" name ="audit_analysis" class="form-control"
                                                        placeholder="6S Audit Analysis Report" value="">
                                                </div>
                                            </div>
                                            <input type="hidden" class="form-control" name="docNo_id"
                                                value="{{ encryptId($staticDocno->id) }}">
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Doc. No</label>
                                                    <input type="text" name ="doc_no" class="form-control"
                                                        value="{{ $staticDocno->doc_no }}" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Issue Dt.</label>
                                                    <input type="text" name ="issue_date" id="issue_date"
                                                        class="form-control"
                                                        value="{{ Displaydateformat($staticDocno->issue_date) }}" readonly>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Rev. & Dt.</label>
                                                    <input type="text" name ="rev_dt" class="form-control"
                                                        value="{{ $staticDocno->rev_dt }}" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="card-header-inner d-flex justify-content-between">
                                                    <h4 class="text-white">6S Audit Analysis CheckList</h4>
                                                    <button class="btn btn-primary addmorebutton"
                                                        data-block='lesson_learned_block' data-row='lesson_learned_row'
                                                        type="button" id="dynamic-add-more"
                                                        style="margin-left: 10px; width: 84px;">
                                                        Add
                                                    </button>
                                                </div>
                                            </div>
                                            <div id="lesson_learned_block">
                                                <div class="row lesson_learned_row" style="margin-top: 20px;">

                                                    <div class="col-md-4 mt-2">
                                                        <div class="form-group form-input">
                                                            <label class="form-label require">SR NO</label>
                                                            <input type="text" name="audit[1][serial_number]"
                                                                id="serial_number_1" class="form-control sr-no"
                                                                value="SN-0001" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 mt-2">
                                                        <div class="form-group form-input">
                                                            <label class="form-label require">Department Name</label>
                                                            <select name="audit[1][department_id]" id="department_id_1"
                                                                class="form-control single-select" style="width: 100%">
                                                                <option value="">Select Department</option>
                                                                @foreach ($departmentList as $department)
                                                                    <option value="{{ encryptId($department->id) }}">
                                                                        {{ $department->department_name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 mt-2">
                                                        <div class="form-group form-input">
                                                            <label class="form-label require">Unit Name</label>
                                                            <select name="audit[1][unit_id]" id="unit_id_1"
                                                                class="form-control single-select" style="width: 100%">
                                                                <option value="">Select Unit</option>
                                                                @foreach ($unitList as $unit)
                                                                    <option value="{{ encryptId($unit->id) }}">
                                                                        {{ $unit->unit_name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 mt-2">
                                                        <div class="form-group form-input">
                                                            <label class="form-label require">Year</label>
                                                            <input type="text" id="year_1" name="audit[1][year]"
                                                                class="form-control" placeholder="Year" value="">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4 mt-2">
                                                        <div class="form-group form-input">
                                                            <label class="form-label require">Month</label>
                                                            <input type="text" id="month_1" name="audit[1][month]"
                                                                class="form-control" placeholder="month" value="">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4 mt-2">
                                                        <div class="form-group form-input">
                                                            <label class="form-label require">Mark</label>
                                                            <input type="text" name="audit[1][mark]" id="mark_1"
                                                                class="form-control" placeholder="mark" value="">
                                                        </div>
                                                    </div>

                                                    <div class="row mt-3">
                                                        <div class="col-md-3">
                                                            <div class="form-group form-input">
                                                                <label class="form-label require">Total No's of
                                                                    Audit</label>
                                                                <input type="text" name="audit[1][no_of_audit]"
                                                                    id="no_of_audit_1" class="form-control">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group form-input">
                                                                <label class="form-label require">Total Marks</label>
                                                                <input type="text" name="audit[1][total_marks]"
                                                                    id="total_marks_1" class="form-control">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group form-input">
                                                                <label class="form-label require">Total Marks
                                                                    Obtained</label>
                                                                <input type="text" name="audit[1][marks_obtained]"
                                                                    id="marks_obtained_1" class="form-control">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group form-input">
                                                                <label class="form-label require"> Percentage %</label>
                                                                <input type="text" name="audit[1][percentage]"
                                                                    id="percentage_1" readonly class="form-control">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2 text-right mt-2">
                                                        <button class="btn btn-danger removerowdata" type="button"
                                                            style="margin:10px;"><i class="fa fa-trash"></i></button>

                                                    </div>

                                                    <hr>
                                                </div>
                                            </div>

                                        </div>

                                        <div class="submit-button" style="text-align: right;">

                                            <x-button-submit class="submit"></x-button-submit>
                                            <x-button-reset class="submit"></x-button-reset>
                                            <x-button-cancel
                                                href="{{ admin_url('audit/6s-analysis/list') }}"></x-button-cancel>
                                        </div>

                                    </form>
                                </div>

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
        document.addEventListener("DOMContentLoaded", function() {

            function initializeFlatpickr() {
                $("input[id^='year_']").datepicker({
                    format: 'yyyy',
                    minViewMode: 'years',
                    viewMode: 'years',
                    autoclose: true
                });

                $("input[id^='month_']").datepicker({
                    format: 'M',
                    minViewMode: 'months',
                    viewMode: 'months',
                    autoclose: true
                });
            }

            function updateRowIndexes() {
                $("#lesson_learned_block .lesson_learned_row").each(function(index) {
                    const newIndex = index + 1;
                    $(this).find("input, select, textarea, button").each(function() {
                        let name = $(this).attr("name");
                        let id = $(this).attr("id");

                        if (name) {
                            $(this).attr("name", name.replace(/\[\d+\]/, "[" + newIndex + "]"));
                        }
                        if (id) {
                            $(this).attr("id", id.replace(/_\d+$/, "_" + newIndex));
                        }
                    });
                    $(this).find(".sr-no").val("SN-" + String(newIndex).padStart(4, '0'));
                });

                initializeFlatpickr();
                $('.single-select').select2();
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

                newRow.find("input[name*='[serial_number]']").val("SN-" + String(newRowNumber).padStart(4,
                    '0'));

                newRow.find(".invalid-feedback").remove();
                newRow.find(".is-invalid").removeClass("is-invalid");

                newRow.find(".single-select").select2();

                $("#lesson_learned_block").append(newRow);

                newRow.find("input[name$='[spm]']").rules("add", {
                    number: true,
                    range: [0, 1000],
                    messages: {
                        number: "Only numeric values are allowed.",
                        range: "Value must be between 0 and 1000 µg/m³."
                    }
                });
                newRow.find("select[name$='[department_id]']").rules("add", {
                    required: true,
                    messages: {
                        required: "Please select a department."
                    }
                });
                newRow.find("select[name$='[unit_id]']").rules("add", {
                    required: true,
                    messages: {
                        required: "Please select a unit."
                    }
                });
                newRow.find("input[name$='[year]']").rules("add", {
                    required: true,
                    number: true,
                    messages: {
                        required: "Please enter the year.",
                        number: "Only numbers are allowed."
                    }
                });
                newRow.find("input[name$='[month]']").rules("add", {
                    required: true,
                    messages: {
                        required: "Please select a month."
                    }
                });
                newRow.find("input[name$='[mark]']").rules("add", {
                    required: true,
                    number: true,
                    messages: {
                        required: "Please enter the mark.",
                        number: "Only numbers are allowed."
                    }
                });
                newRow.find("input[name$='[no_of_audit]']").rules("add", {
                    required: true,
                    number: true,
                    messages: {
                        required: "Please enter the number of audits.",
                        number: "Only numbers are allowed."
                    }
                });
                newRow.find("input[name$='[total_marks]']").rules("add", {
                    required: true,
                    number: true,
                    messages: {
                        required: "Please enter the total marks.",
                        number: "Only numbers are allowed."
                    }
                });
                newRow.find("input[name$='[marks_obtained]']").rules("add", {
                    required: true,
                    number: true,
                    messages: {
                        required: "Please enter the marks obtained.",
                        number: "Only numbers are allowed."
                    }
                });
                newRow.find("input[name$='[percentage]']").rules("add", {
                    required: true,
                    number: true,
                    messages: {
                        required: "Please enter the percentage.",
                        number: "Only numbers are allowed."
                    }
                });

                initializeFlatpickr();
            });

            function addValidationRules(row) {
                row.find("select[name$='[department_id]']").rules("add", {
                    required: true,
                    messages: {
                        required: "Please select a department."
                    }
                });
                row.find("select[name$='[unit_id]']").rules("add", {
                    required: true,
                    messages: {
                        required: "Please select a unit."
                    }
                });
                row.find("input[name$='[year]']").rules("add", {
                    required: true,
                    number: true,
                    messages: {
                        required: "Please enter the year.",
                        number: "Only numbers are allowed."
                    }
                });
                row.find("input[name$='[month]']").rules("add", {
                    required: true,
                    messages: {
                        required: "Please select a month."
                    }
                });
                row.find("input[name$='[mark]']").rules("add", {
                    required: true,
                    number: true,
                    messages: {
                        required: "Please enter the mark.",
                        number: "Only numbers are allowed."
                    }
                });
                row.find("input[name$='[no_of_audit]']").rules("add", {
                    required: true,
                    number: true,
                    messages: {
                        required: "Please enter the number of audits.",
                        number: "Only numbers are allowed."
                    }
                });
                row.find("input[name$='[total_marks]']").rules("add", {
                    required: true,
                    number: true,
                    messages: {
                        required: "Please enter the total marks.",
                        number: "Only numbers are allowed."
                    }
                });
                row.find("input[name$='[marks_obtained]']").rules("add", {
                    required: true,
                    number: true,
                    messages: {
                        required: "Please enter the marks obtained.",
                        number: "Only numbers are allowed."
                    }
                });
                row.find("input[name$='[percentage]']").rules("add", {
                    required: true,
                    number: true,
                    messages: {
                        required: "Please enter the percentage.",
                        number: "Only numbers are allowed."
                    }
                });
            }

            function calculatePercentage(row) {
                const totalMarks = parseFloat(row.find("input[name$='[total_marks]']").val());
                const marksObtained = parseFloat(row.find("input[name$='[marks_obtained]']").val());
                let percentage = 0;

                if (!isNaN(totalMarks) && !isNaN(marksObtained) && totalMarks > 0) {
                    percentage = (marksObtained / totalMarks) * 100;
                    row.find("input[name$='[percentage]']").val(percentage.toFixed(2));
                } else {
                    row.find("input[name$='[percentage]']").val('');
                }
            }

            $(document).on("input", "input[name$='[total_marks]'], input[name$='[marks_obtained]']", function() {
                const row = $(this).closest(".lesson_learned_row");
                calculatePercentage(row);
            });

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

                $('#auditAnalysisAdd').validate({
                    rules: {
                        audit_analysis: {
                            required: true
                        },
                    },
                    messages: {
                        audit_analysis: {
                            required: "Audit Analysis Report is required"
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

            $(document).on("click", ".removerowdata", function() {
                if ($("#lesson_learned_block .lesson_learned_row").length > 1) {
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

            $('#resetform').on('click', function(e) {
                e.preventDefault();
                location.reload();
            });

            initializeFlatpickr();
        });
    </script>
@endpush
