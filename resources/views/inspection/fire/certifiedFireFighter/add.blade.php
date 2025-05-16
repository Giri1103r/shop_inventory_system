@extends('admin.layouts.admin')
@section('title', 'Certified Fire Fighter')
@section('pageurl', admin_url('fire/certified-fire-fighter/list'))


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
                                        href="{{ admin_url('fire/certified-fire-fighter/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form">
                                    <form method="POST" id="addfire"
                                        action="{{ admin_url('fire/certified-fire-fighter/add/submit') }}">
                                        @csrf
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="card-header-inner d-flex justify-content-between">
                                                    <h4 class="text-white">Certified Fire Fighter</h4>
                                                </div>
                                            </div>
                                            <div class="row">

                                                <div class="col-md-4 form-input">
                                                    <label class="form-label">Certified Fire Fighter No</label>
                                                    <input type="text" class="form-control"
                                                        name="certified_fire_fighter_no" id="certified_fire_fighter_no"
                                                        value = "{{ getsequence('certifiedFireFighterNo') }}" readonly>
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

                                                    <div class="input-group date form-input custom-height">
                                                        <input type="text" class="form-control" name="issue_date"
                                                            id="issue_date" readonly
                                                            value="{{ Displaydateformat($staticDocno->issue_date) }}">
                                                        <div class="input-group-addon input-group-text">
                                                            <span class="fa fa-calendar"></span>
                                                        </div>
                                                    </div>
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
                                                    <h4 class="text-white">Certified Fire Fighter Details</h4>
                                                    <button class="btn btn-primary addmorebutton mb-2"
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
                                                        <label class="form-label require">SR NO</label>
                                                        <input type="text" class="form-control" name="fire[1][sr_no]"
                                                            id="sr_no_1" readonly value="SNO-0001">
                                                    </div>

                                                    <div class="col-md-4 form-input">
                                                        <label class="form-label require">Name</label>
                                                        <input type="text" class="form-control" name="fire[1][emp_name]"
                                                            id="emp_name_1">
                                                    </div>

                                                    <div class="col-md-4 form-input">
                                                        <label for="" class="form-label require">Department</label>
                                                        <select class="form-control single-select"
                                                            name="fire[1][department_id]" style="width: 100%"
                                                            id="department_id_1">
                                                            <option value="">Select Department</option>
                                                            @foreach ($departmentList as $list)
                                                                <option value="{{ encryptId($list->id) }}">
                                                                    {{ $list->department_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="col-md-4 form-input mt-2">
                                                        <label class="form-label require">Emp Code</label>
                                                        <input type="text" name="fire[1][emp_code]" class="form-control"
                                                            id="emp_code_1">
                                                    </div>

                                                    <div class="col-md-4 form-input mt-2">
                                                        <label class="form-label require">Contact Number</label>
                                                        <input type="text" name="fire[1][emp_phone]"
                                                            class="form-control" id="emp_phone_1">
                                                    </div>
                                                    <div class="col-md-4 form-input mt-2">
                                                        <label class="form-label require">Status</label>
                                                        <select class="form-control single-select"
                                                            name="fire[1][emp_status]" style="width: 100%"
                                                            id="emp_status_1">
                                                            <option value="">Select Status </option>
                                                            <option value="{{ encryptId(1) }}">Active</option>
                                                            <option value="{{ encryptId(2) }}">Not Active</option>
                                                        </select>
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
                                                href="{{ admin_url('fire/certified-fire-fighter/list') }}"></x-button-cancel>
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
                newRow.find("input[name$='[sr_no]']").val("SNO-" + String(rowCount + 1).padStart(4,
                    '0'));

                newRow.find(".invalid-feedback").remove();
                newRow.find(".is-invalid").removeClass("is-invalid");

                newRow.find(".single-select").select2();

                $("#lesson_learned_block").append(newRow);

                newRow.find("input[name$='[emp_name]']").each(function() {
                    $(this).rules("add", {
                        required: true,
                        pattern: /^[a-zA-Z0-9\s\-_'"()]*$/,
                        messages: {
                            required: "Emp Name is required",
                            pattern: "Only alphanumeric characters and -, _, ', \", () are allowed.",
                        }
                    });
                });

                newRow.find("input[name$='[emp_phone]']").each(function() {
                    $(this).rules("add", {
                        required: true,
                        pattern: /^[0-9]{10}$/,
                        messages: {
                            required: "Phone number is required",
                            pattern: "Phone number must be exactly 10 digits (only numbers)."
                        }
                    });
                });

                newRow.find("input[name$='[emp_code]']").each(function() {
                    $(this).rules("add", {
                        required: true,
                        pattern: /^[a-zA-Z0-9\s\-_'"()]*$/,
                        messages: {
                            required: "Emp Code is required",
                            pattern: "Only alphanumeric characters and -, _, ', \", () are allowed.",
                        }
                    });
                });

                newRow.find("select[name$='[emp_status]']").each(function() {
                    $(this).rules("add", {
                        required: true,
                        messages: {
                            required: "Emp Status is required",
                        }
                    });
                });

                newRow.find("select[name$='[department_id]']").each(function() {
                    $(this).rules("add", {
                        required: true,
                        messages: {
                            required: "Department is required",
                        }
                    });
                });

                $('#addfire').validate();

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
            newRow.find("input[name$='[emp_name]']").each(function() {
                $(this).rules("add", {
                    required: true,
                    pattern: /^[a-zA-Z0-9\s\-_'"()]*$/,
                    messages: {
                        required: "Emp Name is required",
                        pattern: "Only alphanumeric characters and -, _, ', \", () are allowed.",
                    }
                });
            });

            newRow.find("input[name$='[emp_phone]']").each(function() {
                $(this).rules("add", {
                    required: true,
                    pattern: /^[0-9]{10}$/,
                    messages: {
                        required: "Phone number is required",
                        pattern: "Phone number must be exactly 10 digits (only numbers)."
                    }
                });
            });

            newRow.find("input[name$='[emp_code]']").each(function() {
                $(this).rules("add", {
                    required: true,
                    pattern: /^[a-zA-Z0-9\s\-_'"()]*$/,
                    messages: {
                        required: "Emp Code is required",
                        pattern: "Only alphanumeric characters and -, _, ', \", () are allowed.",
                    }
                });
            });

            newRow.find("select[name$='[emp_status]']").each(function() {
                $(this).rules("add", {
                    required: true,
                    messages: {
                        required: "Emp Status is required",
                    }
                });
            });

            newRow.find("select[name$='[department_id]']").each(function() {
                $(this).rules("add", {
                    required: true,
                    messages: {
                        required: "Department is required",
                    }
                });
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
                    'fire[1][emp_name]': {
                        required: true,
                        pattern: /^[a-zA-Z0-9\s\-_'"()]*$/,
                    },
                    'fire[1][emp_code]': {
                        required: true,
                        pattern: /^[a-zA-Z0-9\s\-_'"()]*$/,
                    },
                    'fire[1][emp_phone]': {
                        required: true,
                        number: true,
                        pattern: /^[0-9]{10}$/,
                    },
                    'fire[1][department_id]': {
                        required: true,
                    },
                    'fire[1][emp_status]': {
                        required: true,
                    },
                },
                messages: {
                    'fire[1][emp_name]': {
                        required: "Emp Name is required",
                        pattern: "Only alphanumeric characters and -, _, ', \", () are allowed",

                    },
                    'fire[1][emp_code]': {
                        required: "Emp Code is required",
                        pattern: "Only alphanumeric characters and -, _, ', \", () are allowed",

                    },
                    'fire[1][emp_phone]': {
                        required: "Contact Number is required",
                        number: "Only numeric values are allowed.",
                        pattern: "Phone number must be exactly 10 digits (only numbers)."
                    },
                    'fire[1][department_id]': {
                        required: "Department is required",
                    },
                    'fire[1][emp_status]': {
                        required: "Emp Status is required",
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
