@extends('admin.layouts.admin')
@section('title', 'Medicine Receiving')
@section('pageurl', admin_url('ohc/medicine-receiving-form/list'))
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
                                    <x-button-back
                                        href="{{ admin_url('ohc/medicine-receiving-form/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="basic-form">
                                    <form method="POST" id="MedicineRecevingForm" enctype="multipart/form-data"
                                        action="{{ admin_url('ohc/medicine-receiving-form/add/submit') }}">
                                        @csrf

                                        <hr>
                                        <div class="row">
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label for="medicine_name"
                                                        class="form-label require">{{ __('ohc_management.medicine_name') }}</label>
                                                    <select name="medicine_id" id="medicine_id"
                                                        class="form-control form-control-sm single-select"
                                                        style="width: 100%">
                                                        <option value="">Select the Medicine Name</option>
                                                        {{-- @foreach ($medicineStock as $list)
                                                            @php
                                                                $isDisabled = in_array(
                                                                    $list->medicine_id,
                                                                    $existingMedicineIds,
                                                                )
                                                                    ? 'disabled'
                                                                    : '';
                                                            @endphp
                                                            <option value="{{ encryptId($list->medicine_id) }}"
                                                                {{ $isDisabled }}>
                                                                {{ getMedicinename($list->medicine_id) }}
                                                            </option>
                                                        @endforeach --}}
                                                    </select>
                                                    @error('medicine_id')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label for="hsn_id"
                                                        class="form-label require">{{ __('ohc_management.hsn_number') }}</label>
                                                    <input type="text" name="hsn_id" id="hsn_id"
                                                        class="form-control">
                                                    @error('hsn_id')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label for="pack_id"
                                                        class="form-label require ">{{ __('ohc_management.pack') }}</label>
                                                    <input type="text" name="pack_display" id="pack_id"
                                                        class="form-control" readonly>
                                                    <input type="hidden" name="pack_id" id="pack_hidden_id">
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label for="quantity"
                                                        class="form-label require ">{{ __('ohc_management.quantity') }}</label>
                                                    <input type="text" name="quantity" id="quantity"
                                                        class="form-control">
                                                    @error('quantity')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label for="batch_number"
                                                        class="form-label require ">{{ __('ohc_management.batch_number') }}</label>
                                                    <input type="text" name="batch_number" id="batch_number"
                                                        class="form-control">
                                                    @error('batch_number')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label for="rate"
                                                        class="form-label require ">{{ __('ohc_management.rate') }}</label>
                                                    <input type="text" name="rate" id="rate"
                                                        class="form-control">
                                                    @error('rate')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label for="rate"
                                                        class="form-label require ">{{ __('ohc_management.expiry_date') }}</label>
                                                    <div class="input-group date form-input custom-height">
                                                        <input type="text" name="expire_date" id="expire_date"
                                                            class="form-control" autocomplete="off">
                                                        <div class="input-group-addon input-group-text">
                                                            <span class="fa fa-calendar"></span>
                                                        </div>
                                                    </div>
                                                    @error('expire_date')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label for="vendor_name"
                                                        class="form-label require ">{{ __('ohc_management.vendor_name') }}</label>
                                                    <select name="vendor_id" id="vendor_id"
                                                        class="form-control single-select form-control-sm"
                                                        style="width: 100%">
                                                        <option value="">Select the Vendor Name</option>
                                                        @foreach ($vendor as $list)
                                                            <option value="{{ encryptId($list->id) }}">
                                                                {{ $list->vendor_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('vendor_id')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="submit-button float-end">
                                            <x-button-submit class="submit" id="submit"></x-button-submit>
                                            <x-button-reset class="submit"></x-button-reset>
                                            <x-button-cancel
                                                href="{{ admin_url('ohc/medicine-receiving-form/list') }}"></x-button-cancel>
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
            var fromDatepicker = flatpickr("#expire_date", {
                dateFormat: "d-m-Y",
                minDate: new Date(),

            });
            $('#medicine_id').select2({
                ajax: {
                    url: '{{ admin_url('ohc/medicine-receiving-form/medicineid') }}',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            search: params.term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: $.map(data, function(item) {
                                return {
                                    id: item.id,
                                    text: item.text
                                };
                            })
                        };
                    }
                },
                minimumInputLength: 2,
                dropdownCssClass: 'form-control',
                selectionCssClass: 'form-control'
            });
        });



        $('#medicine_id').on('change', function() {
            var medicineId = $('#medicine_id').val();
            console.log(medicineId);
            if (medicineId) {
                $.ajax({
                    url: "{{ admin_url('ohc/medicine-receiving-form/pack-id') }}",
                    type: 'POST',
                    data: {
                        medicine_id: medicineId,
                    },
                    dataType: 'json',
                    success: function(data) {
                        console.log('Response data:', data);


                        if (data && data.id && data.text && data.encrypted_id) {
                            $('#pack_id').val(data.text);
                            $('#pack_hidden_id').val(data.id);
                        } else {
                            alert('HSN data is incomplete or invalid.');
                        }
                    },
                    error: function(xhr) {
                        console.error(xhr);
                        alert('Error fetching HSN number. Please try again.');
                    },
                });
            } else {

                $('#pack_id').val('').prop('readonly', true);
                $('#pack_hidden_id').val('');
            }
        });




        $(function() {
            $.validator.addMethod(
                "regex",
                function(value, element, pattern) {
                    let regex = new RegExp(pattern); // Convert string pattern to RegExp object
                    return this.optional(element) || regex.test(value);
                },
                "Invalid format."
            );

            $('#MedicineRecevingForm').validate({
                rules: {
                    medicine_id: {
                        required: true,
                    },
                    pack_id: {
                        required: true,
                    },
                    hsn_id: {
                        required: true,
                    },
                    vendor_id: {
                        required: true,
                    },
                    quantity: {
                        required: true,
                        digits: true,
                        min: 1
                    },
                    batch_number: {
                        required: true,
                        minlength: 3,
                        maxlength: 20,
                        regex: /^(?=.*[a-zA-Z0-9])[a-zA-Z0-9\s\-/]*$/, // Use a direct RegExp object
                    },
                    rate: {
                        required: true,
                        number: true,
                        regex: /^[0-9]+(\.[0-9]{1,2})?$/, // Use a direct RegExp object
                    },
                    expire_date: {
                        required: true,
                    },
                },
                messages: {
                    medicine_id: {
                        required: "Please select the medicine name.",
                    },
                    pack_id: {
                        required: "Please select the pack details.",
                    },
                    hsn_id: {
                        required: "HSN Number cannot be empty.",
                    },
                    vendor_id: {
                        required: "Please select a Vendor name.",
                    },
                    quantity: {
                        required: "Please enter the quantity.",
                        digits: "Please enter a valid number for quantity.",
                        min: "Quantity must be greater than 0.",
                    },
                    batch_number: {
                        required: "Please enter a batch number.",
                        minlength: "Minimum 3 characters are required",
                        maxlength: "Batch Number should not exceed more than 20 characters",
                        regex: "Batch number has invalid characters",
                    },
                    rate: {
                        required: "Please enter the rate.",
                        number: "Please enter a valid numeric value for rate.",
                        regex: "Rate has invalid format (only numbers with up to 2 decimal places).",
                    },
                    expire_date: {
                        required: "Please select the expiry date.",
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
