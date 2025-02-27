@extends('admin.layouts.admin')
@section('title', 'Medicine Requisition Edit')
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
                                        action="{{ admin_url('ohc/medicine-requisition/edit/submit') }}">
                                        @csrf
                                        <input type="hidden" name="id" id="id"
                                            value="{{ encryptId($user_medicine_requisition->id) }}">
                                        <hr>
                                        <div class="row">

                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Requestion ID</label>
                                                    <input type="text" name ="req_id" id="req_id" class="form-control"
                                                        placeholder="Requistion ID"
                                                        value="{{ $user_medicine_requisition->req_id }}" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Unit </label>
                                                    <select name="unit_id" id="unit_id"
                                                        class=" form-control single-select" style="width: 100%">
                                                        <option value="">Select Unit</option>
                                                        @foreach ($unit as $unit)
                                                            <option @if ($user_medicine_requisition->unit_id == $unit->id) selected @endif
                                                                value="{{ encryptId($unit->id) }}">
                                                                {{ $unit->unit_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label for="department_id" class="form-label require">Department
                                                    </label>
                                                    <select name="department_id" id="department_id"
                                                        class=" form-control single-select" style="width: 100%">
                                                        <option value="">Select Department </option>
                                                        @foreach ($departmentList as $department)
                                                            <option @if ($user_medicine_requisition->department_id == $department->id) selected @endif
                                                                value="{{ encryptId($department->id) }}">
                                                                {{ $department->department_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label for="rate" class="form-label require ">Request
                                                        Date</label>
                                                    <input type="text" name="request_date" id="request_date"
                                                        value="{{ displaydateformat($user_medicine_requisition->request_date) }}"
                                                        class="form-control">
                                                </div>
                                            </div>

                                        </div>
                                        {{-- <div class="table-responsive">
                                            <div class="col-md-12">
                                                <table class="table table-bordered ">

                                                    <thead class="bg-secondary" style="color: #ffff">
                                                    <tr>
                                                        <th>Medicine</th>
                                                        <th>Quantity</th>
                                                        <th>Remarks</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="medicine-tbody">
                                                    @foreach ($medicine_requisition as $key => $requisition)
                                                        <tr>
                                                            <td>
                                                                <div class="form-group form-input">
                                                                    <label for="medicine_id" class="require">Medicine Name</label>
                                                                    <select name="medicine_id[{{ $key }}]" id="medicine_id" class="form-control single-select" style="width: 100%">
                                                                        <option value="">Select the Medicine Name</option>
                                                                        @foreach ($medicine as $list)
                                                                            <option value="{{ $list->id }}" @if ($requisition->medicine_id == $list->id) selected @endif>
                                                                                {{ $list->medicine }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="form-group form-input">
                                                                    <label for="quantity" class="require">Quantity</label>
                                                                    <input type="text" name="quantity[{{ $key }}]" id="quantity" placeholder="Enter the quantity" class="form-control" value="{{ $requisition->quantity }}">
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="form-group form-input">
                                                                    <label for="remarks" class="require">Remarks</label>
                                                                    <textarea name="remarks[{{ $key }}]" id="remarks" cols="10" rows="2" class="form-control">{{ $requisition->remarks }}</textarea>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="row gap-2">
                                                                    <div class="d-flex justify-content-center align-items-center bg-primary mt-2 ml-2 text-white rounded add-row" style="width: 30px; height: 30px;">
                                                                        <i class="fa-solid fa-plus"></i>
                                                                    </div>
                                                                    <div class="d-flex justify-content-center align-items-center bg-danger mt-2 ml-2 text-white rounded delete-row" style="width: 30px; height: 30px;">
                                                                        <i class="fa-solid fa-trash"></i>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>

                                            </table>
                                        </div> --}}

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
                                                                                <option value="{{ $list->medicine_id }}"
                                                                                    @if ($requisition->medicine_id == $list->medicine_id) selected @endif>
                                                                                    {{ getMedicinename($list->medicine_id) }}
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
                                                                        <input type="text"
                                                                            name="available_quantity[{{ $key }}]"
                                                                            id="available_quantity"
                                                                            value="{{ $requisition->available_quantity }}"
                                                                            placeholder="Available quantity"
                                                                            class="form-control" readonly>
                                                                    </div>
                                                                </td>

                                                                <td>
                                                                    <div class="form-group form-input">
                                                                        <label for="quantity"
                                                                            class="require">Quantity</label>
                                                                        <input type="text"
                                                                            name="quantity[{{ $key }}]"
                                                                            id="quantity"
                                                                            placeholder="Enter the quantity"
                                                                            value="{{ $requisition->quantity }}"
                                                                            class="form-control">
                                                                        <span id="quantity-error" style=" display:none;"
                                                                            class="text-danger">Quantity must be less
                                                                            than available quantity.</span>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="form-group form-input">
                                                                        <label for="remarks"
                                                                            class="">Remarks</label>
                                                                        <textarea name="remarks[{{ $key }}]" id="remarks" cols="10" rows="2" class="form-control">{{ $requisition->remarks }}</textarea>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="row gap-2">

                                                                        <div class="d-flex justify-content-center align-items-center bg-danger mt-2 me-5 text-white rounded delete-row"
                                                                            style="width: 30px; height: 30px;">
                                                                            <i class="fa-solid fa-trash"></i>
                                                                        </div>
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
        $(document).ready(function() {

            var initialUnitId = $('#unit_id').val();
            var preselectedDepartmentId = "{{ encryptId($user_medicine_requisition->department_id) ?? '0' }}";


            if (initialUnitId) {
                fetchDepartments(initialUnitId, preselectedDepartmentId, function() {
                    var department_id = preselectedDepartmentId;

                });

            }

            $('#unit_id').on('change', function() {
                var unit_id = $(this).val();
                fetchDepartments(unit_id, preselectedDepartmentId, function() {
                    $('#department_id').trigger('change');
                });

            });


            function fetchDepartments(unit_id, preselectedDepartmentId, callback) {
                if (unit_id) {
                    $.ajax({
                        url: "{{ admin_url('department/ajax-list/') }}" + unit_id + '/' +
                            preselectedDepartmentId,
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            $('#department_id').empty().append(
                                '<option value="">Select Department</option>');
                            $.each(data, function(key, value) {
                                var selected = (value.id == preselectedDepartmentId) ?
                                    'selected' : '';
                                $('#department_id').append('<option value="' + value.id + '" ' +
                                    selected + '>' + value.name + '</option>');
                            });
                            if (callback) callback();
                        }
                    });
                } else {
                    $('#department_id').empty().append('<option value="">Select Department</option>');
                }
            }
        });


        // delete the row

        $(document).on('click', '.delete-row', function(event) {
            event.preventDefault(); // Prevents the form from submitting

            var row = $(this).closest(".medicinedetails");
            var rowId = row.find("input[name='encryptid']").val();

            if (rowId) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'Do you want to delete this record?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'No, keep it'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ url('ohc/medicine-requisition/delete') }}/" +
                                rowId,
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                _method: 'POST',
                                id: rowId
                            },
                            success: function(response) {
                                if (response.status === 'success') {
                                    row.remove();
                                    Swal.fire('Deleted!', response.msg, 'success');
                                } else {
                                    Swal.fire('Error!', response.msg, 'error');
                                }
                            },
                            error: function() {
                                Swal.fire('Error!',
                                    'Something went wrong. Please try again later.',
                                    'error');
                            }
                        });
                    }
                });
            } else {
                $(this).closest("tr").remove();
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
                        <input type="text" name="quantity[${rowcount}]" placeholder="Enter the quantity" class="form-control">
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
                    @foreach ($medicine_requisition as $key => $requistion)
                        'medicine_id[{{ $key }}]': {
                            required: true
                        },
                        'quantity[{{ $key }}]': {
                            required: true,
                            required: true,
                            digits: true,
                        },
                        // 'remarks[{{ $key }}]': {

                        //     required: true,
                        //     minlength: 3,
                        //     maxlength: 600,
                        // },
                    @endforeach

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
