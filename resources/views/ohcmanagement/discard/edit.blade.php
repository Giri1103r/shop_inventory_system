@extends('admin.layouts.admin')
@section('title', 'Discard the Expire Medicine Edit')
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
                                        action="{{ admin_url('ohc/discard/edit/submit') }}">
                                        @csrf
                                        <input type="hidden" name="id" id="id"
                                            value="{{ encryptId($user_discard->id) }}">
                                        <input type="hidden" name="medicineid" id="medicineid"
                                            value="{{ encryptId($discard->id) }}">
                                        <hr>
                                        <div class="row">


                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Unit</label>
                                                    <input type="text" name="unit_id" id="unit_id" class="form-control"
                                                        placeholder="Enter the Unit Name"
                                                        value="{{ getUnitname($user_discard->unit_id) }}" readonly>
                                                    <input type="hidden" name="hidden_unit_id" id="hidden_unit_id"
                                                        class="form-control" value="{{ encryptId($user_discard->unit_id) }}"
                                                        data-url="{{ admin_url('department/ajax-list') }}" readonly>
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Department</label>
                                                    <select name="department_id" id="department_id"
                                                        class="form-control single-select" style="width: 100%">
                                                        <option value="">Select Department</option>
                                                        @foreach ($departmentList as $department)
                                                            <option value="{{ encryptId($department->id) }}"
                                                                @if (encryptId($department->id) == encryptId($user_discard->department_id)) selected @endif>
                                                                {{ $department->department_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label for="rate" class="form-label require ">Discard
                                                        Date</label>
                                                    <input type="text" name="discard_date" id="discard_date"
                                                        value="{{ displaydateformat($user_discard->discard_date) }}"
                                                        class="form-control">
                                                </div>
                                            </div>

                                        </div>
                                        <div class="row">
                                            <div class="row">
                                                <div class="card-header-inner">
                                                    <h4 class="text-white">Medicine Details</h4>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Medicine name</label>
                                                    <select name="medicine_id" id="medicine_id"
                                                        class="form-control single-select" style="width: 100%">
                                                        <option value="">Select medicine</option>
                                                        @foreach ($medicine as $list)
                                                            <option value="{{ encryptId($list->medicine_id) }}"
                                                                @if ($list->medicine_id == $discard->medicine_id) selected @endif>
                                                                {{ getmedicinename($list->medicine_id) }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label for="rate" class="form-label require ">Available Quantity
                                                    </label>
                                                    <input type="text" name="available_quantity" id="available_quantity"
                                                        value="{{ $discard->available_quantity }}" readonly
                                                        class="form-control">
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label for="rate" class="form-label require "> Quantity
                                                    </label>
                                                    <input type="text" name="quantity" id="quantity"
                                                        value="{{ $discard->quantity }}" class="form-control">
                                                    <span id="quantity-error" style=" display:none;"
                                                        class="text-danger quantity-error">Quantity must be less than
                                                        available quantity.</span>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mb-2">
                                                <div class="form-group form-input">
                                                    <label for="rate" class="form-label require ">Remarks
                                                    </label>
                                                    <textarea name="remarks" id="remarks" cols="10" rows="5" class="form-control">{{ $discard->remarks }}</textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <hr>
                                        <div class="submit-button float-end">
                                            <x-button-submit class="submit" id="submit"></x-button-submit>
                                            <x-button-reset class="submit"></x-button-reset>
                                            <x-button-cancel href="{{ admin_url('ohc/discard/list') }}"></x-button-cancel>
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
            $(document).on('input', '#hidden_unit_id', function() {
                var unitId = $(this).val();
                var ajaxUrl = $(this).data('url');
                var selectedDepartment = $('#department_id').val(); // Get the currently selected department

                console.log("Unit ID:", unitId);
                console.log("Selected Department:", selectedDepartment);

                if (unitId) {
                    $.ajax({
                        url: ajaxUrl + "/" + unitId + "/0",
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            $('#department_id').empty().append(
                                '<option value="">Select Department</option>');

                            $.each(data, function(key, value) {
                                var isSelected = (value.id === selectedDepartment) ?
                                    "selected" : "";
                                $('#department_id').append('<option value="' + value
                                    .id + '" ' + isSelected + '>' + value.name +
                                    '</option>');
                            });

                            $('#department_id').trigger('change');
                        },
                        error: function(xhr) {
                            alert('Error fetching department. Please try again.');
                        }
                    });
                } else {
                    $('#department_id').empty().append('<option value="">Select Department</option>');
                    $('#department_id').trigger('change');
                }
            });

            // Trigger on page load if `hidden_unit_id` has a value
            if ($('#hidden_unit_id').val()) {
                $('#hidden_unit_id').trigger('input');
            }
        });

        $(document).ready(function() {

            $(document).on("change", "#medicine_id", function() {
                var selectedMedicineId = $(this).val(); // Get selected medicine ID
                var availableQuantityInput = $('#available_quantity'); // Reference the input field

                if (selectedMedicineId) {
                    $.ajax({
                        url: "{{ admin_url('ohc/medicine-issuance/quantity') }}/" +
                            selectedMedicineId,
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            availableQuantityInput.val(data.available_quantity);
                        },
                        error: function() {
                            Swal.fire('Error', 'Something went wrong. Please try again.',
                                'error');
                        }
                    });
                } else {
                    availableQuantityInput.val('');
                }
            });

            // Validate input quantity against available quantity
            $(document).on("input", 'input[name="quantity"]', function() {
                var availableQuantity = parseInt($('#available_quantity').val()) || 0;
                var quantity = parseInt($(this).val()) || 0;

                if (quantity > availableQuantity) {
                    $('#quantity-error').show();
                } else {
                    $('#quantity-error').hide();
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
                    discard_date: {
                        required: true,
                    },
                    medicine_id: {
                        required: true,
                        remote: {
                            url: '{{ admin_url('ohc/discard/unique') }}',
                            type: 'post',
                            data: {
                                medicine_id: function() {
                                    return $('#medicine_id').val();
                                },
                                id: function() {
                                    return $('#id').val();
                                },
                                medicineid: function() {
                                    return $('#medicineid').val();
                                },
                            }
                        }
                    },
                    quantity: {
                        required: true,
                    },
                    remarks: {
                        required: true,
                        minlength: 3,
                        maxlength: 600,
                    },
                },
                messages: {
                    unit_id: {
                        required: "Please select the Unit name.",
                    },
                    department_id: {
                        required: "Please select the Department Name.",
                    },

                    discard_date: {
                        required: "Please select the discard date.",
                    },
                    medicine_id: {
                        required: "Please select the Medicine Name.",
                        remote:"The selected Medicine is already taken for this id"
                    },
                    quantity: {
                        required: "Please select the quantity.",
                    },

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
