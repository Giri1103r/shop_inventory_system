@extends('admin.layouts.admin')
@section('title', 'MSDS ')
@section('pageurl', admin_url('msds/list'))

@section('content')

    <style>
        .card-header-inner {
            padding: 10px;
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
                                    <x-button-back href="{{ admin_url('msds/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form">
                                    <form method="POST" id="msdsAdd" action="{{ admin_url('msds/add/submit') }}"
                                        enctype="multipart/form-data">
                                        @csrf

                                        <div class="row">
                                            <div class="card-header-inner">
                                                <h4 class="text-white">MSDS Details</h4>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Document Number</label>
                                                    <input type="text" name ="document_number" class="form-control"
                                                        placeholder="Document Number" value="{{ $document_no->doc_no }}"
                                                        readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Issue Date</label>
                                                    <input type="text" name ="issue_date" id="issue_date"
                                                        class="form-control" placeholder="Issue Date"
                                                        value="{{ displaydateformat($document_no->issue_date) }}" readonly>
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-3">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Revision & Data</label>
                                                    <input type="text" name ="revision_date" class="form-control"
                                                        placeholder="Revision Date" value="{{ $document_no->rev_dt }}"
                                                        readonly>
                                                </div>
                                            </div>
                                            <input type="hidden" name="document_reference_id"
                                                value="{{ encryptId($document_no->id) }}">
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label for="location_id" class="form-label require">
                                                        Location
                                                    </label>
                                                    <select name="location_id" id="location_id"
                                                        class=" form-control single-select" style="width: 100%">
                                                        <option value="">Select Location</option>
                                                        @foreach ($locations as $location)
                                                            <option value="{{ encryptId($location->id) }}">
                                                                {{ $location->location_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label for="unit_id" class="form-label require">
                                                        Unit
                                                    </label>
                                                    <select name="unit_id" id="unit_id"
                                                        class=" form-control single-select" style="width: 100%">
                                                        <option value="">Select Unit</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label for="department_id" class="form-label require">
                                                        Department
                                                    </label>
                                                    <select name="department_id" id="department_id"
                                                        class=" form-control single-select" style="width: 100%">
                                                        <option value="">Select Department</option>
                                                    </select>
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
                                                        <h4 class="text-white">MSDS CheckList</h4>
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
                                                        <div class="col-md-4 mb-3">
                                                            <div class="form-group form-input">
                                                                <label class="form-label require">Serial Number</label>
                                                                <input type="text" name="serial_number[1]"
                                                                    class="form-control" placeholder="Serial Number"
                                                                    value="MSDS-00001" readonly>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-4 mb-3">
                                                            <div class="form-group form-input">
                                                                <label class="form-label require">Item Code</label>
                                                                <input type="text" name="item_code[1]"
                                                                    class="form-control" placeholder="Item Code"
                                                                    value="">
                                                            </div>
                                                        </div>


                                                        <div class="col-md-4 mb-3">
                                                            <div class="form-group form-input">
                                                                <label for="chemicals_1" class="form-label require">
                                                                    Name of Chemical
                                                                </label>
                                                                <input type="text" name="name_of_chemical[1]"
                                                                    class="form-control" placeholder="Enter the Chemicals"
                                                                    value="">

                                                            </div>
                                                        </div>

                                                        <div class="col-md-4 mb-3">
                                                            <div class="form-group form-input">
                                                                <label class="form-label require">Storage Capacity</label>
                                                                <input type="text" name="storage_capacity[1]"
                                                                    class="form-control" placeholder="Storage Capacity"
                                                                    value="">
                                                            </div>
                                                        </div>


                                                        <div class="col-md-4 mb-3 mt-2 form-group form-input">
                                                            <div class="form-group">
                                                                <label for="nfpa_rating" class="require form-label">NFPA
                                                                    Rating</label>
                                                                <div class="d-flex flex-wrap gap-2">
                                                                    @foreach ($nfaratings as $nfarating)
                                                                        <div class="form-check form-check-inline">
                                                                            <input type="radio" name="nfa_rating[1]"
                                                                                value="{{ encryptId($nfarating->id) }}"
                                                                                class="form-check-input"
                                                                                id="nfa_rating_{{ $loop->index }}">
                                                                            <label class="form-check-label"
                                                                                for="nfa_rating_{{ $loop->index }}">
                                                                                {{ $nfarating->nfa_rating }}
                                                                            </label>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-4 mb-3 mt-2">
                                                            <div class="form-group form-input">
                                                                <div class="form-group">
                                                                    <label for="nfpa_rating"
                                                                        class="require form-label">NFPA Rating Value</label>
                                                                    <div class="d-flex flex-wrap gap-2">
                                                                        @foreach ($nfaratingvalues as $nfaratingvalue)
                                                                            <div class="form-check form-check-inline">
                                                                                <input type="radio"
                                                                                    name="value_nfa_rating[1]"
                                                                                    value="{{ encryptId($nfaratingvalue->id) }}"
                                                                                    class="form-check-input"
                                                                                    id="value_nfa_rating_{{ $loop->index }}">
                                                                                <label class="form-check-label"
                                                                                    for="value_nfa_rating_{{ $loop->index }}">
                                                                                    {{ $nfaratingvalue->rating_value }}
                                                                                </label>
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-4 mb-3 mt-2">
                                                            <div class="form-group form-input">
                                                                <label class="form-label require">MSDS Availability
                                                                    Status</label>
                                                                <select name="msds_availability_status[1]"
                                                                    class="form-control single-select"
                                                                    style="width: 100%">
                                                                    <option value="">Select MSDS Availability Status
                                                                    </option>
                                                                    <option value="{{ encryptId(YES) }}">YES</option>
                                                                    <option value="{{ encryptId(NO) }}">NO</option>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-12 mt-2">
                                                            <div class="form-group form-input">
                                                                <label class="form-label require">Remark</label>
                                                                <textarea name="remark[1]" class="form-control" placeholder="Remark" rows="3"></textarea>
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

            $('#location_id').on('change', function() {
                var location_id = $(this).val();
                $('#department_id').val("").trigger("change");
                $('#unit_id').val("").trigger("change");

                var csrf_token = $('meta[name="csrf-token"]').attr('content');

                $.ajax({
                    url: '{{ admin_url('msds/getUnit') }}',
                    type: 'POST',
                    data: {
                        location: location_id,
                        _token: csrf_token
                    },
                    success: function(response) {
                        let options = '<option value="">Select Unit</option>';
                        if (response.unit && response.unit.length > 0) {
                            response.unit.forEach(function(unit) {
                                options +=
                                    `<option value="${unit.id}">${unit.unit}</option>`;
                            });
                        } else {
                            options = '<option value="">No Unit available</option>';
                        }
                        $('#unit_id').html(options);
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX Error:", error);
                        $('#unit_id').html('<option value="">Error loading Unit</option>');
                    }
                });
            });

            $('#unit_id').on('change', function() {
                $('#department_id').val("").trigger("change");

                var location_id = $('#location_id').val();
                var unit_id = $(this).val();
                var csrf_token = $('meta[name="csrf-token"]').attr('content');

                $.ajax({
                    url: '{{ admin_url('msds/getDepartment') }}',
                    type: 'POST',
                    data: {
                        unit: unit_id,
                        location: location_id,
                        _token: csrf_token
                    },
                    success: function(response) {
                        let options = '<option value="">Select Department</option>';
                        if (response.department && response.department.length > 0) {
                            response.department.forEach(function(department) {
                                options +=
                                    `<option value="${department.id}">${department.department_name}</option>`;
                            });
                        } else {
                            options = '<option value="">No Department available</option>';
                        }
                        $('#department_id').html(options);
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX Error:", error);
                        $('#department_id').html(
                            '<option value="">Error loading Unit</option>');
                    }
                });
            });

            $.validator.addMethod("noSpaces", function(value, element) {
                return this.optional(element) || value.trim().length > 0;
            }, "This field cannot contain only spaces");

            $('#msdsAdd').validate({
                rules: {
                    document_number: {
                        required: true,
                        noSpaces: true,
                    },
                    issue_date: {
                        required: true,
                    },
                    issue_date: {
                        required: true,
                    },
                    location_id: {
                        required: true,
                    },
                    unit_id: {
                        required: true,
                    },
                    department_id: {
                        required: true,
                    },
                    'item_code[1]': {
                        required: true,
                        minlength: 3,
                        maxlength: 30,
                        uniqueItemCode: true,
                        noSpaces: true,
                        remote: {
                            url: "{{ admin_url('msds/unique') }}",
                            type: "post",
                            data: {
                                item_code: function() {
                                    return $('[name="item_code[1]"]').val()
                                }
                            }
                        },
                    },
                    'name_of_chemical[1]': {
                        required: true,
                        minlength: 3,
                        maxlength: 30,
                    },
                    'msds_availability_status[1]': {
                        required: true,
                    },
                    'nfa_rating[1]': {
                        required: true,
                    },
                    'storage_capacity[1]': {
                        required: true,
                    },
                    'value_nfa_rating[1]': {
                        required: true,
                    },
                    'remark[1]': {
                        required: true,
                        minlength: 3,
                        maxlength: 300,
                        noSpaces: true,
                    },
                },
                messages: {
                    document_number: {
                        required: "Document Number is Required",
                    },
                    issue_date: {
                        required: "Please Select Issue Date",
                    },
                    revision_date: {
                        required: "Please Select Revision Date",
                    },
                    location_id: {
                        required: "Please Select the Location",
                    },
                    unit_id: {
                        required: "Please Select the Unit",
                    },
                    department_id: {
                        required: "Please Select the Department",
                    },
                    'item_code[1]': {
                        required: "Item Code is Required",
                        minlength: "Minimum Characters should be 3",
                        maxlength: "Maximum Characters should not exceed 30",
                        uniqueItemCode: "Item Code must be unique",
                        remote: "Item code must be unique",
                    },
                    'name_of_chemical[1]': {
                        required: "Name of Chemical is Required",
                        minlength: 'Chemical Name atlest contains 3 letters',
                        maxlength: 'Chemical Name should not Exceed the 30 Characters',
                    },
                    'msds_availability_status[1]': {
                        required: "MSDS Availability Status is Required",
                    },
                    'nfa_rating[1]': {
                        required: "NFPA Type is Required",
                    },
                    'storage_capacity[1]': {
                        required: "Storage Capacity is Required",
                    },
                    'value_nfa_rating[1]': {
                        required: "NFPA Value is Required",
                    },
                    'remark[1]': {
                        required: "Remark is Required",
                        minlength: "Minimum Characters should be 3",
                        maxlength: "Maximum Characters should not exceed 300",
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


            $.validator.addMethod("uniqueItemCode", function(value, element) {
                var itemCodes = [];

                $("input[name^='item_code']").each(function() {
                    var itemCodeValue = $(this).val();
                    if (itemCodeValue) {
                        itemCodes.push(itemCodeValue);
                    }
                });

                return itemCodes.indexOf(value) === itemCodes.lastIndexOf(value);
            }, "Item Code must be unique");

            let form_set_count = 2;
            let serial_number = parseInt("{{ getMSDSCount() }}", 10) + 1;
            const maxFormSets = 200;
            const minFormSets = 1;

            $(document).on('click', ".add-row", function() {
                let currentFormSets = $('#form-wrapper .form-set').length;

                if (currentFormSets >= maxFormSets) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Maximum MSDS CheckList Reached',
                        text: 'You can only add up to 200 MSDS CheckList.',
                        confirmButtonColor: '#3085d6'
                    });
                    return;
                }

                let newSerialNumber = 'MSDS-' + ('0000' + serial_number).slice(-5);

                var newFormSet = `
                <div class="form-set mb-3">
                    <div class="card-header-inner">
                        <h4 class="text-white">MSDS CheckList</h4>
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
                        <div class="col-md-4 mb-3">
                            <div class="form-group form-input">
                                <label class="form-label require">Serial Number</label>
                                <input type="text" name="serial_number[${form_set_count}]" class="form-control" placeholder="Serial Number" value="${newSerialNumber}" readonly>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <div class="form-group form-input">
                                <label class="form-label require">Item Code</label>
                                <input type="text" name="item_code[${form_set_count}]" class="form-control" placeholder="Item Code" value="">
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <div class="form-group form-input">
                                <label class="form-label require">Name of Chemical</label>
                             <input type="text" name="name_of_chemical[${form_set_count}]"
                                 class="form-control" placeholder="Enter the Chemicals"
                                    value="">
                            </div>
                        </div>
                        <div class="col-md-4 form-input mb-3">
                            <div class="form-group">
                                <label class="form-label require">Storage Capacity</label>
                                <input type="text" name="storage_capacity[${form_set_count}]" class="form-control" placeholder="Storage Capacity" value="">
                            </div>
                        </div>

                         <div class="col-md-4 mb-3 mt-2 form-group form-input">
                            <div class="form-group">
                                <label for="nfpa_rating" class="require form-label">NFPA
                                    Rating</label>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach ($nfaratings as $nfarating)
                                        <div class="form-check form-check-inline">
                                            <input type="radio" name="nfa_rating[${form_set_count}]"
                                                value="{{ encryptId($nfarating->id) }}"
                                                class="form-check-input"
                                                id="nfa_rating_{{ $loop->index }}">
                                            <label class="form-check-label"
                                                for="nfa_rating_{{ $loop->index }}">
                                                {{ $nfarating->nfa_rating }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3 mt-2">
                            <div class="form-group form-input">
                                <div class="form-group">
                                    <label for="nfpa_rating"
                                        class="require form-label">NFPA Rating Value</label>
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach ($nfaratingvalues as $nfaratingvalue)
                                            <div class="form-check form-check-inline">
                                                <input type="radio"
                                                    name="value_nfa_rating[${form_set_count}]"
                                                    value="{{ encryptId($nfaratingvalue->id) }}"
                                                    class="form-check-input"
                                                    id="value_nfa_rating_{{ $loop->index }}">
                                                <label class="form-check-label"
                                                    for="value_nfa_rating_{{ $loop->index }}">
                                                    {{ $nfaratingvalue->rating_value }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 mt-2 form-input">
                            <div class="form-group form-input">
                                <label class="form-label require">MSDS Availability Status</label>
                                <select name="msds_availability_status[${form_set_count}]" class="form-control single-select" style="width: 100%">
                                    <option value="">Select MSDS Availability Status</option>
                                    <option value="{{ encryptId(YES) }}">YES</option>
                                    <option value="{{ encryptId(NO) }}">NO</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-12 mt-2">
                            <div class="form-group form-input">
                                <label class="form-label require">Remark</label>
                                <textarea name="remark[${form_set_count}]" class="form-control" placeholder="Remark" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                </div>`;

                $('#form-wrapper').append(newFormSet);

                serial_number++;

                $('select[name^="msds_availability_status["]').each(function() {
                    $(this).select2({
                        placeholder: "Select MSDS Availability Status",
                        width: '100%'
                    });
                });

                $('.single-select').select2();

                var $input = $("input[name='item_code[" + form_set_count + "]']");

                $input.rules('add', {
                    required: true,
                    uniqueItemCode: true,
                    noSpaces: true,
                    minlength: 3,
                    maxlength: 30,
                    remote: {
                        url: "{{ admin_url('msds/unique') }}",
                        type: "post",
                        data: {
                            item_code: function() {
                                return $input
                                    .val();
                            }
                        }
                    },
                    messages: {
                        required: 'Item Code is required',
                        uniqueItemCode: 'Item Code must be unique',
                        remote: 'Item Code must be unique',
                        minlength: "Minimum Characters should be 3",
                        maxlength: "Maximum Characters should not exceed 30",
                        noSpaces: 'Item Code cannot be empty or only spaces'
                    }
                });



                var $input = $("select[name='name_of_chemical[" + form_set_count + "]']");

                $input.rules('add', {
                    required: true,
                    minlength: 3,
                    maxlength: 30,
                    messages: {
                        required: 'Name of Chemical is required',
                        minlength: 'Chemical Name atlest contains 3 letters',
                        maxlength: 'Chemical Name should not Exceed the 30 Characters'
                    }
                });

                $("select[name='msds_availability_status[" + form_set_count + "]']").rules('add', {
                    required: true,
                    messages: {
                        required: 'MSDS Availability Status is required',
                    }
                });

                $("input[name='nfa_rating[" + form_set_count + "]']").rules('add', {
                    required: true,
                    messages: {
                        required: 'Select NFPA Rating',
                    }
                });

                $("input[name='value_nfa_rating[" + form_set_count + "]']").rules('add', {
                    required: true,
                    messages: {
                        required: 'Select NFPA Rating Value',
                    }
                });
                var $input = $("input[name='storage_capacity[" + form_set_count + "]']");

                $input.rules('add', {
                    required: true,
                    noSpaces: true,
                    minlength: 2,
                    maxlength: 30,

                    messages: {
                        required: 'Storage Capacity is required',
                        minlength: "Minimum Characters should be 2",
                        maxlength: "Maximum Characters should not exceed 30",
                        noSpaces: 'Storage Capacity cannot be empty or only spaces'
                    }
                });


                $("textarea[name='remark[" + form_set_count + "]']").rules('add', {
                    required: true,
                    minlength: 3,
                    maxlength: 300,
                    noSpaces: true,
                    messages: {
                        required: 'Remark is required',
                        minlength: "Minimum Characters should be 3",
                        maxlength: "Maximum Characters should not exceed 300",
                        noSpaces: 'Remark cannot be empty or only spaces'
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
                        title: 'Minimum MSDS CheckList Required',
                        text: 'At least 1 MSDS CheckList is required.',
                        confirmButtonColor: '#3085d6'
                    });
                    return;
                }
                $(this).closest('.form-set').remove();
                updatePageIndices();
            });

            function updatePageIndices() {
                $('#form-wrapper .form-set').each(function(index) {
                    $(this).find("input[name^='serial_number']").val('MSDS-' + ('0000' + (index + 1)).slice(
                        -5));

                    $(this).find('input[name^="serial_number"]').attr('name', 'serial_number[' + (index +
                        1) + ']');
                    $(this).find('input[name^="item_code"]').attr('name', 'item_code[' + (index + 1) + ']');
                    $(this).find('input[name^="storage_capacity"]').attr('name', 'storage_capacity[' + (
                        index +
                        1) + ']');
                    $(this).find('select[name^="name_of_chemical"]').attr('name', 'name_of_chemical[' + (
                        index + 1) + ']');
                    $(this).find('select[name^="msds_availability_status"]').attr('name',
                        'msds_availability_status[' + (index + 1) + ']');
                    $(this).find('input[name^="nfa_rating"]').attr('name',
                        'nfa_rating[' + (index + 1) + ']');
                    $(this).find('input[name^="value_nfa_rating"]').attr('name',
                        'value_nfa_rating[' + (index + 1) + ']');
                    $(this).find('textarea[name^="remark"]').attr('name', 'remark[' + (index + 1) + ']');
                });
            }

            $(".submit").on('click', function() {
                if ($("#msdsAdd").valid()) {
                    $("#msdsAdd").submit();
                } else {
                    return false;
                }
            });
        });
    </script>
@endpush
