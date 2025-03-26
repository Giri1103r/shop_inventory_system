@extends('admin.layouts.admin')
@section('title', 'First Aid Record Add')
@section('pageurl', admin_url('ohc/first-aid-record/list'))

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
            <div class="row">
                <div class="col-12">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title"></h4>
                                <div class="align-back-btc">
                                    <x-button-back href="{{ admin_url('ohc/first-aid-record/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form">
                                    <form method="POST" id="fir_add"
                                        action="{{ admin_url('ohc/first-aid-record/add/submit') }}">
                                        @csrf

                                        <div class="row">
                                            <div class="card-header-inner">
                                                <h4 class="text-white">First Aid Record Details</h4>
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
                                                    <label class="form-label require">Revision & Data</label>
                                                    <input type="text" name ="revision_date" class="form-control"
                                                        placeholder="Revision Date"
                                                        value="{{ getDocumentReviewDate('FIR-0') }}" readonly>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mt-4">
                                            <div class="row mt-2">
                                                <div
                                                    class="d-flex justify-content-end align-items-center me-2 mb-3 button-container">
                                                </div>
                                            </div>

                                            <div id="form-wrapper">
                                                <div class="form-set mb-3">
                                                    <div class="card-header-inner">
                                                        <h4 class="text-white">First Aid Record CheckList</h4>
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
                                                        <div class="col-md-4 mt-2">
                                                            <div class="form-group form-input">
                                                                <label class="form-label require">Serial Number</label>
                                                                <input type="text" name="serial_number[1]"
                                                                    class="form-control" placeholder="Serial Number"
                                                                    value="FIR-00001" readonly>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-4 mt-2">
                                                            <div class="form-group form-input">
                                                                <label class="form-label require">Month</label>
                                                                <input type="text" name="month[1]" id="month"
                                                                    class="form-control" placeholder="Month" value="">
                                                            </div>
                                                        </div>

                                                        <div class="col-md-4 mt-2">
                                                            <div class="form-group form-input">
                                                                <label class="form-label require">Unit</label>
                                                                <select name="unit_id[1]" id="unit_id"
                                                                    class="form-control single-select" style="width: 100%">
                                                                    <option value="">Select Unit</option>
                                                                    @foreach ($unit as $list)
                                                                        <option value="{{ encryptId($list->id) }}">
                                                                            {{ $list->unit_name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-4 mt-2">
                                                            <div class="form-group form-input">
                                                                <label class="form-label require">Department</label>
                                                                <select name="department_id[1]" id="department_id"
                                                                    class="form-control single-select"
                                                                    style="width: 100%">
                                                                    <option value="">Select Department</option>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-4 mt-2">
                                                            <div class="form-group form-input">
                                                                <label class="form-label require">First Aid Station
                                                                    Number</label>
                                                                <input type="text" name="first_aid_station_number[1]"
                                                                    class="form-control"
                                                                    placeholder="First Aid Station Number" value=""
                                                                    readonly>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-4 mt-2">
                                                            <div class="form-group form-input">
                                                                <label class="form-label require">First Aid Box
                                                                    Number</label>
                                                                <input type="text" name="first_aid_box_number[1]"
                                                                    class="form-control"
                                                                    placeholder="First Aid Box Number" value=""
                                                                    readonly>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-4 mt-2">
                                                            <div class="form-group form-input">
                                                                <label class="form-label require">Total Number of First
                                                                    Aid</label>
                                                                <input type="text" name="total_number_of_first_aid[1]"
                                                                    class="form-control"
                                                                    placeholder="Total Number of First Aid"
                                                                    value="">
                                                            </div>
                                                        </div>

                                                        <div class="col-md-12 mt-2">
                                                            <div class="form-group form-input">
                                                                <label class="form-label require">Remark</label>
                                                                <textarea name="remark[1]" rows="3" class="form-control" placeholder="Remark"></textarea>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mt-2">
                                            <div class="form-group form-input">
                                                <label class="form-label require">Overall Total Number of First
                                                    Aid</label>
                                                <input type="text" name ="overall_total_number_of_first_aid"
                                                    class="form-control" placeholder="Overall Total Number of First Aid"
                                                    value="0" readonly>
                                            </div>
                                        </div>
                                        
                                        <hr>
                                        <div class="submit-button" style="text-align: right;">

                                            <x-button-submit class="submit"></x-button-submit>
                                            <x-button-reset class="submit"></x-button-reset>
                                            <x-button-cancel
                                                href="{{ admin_url('ohc/first-aid-record/list') }}"></x-button-cancel>
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
                // minDate: new Date(),
            });

            $('#month').datepicker({
                format: 'MM',
                minViewMode: 1,
                autoclose: true
            });

            $.validator.addMethod("noSpaces", function(value, element) {
                return this.optional(element) || value.trim().length > 0;
            }, "This field cannot contain only spaces");

            $(document).on('input', '[name^="total_number_of_first_aid"]', function() {
                var overallTotal = 0;
                $('[name^="total_number_of_first_aid"]').each(function() {
                    var value = parseFloat($(this).val()) || 0;
                    overallTotal += value;
                });
                $('input[name="overall_total_number_of_first_aid"]').val(overallTotal);
            });

            var selectedUnitsDepartments = [];

            $(document).on('change', '#unit_id', function() {
                var unitId = $(this).val();
                if (unitId) {
                    $.ajax({
                        url: "{{ admin_url('department/ajax-list') }}/" + unitId + "/0",
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            $('#department_id').empty().append(
                                '<option value="">Select Department</option>');
                            $.each(data, function(key, value) {
                                $('#department_id').append('<option value="' + value
                                    .id + '">' + value.name + '</option>');
                            });
                            $('#department_id').trigger('change');
                        },
                        error: function(xhr) {
                            alert('Error fetching department. Please try again.');
                        }
                    });
                } else {
                    $('#department_id').empty().append('<option value="">Select Department</option>');
                    $('#department_id').trigger('change');
                }
            });

            $(document).on('change', '#unit_id, #department_id', function() {
                var unitId = $('#unit_id').val();
                var departmentId = $('#department_id').val();

                if (unitId && departmentId) {
                    var combination = unitId + '-' + departmentId;

                    if (selectedUnitsDepartments.indexOf(combination) === -1) {
                        selectedUnitsDepartments.push(combination);
                        $.ajax({
                            url: "{{ admin_url('ohc/first-aid-record/first-aid-location/details') }}",
                            type: 'GET',
                            data: {
                                unit_id: unitId,
                                department_id: departmentId
                            },
                            dataType: 'json',
                            success: function(response) {
                                $('input[name="first_aid_station_number[1]"]').val(response
                                    .station_number);
                                $('input[name="first_aid_box_number[1]"]').val(response
                                    .first_aid_box_no);
                            },
                            error: function(xhr) {
                                alert('Error fetching first aid details. Please try again.');
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Duplicate Selection',
                            text: 'This unit and department combination already exists.',
                            confirmButtonText: 'OK'
                        });

                        $('#department_id').val('');
                    }
                }
            });

            $(document).on('change', '[id^="unit_id-"]', function() {
                var unitId = $(this).val();
                var departmentSelect = $(this).closest('form').find('[id^="department_id-"]');

                if (unitId) {
                    $.ajax({
                        url: "{{ admin_url('department/ajax-list') }}/" + unitId + "/0",
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            departmentSelect.empty().append(
                                '<option value="">Select Department</option>');
                            $.each(data, function(key, value) {
                                departmentSelect.append('<option value="' + value.id +
                                    '">' + value.name + '</option>');
                            });
                            departmentSelect.trigger('change');
                        },
                        error: function(xhr) {
                            alert('Error fetching department. Please try again.');
                        }
                    });
                } else {
                    departmentSelect.empty().append('<option value="">Select Department</option>');
                    departmentSelect.trigger('change');
                }
            });

            $(document).on('change', '[id^="unit_id-"], [id^="department_id-"]', function() {
                var formSetCount = $(this).attr('id').split('-')[1];
                var unitId = $('#unit_id-' + formSetCount).val();
                var departmentId = $('#department_id-' + formSetCount).val();

                if (unitId && departmentId) {
                    var combination = unitId + '-' + departmentId;

                    if (selectedUnitsDepartments.indexOf(combination) === -1) {
                        selectedUnitsDepartments.push(combination);
                        $.ajax({
                            url: "{{ admin_url('ohc/first-aid-record/first-aid-location/details') }}",
                            type: 'GET',
                            data: {
                                unit_id: unitId,
                                department_id: departmentId
                            },
                            dataType: 'json',
                            success: function(response) {
                                $('input[name="first_aid_station_number[' + formSetCount +
                                    ']"]').val(response.station_number);
                                $('input[name="first_aid_box_number[' + formSetCount + ']"]')
                                    .val(response.first_aid_box_no);
                            },
                            error: function(xhr) {
                                alert('Error fetching first aid details. Please try again.');
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Duplicate Selection',
                            text: 'This unit and department combination already exists.',
                            confirmButtonText: 'OK'
                        });

                        $('#department_id-' + formSetCount).val('');
                    }
                }
            });


            $('#fir_add').validate({
                rules: {
                    document_number: {
                        required: true,
                        noSpaces: true,
                        pattern: /^[a-zA-Z0-9\s\-_'"()]+$/,
                    },
                    issue_date: {
                        required: true,
                    },
                    revision_date: {
                        required: true,
                    },
                    'month[1]': {
                        required: true,
                    },
                    'department_id[1]': {
                        required: true,
                    },
                    'unit_id[1]': {
                        required: true,
                    },
                    'first_aid_box_number[1]': {
                        required: true,
                    },
                    'first_aid_station_number[1]': {
                        required: true,
                    },
                    'total_number_of_first_aid[1]': {
                        required: true,
                        number: true,
                        noSpaces: true,
                        pattern: /^[a-zA-Z0-9\s\-_'"()]+$/,
                    },
                    overall_total_number_of_first_aid: {
                        required: true,
                        noSpaces: true,
                    },
                    'remark[1]': {
                        required: true,
                        noSpaces: true,
                        pattern: /^[a-zA-Z0-9\s\-_'"()]+$/,
                    },
                },
                messages: {
                    document_number: {
                        required: "Document Number is Required",
                        pattern: "Only alphanumeric characters and - _ ' \" ( ) are allowed.",
                    },
                    issue_date: {
                        required: "Please Select Issue Date",
                    },
                    revision_date: {
                        required: "Please Select Revision Date",
                    },
                    'month[1]': {
                        required: "Month is Required",
                    },
                    'department_id[1]': {
                        required: "Department is Required",
                    },
                    'unit_id[1]': {
                        required: "Unit is Required",
                    },
                    'first_aid_box_number[1]': {
                        required: "First Aid Box Number is Required",
                    },
                    'first_aid_station_number[1]': {
                        required: "First Aid Station Number is Required",
                    },
                    'total_number_of_first_aid[1]': {
                        required: "Total Number of First Aid is Required",
                        number: "Please enter a valid number.",
                        pattern: "Only alphanumeric characters and - _ ' \" ( ) are allowed.",
                    },
                    overall_total_number_of_first_aid: {
                        required: "OverAll Total Number of First Aid is Required",
                    },
                    'remark[1]': {
                        required: "Remark is Required",
                        pattern: "Only alphanumeric characters and - _ ' \" ( ) are allowed.",
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
            let serial_number = parseInt("{{ getFIRCount() }}", 10) + 1;
            const maxFormSets = 200;
            const minFormSets = 1;

            $(document).on('click', ".add-row", function() {
                let currentFormSets = $('#form-wrapper .form-set').length;

                if (currentFormSets >= maxFormSets) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Maximum First Aid Record CheckList Reached',
                        text: 'You can only add up to 200 First Aid Record CheckList.',
                        confirmButtonColor: '#3085d6'
                    });
                    return;
                }

                let newSerialNumber = 'FIR-' + ('0000' + serial_number).slice(-5);

                var newFormSet = `
                <div class="form-set mb-3">
                    <div class="card-header-inner">
                        <h4 class="text-white">First Aid Record CheckList</h4>
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
                        <div class="col-md-4 mt-2">
                            <div class="form-group form-input">
                                <label class="form-label require">Serial Number</label>
                                <input type="text" name="serial_number[${form_set_count}]" class="form-control" placeholder="Serial Number" value="${newSerialNumber}" readonly>
                            </div>
                        </div>

                        <div class="col-md-4 mt-2">
                            <div class="form-group form-input">
                                <label class="form-label require">Month</label>
                                <input type="text" name="month[${form_set_count}]" id="month" class="form-control month" placeholder="Month" value="">
                            </div>
                        </div>

                        <div class="col-md-4 mt-2">
                            <div class="form-group form-input">
                                <label class="form-label require">Unit</label>
                                <select name="unit_id[${form_set_count}]" id="unit_id-${form_set_count}"
                                    class="form-control single-select" style="width: 100%">
                                    <option value="">Select Unit</option>
                                    @foreach ($unit as $list)
                                        <option value="{{ encryptId($list->id) }}">
                                            {{ $list->unit_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4 mt-2">
                            <div class="form-group form-input">
                                <label class="form-label require">Department</label>
                                <select name="department_id[${form_set_count}]" id="department_id-${form_set_count}"
                                    class="form-control single-select" style="width: 100%">
                                    <option value="">Select Department</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4 mt-2">
                            <div class="form-group form-input">
                                <label class="form-label require">First Aid Station Number</label>
                                <input type="text" name="first_aid_station_number[${form_set_count}]"
                                    class="form-control" placeholder="First Aid Station Number"
                                    value="" readonly>
                            </div>
                        </div>

                        <div class="col-md-4 mt-2">
                            <div class="form-group form-input">
                                <label class="form-label require">First Aid Box Number</label>
                                <input type="text" name="first_aid_box_number[${form_set_count}]"
                                    class="form-control" placeholder="First Aid Box Number"
                                    value="" readonly>
                            </div>
                        </div>

                        <div class="col-md-4 mt-2">
                            <div class="form-group form-input">
                                <label class="form-label require">Total Number of First
                                    Aid</label>
                                <input type="text" name="total_number_of_first_aid[${form_set_count}]"
                                    class="form-control"
                                    placeholder="Total Number of First Aid" value="">
                            </div>
                        </div>

                        <div class="col-md-12 mt-2">
                            <div class="form-group form-input">
                                <label class="form-label require">Remark</label>
                                <textarea name="remark[${form_set_count}]" rows="3"  class="form-control"
                                    placeholder="Remark"></textarea>
                            </div>
                        </div>

                    </div>
                </div>`;

                $('#form-wrapper').append(newFormSet);

                serial_number++;

                $('.month').datepicker({
                    format: 'MM',
                    minViewMode: 1,
                    autoclose: true
                });

                $('select[name^="unit_id["]').each(function() {
                    $(this).select2({
                        placeholder: "Select Unit",
                        width: '100%'
                    });
                });
                $('select[name^="department_id["]').each(function() {
                    $(this).select2({
                        placeholder: "Select Department",
                        width: '100%'
                    });
                });

                $("input[name='month[" + form_set_count + "]']").rules('add', {
                    required: true,
                    messages: {
                        required: 'Month is required',
                    }
                });

                $("select[name='unit_id[" + form_set_count + "]']").rules('add', {
                    required: true,
                    messages: {
                        required: 'Unit is required',
                    }
                });

                $("select[name='department_id[" + form_set_count + "]']").rules('add', {
                    required: true,
                    messages: {
                        required: 'Department is required',
                    }
                });

                $("input[name='first_aid_box_number[" + form_set_count + "]']").rules('add', {
                    required: true,
                    messages: {
                        required: 'First Aid Box Number is required',
                    }
                });

                $("input[name='first_aid_station_number[" + form_set_count + "]']").rules('add', {
                    required: true,
                    messages: {
                        required: 'First Aid Station Number is required',
                    }
                });

                $("input[name='total_number_of_first_aid[" + form_set_count + "]']").rules('add', {
                    required: true,
                    noSpaces: true,
                    number: true,
                    pattern: /^[a-zA-Z0-9\s\-_'"()]+$/,
                    messages: {
                        required: 'Total Number of First Aid is required',
                        noSpaces: 'Total Number of First Aid cannot be empty or only spaces',
                        number: "Please enter a valid number.",
                        pattern: "Only alphanumeric characters and - _ ' \" ( ) are allowed.",
                    }
                });

                $("textarea[name='remark[" + form_set_count + "]']").rules('add', {
                    required: true,
                    noSpaces: true,
                    pattern: /^[a-zA-Z0-9\s\-_'"()]+$/,
                    messages: {
                        required: 'Remark is required',
                        noSpaces: 'Remark cannot be empty or only spaces',
                        pattern: "Only alphanumeric characters and - _ ' \" ( ) are allowed.",
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
                        title: 'Minimum First Aid Record CheckList Required',
                        text: 'At least 1 First Aid Record CheckList is required.',
                        confirmButtonColor: '#3085d6'
                    });
                    return;
                }
                $(this).closest('.form-set').remove();
                updatePageIndices();

            });

            function updatePageIndices() {
                $('#form-wrapper .form-set').each(function(index) {
                    $(this).find("input[name^='serial_number']").val('FIR-' + ('0000' + (index + 1)).slice(- 5));

                    $(this).find('input[name^="serial_number"]').attr('name', 'serial_number[' + (index + 1) + ']');
                    $(this).find('input[name^="month"]').attr('name', 'month[' + (index + 1) + ']');
                    $(this).find('select[name^="unit_id"]').attr('name', 'unit_id[' + (index + 1) + ']');
                    $(this).find('select[name^="department_id"]').attr('name', 'department_id[' + (index + 1) + ']');
                    $(this).find('input[name^="first_aid_box_number"]').attr('name', 'first_aid_box_number[' + (index + 1) + ']');
                    $(this).find('input[name^="first_aid_station_number"]').attr('name', 'first_aid_station_number[' + (index + 1) + ']');
                    $(this).find('input[name^="total_number_of_first_aid"]').attr('name', 'total_number_of_first_aid[' + (index + 1) + ']');
                    $(this).find('textarea[name^="remark"]').attr('name', 'remark[' + (index + 1) + ']');
                });
            }

            $(".submit").on('click', function() {
                if ($("#fir_add").valid()) {
                    $("#fir_add").submit();
                } else {
                    return false;
                }
            });
        });
    </script>
@endpush
