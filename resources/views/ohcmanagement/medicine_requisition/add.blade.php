@extends('admin.layouts.admin')
@section('title', 'Discard The Expire Medicine')
@section('pageurl', admin_url('ohc/discard/list'))
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
                                    <x-button-back href="{{ admin_url('ohc/discard/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="basic-form">
                                    <form method="POST" id="MedicineRequisitionForm" enctype="multipart/form-data"
                                        action="{{ admin_url('ohc/discard/add/submit') }}">
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
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Unit </label>
                                                    <input type="text" name ="unit_id" id="unit_id"
                                                        class="form-control" placeholder="Enter the Unit Name"
                                                        value="{{ getUnitname(Auth::user()->unit_id) }}" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Department </label>
                                                    <input type="text" name ="department_id" id="department_id"
                                                        class="form-control" placeholder="Enter the Department Name"
                                                        value="{{ getDepartment(Auth::user()->department_id) }}" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label for="rate" class="form-label require ">Request
                                                        Date</label>
                                                    <div class="input-group date form-input custom-height">
                                                        <input type="text" name="request_date" id="request_date"
                                                            class="form-control"autocomplete="off"
                                                            value="{{ date('d-m-Y ') }}" readonly>
                                                        <div class="input-group-addon input-group-text">
                                                            <span class="fa fa-calendar"></span>
                                                        </div>
                                                    </div>
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

                                        <div class="table-responsive">
                                            <div class="col-md-12">
                                                <table class="table table-bordered ">

                                                    <thead class="bg-secondary" style="color: #ffff">
                                                        <tr>
                                                            <th>Medicine</th>
                                                            <th>Available Quantity</th>
                                                            <th>Quantity</th>
                                                            <th>Remarks</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>

                                                    <tbody id="medicine-tbody">
                                                        <tr>
                                                            <td>
                                                                <div class="form-group form-input">
                                                                    <label for="medicine_id" class="require">Medicine
                                                                        Name</label>
                                                                    <select name="medicine_id[0]" id="medicine_id"
                                                                        class="form-control single-select"
                                                                        style="width: 100%">
                                                                        <option value="">Select the Medicine Name
                                                                        </option>
                                                                        @foreach ($medicine as $list)
                                                                            <option value="{{ encryptId($list->id) }}">
                                                                                {{ $list->medicine_id }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="form-group form-input">
                                                                    <label for="available_quantity"
                                                                        class="require">Available
                                                                        Quantity</label>
                                                                    <input type="text" name="available_quantity[0]"
                                                                        id="available_quantity" value=""
                                                                        placeholder="Available quantity"
                                                                        class="form-control" readonly>
                                                                </div>
                                                            </td>

                                                            <td>
                                                                <div class="form-group form-input">
                                                                    <label for="quantity" class="require">Quantity</label>
                                                                    <input type="text" name="quantity[0]" id="quantity"
                                                                        placeholder="Enter the quantity"
                                                                        class="form-control">
                                                                    <span id="quantity-error" style=" display:none;"
                                                                        class="text-danger">Quantity must be less
                                                                        than available quantity.</span>
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
                                            <x-button-cancel
                                                href="{{ admin_url('ohc/discard/list') }}"></x-button-cancel>
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
        // $(document).ready(function() {
        //     var fromDatepicker = flatpickr("#request_date", {
        //         dateFormat: "d-m-Y",
        //         minDate: new Date(),

        //     });
        // });
        $('#medicine_id').on('change', function() {
            var selectedOption = $(this).find(':selected');
            var availableQuantity = selectedOption.data('available-quantity');

            $('#available_quantity').val(availableQuantity);
        });

        $('#quantity').on('input', function() {
            var availableQuantity = parseInt($('#available_quantity').val());
            var quantity = parseInt($(this).val());


            if (quantity > availableQuantity) {
                $('#quantity-error').show();
                $(this).val(availableQuantity);
            } else {
                $('#quantity-error').hide();
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
                                <option value="{{ encryptId($list->id) }}" data-available-quantity="{{ $list->available_quantity }}">{{ $list->medicine_id }}</option>
                            @endforeach
                        </select>
                    </div>
                </td>
                <td>
                    <div class="form-group form-input">
                        <label for="quantity" class="require">Available Quantity</label>
                        <input type="text" name="available_quantity[${medicine_requisition_row_count}]" class="form-control" readonly>
                    </div>
                </td>
                <td>
                    <div class="form-group form-input">
                        <label for="quantity" class="require">Quantity</label>
                        <input type="text" name="quantity[${medicine_requisition_row_count}]"   placeholder="Enter the quantity" class="form-control">
                         <span id="quantity-error" style=" display:none;"  class="text-danger quantity-error">Quantity must be less than available quantity.</span>


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


                    $('select[name="medicine_id[' + medicine_requisition_row_count + ']"]').select2({
                        placeholder: "Select the Medicine Name",
                        width: '100%'
                    });


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
                    filterMedicineOptions();
                    medicine_requisition_row_count++;
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Warning',
                        text: 'Your request has exceeded the limit.',
                        confirmButtonColor: '#3085d6'
                    });
                }
            });

            function filterMedicineOptions() {
                let selectedValues = [];

                // Collect all selected values
                $('select[name^="medicine_id"]').each(function() {
                    let selectedVal = $(this).val();
                    if (selectedVal) {
                        selectedValues.push(selectedVal);
                    }
                });

                $('select[name^="medicine_id"]').each(function() {
                    let currentSelect = $(this);
                    let currentValue = currentSelect.val();

                    currentSelect.find('option').each(function() {
                        let optionValue = $(this).val();

                        // Always enable all options first
                        $(this).prop('disabled', false);

                        // Disable option if it's selected in another dropdown
                        if (selectedValues.includes(optionValue) && optionValue !== currentValue) {
                            $(this).prop('disabled', true);
                        }
                    });
                });
            }




            $(document).on('change', 'select[name^="medicine_id"]', function() {
                var medicine_id = $(this).val();
                var row = $(this).closest('tr'); // Get the row of the current select

                if (medicine_id) {
                    $.ajax({
                        url: "{{ admin_url('ohc/discard/quantity') }}/" + medicine_id,
                        type: 'POST',
                        dataType: 'json',
                        success: function(data) {
                            row.find('input[name^="available_quantity"]').val(data
                                .available_quantity);
                        },
                        error: function() {
                            Swal.fire('Error', 'Something went wrong. Please try again.',
                                'error');
                        }
                    });
                } else {
                    row.find('input[name^="available_quantity"]').val('');
                }
            });

            // Quantity validation
            $(document).on("input", 'input[name^="quantity"]', function() {
                var row = $(this).closest('tr'); // Get the row of the current input
                var availableQuantity = parseInt(row.find('input[name^="available_quantity"]').val());
                var quantity = parseInt($(this).val());

                if (quantity > availableQuantity) {
                    row.find('.quantity-error').show();
                    $(this).val(availableQuantity);
                } else {
                    row.find('.quantity-error').hide();
                }
            });


            $(document).on("click", ".delete-row", function() {
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
