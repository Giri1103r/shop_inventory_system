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
                                        action="{{ admin_url('ohc/medicine-issuance/issue/submit') }}">
                                        @csrf
                                        <input type="hidden" id="id" name="id"
                                            value="{{ encryptId($user_medicine_requisition->id) }}">
                                        <hr>
                                        <div class="row">


                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Unit</label>
                                                    <input type="text" name="unit_id" id="unit_id" class="form-control"
                                                        readonly
                                                        value = "{{ getUnitname($user_medicine_requisition->unit_id) }}">

                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Department</label>
                                                    <input type="text" name="department_id" id="department_id"
                                                        class=" form-control" readonly
                                                        value = "{{ getDepartment($user_medicine_requisition->department_id) }}">


                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label for="rate" class="form-label require ">Issued
                                                        Date</label>
                                                    <input type="text" name="issue_date" id="issue_date"
                                                        class="form-control" value="{{ date('d-m-Y') }}">
                                                </div>
                                            </div>

                                        </div>
                                        <div class="row mt-2">
                                            <div class="card-header-inner">
                                                <h4 class="text-white">Medicine details</h4>

                                            </div>
                                            <div
                                                class="d-flex justify-content-end align-items-center mb-3 button-container">

                                                <button class="btn btn-primary add-row" type="button" id="add-row"
                                                    style="margin-left: 10px; width: 84px;">
                                                    Add
                                                </button>


                                            </div>

                                        </div>
                                </div class="mt-2">
                                <div class="table-responsive">
                                    <div class="col-md-12">
                                        <table class="table table-bordered ">

                                            <thead class="bg-secondary" style="color: #ffff">
                                                <tr>
                                                    <th>Medicine</th>
                                                    <th>Available Quantity</th>
                                                    <th>Quantity</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="medicine-tbody">
                                                @foreach ($medicine_requisition as $requisition)

                                                    <tr>
                                                        <td>
                                                            <div class="form-group form-input">
                                                                <label for="medicine_id" class="require">Medicine
                                                                    Name</label>
                                                                <select name="medicine_id[]" id="medicine_id"
                                                                    class="form-control single-select" style="width: 100%">
                                                                    <option value="">Select the Medicine Name
                                                                    </option>
                                                                    @foreach ($medicine as $list)
                                                                        <option value="{{ encryptId($list->medicine_id) }}"
                                                                            @if ($requisition->medicine_id == $list->medicine_id) selected @endif>
                                                                            {{ getMedicinename($list->medicine_id) }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="form-group form-input">
                                                                <label for="available_quantity" class="require">Available
                                                                    Quantity</label>
                                                                <input type="text" name="available_quantity[]"
                                                                    id="available_quantity"
                                                                    value="{{ $requisition->available_quantity }}"
                                                                    placeholder="Available quantity" class="form-control"
                                                                    readonly>
                                                            </div>
                                                        </td>

                                                        <td>
                                                            <div class="form-group form-input">
                                                                <label for="quantity" class="require">Quantity</label>
                                                                <input type="text" name="quantity[]" id="quantity"
                                                                    value="{{ $requisition->quantity }}"
                                                                    placeholder="Enter the quantity" class="form-control">
                                                                <span id="quantity-error" style=" display:none;"
                                                                    class="text-danger">Quantity must be less
                                                                    than available quantity.</span>
                                                            </div>
                                                        </td>

                                                        <td>

                                                            <div class="d-flex justify-content-center align-items-center bg-danger mt-2 ml-2 text-white rounded delete-row"
                                                                style="width: 30px; height: 30px;">
                                                                <i class="fa-solid fa-trash"></i>
                                                            </div>


                                                        </td>
                                                    </tr>
                                                @endforeach

                                            </tbody>
                                        </table>
                                    </div>



                                </div>


                                <hr>
                                <div class="submit-button float-end">
                                    <x-button-submit class="submit" id="submit"></x-button-submit>
                                    <x-button-reset class="submit"></x-button-reset>
                                    <x-button-cancel
                                        href="{{ admin_url('ohc/medicine-issuance/list') }}"></x-button-cancel>
                                </div>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
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


            $(document).ready(function () {
    // Initialize Select2 for existing select elements
    $('#medicine-tbody select').each(function () {
        $(this).select2({
            placeholder: "Select the Medicine Name",
            width: '100%'
        });
    });

    let medicine_issuance_row_count = $('#medicine-tbody tr').length;

    // Function to add a new row
    $(".add-row").click(function () {
        let rowCount = $('#medicine-tbody tr').length;

        let newRow = `
            <tr>
                <td>
                    <div class="form-group form-input">
                        <label class="require">Medicine Name</label>
                        <select name="medicine_id[${rowCount}]" class="form-control single-select" style="width: 100%">
                            <option value="">Select the Medicine Name</option>
                            @foreach ($medicine as $list)
                                <option value="{{ $list->medicine_id }}">{{ getMedicinename($list->medicine_id) }}</option>
                            @endforeach
                        </select>
                    </div>
                </td>
                <td>
                    <div class="form-group form-input">
                        <label class="require">Available Quantity</label>
                        <input type="text" name="available_quantity[${rowCount}]" placeholder="Available quantity" class="form-control" readonly>
                    </div>
                </td>
                <td>
                    <div class="form-group form-input">
                        <label class="require">Quantity</label>
                        <input type="text" name="quantity[${rowCount}]" placeholder="Enter the quantity" class="form-control">
                        <span class="text-danger quantity-error" style="display:none;">Quantity must be less than available quantity.</span>
                    </div>
                </td>
                <td>
                    <div class="d-flex justify-content-center align-items-center bg-danger mt-2 ml-2 text-white rounded delete-row" style="width: 30px; height: 30px;">
                        <i class="fa-solid fa-trash text-white"></i>
                    </div>
                </td>
            </tr>`;

        $('#medicine-tbody').append(newRow);

        // Initialize Select2 for new row
        $('select[name="medicine_id[' + rowCount + ']"]').select2({
            placeholder: "Select the Medicine Name",
            width: '100%'
        });

        medicine_issuance_row_count++;
    });

    // Function to validate quantity (less than available quantity)
    $(document).on("keyup", "input[name^='quantity']", function () {
        let row = $(this).closest("tr");
        let availableQty = parseInt(row.find("input[name^='available_quantity']").val()) || 0;
        let enteredQty = parseInt($(this).val()) || 0;

        if (enteredQty > availableQty) {
            row.find(".quantity-error").show();
        } else {
            row.find(".quantity-error").hide();
        }
    });

    // Function to remove a row
    $(document).on("click", ".delete-row", function () {
        var rowCount = $('#medicine-tbody tr').length;

        if (rowCount > 1) {
            $(this).closest("tr").remove();
        } else {
            Swal.fire({
                icon: 'warning',
                title: 'Warning',
                text: 'At least one row is required.',
                confirmButtonColor: '#3085d6'
            });
        }
    });

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
