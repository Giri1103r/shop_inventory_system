@extends('admin.layouts.admin')
@section('title', 'Checklist Observation Follow-up Add')
@section('pageurl', admin_url('fire/checklist-observation/list'))


@section('content')

    <style>
        .card-header-inner {
            padding: 11px;
        }
    </style>
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
                                        href="{{ admin_url('fire/checklist-observation/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form">
                                    <form method="POST" id="observationadd"
                                        action="{{ admin_url('fire/checklist-observation/add/submit') }}" autocomplete="off"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="document_reference_id"
                                            value="{{ encryptId($staticDocno->id) }}">
                                        <input type="hidden" name="inspection_type" value="{{ $inspection_type }}">
                                        <input type="hidden" name="inspection_id" value="{{ $inspection_id }}">
                                        <div class="row">
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Observation ID</label>
                                                    <input type="text" name="observation_id" id = "observation_id"
                                                        class="form-control" readonly
                                                        value="{{ getSequence('Observation') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4 form-input">
                                                <label class="form-label">Doc. No</label>
                                                <input type="text" class="form-control" name="doc_no" id="doc_no"
                                                    readonly value="{{ $staticDocno->doc_no }}">
                                            </div>

                                            <div class="col-md-4 form-input">
                                                <label class="form-label">Issue Dt.</label>
                                                <input type="text" class="form-control" name="issue_date" id="issue_date"
                                                    readonly value="{{ Displaydateformat($staticDocno->issue_date) }}">
                                            </div>

                                            <div class="col-md-4 form-input mb-2">
                                                <label class="form-label">Rev. & Dt.</label>
                                                <input type="text" class="form-control" name="rev_dt" id="rev_dt"
                                                    readonly value="{{ $staticDocno->rev_dt }}">
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Date of Inspection</label>
                                                    <input type="text" id="date_of_inspection" name="date_of_inspection"
                                                        class="form-control" placeholder="Date of Inspection"
                                                        value="">
                                                </div>
                                            </div>

                                        </div>

                                        <div class="row mt-4">
                                            <div id="form-wrapper">
                                                <div class="form-set mb-3">
                                                    <div class="card-header-inner">
                                                        <h4 class="text-white">Observation</h4>
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
                                                                <input type="text" name="obs[1][serial_number]"
                                                                    id="serial_number_1" class="form-control"
                                                                    value="OBS-CHECKLIST-00001" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group form-input">
                                                                <label class="form-label require">Unit Name</label>
                                                                <select name="obs[1][unit_id]" id="unit_id_1"
                                                                    class="form-control single-select" style="width: 100%">
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
                                                                <label class="form-label require">Department Name</label>
                                                                <select name="obs[1][department_id]" id="department_id_1"
                                                                    class="form-control single-select"
                                                                    style="width: 100%">
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
                                                                <label class="form-label require">Name of Equipment</label>
                                                                <input type="text" id="year_1"
                                                                    name="obs[1][equipment_name]" class="form-control"
                                                                    placeholder="Name of Equipment" value="">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group form-input">
                                                                <label class="form-label require">Resource Code of
                                                                    Equipment</label>
                                                                <input type="text" id="year_1"
                                                                    name="obs[1][equipment_code]" class="form-control"
                                                                    placeholder="Resource Code of Equipment"
                                                                    value="">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group form-input">
                                                                <label class="form-label require">Observation</label>
                                                                <input type="text" id="year_1"
                                                                    name="obs[1][observation]" class="form-control"
                                                                    placeholder="Observation" value="">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group form-input">
                                                                <label class="form-label require">Date of Observation /
                                                                    Inspection</label>
                                                                <input type="text" id="date" name="obs[1][date]"
                                                                    class="form-control" placeholder="Date"
                                                                    value="">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group form-input">
                                                                <label class="form-label require">Observation of the
                                                                    Month</label>
                                                                <input type="text" id="month" name="obs[1][month]"
                                                                    class="form-control" placeholder="Month"
                                                                    value="">
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
                                            <x-button-cancel
                                                href="{{ admin_url('fire/checklist-observation/list') }}"></x-button-cancel>
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
                flatpickr("#date_of_inspection", {
                    dateFormat: "d-m-Y",
                });
                flatpickr("#date", {
                    dateFormat: "d-m-Y",
                });
                $('#month').datepicker({
                    format: 'M',
                    minViewMode: 'months',
                    viewMode: 'months',
                    autoclose: true
                });
                let form_set_count = 2;
                let serial_number = parseInt("{{ getObservation() }}", 10) + 1;
                const maxFormSets = 10;
                const minFormSets = 1;

                // $(".add-row").click(function() {
                $(document).on('click', ".add-row", function() {
                    let currentFormSets = $('#form-wrapper .form-set').length;

                    if (currentFormSets >= maxFormSets) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Maximum Observation Reached',
                            text: 'You can only add up to 10 Observation CheckList.',
                            confirmButtonColor: '#3085d6'
                        });
                        return;
                    }

                    let serial_number = $('#form-wrapper .form-set').length + 1;
                    let newSerialNumber = 'OBS-CHECKLIST-' + ('0000' + serial_number).slice(-5);


                    var newFormSet = `
                <div class="form-set mb-3">
                    <div class="card-header-inner">
                        <h4 class="text-white">Observation</h4>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button class="btn btn-primary add-row me-3" type="button"
                            id="add-row" style="width: 84px;">
                            Add
                        </button>
                        <button type="button" class="btn btn-danger remove-row">
                            <i class="fa-solid fa-trash"></i> Remove
                        </button>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group form-input">
                                <label class="form-label require">Serial Number</label>
                                <input type="text" name="obs[${form_set_count}][serial_number]" class="form-control" placeholder="Serial Number" value="${newSerialNumber}" readonly>
                            </div>
                        </div>

                            <div class="col-md-4">
                                <div class="form-group form-input">
                                    <label class="form-label require">Unit Name</label>
                                    <select name="obs[${form_set_count}][unit_id]" id="unit_id_${form_set_count}" class="form-control single-select">
                                        <option value="">Select Unit</option>
                                        @foreach ($unitList as $unit)
                                            <option value="{{ $unit->id }}">{{ $unit->unit_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                             <div class="col-md-4">
                                <div class="form-group form-input">
                                    <label class="form-label require">Department Name</label>
                                    <select name="obs[${form_set_count}][department_id]" id="department_id_${form_set_count}" class="form-control single-select" style="width: 100%">
                                        <option value="">Select Department</option>
                                        @foreach ($departmentList as $department)
                                            <option value="{{ $department->id }}">{{ $department->department_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        <div class="col-md-4 mt-2">
                            <div class="form-group form-input">
                                <label class="form-label require">Name of Equipment</label>
                                <input name="obs[${form_set_count}][equipment_name]" class="form-control" placeholder="Remark">
                            </div>
                        </div>
                          <div class="col-md-4 mt-2">
                            <div class="form-group form-input">
                                <label class="form-label require">Resource Code of Equipment</label>
                                <input name="obs[${form_set_count}][equipment_code]" class="form-control" placeholder="Remark">
                            </div>
                        </div>
                         <div class="col-md-4 mt-2">
                            <div class="form-group form-input">
                                <label class="form-label require">Observation</label>
                                <input name="obs[${form_set_count}][observation]" class="form-control" placeholder="Remark">
                            </div>
                        </div>
                         <div class="col-md-4 mt-2">
                            <div class="form-group form-input">
                                <label class="form-label require">Date of Observation / Inspection</label>
                                <input name="obs[${form_set_count}][date]"  id="date_${form_set_count}" class="form-control" placeholder="Date">
                            </div>
                        </div>
                        <div class="col-md-4 mt-2">
                            <div class="form-group form-input">
                                <label class="form-label require">Observation of the Month</label>
                                <input name="obs[${form_set_count}][month]" id="month_${form_set_count}" class="form-control" placeholder="Month">
                            </div>
                        </div>
                    </div>
                </div>`;

                    $('#form-wrapper').append(newFormSet);

                    serial_number++;

                    flatpickr('#date_' + form_set_count, {
                        dateFormat: "d-m-Y",
                    });

                    $('#month_' + form_set_count).datepicker({
                        format: 'M',
                        minViewMode: 'months',
                        viewMode: 'months',
                        autoclose: true
                    });

                    $('select[name="unit_id_[' + form_set_count + ']"]').select2({
                        placeholder: "Select the Unit Name",
                        width: '100%'
                    });
                    $('select[name="department_id_[' + form_set_count + ']"]').select2({
                        placeholder: "Select the Department Name",
                        width: '100%'
                    });

                    $('#unit_id_' + form_set_count).rules('add', {
                        required: true,
                        messages: {
                            required: 'Unit is required'
                        }
                    });

                    $('#department_id_' + form_set_count).rules('add', {
                        required: true,
                        messages: {
                            required: 'Department is required'
                        }
                    });

                    $("input[name='obs[" + form_set_count + "][equipment_name]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Equipment name is required'
                        }
                    });

                    $("input[name='obs[" + form_set_count + "][equipment_code]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Resource code is required'
                        }
                    });

                    $("input[name='obs[" + form_set_count + "][observation]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Observation is required'
                        }
                    });

                    $("input[name='obs[" + form_set_count + "][date]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Date is required',
                        }
                    });

                    $("input[name='obs[" + form_set_count + "][month]']").rules('add', {
                        required: true,
                        messages: {
                            required: 'Observation month is required'
                        }
                    });

                    form_set_count++;
                    updatePageIndices();
                });

                $(document).on('click', '.remove-row', function() {
                    let currentFormSets = $('#form-wrapper .form-set').length;

                    if (currentFormSets <= minFormSets) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Minimum Observation Required',
                            text: 'At least 1 Observation is required.',
                            confirmButtonColor: '#3085d6'
                        });
                        return;
                    }
                    $(this).closest('.form-set').remove();
                    updatePageIndices();
                });

                function updatePageIndices() {
                    $('#form-wrapper .form-set').each(function(index) {
                        let newIndex = index + 1;
                        $(this).find("input[name^='serial_number']").val('OBS-CHECKLIST-' + ('0000' + newIndex)
                            .slice(-5));
                        $(this).find("input[name^='serial_number']").attr('name', 'serial_number[' + newIndex +
                            ']');

                        $(this).find("select[name*='[unit_id]']").attr('name', `obs[${newIndex}][unit_id]`);
                        $(this).find("select[name*='[department_id]']").attr('name',
                            `obs[${newIndex}][department_id]`);
                        $(this).find("input[name*='[equipment_name]']").attr('name',
                            `obs[${newIndex}][equipment_name]`);
                        $(this).find("input[name*='[equipment_code]']").attr('name',
                            `obs[${newIndex}][equipment_code]`);
                        $(this).find("input[name*='[observation]']").attr('name',
                            `obs[${newIndex}][observation]`);
                        $(this).find("input[name*='[date]']").attr('name', `obs[${newIndex}][date]`);
                        $(this).find("input[name*='[month]']").attr('name', `obs[${newIndex}][month]`);
                    });
                }


                $(".submit").on('click', function() {
                    if ($("#observationadd").validate()) {
                        $("#observationadd").submit();
                    } else {
                        return false;
                    }
                });

            });
            $(function() {
                $('#observationadd').validate({
                    rules: {

                        date_of_inspection: {
                            required: true,
                        },
                        'obs[1][month]': {
                            required: true,
                        },
                        'obs[1][date]': {
                            required: true,
                        },
                        'bs[1][observation]': {
                            required: true,
                        },
                        'obs[1][equipment_code]': {
                            required: true,
                        },
                        'obs[1][equipment_name]': {
                            required: true,
                        },
                        'obs[1][department_id]': {
                            required: true,
                        },
                        'obs[1][unit_id]': {
                            required: true,
                        },


                    },
                    messages: {
                        date_of_inspection: {
                            required: "{{ __(' Date of inspection is Required') }}",
                        },
                        'obs[1][month]': {
                            required: "{{ __('Month  is Required') }}",
                        },
                        'obs[1][date]': {
                            required: "{{ __('Date  is Required') }}",
                        },
                        'obs[1][observation]': {
                            required: "{{ __(' Observation is Required') }}",
                        },
                        'obs[1][equipment_code]': {
                            required: "{{ __('Equipment Code  is Required') }}",
                        },
                        'obs[1][equipment_name]': {
                            required: "{{ __('Equipment Name  is Required') }}",
                        },
                        'obs[1][department_id]': {
                            required: "{{ __('Department  is Required') }}",
                        },
                        'obs[1][unit_id]': {
                            required: "{{ __(' Unit is Required') }}",
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
