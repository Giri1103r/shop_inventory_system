@extends('admin.layouts.admin')
@section('title', 'OHS Sumary Report')
@section('pageurl', admin_url('ohc-plant-summary/list'))
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
                                <div class="align-back-btc">
                                    <x-button-back href="{{ admin_url('safety/ohc-plant-summary/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="basic-form mx-3">
                                    <form method="POST" id="safetyWalkAdd"
                                        action="{{ admin_url('safety/ohc-plant-summary/add/submit') }}" autocomplete="off"
                                        enctype="multipart/form-data">
                                        @csrf

                                        <div class="row">
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">{{ __('inspection.doc_no') }}</label>
                                                    <input type="text" name="doc_no" id = "doc_no" class="form-control"
                                                        placeholder="Enter the Document Number"
                                                        value="{{ $document_no->doc_no }}" readonly>
                                                    @error('doc_no')
                                                        <div class="error">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label require">{{ __('inspection.issue_date') }}</label>
                                                    <input type="text" name="issue_date" id = "issue_date"
                                                        class="form-control" placeholder="Issued Date"
                                                        value="{{ displaydateformat($document_no->issue_date) }}" readonly>
                                                    @error('issue_date')
                                                        <div class="error">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label require">{{ __('inspection.rev_date') }}</label>
                                                    <input type="text" name="rev_date" id = "rev_date"
                                                        class="form-control" value="{{ $document_no->rev_dt }}" readonly>
                                                </div>
                                            </div>
                                            <input type="hidden" name="document_reference_id"
                                                value="{{ encryptId($document_no->id) }}">
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label require">{{ __('inspection.inspection_date') }}</label>
                                                    <input type="text" name="inspection_date" id = "inspection_date"
                                                        class="form-control" value="{{ old('inspection_date') }}">
                                                    @error('inspection_date')
                                                        <div class="error">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label require">{{ __('inspection.updated_frequency') }}</label>
                                                    <input type="text" name="updated_frequency" id = "updated_frequency"
                                                        class="form-control" value="{{ old('updated_frequency') }}">
                                                    @error('updated_frequency')
                                                        <div class="error">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            @php
                                                $rowcount = count($units);
                                            @endphp

                                            <div class="table-responsive">
                                                <table id="dataTable" class="table table-bordered text-center"
                                                    style="border-collapse: collapse;">
                                                    <thead>
                                                        <tr>
                                                            <th rowspan="2"
                                                                style="border: 1px solid #000; vertical-align:middle; text-align:center">
                                                                Sr. No.</th>
                                                            <th rowspan="2"
                                                                style="border: 1px solid #000; vertical-align:middle; text-align:center">
                                                                Description
                                                            </th>
                                                            <th colspan="{{ $rowcount }}"
                                                                style="border: 1px solid #000; vertical-align:middle; text-align:center">
                                                                Quantity (in Nos/m²)
                                                            </th>

                                                            <th rowspan="2"
                                                                style="border: 1px solid #000; vertical-align:middle; text-align:center">
                                                                Total
                                                                Quantity (in Nos/m²)</th>
                                                            <th rowspan="2"
                                                                style="border: 1px solid #000; vertical-align:middle; text-align:center">
                                                                Action</th>
                                                        </tr>
                                                        @foreach ($units as $unit)
                                                            <th
                                                                style="border: 1px solid #000; vertical-align:middle; text-align:center">
                                                                {{ $unit->unit_name }}</th>
                                                        @endforeach
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td style="border: 1px solid #000;">1</td>
                                                            <td style="border: 1px solid #000;">
                                                                <div class="form-input">
                                                                    <textarea type="text" class="form-control description" style="resize: none;" name="description[1]">{{ old('description.1') }}</textarea>
                                                                    @error('description.1')
                                                                        <div class="error">{{ $message }}</div>
                                                                    @enderror
                                                                </div>
                                                            </td>
                                                            @foreach ($units as $index => $unit)
                                                                <td style="border: 1px solid #000;">
                                                                    <div class="form-input">
                                                                        <input type="number" class="form-control"
                                                                            name="unit_{{ $index + 1 }}[1]"
                                                                            data-row-id="1"
                                                                            data-index="{{ $index + 1 }}"
                                                                            value="{{ old('unit_' . ($index + 1) . '.1') }}">
                                                                    </div>
                                                                    @error('unit_' . ($index + 1) . '.1')
                                                                        <div class="error">{{ $message }}</div>
                                                                    @enderror
                                                                </td>
                                                            @endforeach
                                                            <td style="border: 1px solid #000;">
                                                                <input type="number" class="form-control" readonly
                                                                    name="total_quantity[1]"
                                                                    value="{{ old('total_quantity.1') }}">
                                                                @error('total_quantity.1')
                                                                    <div class="error">{{ $message }}</div>
                                                                @enderror
                                                            </td>
                                                            <td style="border: 1px solid #000; margin:10px;">
                                                                <i id="addRow" class="fas fa-plus-circle text-primary"
                                                                    style="cursor: pointer; font-size: 20px;"></i>
                                                                <i class="fas fa-trash removeRow"
                                                                    style="cursor: pointer; font-size: 20px; color: red;"></i>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>


                                            </div>


                                            {{-- fire water pump house details --}}
                                            <div class="card-header-inner p-2">
                                                <h4 class="text-white">Fire Water Pump House Details</h4>
                                            </div>

                                            <div class="table-responsive">
                                                <table id="firewaterpump" class="table table-bordered text-center"
                                                    style="border-collapse: collapse;">
                                                    <thead>
                                                        <tr>
                                                            <th rowspan="2"
                                                                style="border: 1px solid #000; vertical-align:middle; text-align:center">
                                                                Sr. No.
                                                            </th>
                                                            <th rowspan="2"
                                                                style="border: 1px solid #000; vertical-align:middle; text-align:center">
                                                                Name of
                                                                Water
                                                                Pump & Water Storage Tank
                                                            </th>
                                                            <th colspan="{{ $rowcount }}"
                                                                style="border: 1px solid #000; text-align: center; vertical-align:middle; text-align:center">
                                                                Capacity
                                                            </th>
                                                            <th rowspan="2"
                                                                style="border: 1px solid #000; vertical-align:middle; text-align:center">
                                                                Action</th>
                                                        </tr>
                                                        @foreach ($units as $unit)
                                                            <th
                                                                style="border: 1px solid #000; vertical-align:middle; text-align:center">
                                                                {{ $unit->unit_name }}
                                                            </th>
                                                        @endforeach
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td style="border: 1px solid #000;">1</td>
                                                            <td style="border: 1px solid #000;">
                                                                <div class="form-input">
                                                                    <textarea type="text" class="form-control" style="resize: none;" name="fire_pump_details[1]">{{ old('fire_pump_details.1') }}</textarea>
                                                                </div>
                                                                @error('fire_pump_details.1')
                                                                    <div class="error">{{ $message }}</div>
                                                                @enderror
                                                            </td>
                                                            @foreach ($units as $index => $unit)
                                                                <td style="border: 1px solid #000;">
                                                                    <div class="form-input">
                                                                        <input type="number" class="form-control"
                                                                            name="fire_pump_details_unit_{{ $index + 1 }}[1]"
                                                                            value="{{ old('fire_pump_details_unit_' . ($index + 1) . '.1') }}">
                                                                    </div>
                                                                    @error('fire_pump_details_unit_' . ($index + 1) . '.1')
                                                                        <div class="error">{{ $message }}</div>
                                                                    @enderror
                                                                </td>
                                                            @endforeach

                                                            <td style="border: 1px solid #000; margin:10px;">
                                                                <i id="firepump_addrow"
                                                                    class="fas fa-plus-circle text-primary"
                                                                    style="cursor: pointer; font-size: 20px;"></i>
                                                                <i class="fas fa-trash firepump_remove"
                                                                    style="cursor: pointer; font-size: 20px; color: red;"></i>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="submit-button" style="text-align: right;">
                                                <x-button-submit class="submit"></x-button-submit>
                                                <x-button-reset class="submit"></x-button-reset>
                                                <x-button-cancel
                                                    href="{{ admin_url('safety/ohc-plant-summary/list') }}"></x-button-cancel>
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
              
                flatpickr("#inspection_date", {
                    dateFormat: "d-m-Y",
                });

                $(document).on("click", "#addRow", function() {
                    let table = $("#dataTable tbody");
                    let lastRow = table.find("tr:last");
                    let newRow = lastRow.clone();

                    let rowCount = table.find("tr").length + 1;

                    newRow.find("td:first").text(rowCount);

                    newRow.find("textarea").attr("name", "description[" + rowCount + "]");
                    newRow.find("input[type='number']").each(function() {
                        let name = $(this).attr("name");
                        if (name) {
                            let updatedName = name.replace(/\[\d+\]/, "[" + rowCount + "]");
                            $(this).attr("name", updatedName);
                        }

                        $(this).attr("data-row-id", rowCount);
                        $(this).attr("data-index", rowCount);
                    });

                    newRow.find("input[readonly]").attr("name", "total_quantity[" + rowCount + "]");

                    newRow.find("input[type='number'], textarea").val("");

                    table.append(newRow);

                    updateTotalQuantity(rowCount);
                });


                $(document).on("click", ".removeRow", function() {
                    let table = $("#dataTable tbody");
                    if (table.find("tr").length > 1) {
                        $(this).closest("tr").remove();

                        table.find("tr").each(function(index) {
                            $(this).find("td:first").text(index + 1);

                            let rowIndex = index + 1;
                            $(this).find("textarea").attr("name", "description[" + rowIndex + "]");
                            $(this).find("input[type='number']").each(function() {
                                let name = $(this).attr("name");
                                if (name) {
                                    let updatedName = name.replace(/\[\d+\]/, "[" + rowIndex +
                                        "]");
                                    $(this).attr("name", updatedName);
                                }
                            });
                            $(this).find("input[readonly]").attr("name", "total_quantity[" + rowIndex +
                                "]");
                        });
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Minimum Limit Required',
                            text: 'At least one row is required.',
                            confirmButtonColor: '#3085d6'
                        });
                    }

                    updateTotalQuantity(rowIndex);
                });


                $(document).on("click", "#firepump_addrow", function() {
                    let table = $("#firewaterpump tbody");
                    let lastRow = table.find("tr:last");
                    let newRow = lastRow.clone();

                    let rowCount = table.find("tr").length + 1;
                    newRow.find("td:first").text(rowCount);

                    newRow.find("textarea").attr("name", "fire_pump_details[" + rowCount + "]");

                    newRow.find("input").each(function() {
                        let name = $(this).attr("name");
                        if (name) {
                            let updatedName = name.replace(/\[\d+\]/, "[" + rowCount + "]");
                            $(this).attr("name", updatedName);
                        }
                    });

                    newRow.find("input[type='number'], textarea").val("");
                    table.append(newRow);
                });

                $(document).on("click", ".firepump_remove", function() {
                    let table = $("#firewaterpump tbody");
                    if (table.find("tr").length > 1) {
                        $(this).closest("tr").remove();

                        table.find("tr").each(function(index) {
                            $(this).find("td:first").text(index + 1);

                            let rowIndex = index + 1;
                            $(this).find("textarea").attr("name", "fire_pump_details[" + rowIndex +
                                "]");

                            $(this).find("input").each(function() {
                                let name = $(this).attr("name");
                                if (name) {
                                    let updatedName = name.replace(/\[\d+\]/, "[" + rowIndex +
                                        "]");
                                    $(this).attr("name", updatedName);
                                }
                            });
                        });
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Minimum Limit Required',
                            text: 'At least one row is required.',
                            confirmButtonColor: '#3085d6'
                        });
                    }
                });




            });
            $(function() {
                $.validator.addMethod("noSpaces", function(value, element) {
                    return this.optional(element) || value.trim().length > 0;
                }, "This field cannot contain only spaces");


                $('#safetyWalkAdd').validate({
                    rules: {
                        doc_no: {
                            required: true,
                            minlength: 3,
                            maxlength: 100,
                            noSpaces: true,
                        },
                        issue_date: {
                            required: true,
                        },
                        rev_date: {
                            required: true,
                        },
                        "inspection_date": {
                            required: true,
                        },
                        updated_frequency: {
                            required: true,
                        },
                    },
                    messages: {
                        doc_no: {
                            required: "Document Number is Required",
                            minlength: "Minimum Characters should be 3",
                            maxlength: "Maximum Characters should not exceed 100",
                        },
                        issue_date: {
                            required: "Issue  is required",
                        },
                        rev_date: {
                            required: "Revision Date required",
                        },
                        "inspection_date": {
                            required: "Inspection Date is required",
                        },
                        updated_frequency: {
                            required: "Updated Frequency is required",
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
                        validator.errorList.forEach(function(error) {

                        });
                    }
                });

                $('textarea[name^="description"]').each(function() {
                    $(this).rules("add", {
                        required: true,
                        minlength: 3,
                        maxlength: 255,
                        messages: {
                            required: "Description is required",
                            minlength: "Minimum Characters should be 3",
                            maxlength: "Maximum Characters should not exceed 255",
                        }
                    });
                });
                $('input[name^="unit"]').each(function() {
                    $(this).rules("add", {
                        required: true,
                        number: true,
                        messages: {
                            required: "Unit is required",
                        }
                    });
                });
                $('input[name^="fire_pump_details_unit"]').each(function() {
                    $(this).rules("add", {
                        required: true,
                        number: true,
                        messages: {
                            required: "Unit is required",
                        }
                    });
                });
                $('textarea[name^="fire_pump_details"]').each(function() {
                    $(this).rules("add", {
                        required: true,
                        minlength: 3,
                        maxlength: 255,
                        messages: {
                            required: "Fire Pump Details is required",
                            minlength: "Minimum Characters should be 3",
                            maxlength: "Maximum Characters should not exceed 255",
                        }
                    });
                });
            });

            function updateTotalQuantity(rowId) {
                let total = 0;
                const quantityInputs = $(`input[data-row-id='${rowId}']`);
                let allFilled = true;

                $(`input[name="total_quantity[${rowId}]"]`).val("");

                quantityInputs.each(function() {
                    const value = parseFloat($(this).val()) || 0;
                    if (value === 0 && $(this).val() !== "") {
                        allFilled = false;
                    }
                    total += value;
                });

                if (allFilled) {
                    $(`input[name="total_quantity[${rowId}]"]`).val(total.toFixed(2));
                } else {
                    $(`input[name="total_quantity[${rowId}]"]`).val("");
                }
            }

            $(document).on("blur", "input[type='number']", function() {
                let rowId = $(this).data('row-id');
                updateTotalQuantity(rowId);
            });
        </script>
    @endpush
