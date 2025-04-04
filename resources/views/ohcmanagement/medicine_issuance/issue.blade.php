@extends('admin.layouts.admin')
@section('title', 'Medicine Issuance ')
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

                                                </tr>
                                            </thead>
                                            <tbody id="medicine-tbody">
                                                @foreach ($medicine_requisition as $key => $requisition)
                                                    <tr class="medicinedetails">
                                                        <td>
                                                            <input type="hidden" name="encryptid" class="encryptid"
                                                                value="{{ encryptId($requisition->id) }}">
                                                            <div class="form-group form-input">
                                                                <label for="medicine_id" class="require">Medicine
                                                                    Name</label>
                                                                <select name="medicine_id[{{ $key }}]"
                                                                    class="form-control medicine">
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
                                                                <input type="text"
                                                                    name="available_quantity[{{ $key }}]"
                                                                    id="available_quantity"
                                                                    value="{{ $requisition->available_quantity }}"
                                                                    placeholder="Available quantity" class="form-control"
                                                                    readonly>
                                                            </div>
                                                        </td>

                                                        <td>
                                                            <div class="form-group form-input">
                                                                <label for="quantity" class="require">Quantity</label>
                                                                <input type="number" min = "1"
                                                                    name="quantity[{{ $key }}]" id="quantity"
                                                                    placeholder="Enter the quantity"
                                                                    value="{{ $requisition->quantity }}"
                                                                    class="form-control">
                                                                <span id="quantity-error" style=" display:none;"
                                                                    class="text-danger">Quantity must be less
                                                                    than available quantity.</span>
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



            $('.single-select2').select2({

            });
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
                $(".medicine").select2({
                    placeholder: "Select the Medicine Name",
                    width: '100%'
                });
                const MAX_ROWS = 5;
                let rowcount = {{ count($medicine_requisition) }};
                $(".add-row").click(function() {
                    var rowCount = $('#medicine-tbody tr').length;

                    if (rowCount < MAX_ROWS) {
                        var newRow = `
            <tr>
                <td>
                    <div class="form-group form-input">
                        <label class="require">Medicine Name</label>
                        <select name="medicine_id[${rowcount}]" class="form-control single-select" style="width: 100%">
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
                        <input type="text" name="available_quantity[${rowcount}]" class="form-control" readonly>
                    </div>
                </td>
                <td>
                    <div class="form-group form-input">
                        <label class="require">Quantity</label>
                        <inputtype="number" min = "1" name="quantity[${rowcount}]" placeholder="Enter the quantity" class="form-control">
                        <span class="text-danger quantity-error" style="display:none;">Quantity must be less than available quantity.</span>
                    </div>
                </td>
                 <td>
                    <div class="form-group form-input">
                        <label for="remarks" class="">Remarks</label>
                        <textarea name="remarks[${rowcount}]" cols="10" rows="2" class="form-control"></textarea>
                    </div>
                </td>
                <td>
                    <div class="d-flex justify-content-center align-items-center bg-danger mt-2 ml-2 text-white rounded delete-row" style="width: 30px; height: 30px;">
                        <i class="fa-solid fa-trash"></i>
                    </div>
                </td>
            </tr>`;

                        $('#medicine-tbody').append(newRow);

                        $('select[name="medicine_id[' + rowcount + ']"]').select2({
                            placeholder: "Select the Medicine Name",
                            width: '100%'
                        });

                        // Validation rules for new dynamic fields
                        $('select[name="medicine_id[' + rowcount + ']"]').rules('add', {
                            required: true,
                            messages: {
                                required: 'This Medicine name is required'
                            }
                        });

                        $('input[name="quantity[' + rowcount + ']"]').rules('add', {
                            required: true,
                            digits: true,
                            messages: {
                                required: 'Quantity is required',
                                digits: 'Quantity must be numeric'
                            }
                        });

                        rowcount++;
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Warning',
                            text: 'Row limit exceeded.',
                            confirmButtonColor: '#3085d6'
                        });
                    }
                });
                $(document).on('change', 'select[name^="medicine_id"]', function() {
                    var selectedMedicineId = $(this).val();
                    var row = $(this).closest('tr');
                    var duplicateFound = false;

                    $('select[name^="medicine_id"]').each(function() {
                        if ($(this).val() === selectedMedicineId && $(this).attr('name') !== row.find(
                                'select[name^="medicine_id"]').attr('name')) {
                            duplicateFound = true;
                        }
                    });

                    if (duplicateFound) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Duplicate Medicine Selected',
                            text: 'This medicine is already selected. Please choose a different one.',
                            confirmButtonColor: '#3085d6'
                        });

                        $(this).val('').trigger('change');
                        row.find('input[name^="available_quantity"]').val('');
                        row.find('input[name^="quantity"]').val('');
                    } else {
                        if (selectedMedicineId) {
                            $.ajax({
                                url: "{{ admin_url('ohc/medicine-first-aid/editquantity') }}/" +
                                    selectedMedicineId,
                                type: 'get',
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
                    }
                });


                $(document).on("input", 'input[name^="quantity"]', function() {
                    var row = $(this).closest('tr');
                    var availableQuantity = parseInt(row.find('input[name^="available_quantity"]').val());
                    var quantity = parseInt($(this).val());

                    if (quantity > availableQuantity) {
                        row.find('.quantity-error').show();
                        $(this).val(availableQuantity);
                    } else {
                        row.find('.quantity-error').hide();
                    }
                });



            });


            $(document).ready(function() {
                // Initialize validation
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
                        }
                    },
                    messages: {
                        unit_id: {
                            required: "Please select the Unit name.",
                        },
                        department_id: {
                            required: "Please select the Department Name.",
                        },
                        issue_date: {
                            required: "Please select the request date.",
                        }
                    },
                    errorElement: 'span',
                    errorPlacement: function(error, element) {
                        error.addClass('invalid-feedback');
                        element.closest('.form-input').append(error);
                    },
                    highlight: function(element) {
                        $(element).addClass('is-invalid');
                    },
                    unhighlight: function(element) {
                        $(element).removeClass('is-invalid');
                    }
                });

                // Attach validation rules dynamically
                $('#medicine-tbody').find('.medicinedetails').each(function(index, element) {
                    $(element).find('select.medicine').rules("add", {
                        required: true,
                        messages: {
                            required: "Medicine Name is required",
                        }
                    });

                    $(element).find('input[name^="quantity"]').rules("add", {
                        required: true,
                        digits: true,
                        min: 1,
                        messages: {
                            required: "Quantity is required",
                            digits: "Quantity should be numeric",
                            min: "Quantity must be at least 1",
                        }
                    });

                    $(element).find('input[name^="remarks"]').rules("add", {
                        required: true,
                        minlength: 3,
                        maxlength: 600,
                        messages: {
                            required: "Remarks are required",
                            minlength: "Minimum 3 characters required",
                            maxlength: "Remarks should not exceed 600 characters",
                        }
                    });
                });


                $(document).on('change keyup', 'select.medicine, input[name^="quantity"], input[name^="remarks"]',
                    function() {
                        let $field = $(this);
                        $field.valid();


                        let $error = $field.siblings('.error');
                        if ($error.length > 1) {
                            $error.not(':first').remove();
                        }
                    });

            });
        </script>
    @endpush
