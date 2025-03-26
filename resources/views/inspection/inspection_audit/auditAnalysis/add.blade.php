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
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Document Number</label>
                                                    <input type="text" name ="document_number" class="form-control"
                                                        placeholder="Document Number" value="">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Issue Date</label>
                                                    <input type="text" name ="issue_date" id="issue_date"
                                                        class="form-control" placeholder="Issue Date" value="">
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Revision Date</label>
                                                    <input type="text" name ="revision_date" class="form-control"
                                                        placeholder="Revision Date"
                                                        value="{{ getDocumentReviewDate('Audit Analysis-0') }}" readonly>
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
                                                                <input type="text" name="serial_number[1]"
                                                                    class="form-control" value="00001" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group form-input">
                                                                <label class="form-label require">Department Name</label>
                                                                <select name="department_id[1]"
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
                                                                <select name="unit_id[1]" class="form-control single-select"
                                                                    style="width: 100%">
                                                                    <option value="">Select Unit</option>
                                                                    @foreach ($unitList as $unit)
                                                                        <option value="{{ $unit->id }}">
                                                                            {{ $unit->unit_name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row mt-3">
                                                        @foreach ($months as $month)
                                                            <div class="col-md-6 mt-2">
                                                                <div
                                                                    class="form-group form-input d-flex align-items-center">
                                                                    <label class="form-label require me-2"
                                                                        style="min-width: 100px;">{{ $month }}</label>
                                                                    <input type="number"
                                                                        name="marks_{{ strtolower($month) }}[1]"
                                                                        class="form-control" placeholder="Enter Marks">
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>

                                                    <div class="row mt-3">
                                                        <div class="col-md-3">
                                                            <div class="form-group form-input">
                                                                <label class="form-label require">Total No's of
                                                                    Audit</label>
                                                                <input type="text" name="no_of_audit[1]"
                                                                    class="form-control">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group form-input">
                                                                <label class="form-label require">Total Marks</label>
                                                                <input type="text" name="total_marks[1]"
                                                                    class="form-control">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group form-input">
                                                                <label class="form-label require">Total Marks
                                                                    Obtained</label>
                                                                <input type="text" name="total_marks[1]"
                                                                    class="form-control">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group form-input">
                                                                <label class="form-label require">%</label>
                                                                <input type="text" name="percentage[1]"
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

            $('#resetform').on('click', function(e) {
                e.preventDefault();
                location.reload();
            });

            var fromDatepicker = flatpickr("#issue_date", {
                dateFormat: "d-m-Y",
                minDate: new Date(),
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

            // $(".add-row").click(function() {
            const months = [
                'April', 'May', 'June', 'July', 'August', 'September',
                'October', 'November', 'December', 'January', 'February', 'March'
            ];

            $(document).on('click', ".add-row", function() {
                let currentFormSets = $('#form-wrapper .form-set').length;

                if (currentFormSets >= maxFormSets) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Maximum Checklist Reached',
                        text: 'You can only add up to 200 Audit CheckLists.',
                        confirmButtonColor: '#3085d6'
                    });
                    return;
                }

                // Ensure it starts from 00002 format
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
                                    <input type="text" name="serial_number[${form_set_count}]" class="form-control" value="${newSerialNumber}" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group form-input">
                                    <label class="form-label require">Department Name</label>
                                    <select name="department_id[${form_set_count}]" class="form-control single-select" style="width: 100%">
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
                                    <select name="unit_id[${form_set_count}]" class="form-control single-select">
                                        <option value="">Select Unit</option>
                                        @foreach ($unitList as $unit)
                                                <option value="{{ $unit->id }}">
                                                {{ $unit->unit_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            @foreach ($months as $month)
                                <div class="col-md-6 mt-2">
                                    <div class="form-group form-input d-flex align-items-center">
                                        <label class="form-label require me-2" style="min-width: 100px;">{{ $month }}</label>
                                        <input type="number" name="marks_{{ strtolower($month) }}[${form_set_count}]" class="form-control" placeholder="Enter Marks">
                                    </div>
                                 </div>
                             @endforeach
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-3">
                                <div class="form-group form-input">
                                    <label class="form-label require">Total No's of Audit</label>
                                    <input type="text" name="no_of_audit[${form_set_count}]" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group form-input">
                                    <label class="form-label require">Total Marks</label>
                                    <input type="text" name="total_marks[${form_set_count}]" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group form-input">
                                    <label class="form-label require">Total Marks Obtained</label>
                                    <input type="text" name="marks_obtained[${form_set_count}]" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group form-input">
                                    <label class="form-label require">%</label>
                                    <input type="text" name="percentage[${form_set_count}]" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>`;

                // Append New Form Set
                $('#form-wrapper').append(newFormSet);
                serial_number++;
                $(`input[name="no_of_audit[${form_set_count}]"]`).rules("add", {
                    required: true,
                    number: true,
                    min: 1
                });
                $(`input[name="total_marks[${form_set_count}]"]`).rules("add", {
                    required: true,
                    number: true,
                    min: 0
                });
                $(`input[name="marks_obtained[${form_set_count}]"]`).rules("add", {
                    required: true,
                    number: true,
                    min: 0
                });
                $(`input[name="percentage[${form_set_count}]"]`).rules("add", {
                    required: true,
                    number: true,
                    min: 0,
                    max: 100
                });
                // Apply Select2 ONLY to Newly Added Select Elements
                $('#form-wrapper .form-set:last .single-select').select2({
                    width: '100%'
                });

                updatePageIndices();
            });

            // Remove Row Functionality
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
