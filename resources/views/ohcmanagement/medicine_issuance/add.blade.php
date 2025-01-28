@extends('admin.layouts.admin')
@section('title', 'Medicine Issuance')
@section('pageurl', admin_url('ohc/medicine-issuance/list'))
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
                                    <x-button-back href="{{ admin_url('ohc/medicine-issuance/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="basic-form">
                                    <form method="POST" id="MedicineRequisitionForm" enctype="multipart/form-data"
                                        action="{{ admin_url('ohc/medicine-issuance/add/submit') }}">
                                        @csrf

                                        <hr>
                                        <div class="row">


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
                                                    <label for="rate" class="form-label require ">Issued
                                                        Date</label>
                                                    <input type="text" name="issue_date" id="issue_date"
                                                        class="form-control">
                                                </div>
                                            </div>

                                        </div>
                                        <div class="table-responsive">
                                            <table class="table view_card" id="requisition_medicine">
                                                <thead style="background-color: #0000;color:#ffff">
                                                    <tr>
                                                        <th>Medicine</th>
                                                        <th>Available Quantity</th>
                                                        <th>Quantity</th>
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
                                                            </div> </select>
                                                        </td>
                                                        <td>
                                                            <div class="form-group form-input">
                                                                <label for="available_quantity " class="require">Available
                                                                    Quantity</label>

                                                                <input type="text" name="available_quantity[0]"
                                                                    id="available_quantity"
                                                                    placeholder="Enter the available quantity"
                                                                    class="form-control" readonly>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="form-group form-input">
                                                                <label for="remarks"class="require">Quantity</label>
                                                                <input type="text" name="quantity[0]" id="quantity"
                                                                    placeholder="Enter the quantity" class="form-control">
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
                                </div>
                                <hr>
                                <div class="submit-button float-end">
                                    <x-button-submit class="submit" id="submit"></x-button-submit>
                                    <x-button-reset class="submit"></x-button-reset>
                                    <x-button-cancel href="{{ admin_url('ohc/medicine-issuance/list') }}"></x-button-cancel>
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
            var fromDatepicker = flatpickr("#issue_date", {
                dateFormat: "d-m-Y",
                minDate: new Date(),

            });
        });


        $(document).ready(function() {
            let medicine_issuance_row_count = 1;

            // Adding a new row
            $(".add-row").click(function() {
                var newRow = `
        <tr>
            <td>
                <div class="form-group form-input">
                    <label for="medicine_id" class="require">Medicine Name</label>
                    <select name="medicine_id[${medicine_issuance_row_count}]" class="form-control single-select" style="width: 100%">
                        <option value="">Select the Medicine Name</option>
                        @foreach ($medicine as $list)
                            <option value="{{ $list->id }}">{{ $list->medicine }}</option>
                        @endforeach
                    </select>
                </div>
            </td>
            <td>
                <div class="form-group form-input">
                    <label for="available_quantity" class="require">Available Quantity</label>
                    <input type="text" name="available_quantity[${medicine_issuance_row_count}]" id="available_quantity_${medicine_issuance_row_count}" placeholder="Enter the available quantity" class="form-control" readonly>
                </div>
            </td>
            <td>
                <div class="form-group form-input">
                    <label for="quantity" class="require">Quantity</label>
                    <input type="text" name="quantity[${medicine_issuance_row_count}]" id="quantity_${medicine_issuance_row_count}" class="form-control" placeholder="Enter the quantity">
                </div>
            </td>
            <td>
                <div class="d-flex justify-content-center align-items-center bg-danger mt-2 ml-2 text-white rounded delete-row" style="width: 30px; height: 30px;">
                    <i class="fa-solid fa-trash"></i>
                </div>
            </td>
        </tr>`;

                // Append the new row to the table body
                $('#medicine-tbody').append(newRow);

                // Initialize select2 for the newly added select element
                $('select[name="medicine_id[' + medicine_issuance_row_count + ']"]').select2({
                    placeholder: "Select the Medicine Name",
                    width: '100%' // Ensuring it spans the full width
                });

                // Add validation rules for the newly added row
                $('select[name="medicine_id[' + medicine_issuance_row_count + ']"]').rules('add', {
                    required: true,
                    messages: {
                        required: 'This Medicine name is required'
                    }
                });

                $('input[name="quantity[' + medicine_issuance_row_count + ']"]').rules('add', {
                    required: true,
                    digits: true,
                    messages: {
                        required: 'Quantity is required',
                        digits: 'Quantity must be numeric',
                    }
                });

                // $('input[name="available_quantity[' + medicine_issuance_row_count + ']"]').rules('add', {
                //     required: true,
                //     digits: true,
                //     messages: {
                //         required: 'Quantity is required',
                //         digits: 'Quantity must be numeric',
                //     }
                // });

                // Increment the row count for the next row
                medicine_issuance_row_count++;
            });

            // Delete a row
            $(document).on("click", ".delete-row", function() {
                var rowCount = $('#medicine-tbody tr').length;


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


        $(document).on("change", "[name^='medicine_id']", function() {
            var medicineIds = [];
            var isDuplicate = false;
            var currentRow = $(this).closest("tr");
            var medicineId = $(this).val();


            $("[name^='medicine_id']").each(function() {
                var otherMedicineId = $(this).val();
                if (otherMedicineId) {
                    if (medicineIds.includes(otherMedicineId)) {
                        isDuplicate = true;
                    }
                    medicineIds.push(otherMedicineId);
                }
            });


            if (isDuplicate) {
                Swal.fire({
                    icon: "error",
                    title: "Duplicate Medicine Selection!",
                    text: "Each Medicine must be unique across all rows.",
                });

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
                    issue_date: {
                        required: true,
                    },
                    'medicine_id[0]': {
                        required: true,
                    },
                    // 'available_quantity[0]': {
                    //     required: true,
                    //     digits: true,
                    // },
                    'quantity[0]': {
                        required: true,
                        digits: true,
                    }

                },
                messages: {
                    unit_id: {
                        required: "Please select the Unit name.",
                    },
                    department_id: {
                        required: "Please select the Department Name.",
                    },
                    request_date: {
                        required: "Please select the request date.",
                    },
                    'medicine_id[0]': {
                        required: 'Medicine Name is required',
                    },
                    // 'available_quantity[0]': {
                    //     required: 'Available Quantity is required',
                    //     digits: 'Quantity should be numeric',
                    // },
                    'quantity[0]': {
                        required: ' Quantity is required',
                        digits: 'Quantity should be numeric',

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
