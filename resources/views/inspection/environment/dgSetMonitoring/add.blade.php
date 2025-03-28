@extends('admin.layouts.admin')
@section('title', 'DG Set Stack Emission Monitoring Add')
@section('pageurl', admin_url('environment/dg-set-stack-emission/list'))


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
                                <h4 class="card-title"></h4>
                                <div class="align-back-btc">
                                    <x-button-back
                                        href="{{ admin_url('environment/dg-set-stack-emission/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form">
                                    <form method="POST" id="addambient"
                                        action="{{ admin_url('environment/dg-set-stack-emission/add/submit') }}">
                                        @csrf
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="card-header-inner d-flex justify-content-between">
                                                    <h4 class="text-white"> DG Set Stack Emission Monitoring</h4>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4 form-input">
                                                    <label class="form-label">DG Set Stack Emission No</label>
                                                    <input type="text" class="form-control" name="dg_set_no"
                                                        id="dg_set_no" value = "{{ getsequence('dgsetNo') }}" readonly>
                                                </div>
                                                <div class="col-md-4 form-input">
                                                    <label class="form-label">Doc. No</label>
                                                    <input type="text" class="form-control" name="doc_no" id="doc_no"
                                                        readonly value="{{ $staticDocno->doc_no }}">
                                                </div>

                                                <div class="col-md-4 form-input">
                                                    <label class="form-label">Issue Dt.</label>
                                                    <input type="text" class="form-control" name="issue_date"
                                                        id="issue_date" readonly
                                                        value="{{ Displaydateformat($staticDocno->issue_date) }}">
                                                </div>

                                                <div class="col-md-4 form-input mt-2">
                                                    <label class="form-label">Rev. & Dt.</label>
                                                    <input type="text" class="form-control" name="rev_dt" id="rev_dt"
                                                        readonly value="{{ $staticDocno->rev_dt }}">
                                                </div>
                                            </div>

                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="card-header-inner d-flex justify-content-between">
                                                    <h4 class="text-white"> DG Set Stack Emission Monitoring Details</h4>
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

                                                    <div class="col-md-4 form-input">
                                                        <label class="form-label">SR NO</label>
                                                        <input type="text" class="form-control sr-no"
                                                            name="monitoring[1][sr_no]" id="sr_no_1" readonly
                                                            value="DG-0001">

                                                    </div>

                                                    <div class="col-md-4 form-input">
                                                        <label class="form-label">D.G Set Resource Code</label>
                                                        <input type="text" class="form-control"
                                                            name="monitoring[1][dg_no]" id="dg_no_1">

                                                    </div>
                                                    <div class="col-md-4 form-input">
                                                        <label class="form-label">KVA Rating</label>
                                                        <input type="text" class="form-control"
                                                            name="monitoring[1][kva_rating]" id="kva_rating_1">

                                                    </div>
                                                    <div class="col-md-4 form-input mt-2">
                                                        <label class="form-label">Installation Location</label>
                                                        <input type="text" class="form-control"
                                                            name="monitoring[1][location]" id="location_1">

                                                    </div>
                                                    <div class="col-md-4 form-input mt-2">
                                                        <label class="form-label">Engine Sr. No.</label>
                                                        <input type="text" class="form-control"
                                                            name="monitoring[1][engine_srno]" id="engine_srno_1">

                                                    </div>
                                                    <div class="col-md-4 form-input mt-2">
                                                        <label class="form-label">Date of Monitoring</label>
                                                        <input type="text" name="monitoring[1][date_of_monitoring]"
                                                            class="form-control" id="date_of_monitoring_1">
                                                    </div>
                                                    <div class="col-md-4 form-input mt-2">
                                                        <label class="form-label">Next Due Date of
                                                            Monitoring</label>
                                                        <input type="text"
                                                            name="monitoring[1][next_due_date_of_monitoring]"
                                                            class="form-control" id="next_due_date_of_monitoring_1">
                                                    </div>

                                                    <div class="col-md-4 form-input mt-2">
                                                        <label class="form-label">Remark</label>
                                                        <textarea name="monitoring[1][remark]" class="form-control" id="remark_1"> </textarea>
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
                                            <x-button-reset class=""></x-button-reset>
                                            <x-button-cancel
                                                href="{{ admin_url('environment/dg-set-stack-emission/list') }}"></x-button-cancel>
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
        $(document).ready(function() {
            $('#resetform').on('click', function(e) {
                e.preventDefault();
                location.reload();
            });
        });


        document.addEventListener("DOMContentLoaded", function() {
            function initializeFlatpickr() {
                $("input[id^='date_of_monitoring_']").flatpickr({
                    dateFormat: "d-m-Y",
                    onChange: function(selectedDates, dateStr, instance) {
                        let index = instance.element.id.replace("date_of_monitoring_",
                            ""); // Extract index
                        let $nextDueDate = $("#next_due_date_of_monitoring_" + index);

                        if ($nextDueDate.length) {
                            $nextDueDate.flatpickr({
                                dateFormat: "d-m-Y",
                                minDate: selectedDates[0].fp_incr(
                                    1) // Next Due Date should be after Date of Monitoring
                            });
                        }
                    }
                });

            }

            function updateRowIndexes() {
                $("#lesson_learned_block .lesson_learned_row").each(function(index) {
                    let newIndex = index + 1;
                    let srNoValue = "DG-" + String(newIndex).padStart(4, '0');

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

                    $(this).find(".sr-no").val(srNoValue);
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
                newRow.find("input[name*='[sr_no]']").val("DG-" + String(rowCount + 1).padStart(4,
                    '0'));

                newRow.find(".invalid-feedback").remove();
                newRow.find(".is-invalid").removeClass("is-invalid");
                newRow.find(".select2-container").remove();
                newRow.find(".single-select").select2();

                $("#lesson_learned_block").append(newRow);
                newRow.find("input[name*='[kva_rating]']").rules("add", {
                    number: true,
                    min: 0,
                    messages: {
                        number: "Only numeric values are allowed.",
                        min: "KVA Rating must be a positive number."
                    }
                });

                newRow.find("input[name*='[dg_no]']").rules("add", {
                    pattern: /^[a-zA-Z0-9\-_'"()\s]+$/,
                    messages: {
                        pattern: "Only alphanumeric characters and (-, _, ‘, “, ()) are allowed.",
                    }
                });
                newRow.find("input[name*='[location]']").rules("add", {
                    pattern: /^[a-zA-Z0-9\-_'"()\s]+$/,
                    messages: {
                        pattern: "Only alphanumeric characters and (-, _, ‘, “, ()) are allowed.",
                    }
                });
                newRow.find("input[name*='[engine_srno]']").rules("add", {
                    pattern: /^[a-zA-Z0-9\-_'"()\s]+$/,
                    messages: {
                        pattern: "Only alphanumeric characters and (-, _, ‘, “, ()) are allowed.",
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


        $('#addambient').validate({
            rules: {
                'monitoring[1][kva_rating]': {
                    number: true,
                    min: 0,
                },
                'monitoring[1][dg_no]': {
                    pattern: /^[a-zA-Z0-9\-_'"()\s]+$/,
                },
                'monitoring[1][location]': {
                    pattern: /^[a-zA-Z0-9\-_'"()\s]+$/,
                },
                'monitoring[1][engine_srno]': {
                    pattern: /^[a-zA-Z0-9\-_'"()\s]+$/,
                },
            },
            messages: {
                'monitoring[1][kva_rating]': {
                    number: "Only numeric values are allowed.",
                    min: "KVA Rating must be a positive number."
                },
                'monitoring[1][dg_no]': {
                    pattern: "Only alphanumeric characters and (-, _, ‘, “, ()) are allowed.",
                },
                'monitoring[1][location]': {
                    pattern: "Only alphanumeric characters and (-, _, ‘, “, ()) are allowed.",
                },
                'monitoring[1][engine_srno]': {
                    pattern: "Only alphanumeric characters and (-, _, ‘, “, ()) are allowed.",
                },
            },
            errorElement: 'span',
            errorPlacement: function(error, element) {
                error.addClass('invalid-feedback');
                element.closest('.form-input').append(error);
            },
            highlight: function(element) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function(element) {
                $(element).removeClass('is-invalid');
            }
        });
    </script>
@endpush
