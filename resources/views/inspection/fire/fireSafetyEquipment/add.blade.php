@extends('admin.layouts.admin')
@section('title', 'Fire Safety Equipments Resource Code Sheet')
@section('pageurl', admin_url('fire/fire-safety/equipments/code-sheet/list'))


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
                                        href="{{ admin_url('fire/fire-safety/equipments/code-sheet/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form">
                                    <form method="POST" id="addfire"
                                        action="{{ admin_url('fire/fire-safety/equipments/code-sheet/add/submit') }}">
                                        @csrf
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="card-header-inner d-flex justify-content-between">
                                                    <h4 class="text-white">Fire Safety Equipments</h4>
                                                </div>
                                            </div>
                                            <div class="row">

                                                <div class="col-md-4 form-input">
                                                    <label class="form-label">Fire Safety Equipments No</label>
                                                    <input type="text" class="form-control"
                                                        name="fire_safety_equipment_no" id="fire_safety_equipment_no"
                                                        value = "{{ getsequence('fireSafetyNO') }}" readonly>
                                                </div>
                                                <input type="hidden" class="form-control" name="docNo_id"
                                                    value="{{ encryptId($staticDocno->id) }}">

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
                                                    <h4 class="text-white">Fire Safety Equipments Details</h4>
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
                                                        <input type="text" class="form-control" name="fire[1][sr_no]"
                                                            id="sr_no_1" readonly value="SNO-0001">
                                                    </div>

                                                    <div class="col-md-4 form-input">
                                                        <label class="form-label">Name of Fire & Safety Equipment</label>
                                                        <input type="text" class="form-control"
                                                            name="fire[1][name_of_fire_safety]" id="name_of_fire_safety_1">
                                                    </div>

                                                    <div class="col-md-4 form-input">
                                                        <label class="form-label">Resource Code No</label>
                                                        <input type="text" class="form-control"
                                                            name="fire[1][resource_code]" id="resource_code_1">
                                                    </div>


                                                    <div class="col-md-4 form-input mt-2">
                                                        <label class="form-label">Series Code</label>
                                                        <input type="text" name="fire[1][series_code]"
                                                            class="form-control" id="series_code_1">
                                                    </div>

                                                    <div class="col-md-4 form-input">
                                                        <label for="" class="form-label">Unit</label>
                                                        <select class="form-control single-select" name="fire[1][unit_id]"
                                                            style="width: 100%" id="unit_id_1">
                                                            <option value="">Select Unit</option>
                                                            @foreach ($unitList as $list)
                                                                <option value="{{ encryptId($list->id) }}">
                                                                    {{ $list->unit_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>


                                                    <div class="col-md-4 form-input mt-2">
                                                        <label class="form-label">Allotted Series Code</label>
                                                        <input type="text" name="fire[1][allotted_series_code]"
                                                            class="form-control" id="allotted_series_code_1">
                                                    </div>

                                                    <div class="col-md-4 form-input mt-2">
                                                        <label class="form-label">Total Allotted Code</label>
                                                        <input type="text" name="fire[1][total_allotted_code]"
                                                            class="form-control" id="total_allotted_code_1">
                                                    </div>
                                                    <div class="col-md-4 form-input mt-2">
                                                        <label class="form-label">Remark</label>
                                                        <textarea name="fire[1][remark]" class="form-control" id="remark_1"> </textarea>
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
                                                href="{{ admin_url('fire/fire-safety/equipments/code-sheet/list') }}"></x-button-cancel>
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

            function updateRowIndexes() {
                $("#lesson_learned_block .lesson_learned_row").each(function(index) {
                    let newIndex = index + 1;
                    let srNoValue = "SNO-" + String(newIndex).padStart(4, '0');
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

                newRow.find("input[name$='[name_of_fire_safety]']").rules("add", {
                    required: true,
                    minlength: 3,
                    maxlength: 200,
                    pattern: /^[a-zA-Z0-9\s\-_'"()]*$/,
                    messages: {
                        required: "The name is required",
                        minlength: "The name must be at least 3 characters long.",
                        maxlength: "The name cannot exceed 200 characters.",
                        pattern: "Only letters, numbers, spaces, and special characters (-, _, ', \", ()) are allowed."
                    }
                });

                newRow.find("input[name$='[resource_code]']").rules("add", {
                    required: true,
                    minlength: 3,
                    maxlength: 100,
                    messages: {
                        required: "Resource code is required",
                        minlength: "Resource code must be exactly 3 digits.",
                        maxlength: "Resource code must be exactly 100 digits."
                    }
                });
                newRow.find("input[name$='[series_code]']").rules("add", {
                    required: true,
                    minlength: 3,
                    maxlength: 100,
                    messages: {
                        required: "Series code is required",
                        minlength: "Series code must be exactly 3 digits.",
                        maxlength: "Series code must be exactly 100 digits."
                    }
                });
                newRow.find("input[name$='[allotted_series_code]']").rules("add", {
                    required: true,
                    minlength: 3,
                    maxlength: 100,
                    messages: {
                        required: "Allotted Series code is required",
                        minlength: "Allotted Series code must be exactly 3 digits.",
                        maxlength: "Allotted Series code must be exactly 100 digits."
                    }
                });
                newRow.find("input[name$='[total_allotted_code]']").rules("add", {
                    required: true,
                    minlength: 3,
                    maxlength: 100,
                    messages: {
                        required: "Total Allotted Code is required",
                        minlength: "Total Allotted Code must be exactly 3 digits.",
                        maxlength: "Total Allotted Code must be exactly 100 digits."
                    }
                });
                newRow.find("select[name$='[unit_id]']").rules("add", {
                    required: true,
                    messages: {
                        required: "Total Allotted Code is required",
                    }
                });
                newRow.find("textarea[name$='[remark]']").rules("add", {
                    required: true,
                    messages: {
                        required: "Remark is required",
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


        function addValidationRules(row) {
            newRow.find("input[name$='[name_of_fire_safety]']").rules("add", {
                required: true,
                minlength: 3,
                maxlength: 200,
                pattern: /^[a-zA-Z0-9\s\-_'"()]*$/,
                messages: {
                    required: "The name is required",
                    minlength: "The name must be at least 3 characters long.",
                    maxlength: "The name cannot exceed 200 characters.",
                    pattern: "Only letters, numbers, spaces, and special characters (-, _, ', \", ()) are allowed."
                }
            });

            newRow.find("input[name$='[resource_code]']").rules("add", {
                required: true,
                minlength: 3,
                maxlength: 100,
                messages: {
                    required: "Resource code is required",
                    minlength: "Resource code must be exactly 3 digits.",
                    maxlength: "Resource code must be exactly 100 digits."
                }
            });
            newRow.find("input[name$='[series_code]']").rules("add", {
                required: true,
                minlength: 3,
                maxlength: 100,
                messages: {
                    required: "Series code is required",
                    minlength: "Series code must be exactly 3 digits.",
                    maxlength: "Series code must be exactly 100 digits."
                }
            });
            newRow.find("input[name$='[allotted_series_code]']").rules("add", {
                required: true,
                minlength: 3,
                maxlength: 100,
                messages: {
                    required: "Allotted Series code is required",
                    minlength: "Allotted Series code must be exactly 3 digits.",
                    maxlength: "Allotted Series code must be exactly 100 digits."
                }
            });
            newRow.find("input[name$='[total_allotted_code]']").rules("add", {
                required: true,
                minlength: 3,
                maxlength: 100,
                messages: {
                    required: "Total Allotted Code is required",
                    minlength: "Total Allotted Code must be exactly 3 digits.",
                    maxlength: "Total Allotted Code must be exactly 100 digits."
                }
            });
            newRow.find("select[name$='[unit_id]']").rules("add", {
                required: true,
                messages: {
                    required: "Total Allotted Code is required",
                }
            });
            newRow.find("textarea[name$='[remark]']").rules("add", {
                required: true,
                messages: {
                    required: "Remark is required",
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

            $('#addfire').validate({
                rules: {
                'fire[1][name_of_fire_safety]': {
                    required: true,
                    minlength: 3,
                    maxlength: 200,
                    pattern: /^[a-zA-Z0-9\s\-_'"()]*$/
                },
                'fire[1][resource_code]': {
                    required: true,
                    minlength: 3,
                    maxlength: 100
                },
                'fire[1][series_code]': {
                    required: true,
                    minlength: 3,
                    maxlength: 100
                },
                'fire[1][allotted_series_code]': {
                    required: true,
                    minlength: 3,
                    maxlength: 100
                },
                'fire[1][total_allotted_code]': {
                    required: true,
                    minlength: 3,
                    maxlength: 100
                },
                'fire[1][remark]': {
                    required: true,
                },
                'fire[1][unit_id]': {
                    required: true,
                },
            },
            messages: {
                'fire[1][name_of_fire_safety]': {
                    required: "The name is required",
                    minlength: "The name must be at least 3 characters long.",
                    maxlength: "The name cannot exceed 200 characters.",
                    pattern: "Only letters, numbers, spaces, and special characters (-, _, ', \", ()) are allowed."
                },
                'fire[1][resource_code]': {
                    required: "Resource code is required",
                    minlength: "Resource code must be exactly 3 digits.",
                    maxlength: "Resource code must be exactly 100 digits."
                },
                'fire[1][series_code]': {
                    required: "Series code is required",
                    minlength: "Series code must be at least 3 characters long.",
                    maxlength: "Series code cannot exceed 100 characters."
                },
                'fire[1][allotted_series_code]': {
                    required: "Allotted series code is required",
                    minlength: "Allotted series code must be at least 3 characters long.",
                    maxlength: "Allotted series code cannot exceed 100 characters."
                },
                'fire[1][total_allotted_code]': {
                    required: "Total allotted code is required",
                    minlength: "Total allotted code must be at least 3 characters long.",
                    maxlength: "Total allotted code cannot exceed 100 characters."
                },
                'fire[1][remark]': {
                    required: "Remark is required",
                },
                'fire[1][unit_id]': {
                    required: "Unit is required",
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
