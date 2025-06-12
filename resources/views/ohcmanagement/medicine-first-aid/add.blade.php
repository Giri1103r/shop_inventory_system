@extends('admin.layouts.admin')
@section('title', 'First Aid Box Medicine')
@section('pageurl', admin_url('ohc/medicine-first-aid/list'))
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
                                    <x-button-back href="{{ admin_url('ohc/medicine-first-aid/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="basic-form">
                                    <form method="POST" id="MedicineRequisitionForm" enctype="multipart/form-data"
                                        action="{{ admin_url('ohc/medicine-first-aid/add/submit') }}">
                                        @csrf

                                        <hr>
                                        <div class="row">

                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">{{ __('common.unit') }}</label>
                                                    <input type="text" name ="unit_id" id="unit_id"
                                                        class="form-control" placeholder="Enter the Unit Name"
                                                        value="{{ getUnitname(Auth::user()->unit_id) }}" readonly>
                                                    @error('unit_id')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">{{ __('common.department') }}</label>
                                                    <select name="department_id" id="department_id"
                                                        class="form-control single-select" style="width: 100%">
                                                        <option value="">Select the department</option>
                                                        @foreach ($departmentList as $list)
                                                            <option value="{{ encryptId($list->id) }}">
                                                                {{ $list->department_name }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('department_id')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label for="rate"
                                                        class="form-label require ">{{ __('ohc_management.issued_date') }}</label>
                                                    <div class="input-group date form-input custom-height">
                                                        <input type="text" name="issue_date" id="issue_date"
                                                            class="form-control"autocomplete="off"
                                                            value="{{ date('d-m-Y ') }}" readonly>
                                                        <div class="input-group-addon input-group-text">
                                                            <span class="fa fa-calendar"></span>
                                                        </div>
                                                        @error('issue_date')
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
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
                                                        <tr>
                                                            <td>
                                                                <div class="form-group form-input">
                                                                    <label for="medicine_id" class="require">{{ __('ohc_management.medicine_name') }}</label>
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
                                                                    @error('medicine_id.0')
                                                                        <div class="text-danger">{{ $message }}</div>
                                                                    @enderror
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="form-group form-input">
                                                                    <label for="available_quantity"
                                                                        class="require">{{ __('ohc_management.available_quantity') }}</label>
                                                                    <input type="number" min = "1"
                                                                        name="available_quantity[0]" id="available_quantity"
                                                                        value="" placeholder="Available quantity"
                                                                        class="form-control" readonly>
                                                                    @error('available_quantity.0')
                                                                        <div class="text-danger">{{ $message }}</div>
                                                                    @enderror
                                                                </div>
                                                            </td>

                                                            <td>
                                                                <div class="form-group form-input">
                                                                    <label for="quantity" class="require">{{ __('ohc_management.available_quantity') }}</label>
                                                                    <input type="text" name="quantity[0]" id="quantity"
                                                                        placeholder="Enter the quantity"
                                                                        class="form-control">
                                                                    <span id="quantity-error" style=" display:none;"
                                                                        class="text-danger">Quantity must be less
                                                                        than available quantity.</span>
                                                                    @error('quantity.0')
                                                                        <div class="text-danger">{{ $message }}</div>
                                                                    @enderror
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
                                        <hr>
                                        <div class="submit-button float-end">
                                            <x-button-submit class="submit" id="submit"></x-button-submit>
                                            <x-button-reset class="submit"></x-button-reset>
                                            <x-button-cancel
                                                href="{{ admin_url('ohc/medicine-first-aid/list') }}"></x-button-cancel>
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
        // $(document).ready(function() {
        //     var fromDatepicker = flatpickr("#issue_date", {
        //         dateFormat: "d-m-Y",
        //         minDate: new Date(),

        //     });
        // });

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

            let rowcount = 1;

            $(".add-row").click(function() {
                var rowCount = $('#medicine-tbody tr').length;


                var newRow = `
            <tr>
                <td>
                    <div class="form-group form-input">
                        <label for="medicine_id" class="require">{{ __('ohc_management.medicine_name') }}/label>
                        <select name="medicine_id[${rowcount}]" class="form-control single-select" style="width: 100%">
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
                        <label for="quantity" class="require">{{ __('ohc_management.available_quantity') }}/label>
                        <input type="text" name="available_quantity[${rowcount}]" class="form-control" readonly>

                    </div>
                </td>
                <td>
                    <div class="form-group form-input">
                        <label for="quantity" class="require">{{ __('ohc_management.quantity') }}</label>
                        <input type="number" min = "1" name="quantity[${rowcount}]"   placeholder="Enter the quantity" class="form-control">

                         <span id="quantity-error" style=" display:none;"  class="text-danger quantity-error">Quantity must be less than available quantity.</span>


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
                        digits: 'Quantity must be numeric',
                    }
                });

                rowcount++;
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
                            url: "{{ admin_url('ohc/medicine-first-aid/quantity') }}/" +
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
                    issue_date: {
                        required: true,
                    },
                    'medicine_id[0]': {
                        required: true,
                    },
                    'quantity[0]': {
                        required: true,
                        digits: true,
                    },


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
                    issue_date: {
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
