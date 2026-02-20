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
                                <div class="align-back-btc d-flex justify-content-end align-items-center">
                                    <x-button-back href="{{ admin_url('ohc/medicine-issuance/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="basic-form">
                                    <form method="POST" id="MedicineRequisitionForm" enctype="multipart/form-data"
                                        action="{{ admin_url('ohc/medicine-issuance/edit/submit') }}">
                                        @csrf
                                        <input type="hidden" name="id" id="id"
                                            value="{{ encryptId($user_medicine_issuance->id) }}">
                                        <hr>
                                        <div class="row">

                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">{{ __('common.unit') }}</label>
                                                    <select name="unit_id" id="unit_id" class="form-control single-select"
                                                        style="width: 100%">
                                                        <option value="">Select the unit</option>

                                                        @foreach ($unit as $list)
                                                            <option @if ($user_medicine_issuance->unit_id == $list->id) selected @endif
                                                                value="{{ encryptId($list->id) }}">
                                                                {{ $list->unit_name }}
                                                            </option>
                                                        @endforeach

                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">{{ __('common.department') }}</label>
                                                    <select name="department_id" id="department_id"
                                                        class=" form-control single-select" style="width: 100%">
                                                        <option value="">Select Department </option>
                                                        @foreach ($departmentList as $department)
                                                            <option @if ($user_medicine_issuance->department_id == $department->id) selected @endif
                                                                value="{{ encryptId($department->id) }}">
                                                                {{ $department->department_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label for="rate"
                                                        class="form-label require ">{{ __('ohc_management.issued_date') }}</label>
                                                    <div class="input-group date form-input custom-height">
                                                        <input type="text" name="issue_date" id="issue_date"
                                                            class="form-control"autocomplete="off"
                                                            value="{{ displaydateformat($user_medicine_issuance->issue_date) }}"
                                                            readonly>
                                                        <div class="input-group-addon input-group-text">
                                                            <span class="fa fa-calendar"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="row mt-2">
                                            <div class="card-header-inner">
                                                <h4 class="text-white">{{ __('ohc_management.medicine_details') }}</h4>

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
                                                            <th>{{ __('ohc_management.medicine_name') }}</th>
                                                            <th>{{ __('ohc_management.available_quantity') }}</th>
                                                            <th>{{ __('ohc_management.quantity') }}</th>
                                                            <th>{{ __('common.action') }}</th>
                                                        </tr>
                                                    </thead>

                                                    <tbody id="medicine-tbody">
                                                        @foreach ($medicine_issuance as $key => $issuance)
                                                            <tr class="medicinedetails">
                                                                <td>
                                                                    <input type="hidden" name="encryptid" class="encryptid"
                                                                        value="{{ encryptId($issuance->id) }}">
                                                                    <div class="form-group form-input">
                                                                        <label for="medicine_id"
                                                                            class="require">{{ __('ohc_management.medicine_name') }}</label>
                                                                        <select name="medicine_id[{{ $key }}]"
                                                                            class="form-control medicine">
                                                                            <option value="">Select the Medicine Name
                                                                            </option>

                                                                            @foreach ($medicine as $list)
                                                                                <option value="{{ $list->medicine_id }}"
                                                                                    @if ($issuance->medicine_id == $list->medicine_id) selected @endif>
                                                                                    {{ getMedicinename($list->medicine_id) }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="form-group form-input">
                                                                        <label for="available_quantity"
                                                                            class="require">{{ __('ohc_management.available_quantity') }}</label>
                                                                        <input type="text"
                                                                            name="available_quantity[{{ $key }}]"
                                                                            id="available_quantity"
                                                                            value="{{ $issuance->available_quantity }}"
                                                                            placeholder="Available quantity"
                                                                            class="form-control" readonly>
                                                                    </div>
                                                                </td>

                                                                <td>
                                                                    <div class="form-group form-input">
                                                                        <label for="quantity"
                                                                            class="require">{{ __('ohc_management.quantity') }}</label>
                                                                        <input type="number" min = "1"
                                                                            name="quantity[{{ $key }}]"
                                                                            id="quantity" placeholder="Enter the quantity"
                                                                            value="{{ $issuance->quantity }}"
                                                                            class="form-control">
                                                                        <span id="quantity-error" style=" display:none;"
                                                                            class="text-danger">Quantity must be less
                                                                            than available quantity.</span>
                                                                    </div>
                                                                </td>

                                                                <td>
                                                                    <div class="d-flex justify-content-center align-items-center bg-danger mt-2 me-5 text-white rounded delete-row"
                                                                        style="width: 30px; height: 30px;">
                                                                        <i class="fa-solid fa-trash"></i>
                                                                    </div>


                                                                </td>
                                                                <input type="hidden" name="deletedPage" id="deletedPage"
                                                                    value="[]">
                                                                <input type="hidden" name="deletedMedicine"
                                                                    id="deletedMedicine" value="[]">
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
        </div>

    </div>

@stop

@push('script')
    <script>
        $(document).ready(function() {
            $('#resetform').on('click', function(e) {
                e.preventDefault();
                location.reload();

            });
        });


        let deletedPages = [];
        let deletedMedicine = [];

        $(document).on('click', '.delete-row', function(event) {
            event.preventDefault();

            var row = $(this).closest(".medicinedetails");
            var rowId = row.find("input[name='encryptid']").val();
            var totalRows = $(".medicinedetails").length;
            var MedicineId = row.find("select[name^='medicine_id']").val();

            if (totalRows > 1) {
                Swal.fire({
                    title: "Are you sure?",
                    text: "Do you want to delete this medicine from the list?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Yes, delete it!",
                    cancelButtonText: "No, cancel!",
                }).then((result) => {
                    if (result.isConfirmed) {

                        deletedPages.push(rowId);
                        deletedMedicine.push(MedicineId);

                        $('#deletedPage').val(JSON.stringify(deletedPages));
                        $('#deletedMedicine').val(JSON.stringify(deletedMedicine));

                        row.remove();

                        Swal.fire("Deleted!", "The medicine has been removed from the list.", "success");
                    }
                });
            } else {
                Swal.fire({
                    title: "Warning!",
                    text: "At least one row must remain!",
                    icon: "error",
                });
            }
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
            let rowcount = {{ count($medicine_issuance) }};
            $(".add-row").click(function() {
                var rowCount = $('#medicine-tbody tr').length;

                if (rowCount < MAX_ROWS) {
                    var newRow = `
            <tr>
                <td>
                    <div class="form-group form-input">
                        <label class="require">{{ __('ohc_management.medicine_name') }}</label>
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
                        <label class="require">{{ __('ohc_management.available_quantity') }}</label>
                        <input type="text" name="available_quantity[${rowcount}]" class="form-control" readonly>
                    </div>
                </td>
                <td>
                    <div class="form-group form-input">
                        <label class="require">{{ __('ohc_management.quantity') }}</label>
                        <input type="number" min = "1" name="quantity[${rowcount}]" placeholder="Enter the quantity" class="form-control">
                        <span class="text-danger quantity-error" style="display:none;">Quantity must be less than available quantity.</span>
                    </div>
                </td>

                <td>
                    <div class="d-flex justify-content-center align-items-center bg-danger mt-2 ml-2 text-white rounded deleted-row" style="width: 30px; height: 30px;">
                        <i class="fa-solid fa-trash"></i>
                    </div>
                </td>
            </tr>`;
                    $(document).on("click", ".deleted-row", function() {
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
