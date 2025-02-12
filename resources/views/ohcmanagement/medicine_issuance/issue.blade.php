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
                                                    <select name="unit_id" id="unit_id" class="form-control single-select"
                                                        style="width: 100%">
                                                        <option value="">Select the unit</option>
                                                        @foreach ($unit as $unit)
                                                            <option @if ($user_medicine_requisition->unit_id == $unit->id) selected @endif
                                                                value="{{ encryptId($unit->id) }}">
                                                                {{ $unit->unit_name }}</option>
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
                                                                <select name="medicine_id[0]" id="medicine_id"
                                                                    class="form-control single-select" style="width: 100%">
                                                                    <option value="">Select the Medicine Name
                                                                    </option>
                                                                    @foreach ($medicine as $list)
                                                                        <option value="{{ encryptId($list->id) }}"
                                                                            @if ($requisition->medicine_id == $list->id) selected @endif>
                                                                            {{ $list->medicine_id }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="form-group form-input">
                                                                <label for="available_quantity" class="require">Available
                                                                    Quantity</label>
                                                                <input type="text" name="available_quantity[0]"
                                                                    id="available_quantity"
                                                                    value="{{ $requisition->available_quantity }}"
                                                                    placeholder="Available quantity" class="form-control"
                                                                    readonly>
                                                            </div>
                                                        </td>

                                                        <td>
                                                            <div class="form-group form-input">
                                                                <label for="quantity" class="require">Quantity</label>
                                                                <input type="text" name="quantity[0]" id="quantity"
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
                let medicine_issuance_row_count = {{ count($medicine_requisition) }};;

                $(".add-row").click(function() {
                    var rowCount = $('#medicine-tbody tr').length;

                    if (rowCount < MAX_ROWS) {
                        var newRow = `
<tr>
    <td>
        <div class="form-group form-input">
            <label for="medicine_id" class="require">Medicine Name</label>
            <select name="medicine_id[${medicine_issuance_row_count}][]" class="form-control" style="width: 100%">
                <option value="">Select the Medicine Name</option>
                @foreach ($medicine as $list)
                    <option value="{{ $list->id }}">{{ $list->medicine_id }}</option>
                @endforeach
            </select>
        </div>
    </td>
    <td>
        <div class="form-group form-input">
            <label for="quantity" class="require">Quantity</label>
            <input type="text" name="available_quantity[${medicine_issuance_row_count}][]" placeholder="Enter the Available quantity" class="form-control" readonly>
        </div>
    </td>
    <td>
        <div class="form-group form-input">
            <label for="remarks" class="require">Quantity</label>
      <input type="text" name="quantity[${medicine_issuance_row_count}][]" placeholder="Enter the  quantity" class="form-control" >
        </div>
    </td>
    <td>
       <div class="d-flex justify-content-center align-items-center bg-danger mt-2 ml-2 text-white rounded delete-row" style="width: 30px; height: 30px;">
            <a href="javascript:void(0);" data-id="" class="recordDelete " title="Delete">
                <i class="fa-solid fa-trash text-white"></i>
            </a>
    </div>

    </td>
</tr>`;

                        $('#medicine-tbody').append(newRow);


                        $('select[name="medicine_id[' + medicine_issuance_row_count + '][]"]').select2({
                            placeholder: "Select the Medicine Name",
                            width: '100%'
                        });
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

                        filterMedicineOptions();
                        medicine_issuance_row_count++;
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


                            $(this).prop('disabled', false);


                            if (selectedValues.includes(optionValue) && optionValue !== currentValue) {
                                $(this).prop('disabled', true);
                            }
                        });
                    });
                }


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
