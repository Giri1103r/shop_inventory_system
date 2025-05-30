@extends('admin.layouts.admin')
@section('title', ' Medical Requisition Slip- Floor ')
@section('pageurl', admin_url('ohc/medical-requisition-slip/list'))


@section('content')
    <div class="clearfix"></div>
    <div class="page-titles">
        <div class="d-flex align-items-center">
            {{-- <h4 class="text-black">Company Add</h4> --}}

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
                                        href="{{ admin_url('ohc/medical-requisition-slip/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form">
                                    <form method="POST" id="medicineRequisitionFloor"
                                        action="{{ admin_url('ohc/medical-requisition-slip/add/submit') }}"
                                        autocomplete="off" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" value="2" name="ohc_type">
                                        <div class="row">
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">{{ __('inspection.doc_no') }}</label>
                                                    <input type="text" name="document_no" id = "document_no"
                                                        class="form-control" placeholder="Enter the Document Number"
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
                                                    <div class="input-group date form-input custom-height">
                                                        <input type="text" name="issue_date" id="issue_date"
                                                            class="form-control"autocomplete="off"
                                                            value="{{ displaydateformat($document_no->issue_date) }}"
                                                            readonly>
                                                        <div class="input-group-addon input-group-text">
                                                            <span class="fa fa-calendar"></span>
                                                        </div>
                                                    </div>
                                                </div>

                                                @error('issue_date')
                                                    <div class="error">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label require">{{ __('inspection.rev_date') }}</label>
                                                    <input type="text" name="review_date" id = "review_date"
                                                        class="form-control" value="{{ $document_no->rev_dt }}" readonly>
                                                </div>
                                            </div>
                                            <input type="hidden" name="document_reference_id"
                                                value="{{ encryptId($document_no->id) }}">
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
                                                <button class="btn btn-primary add-row me-3" type="button" id="add-row"
                                                    style="width: 84px;">
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
                                                            <th>Freeze Quantity</th>
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
                                                                        class="form-control medicine_id single-select"
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
                                                                    <label for="quantity" class="require">Freeze
                                                                        Quantity</label>
                                                                    <input type="text" name="freeze_quantity[0]"
                                                                        id="freeze_quantity"
                                                                        placeholder="Enter the Freeze quantity"
                                                                        class="form-control freeze_quantity" readonly>

                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="form-group form-input">
                                                                    <label for="quantity" class="require">Quantity</label>
                                                                    <input type="number" min="1"
                                                                        name="quantity[0]" id="quantity"
                                                                        placeholder="Enter the quantity"
                                                                        class="form-control">
                                                                    <span id="quantity-error" style=" display:none;"
                                                                        class="text-danger">Quantity must be less
                                                                        than available quantity.</span>
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

                                        {{-- @if ($signature_upload->signature_upload != '')
                                            <label class="form-label view_label">Requestor Signature</label>

                                            <p>
                                                <a href="{{ asset($signature_upload->signature_upload) }}"
                                                    target="_blank">
                                                    <img src="{{ asset($signature_upload->signature_upload) }}"
                                                        style="width: 100px" alt="image">
                                                </a>
                                            </p>
                                        @else
                                            <div class="col-md-4 mb-3">
                                                <div class="form-group form-input">
                                                    <label for="signature_image"
                                                        class="form-label fw-bold require">Requestor
                                                        Signature</label>
                                                    <input type="file"
                                                        class="form-control validate-file-accept validate-file-required"
                                                        accept="image/png, image/jpeg, image/jpg" name="signature_image"
                                                        id="signature_image">
                                                    <div class="text-danger"></div>
                                                    <small>Allowed file types: png, jpeg, jpg</small>

                                                    <!-- Preview Container -->
                                                    <div id="imagePreviewContainer" class="mt-2"
                                                        style="display: none;">
                                                        <img id="imagePreview" src="#" alt="Signature Preview"
                                                            class="img-thumbnail" width="200">
                                                    </div>
                                                </div>
                                            </div>
                                        @endif --}}
                                </div>
                                <hr>
                                <div class="submit-button" style="text-align: right;">
                                    <x-button-submit class="submit"></x-button-submit>
                                    <x-button-reset class="submit"></x-button-reset>
                                    <x-button-cancel
                                        href="{{ admin_url('ohc/medical-requisition-slip/list') }}"></x-button-cancel>
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
        $(document).on('change', '.medicine_id', function() {
            var $row = $(this).closest('tr'); // Get current row
            var medicineId = $(this).val();

            if (medicineId) {
                $.ajax({
                    url: "{{ admin_url('ohc/medical-requisition-slip/freeze-medicine-quantity') }}",
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        medicineId: medicineId
                    },
                    success: function(data) {
                        if (data && data.freeze_quantity !== undefined) {
                            $row.find('.freeze_quantity').val(data.freeze_quantity);
                        } else {
                            $row.find('.freeze_quantity').val('');
                        }
                    },
                    error: function(xhr) {
                        alert('Error fetching freeze quantity. Please try again.');
                    }
                });
            } else {
                $row.find('.freeze_quantity').val('');
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
                            <select name="medicine_id[${medicine_requisition_row_count}]" class="form-control medicine_id single-select" style="width: 100%">
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
                            <label for="quantity" class="require">Freeze Quantity</label>
                            <input type="text" name="freeze_quantity[${medicine_requisition_row_count}]"   placeholder="Enter the quantity" class="form-control freeze_quantity" readonly>
                            <span id="quantity-error" style=" display:none;"  class="text-danger quantity-error">Quantity must be less than available quantity.</span>


                        </div>
                    </td>
                    <td>
                        <div class="form-group form-input">
                            <label for="quantity" class="require">Quantity</label>
                            <input type="number" min="1" name="quantity[${medicine_requisition_row_count}]"   placeholder="Enter the quantity" class="form-control">
                            <span id="quantity-error" style=" display:none;"  class="text-danger quantity-error">Quantity must be less than available quantity.</span>


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

                $('input[name="quantity[' + medicine_requisition_row_count + ']"]').rules('add', {
                    required: true,
                    digits: true,
                    messages: {
                        required: 'Quantity is required',
                        digits: 'Quantity must be numeric',
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

                $(document).on('change', '.medicine_id', function() {
                    var $row = $(this).closest('tr'); // Get current row
                    var medicineId = $(this).val();

                    if (medicineId) {
                        $.ajax({
                            url: "{{ admin_url('ohc/medical-requisition-slip/freeze-medicine-quantity') }}",
                            type: 'GET',
                            dataType: 'json',
                            data: {
                                medicineId: medicineId
                            },
                            success: function(data) {
                                if (data && data.freeze_quantity !== undefined) {
                                    $row.find('.freeze_quantity').val(data
                                        .freeze_quantity);
                                } else {
                                    $row.find('.freeze_quantity').val('');
                                }
                            },
                            error: function(xhr) {
                                alert(
                                    'Error fetching freeze quantity. Please try again.'
                                    );
                            }
                        });
                    } else {
                        $row.find('.freeze_quantity').val('');
                    }
                });
                medicine_requisition_row_count++;

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
                    row.find('input[name^="quantity"]').val('');
                } else {

                    if (selectedMedicineId) {
                        $.ajax({
                            url: "{{ admin_url('ohc/medicine-requisition/quantity') }}/" +
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
                    review_date: {
                        required: true,
                    },
                    date: {
                        required: true,
                    },
                    signature_image:{
                        filesize: 15728640,
                    },
                    'medicine_id[0]': {
                        required: true,
                    },
                    'quantity[0]': {
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
                    date: {
                        required: "Please select the  date.",
                    },
                    document_no: {
                        required: "Document Number is Required",
                        minlength: "Minimum Characters should be 3",
                        maxlength: "Maximum Characters should not exceed 100",
                    },
                    review_date: {
                        required: "Please select the request date.",
                    },
                    signature_image:{
                        filesize: "File size must be less than 15MB."
                    },
                    'medicine_id[0]': {
                        required: 'Medicine Name is required',
                    },
                    'freeze_quantity[0]': {
                        required: 'Freeze Quantity is required',
                        digits: 'Freeze Quantity should be numeric',
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
        document.getElementById("signature_image").addEventListener("change", function(event) {
            let file = event.target.files[0];

            if (file) {
                let reader = new FileReader();

                reader.onload = function(e) {
                    document.getElementById("imagePreview").src = e.target.result;
                    document.getElementById("imagePreviewContainer").style.display = "block";
                };

                reader.readAsDataURL(file);
            } else {
                document.getElementById("imagePreviewContainer").style.display = "none";
            }
        });
    </script>
@endpush
