@extends('admin.layouts.admin')
@section('title', ' Daily Departmental First Aid Box ')
@section('pageurl', admin_url('ohc/first-aid-box/daily-departmental/list'))


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
                                {{-- <h4 class="card-title">{{ __('master.company_add') }}</h4> --}}
                                <div class="align-back-btc">
                                    <x-button-back
                                        href="{{ admin_url('ohc/first-aid-box/daily-departmental/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form">
                                    <form method="POST" id="medicineRequisitionFloor"
                                        action="{{ admin_url('ohc/first-aid-box/daily-departmental/add/submit') }}"
                                        autocomplete="off" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" value="3" name="ohc_type">
                                        <div class="row">
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Document Number</label>
                                                    <input type="text" name="document_no" id = "document_no"
                                                        class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label for="rate" class="form-label require ">Issued
                                                        Date</label>
                                                    <div class="input-group date form-input custom-height">
                                                        <input type="text" name="issue_date" id="issue_date"
                                                            class="form-control"autocomplete="off">
                                                        <div class="input-group-addon input-group-text">
                                                            <span class="fa fa-calendar"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label for="rate" class="form-label require ">Review
                                                        Date</label>
                                                    <div class="input-group date form-input custom-height">
                                                        <input type="text" value="{{ getDocumentReviewDate('0') }}"
                                                            name="review_date" id="review_date" class="form-control"
                                                            autocomplete="off" readonly>

                                                        <div class="input-group-addon input-group-text">
                                                            <span class="fa fa-calendar"></span>
                                                        </div>
                                                    </div>
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
                                                    <label class="form-label require">Shift</label>
                                                    <select name="shift" id="shift" style="width: 100%"
                                                        class="form-control single-select">
                                                        <option value="">Select the option</option>
                                                        @foreach ($shift as $list)
                                                            <option value="{{ encryptId($list->id) }}">{{ $list->shift }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">First Aid Box Number</label>
                                                    <input type="text" name="first_aid_box_no" id = "first_aid_box_no"
                                                        class="form-control">
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">First Aider Name</label>
                                                    <select name="first_aider" id="first_aider" class="form-control single-select"
                                                        style="width: 100%">
                                                        <option value="">Select the option</option>
                                                        @foreach ($First_aid as $list)
                                                            <option value="{{ encryptId($list->id) }}">
                                                                {{ $list->certifier_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label for="rate" class="form-label require ">
                                                        Date</label>
                                                    <div class="input-group date form-input custom-height">
                                                        <input type="text" name="date" id="date"
                                                            class="form-control"autocomplete="off">
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
                                                class="d-flex justify-content-end align-items-center me-2 mb-3 button-container">
                                                <button class="btn btn-primary add-row me-3" type="button"
                                                    id="add-row" style="width: 84px;">
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
                                                            <th>Freeze Quantity</th>
                                                            <th>Material Expiry</th>
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
                                                                            <option
                                                                                value="{{ encryptId($list->medicine_id) }}">
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
                                                                    <input type="text" name="available_quantity[0]"
                                                                        id="available_quantity" value=""
                                                                        placeholder="Available quantity"
                                                                        class="form-control" >
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="form-group form-input">
                                                                    <label for="quantity" class="require">Freeze
                                                                        Quantity</label>
                                                                    <input type="text" name="freeze_quantity[0]"
                                                                        id="freeze_quantity"
                                                                        placeholder="Enter the Freeze quantity"
                                                                        class="form-control">

                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="form-group form-input">
                                                                    <label for="quantity" class="require">Material
                                                                        Expiry</label>
                                                                    <input type="text" name="material_expiry[0]"
                                                                        id="material_expiry" placeholder=""
                                                                        class="form-control">

                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="form-group form-input">
                                                                    <label for="remarks"class="">Remarks</label>
                                                                    <textarea name="remarks[0]" id="remarks" cols="10" rows="2" class="form-control"></textarea>
                                                                </div>
                                                            </td>
                                                            <td>

                                                                <div class="d-flex justify-content-center align-items-center bg-danger mt-2 ml-2 text-white rounded delete-row"
                                                                    style="width: 30px; height: 30px;">
                                                                    <i class="fa-solid fa-trash"></i>
                                                                </div>


                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                </div>
                                <hr>
                                <div class="submit-button" style="text-align: right;">
                                    <x-button-submit class="submit"></x-button-submit>
                                    <x-button-reset class="submit"></x-button-reset>
                                    <x-button-cancel
                                        href="{{ admin_url('ohc/first-aid-box/daily-departmental/list') }}"></x-button-cancel>
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
        });
        var IssueDatepicker = flatpickr("#issue_date", {
            dateFormat: "d-m-Y",
            minDate: new Date()

        });
        var Datepicker = flatpickr("#date_of_inspection", {
            dateFormat: "d-m-Y",
            minDate: new Date()

        });
        var dueDate = flatpickr("#next_due_on", {
            dateFormat: "d-m-Y",
            minDate: new Date()

        });
        var dueDate = flatpickr("#date", {
            dateFormat: "d-m-Y",
            minDate: new Date()

        });
        var materialExpiryDatepicker = flatpickr("#material_expiry", {
            dateFormat: "d-m-Y",


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

            let medicine_requisition_row_count = 1;



            $(".add-row").click(function() {
                var rowCount = $('#medicine-tbody tr').length;


                var newRow = `
            <tr>
                <td>
                    <div class="form-group form-input">
                        <label for="medicine_id" class="require">Medicine Name</label>
                        <select name="medicine_id[${medicine_requisition_row_count}]" class="form-control single-select" style="width: 100%">
                            <option value="">Select the Medicine Name</option>
                            @foreach ($medicine as $list)
                                                                            <option value="{{ encryptId($list->medicine_id) }}">
                                                                                {{ getMedicinename($list->medicine_id) }}
                                                                            </option>
                                                                        @endforeach
                        </select>
                    </div>
                </td>
                <td>
                    <div class="form-group form-input">
                        <label for="quantity" class="require">Available Quantity</label>
                        <input type="text" name="available_quantity[${medicine_requisition_row_count}]"   placeholder="Enter the quantity" class="form-control">
                         <span id="quantity-error" style=" display:none;"  class="text-danger quantity-error">Quantity must be less than available quantity.</span>


                    </div>
                </td>
  <td>
                    <div class="form-group form-input">
                        <label for="quantity" class="require">Freeze Quantity</label>
                        <input type="text" name="freeze_quantity[${medicine_requisition_row_count}]"   placeholder="Enter the quantity" class="form-control">
                         <span id="quantity-error" style=" display:none;"  class="text-danger quantity-error">Quantity must be less than available quantity.</span>


                    </div>
                </td>
                <td>
                    <div class="form-group form-input">
                        <label for="quantity" class="require">Material Expiry</label>
                        <input type="text" name="material_expiry[${medicine_requisition_row_count}]"  id="material_expiry"   class="form-control">



                    </div>
                </td>
                <td>
                    <div class="form-group form-input">
                        <label for="remarks" class="">Remarks</label>
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
                $('input[name="available_quantity[' + medicine_requisition_row_count + ']"]').rules('add', {
                    required: true,
                    digits: true,
                    messages: {
                        required: 'Available Quantity is required',
                        digits: 'Available Quantity must be numeric',
                    }
                });
                $('input[name="material_expiry[' + medicine_requisition_row_count + ']"]').rules('add', {
                    required: true,

                    messages: {
                        required: 'material_expiry is required',

                    }
                });
                $('input[name="freeze_quantity[' + medicine_requisition_row_count + ']"]').rules('add', {
                    required: true,
                    digits: true,
                    messages: {
                        required: 'Freeze Quantity is required',
                        digits: 'Freeze Quantity must be numeric',
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
                var materialExpiryDatepicker = flatpickr("#material_expiry", {
                    dateFormat: "d-m-Y",


                });
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
                var selectedMedicineId = $(this).val();
                var row = $(this).closest('tr');
                var duplicateFound = false;


                $('select[name^="medicine_id"]').not(this).each(function() {
                    if ($(this).val() === selectedMedicineId && selectedMedicineId !== "") {
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
                    row.find('input[name^="freeze_quantity"]').val('');

                    row.find('input[name^="quantity"]').val('');
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

            $('#medicineRequisitionFloor').validate({
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
                    document_no: {
                        required: true,
                        minlength: 3,
                        maxlength: 30,
                    },
                    first_aider: {
                        required: true,

                    },
                    first_aid_box_no: {
                        required: true,
                        digits: true,
                    },
                    shift: {
                        required: true,
                    },
                    review_date: {
                        required: true,
                    },
                    date: {
                        required: true,
                    },
                    'medicine_id[0]': {
                        required: true,
                    },
                    'material_expiry[0]': {
                        required: true,

                    },
                    'available_quantity[0]': {
                        required: true,
                        digits: true,
                    },
                    'freeze_quantity[0]': {
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
                    issue_date: {
                        required: "Please select the issue date.",
                    },
                    shift: {
                        required: "Shift is required",
                    },
                    date: {
                        required: "Please select the  date.",
                    },
                    document_no: {
                        required: "Document Number is Required",
                        minlength: "Minimum Characters should be 3",
                        maxlength: "Maximum Characters should not exceed 100",
                    },
                    first_aider: {
                        required: "First Aider is Required",
                       
                    },
                    first_aid_box_no: {
                        required: 'First Aid Box Number is required',
                        digits: 'First Aid Box Number should be numeric',
                    },
                    review_date: {
                        required: "Please select the request date.",
                    },
                    'medicine_id[0]': {
                        required: 'Medicine Name is required',
                    },
                    'freeze_quantity[0]': {
                        required: 'Freeze Quantity is required',
                        digits: 'Freeze Quantity should be numeric',
                    },
                    'available_quantity[0]': {
                        required: 'Available Quantity is required',
                        digits: 'Available Quantity should be numeric',
                    },
                    'material_expiry[0]': {
                        required: 'material expiry is required',

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
