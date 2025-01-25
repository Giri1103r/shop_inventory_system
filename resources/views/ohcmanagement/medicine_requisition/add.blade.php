@extends('admin.layouts.admin')
@section('title', 'Medicine Requisition')
@section('pageurl', admin_url('ohc/medicine-requisition/list'))
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
                                    <x-button-back href="{{ admin_url('ohc/medicine-requisition/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="basic-form">
                                    <form method="POST" id="MedicineRequisitionForm" enctype="multipart/form-data"
                                        action="{{ admin_url('ohc/medicine-requisition/add/submit') }}">
                                        @csrf

                                        <hr>
                                        <div class="row">

                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Requestion ID</label>
                                                    <input type="text" name ="req_id" id="req_id" class="form-control"
                                                        placeholder="Requistion ID" value="{{ getsequence('requistion') }}"
                                                        readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Unit</label>
                                                    <select name="unit_id" id="unit_id" class="form-control single-select"
                                                        style="width: 100%">
                                                        <option value="">Select the unit</option>
                                                        @foreach ($unit as $list)
                                                            <option value="{{ encryptId($list->id) }}">
                                                                {{ $list->unit_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Department</label>
                                                    <select name="department_id" id="department_id"
                                                        class=" form-control single-select" style="width: 100%">
                                                        <option value="">Select Department </option>

                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label for="rate" class="form-label require ">Request
                                                        Date</label>
                                                    <input type="text" name="request_date" id="request_date"
                                                        class="form-control">
                                                </div>
                                            </div>

                                        </div>
                                        <div class="table-responsive">
                                            <table class="table view_card" id="requisition_medicine">
                                                <thead style="background-color: #0000;color:#ffff">
                                                    <tr>
                                                        <th>Medicine</th>
                                                        <th>Quantity</th>
                                                        <th>Remarks</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="medicine-tbody">
                                                    <tr>
                                                        <td>
                                                            <div class="form-group form-input">
                                                                <label for="medicine_id"class="require">Medicine
                                                                    Name</label>
                                                                <select name="medicine_id[0]" id="medicine_id"
                                                                    class="form-control single-select" style="width: 100%">
                                                                    <option value="">Select the Medicine Name</option>
                                                                    @foreach ($medicine as $list)
                                                                        <option value="{{ $list->id }}">
                                                                            {{ $list->medicine }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="form-group form-input">
                                                                <label for="quantity " class="require">Quantity</label>

                                                                <input type="text" name="quantity[0]" id="quantity"
                                                                    placeholder="Enter the quantity" class="form-control">
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="form-group form-input">
                                                                <label for="remarks"class="require">Remarks</label>
                                                                <textarea name="remarks[0]" id="remarks" cols="10" rows="2" class="form-control"></textarea>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="row gap-2">
                                                                <div class="d-flex justify-content-center align-items-center bg-primary mt-2 ml-2 text-white rounded add-row"
                                                                    style="width: 30px; height: 30px;">
                                                                    <i class="fa-solid fa-plus"></i>
                                                                </div>
                                                                <div class="d-flex justify-content-center align-items-center bg-danger mt-2 ml-2 text-white rounded delete-row"
                                                                    style="width: 30px; height: 30px;">
                                                                    <i class="fa-solid fa-trash"></i>
                                                                </div>
                                                            </div>

                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <hr>
                                        <div class="submit-button float-end">
                                            <x-button-submit class="submit" id="submit"></x-button-submit>
                                            <x-button-reset class="submit"></x-button-reset>
                                            <x-button-cancel
                                                href="{{ admin_url('ohc/medicine-requisition/list') }}"></x-button-cancel>
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
    <script>
        $(document).ready(function() {
            var fromDatepicker = flatpickr("#request_date", {
                dateFormat: "d-m-Y",
                minDate: new Date(),

            });
        });
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
                        $('#department_id').trigger('change.');
                    },
                    error: function(xhr) {
                        alert('Error fetching department. Please try again.');
                    }
                });
            } else {
                $('#department_id').empty().append('<option value="">Select Department</option>');
                $('#department_id').trigger('change.');
            }
        });
        $(document).ready(function() {

            const MAX_ROWS = 5;
            let medicine_requisition_row_count = 1;

            $(".add-row").click(function() {
                var rowCount = $('#medicine-tbody tr').length;

                if (rowCount < MAX_ROWS) {
                    var newRow = `
            <tr>
                <td>
                    <div class="form-group form-input">
                        <label for="medicine_id" class="require">Medicine Name</label>
                        <select name="medicine_id[${medicine_requisition_row_count}]" class="form-control single-select" style="width: 100%">
                            <option value="">Select the Medicine Name</option>
                            @foreach ($medicine as $list)
                                <option value="{{ $list->id }}">{{ $list->medicine }}</option>
                            @endforeach
                        </select>
                    </div>
                </td>
                <td>
                    <div class="form-group form-input">
                        <label for="quantity" class="require">Quantity</label>
                        <input type="text" name="quantity[${medicine_requisition_row_count}]" placeholder="Enter the quantity" class="form-control">
                    </div>
                </td>
                <td>
                    <div class="form-group form-input">
                        <label for="remarks" class="require">Remarks</label>
                        <textarea name="remarks[${medicine_requisition_row_count}]" cols="10" rows="2" class="form-control"></textarea>
                    </div>
                </td>
                <td>
                    <div class="d-flex justify-content-center align-items-center bg-danger mt-2 ml-2 text-white rounded delete-row" style="width: 30px; height: 30px;">
                        <i class="fa-solid fa-trash"></i>
                    </div>
                </td>
            </tr>`;

                    $('#medicine-tbody').append(newRow);

                    // Initialize select2 for the newly added select element
                    $('select[name="medicine_id[' + medicine_requisition_row_count + ']"]').select2({
                        placeholder: "Select the Medicine Name", // Optional placeholder
                        width: '100%' // Ensuring it spans the full width
                    });

                    // Add validation rules
                    $('select[name="medicine_id[' + medicine_requisition_row_count + ']"]').rules('add', {
                        required: true,
                        messages: {
                            required: 'This Medicine name is required'
                        }
                    });

                    $('input[name="quantity[' + medicine_requisition_row_count + ']"]').rules('add', {
                        required: true,
                        digits: true,
                        messages: {
                            required: 'Quantity is required',
                            digits: 'Quantity must be numeric',
                        }
                    });

                    $('textarea[name="remarks[' + medicine_requisition_row_count + ']"]').rules('add', {
                        required: true,
                        minlength: 3,
                        maxlength: 600,
                        messages: {
                            required: 'Remarks are required',
                            minlength: 'Minimum 3 characters are required',
                            maxlength: 'Remarks should not exceed 600 characters',
                        }
                    });

                    // Increment the row count for the next addition
                    medicine_requisition_row_count++;
                } else {
                    // Display SweetAlert warning if the limit is exceeded
                    Swal.fire({
                        icon: 'warning',
                        title: 'Warning',
                        text: 'Your request has exceeded the limit.',
                        confirmButtonColor: '#3085d6'
                    });
                }
            });

            $(document).on("click", ".delete-row", function() {
                var rowCount = $('#medicine-tbody tr').length;

                // Check if there is more than one row
                if (rowCount > 1) {
                    $(this).closest("tr").remove();
                } else {
                    // Show SweetAlert warning if there's only one row
                    Swal.fire({
                        icon: 'warning',
                        title: 'Warning',
                        text: 'At least one row is required.',
                        confirmButtonColor: '#3085d6'
                    });
                }
            });
        });

        $(document).on("change", "[name^='medicine_id']", function() {
            var medicineIds = [];
            var isDuplicate = false;
            var currentRow = $(this).closest("tr");
            var medicineId = $(this).val();

            // Loop through all medicine_id fields to check for duplicates
            $("[name^='medicine_id']").each(function() {
                var otherMedicineId = $(this).val();
                if (otherMedicineId) {
                    if (medicineIds.includes(otherMedicineId)) {
                        isDuplicate = true;
                    }
                    medicineIds.push(otherMedicineId);
                }
            });

            // If a duplicate is found, show an alert and reset the current row's medicine field
            if (isDuplicate) {
                Swal.fire({
                    icon: "error",
                    title: "Duplicate Medicine Selection!",
                    text: "Each Medicine must be unique across all rows.",
                });
                // Clear medicine selection in the current row
                currentRow.find('select[name^="medicine_id"]').val("");
                return;
            }
        });



        $(function() {

            $.validator.addMethod(
                "regex",
                function(value, element, regex) {
                    return this.optional(element) || regex.test(value);
                },
                "Invalid format."
            );

            $('#MedicineRequisitionForm').validate({
                rules: {
                    unit_id: {
                        required: true,
                    },
                    department_id: {
                        required: true,
                    },
                    req_id: {
                        required: true,
                    },
                    request_date: {
                        required: true,
                    },
                    'medicine_id[0]': {
                        required: true,
                    },
                    'quantity[0]': {
                        required: true,
                        digits: true,
                    },
                    'remarks[0]': {
                        required: true,
                        minlength: 3,
                        maxlength: 600,
                    }

                },
                messages: {
                    unit_id: {
                        required: "Please select the Unit name.",
                    },
                    department_id: {
                        required: "Please select the Department Name.",
                    },
                    req_id: {
                        required: "Requisition ID cannot be empty.",
                    },
                    request_date: {
                        required: "Please select the request date.",
                    },
                    'medicine_id[0]': {
                        required: 'Medicine Name is required',
                    },
                    'quantity[0]': {
                        required: 'Quantity is required',
                        digits: 'Quantity should be numeric',
                    },
                    'remarks[0]': {
                        required: 'Remarks is required',
                        minlength: 'Minimum 3 character is required',
                        maxlength: 'Remarks should not exceed more than the 600 characters',

                    }
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
                    console.log("Form has " + errors + " invalid fields.");
                },
            });
        });
    </script>
@endpush
