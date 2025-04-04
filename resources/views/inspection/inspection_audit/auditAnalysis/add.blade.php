@extends('admin.layouts.admin')
@section('title', '6S Audit Analysis Add')
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
            {{-- <h4 class="text-black">{{ __('MSDS Add') }}</h4> --}}
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
                                                <h4 class="text-white">Audit Analysis</h4>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Audit Analysis ID </label>
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

                                        <div class="row mt-4">
                                            <div id="form-wrapper">
                                                <div class="form-set mb-3">
                                                    <div class="card-header-inner">
                                                        <h4 class="text-white">Audit Analysis CheckList</h4>
                                                    </div>
                                                    <div class="d-flex justify-content-end">
                                                        <button class="btn btn-primary add-row me-3" type="button"
                                                            id="add-row" style="width: 84px;">Add</button>
                                                        <button type="button" class="btn btn-danger remove-row">
                                                            <i class="fa-solid fa-trash"></i> Remove
                                                        </button>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="form-group form-input">
                                                                <label class="form-label require">Serial Number</label>
                                                                <input type="text" name="audit[1][serial_number]" id="serial_number_1"
                                                                    class="form-control" value="AUDIT-0001" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group form-input">
                                                                <label class="form-label require">Department Name</label>
                                                                <select name="audit[1][department_id]" id="department_id_1"
                                                                    class="form-control single-select" style="width: 100%">
                                                                    <option value="">Select Department</option>
                                                                    @foreach ($departmentList as $department)
                                                                        <option value="{{ $department->id }}">
                                                                            {{ $department->department_name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group form-input">
                                                                <label class="form-label require">Unit Name</label>
                                                                <select name="audit[1][unit_id]"  id="unit_id_1" class="form-control single-select"
                                                                    style="width: 100%">
                                                                    <option value="">Select Unit</option>
                                                                    @foreach ($unitList as $unit)
                                                                        <option value="{{ $unit->id }}">
                                                                            {{ $unit->unit_name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group form-input">
                                                                <label class="form-label require">Year</label>
                                                                <input type="text" id="year_1" name="audit[1][year]"
                                                                    class="form-control" placeholder="Year"
                                                                    value="">
                                                            </div>
                                                        </div>

                                                        <div class="col-md-4">
                                                            <div class="form-group form-input">
                                                                <label class="form-label require">Month</label>
                                                                <input type="text" id="month_1" name="audit[1][month]"
                                                                    class="form-control" placeholder="month"
                                                                    value="">
                                                            </div>
                                                        </div>

                                                        <div class="col-md-4">
                                                            <div class="form-group form-input">
                                                                <label class="form-label require">Mark</label>
                                                                <input type="text" name="audit[1][mark]" id="mark_1"
                                                                    class="form-control" placeholder="mark"
                                                                    value="">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row mt-3">
                                                        <div class="col-md-3">
                                                            <div class="form-group form-input">
                                                                <label class="form-label require">Total No's of
                                                                    Audit</label>
                                                                <input type="text" name="audit[1][no_of_audit]" id="no_of_audit_1"
                                                                    class="form-control">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group form-input">
                                                                <label class="form-label require">Total Marks</label>
                                                                <input type="text" name="audit[1][total_marks]" id="total_marks_1"
                                                                    class="form-control">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group form-input">
                                                                <label class="form-label require">Total Marks
                                                                    Obtained</label>
                                                                <input type="text" name="audit[1][marks_obtained]" id="marks_obtained_1"
                                                                    class="form-control">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group form-input">
                                                                <label class="form-label require">%</label>
                                                                <input type="text" name="audit[1][percentage]" id="percentage_1"
                                                                    class="form-control">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                        <hr>
                                        <div class="submit-button" style="text-align: right;">

                                            <x-button-submit class="submit"></x-button-submit>
                                            <x-button-reset class="submit"></x-button-reset>
                                            <x-button-cancel href="{{ admin_url('msds/list') }}"></x-button-cancel>
                                        </div>

                                    </form>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </form>
    </div>
@stop


@push('script')
    <script type="text/javascript" nonce="projectcab">
        $(document).ready(function() {
            function checkSelections() {
                var departmentId = $("input[id^='department_id_']").val();
                var unitId = $("input[id^='unit_id_']").val();
                var year = $("input[id^='year_']").val();
                var month = $("input[id^='month_']").val();

                if (month && year && unitId && departmentId) {
                    $.ajax({
                        url: "{{ url('audit/6s-analysis/ajax-list') }}",
                        type: 'GET',
                        dataType: 'json',
                        data: {
                            departmentId: departmentId,
                            unitId: unitId,
                            year: year,
                            month: month,
                        },
                        success: function(response) {
                            $('.text-danger').remove();
                            var conflicts = response.conflicts;

                            if (Object.keys(conflicts).length > 0) {
                                for (var key in conflicts) {
                                    if (conflicts.hasOwnProperty(key)) {
                                        $('#' + key).closest('.form-group').append(
                                            '<div><span class="text-danger">' + conflicts[key] +
                                            '</span></div>'
                                        );
                                    }
                                }
                                $('#auditAnalysisAdd').data('conflict', true);
                            } else {
                                $('#auditAnalysisAdd').data('conflict', false);
                            }
                        },
                        error: function(xhr) {
                            alert('Error fetching data. Please try again.');
                        }
                    });
                } else {
                    $('.text-danger').remove();
                    $('#auditAnalysisAdd').data('conflict', false);
                }
            }

            $("input[id^='department_id_']").on('change', checkSelections);
            $("input[id^='unit_id_']").on('change', checkSelections);
            $("input[id^='year_']").on('change', checkSelections);
            $("input[id^='month_']").on('change', checkSelections);

            $('#resetform').on('click', function(e) {
                e.preventDefault();
                location.reload();
            });

            var fromDatepicker = flatpickr("#issue_date", {
                dateFormat: "d-m-Y",
                minDate: new Date(),
            });
            $('#year').datepicker({
                format: 'yyyy',
                minViewMode: 'years',
                viewMode: 'years',
                autoclose: true
            });

            $('#month').datepicker({
                format: 'M',
                minViewMode: 'months',
                viewMode: 'months',
                autoclose: true
            });


            $.validator.addMethod("noSpaces", function(value, element) {
                return this.optional(element) || value.trim().length > 0;
            }, "This field cannot contain only spaces");

            $('#auditAnalysisAdd').validate({
                rules: {
                    audit_analysis: {
                        required: true,
                    },
                    document_number: {
                        required: true,
                        noSpaces: true,
                    },
                    issue_date: {
                        required: true,
                    },
                    revision_date: {
                        required: true,
                    },
                    year: {
                        required: true,
                    },
                    month: {
                        required: true,
                    },
                    mark: {
                        required: true,
                    },
                    'department_id[1]': {
                        required: true,
                    },
                    'unit_id[1]': {
                        required: true,
                    },
                    'no_of_audit[1]': {
                        required: true,
                    },
                    'total_marks[1]': {
                        required: true,
                    },
                    'marks_obtained[1]': {
                        required: true,
                    },
                    'percentage[1]': {
                        required: true,
                    },
                },
                messages: {
                    audit_analysis: {
                        required: "6S Audit Analysis Report is Required",
                    },
                    document_number: {
                        required: "Document Number is Required",
                    },
                    issue_date: {
                        required: "Issue Date is Required",
                    },
                    revision_date: {
                        required: "Revision Date is Required",
                    },
                    year: {
                        required: "Year is Required",
                    },
                    month: {
                        required: "Month is Required",
                    },
                    mark: {
                        required: "Mark is Required",
                    },
                    'department_id[1]': {
                        required: "Department Name is Required",
                    },
                    'unit_id[1]': {
                        required: "Unit Name is Required",
                    },
                    'no_of_audit[1]': {
                        required: "Total No's of Audit is Required",
                    },
                    'total_marks[1]': {
                        required: "Total Marks is Required",
                    },
                    'marks_obtained[1]': {
                        required: "Total Marks Obtained is Required",
                    },
                    'percentage[1]': {
                        required: "Percentage is Required",
                    },
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
                    form.submit();
                },
                invalidHandler: function(event, validator) {
                    var errors = validator.numberOfInvalids();
                    validator.errorList.forEach(function(error) {});
                }
            });



            let form_set_count = 2;
            const maxFormSets = 200;
            const minFormSets = 1;
            $(document).on('click', ".add-row", function() {
                let currentFormSets = $('#form-wrapper .form-set').length;
                let maxFormSets = 200;

                if (currentFormSets >= maxFormSets) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Maximum Checklist Reached',
                        text: 'You can only add up to 200 Audit CheckLists.',
                        confirmButtonColor: '#3085d6'
                    });
                    return;
                }

                let form_set_count = currentFormSets + 1;
                let newSerialNumber = ('00000' + form_set_count).slice(-5);

                let newFormSet = `
                    <div class="form-set mb-3">
                        <div class="card-header-inner">
                            <h4 class="text-white">Audit Analysis CheckList</h4>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button class="btn btn-primary add-row me-3" type="button" style="width: 84px;">Add</button>
                            <button type="button" class="btn btn-danger remove-row">
                                <i class="fa-solid fa-trash"></i> Remove
                            </button>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group form-input">
                                    <label class="form-label require">Serial Number</label>
                                    <input type="text" name="audit[${form_set_count}][serial_number]" id="serial_number_${form_set_count}" class="form-control" value="${newSerialNumber}" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group form-input">
                                    <label class="form-label require">Department Name</label>
                                    <select name="audit[${form_set_count}][department_id]" id="department_id_${form_set_count}" class="form-control single-select" style="width: 100%">
                                        <option value="">Select Department</option>
                                        @foreach ($departmentList as $department)
                                            <option value="{{ $department->id }}">{{ $department->department_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group form-input">
                                    <label class="form-label require">Unit Name</label>
                                    <select name="audit[${form_set_count}][unit_id]" id="unit_id_${form_set_count}" class="form-control single-select">
                                        <option value="">Select Unit</option>
                                        @foreach ($unitList as $unit)
                                            <option value="{{ $unit->id }}">{{ $unit->unit_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group form-input">
                                    <label class="form-label require">Year</label>
                                    <input type="text" name="audit[${form_set_count}][year]" class="form-control year-picker" id="year_${form_set_count}" placeholder="Year">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group form-input">
                                    <label class="form-label require">Month</label>
                                    <input type="text" name="audit[${form_set_count}][month]" class="form-control month-picker" id="month_${form_set_count}" placeholder="Month">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group form-input">
                                    <label class="form-label require">Mark</label>
                                    <input type="text" name="audit[${form_set_count}][mark]" id="mark_${form_set_count}" class="form-control" placeholder="Mark">
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-3">
                                <div class="form-group form-input">
                                    <label class="form-label require">Total No's of Audit</label>
                                    <input type="text" name="audit[${form_set_count}][no_of_audit]" id="no_of_audit_${form_set_count}" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group form-input">
                                    <label class="form-label require">Total Marks</label>
                                    <input type="text" name="audit[${form_set_count}][total_marks]" id="total_marks_${form_set_count}" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group form-input">
                                    <label class="form-label require">Total Marks Obtained</label>
                                    <input type="text" name="audit[${form_set_count}][marks_obtained]" id="marks_obtained_${form_set_count}"  class="form-control">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group form-input">
                                    <label class="form-label require">%</label>
                                    <input type="text" name="audit[${form_set_count}][percentage]"  id="percentage_${form_set_count}" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>`;


                $('#form-wrapper').append(newFormSet);

                $('#form-wrapper .form-set:last .single-select').select2({
                    width: '100%'
                });

                $('.year-picker').datepicker({
                    format: 'yyyy',
                    minViewMode: 'years',
                    viewMode: 'years',
                    autoclose: true
                });

                $('.month-picker').datepicker({
                    format: 'M',
                    minViewMode: 'months',
                    viewMode: 'months',
                    autoclose: true
                });

            });

            $(document).on('click', '.remove-row', function() {
                if ($('#form-wrapper .form-set').length > 1) {
                    $(this).closest('.form-set').remove();
                    updatePageIndices();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Action Not Allowed',
                        text: 'At least one Audit Checklist must remain.',
                        confirmButtonColor: '#d33'
                    });
                }
            });

            // Function to Update Input Indices
            function updatePageIndices() {
                $('#form-wrapper .form-set').each(function(index) {
                    $(this).find(':input').each(function() {
                        let name = $(this).attr('name');
                        if (name) {
                            name = name.replace(/\[\d+\]/, `[${index + 1}]`);
                            $(this).attr('name', name);
                        }
                    });
                });
            }


            $(".submit").on('click', function() {
                if ($("#auditAnalysisAdd").valid()) {
                    $("#auditAnalysisAdd").submit();
                } else {
                    return false;
                }
            });
        });
    </script>
@endpush
