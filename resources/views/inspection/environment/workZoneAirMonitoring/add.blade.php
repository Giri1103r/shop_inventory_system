@extends('admin.layouts.admin')
@section('title', 'Work Zone Air Monitoring Add')
@section('pageurl', admin_url('environment/work-zone/air/list'))


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
                                    <x-button-back href="{{ admin_url('environment/work-zone/air/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form">
                                    <form method="POST" id="addambient"
                                        action="{{ admin_url('environment/work-zone/air/add/submit') }}">
                                        @csrf
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="card-header-inner d-flex justify-content-between">
                                                    <h4 class="text-white">Work Zone Air Monitoring</h4>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4 form-input">
                                                    <label class="form-label">Work Zone Air No</label>
                                                    <input type="text" class="form-control" name="work_zone_air_no"
                                                        id="work_zone_air_no" value = "{{ getsequence('workZoneAirNo') }}"
                                                        readonly>
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
                                                    <h4 class="text-white">Work Zone Air Monitoring Details</h4>
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
                                                            value="WORKZONE-AIR-0001">

                                                    </div>
                                                    <div class="col-md-4 form-input">
                                                        <label for="" class="form-label">Location</label>
                                                        <select class="form-control single-select"
                                                            name="monitoring[1][location_id]" style="width: 100%"
                                                            id="location_id_1">
                                                            <option value="">Select Location</option>
                                                            @foreach ($locationList as $list)
                                                                <option value="{{ encryptId($list->id) }}">
                                                                    {{ $list->location_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-4 form-input">
                                                        <label for="" class="form-label">Unit</label>
                                                        <select class="form-control single-select"
                                                            name="monitoring[1][unit_id]" style="width: 100%"
                                                            id="unit_id_1">
                                                            <option value="">Select Unit</option>

                                                        </select>
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
                                                        <label class="form-label">SPM (Session 1)</label>
                                                        <input type="text" name="monitoring[1][spm]"
                                                            class="form-control" id="spm_1">
                                                    </div>

                                                    <div class="col-md-4 form-input mt-2">
                                                        <label class="form-label">SO2 (Session 1)</label>
                                                        <input type="text" name="monitoring[1][so2]"
                                                            class="form-control" id="so2_1">
                                                    </div>
                                                    <div class="col-md-4 form-input mt-2">
                                                        <label class="form-label">NO2 (Session 1)</label>
                                                        <input type="text" name="monitoring[1][no2]"
                                                            class="form-control" id="no2_1">
                                                    </div>

                                                    <div class="col-md-4 form-input mt-2">
                                                        <label class="form-label">Date of Monitoring</label>
                                                        <input type="text" name="monitoring[1][date_of_monitoring2]"
                                                            class="form-control" id="date_of_monitoring2_1">
                                                    </div>
                                                    <div class="col-md-4 form-input mt-2">
                                                        <label class="form-label">Next Due Date of
                                                            Monitoring</label>
                                                        <input type="text"
                                                            name="monitoring[1][next_due_date_of_monitoring2]"
                                                            class="form-control" id="next_due_date_of_monitoring2_1">
                                                    </div>
                                                    <div class="col-md-4 form-input mt-2">
                                                        <label class="form-label">SPM (Session 2)</label>
                                                        <input type="text" name="monitoring[1][spm_session2]"
                                                            class="form-control" id="spm_session2_1">
                                                    </div>

                                                    <div class="col-md-4 form-input mt-2">
                                                        <label class="form-label">SO2 (Session 2)</label>
                                                        <input type="text" name="monitoring[1][so2_session2]"
                                                            class="form-control" id="so2_session2_1">
                                                    </div>
                                                    <div class="col-md-4 form-input mt-2">
                                                        <label class="form-label">NO2 (Session 2)</label>
                                                        <input type="text" name="monitoring[1][no2_session2]"
                                                            class="form-control" id="no2_session2_1">
                                                    </div>
                                                    <div class="col-md-4 form-input mt-2">
                                                        <label class="form-label">Act/Rule</label>
                                                        <input type="text" name="monitoring[1][act_rule]"
                                                            class="form-control" id="act_rule_1">
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
                                                href="{{ admin_url('environment/work-zone/air/list') }}"></x-button-cancel>
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

                $("input[id^='date_of_monitoring2_']").flatpickr({
                    dateFormat: "d-m-Y",
                    onChange: function(selectedDates, dateStr, instance) {
                        let index = instance.element.id.replace("date_of_monitoring2_",
                        ""); // Extract index
                        let $nextDueDate = $("#next_due_date_of_monitoring2_" + index);

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
                    let srNoValue = "WORKZONE-AIR-" + String(newIndex).padStart(4, '0');

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

            $(document).on("change", "select[id^='location_id_']", function() {
                let locationId = $(this).val();
                let rowId = $(this).attr("id").match(/\d+/)[0];
                let unitSelect = $("#unit_id_" + rowId);

                if (locationId) {
                    $.ajax({
                        url: "{{ admin_url('unit/ajax-list') }}/" + locationId + "/0",
                        type: "GET",
                        dataType: "json",
                        success: function(data) {
                            unitSelect.empty().append('<option value="">Select Unit</option>');
                            $.each(data, function(key, value) {
                                unitSelect.append('<option value="' + value.id + '">' +
                                    value.name + '</option>');
                            });
                        },
                        error: function() {
                            alert("Error fetching unit. Please try again.");
                        },
                    });
                } else {
                    unitSelect.empty().append('<option value="">Select Unit</option>');
                }
            });

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
                newRow.find("input[name*='[sr_no]']").val("WORKZONE-AIR-" + String(rowCount + 1).padStart(4,
                    '0'));

                newRow.find(".invalid-feedback").remove();
                newRow.find(".is-invalid").removeClass("is-invalid");
                newRow.find(".select2-container").remove();
                newRow.find(".single-select").select2();

                $("#lesson_learned_block").append(newRow);
                newRow.find("input[name*='[spm]']").rules("add", {
                    number: true,
                    range: [0, 1000],
                    messages: {
                        number: "Only numeric values are allowed.",
                        range: "Value must be between 0 and 1000 µg/m³."
                    }
                });

                newRow.find("input[name*='[spm_session2]']").rules("add", {
                    number: true,
                    range: [0, 1000],
                    messages: {
                        number: "Only numeric values are allowed.",
                        range: "Value must be between 0 and 1000 µg/m³."
                    }
                });

                newRow.find("input[name*='[so2]']").rules("add", {
                    number: true,
                    range: [0, 500],
                    messages: {
                        number: "Only numeric values are allowed.",
                        range: "Value must be between 0 and 500 µg/m³."
                    }
                });
                newRow.find("input[name*='[so2_session2]']").rules("add", {
                    number: true,
                    range: [0, 500],
                    messages: {
                        number: "Only numeric values are allowed.",
                        range: "Value must be between 0 and 500 µg/m³."
                    }
                });

                newRow.find("input[name*='[no2]']").rules("add", {
                    number: true,
                    range: [0, 500],
                    messages: {
                        number: "Only numeric values are allowed.",
                        range: "Value must be between 0 and 500 µg/m³."
                    }
                });

                newRow.find("input[name*='[no2_session2]']").rules("add", {
                    number: true,
                    range: [0, 500],
                    messages: {
                        number: "Only numeric values are allowed.",
                        range: "Value must be between 0 and 500 µg/m³."
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
                'monitoring[1][spm]': {
                    number: true,
                    range: [0, 1000]
                },
                'monitoring[1][spm_session2]': {
                    number: true,
                    range: [0, 1000]
                },
                'monitoring[1][so2]': {
                    number: true,
                    range: [0, 500]
                },
                'monitoring[1][so2_session2]': {
                    number: true,
                    range: [0, 500]
                },
                'monitoring[1][no2]': {
                    number: true,
                    range: [0, 500]
                },
                'monitoring[1][no2_session2]': {
                    number: true,
                    range: [0, 500]
                },
            },
            messages: {
                'monitoring[1][spm]': {
                    number: "Only numeric values are allowed.",
                    range: "Value must be between 0 and 1000 µg/m³."
                },
                'monitoring[1][spm_session2]': {
                    number: "Only numeric values are allowed.",
                    range: "Value must be between 0 and 1000 µg/m³."
                },
                'monitoring[1][so2]': {
                    number: "Only numeric values are allowed.",
                    range: "Value must be between 0 and 500 µg/m³."
                },
                'monitoring[1][so2_session2]': {
                    number: "Only numeric values are allowed.",
                    range: "Value must be between 0 and 500 µg/m³."
                },
                'monitoring[1][no2]': {
                    number: "Only numeric values are allowed.",
                    range: "Value must be between 0 and 500 µg/m³."
                },
                'monitoring[1][no2_session2]': {
                    number: "Only numeric values are allowed.",
                    range: "Value must be between 0 and 500 µg/m³."
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
