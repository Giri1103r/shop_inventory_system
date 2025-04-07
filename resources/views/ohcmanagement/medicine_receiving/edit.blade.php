@extends('admin.layouts.admin')
@section('title', 'Medicine Receiving Edit')
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
                                        action="{{ admin_url('ohc/medicine-receiving-form/edit/submit') }}">
                                        @csrf
                                        <input type="hidden" name="id" id="id"
                                            value="{{ encryptId($medicine_receiving->id) }}">
                                        <hr>
                                        <div class="row">

                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label for="medicine_name" class="form-label require ">Medicine
                                                        Name</label>
                                                    <select name="medicine_id" id="medicine_id"
                                                        class="form-control form-control-sm single-select"
                                                        style="width: 100%">
                                                        <option value="">Select the Medicine Name</option>
                                                        @foreach ($medicineStock as $list)
                                                            <option value="{{ encryptId($list->medicine_id) }}"
                                                                @if ($medicine_receiving->medicine_id == $list->medicine_id) selected @endif>
                                                                {{ getmedicinename($list->medicine_id) }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('medicine_id')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label for="hsn_id" class="form-label require">HSN Number</label>
                                                    <input type="text" name="hsn_id" id="hsn_id" class="form-control"
                                                        value="{{ $medicine_receiving->hsn_id }}">
                                                    @error('hsn_id')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label for="pack_id" class="form-label require ">Pack Detatils</label>
                                                    <input type="text" name="pack_display" id="pack_id"
                                                        class="form-control" readonly value="{{ $hsn->pack }}">
                                                    <input type="hidden" name="pack_id" id="pack_hidden_id">
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label for="quantity" class="form-label require ">Quantity</label>
                                                    <input type="text" name="quantity" id="quantity"
                                                        class="form-control"value={{ $medicine_receiving->quantity }}>

                                                    @error('quantity')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label for="batch_number" class="form-label require ">Batch
                                                        Number</label>
                                                    <input type="text" name="batch_number" id="batch_number"
                                                        value={{ $medicine_receiving->batch_number }} class="form-control">

                                                    @error('batch_number')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label for="rate" class="form-label require ">Rate</label>
                                                    <input type="text" name="rate" id="rate"
                                                        class="form-control"value={{ $medicine_receiving->rate }}>
                                                    @error('expire_date')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label for="rate" class="form-label require ">Expire Date</label>

                                                    <div class="input-group date form-input custom-height">
                                                        <input type="text" name="expire_date" id="expire_date"
                                                            value={{ displaydateformat($medicine_receiving->expire_date) }}
                                                            class="form-control">
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
                                                    <label for="vendor_name" class="form-label require ">Vendor
                                                        Name</label>
                                                    <select name="vendor_id" id="vendor_id"
                                                        class="form-control form-control-sm single-select"
                                                        style="width: 100%">
                                                        <option value="">Select the Vendor Name</option>
                                                        @foreach ($vendor as $list)
                                                            <option
                                                                value="{{ encryptId($list->id) }}"@if ($medicine_receiving->vendor_id == $list->id) selected @endif>
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



    </div>

@stop

@push('script')
    <script>
        $(document).ready(function() {
            var fromDatepicker = flatpickr("#expire_date", {
                dateFormat: "d-m-Y",
                minDate: new Date(),

            });
        });
        $(document).on('change', '#unit_id', function() {
            var unitId = $(this).val();
            if (unitId) {
                $.ajax({
                    url: "{{ admin_url('ohc/medicine-receiving-form/ajax-list') }}/" + unitId,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        $('#medicine_id').empty().append(
                            '<option value="">Select Medicine Name</option>');
                        $.each(data, function(key, value) {
                            $('#medicine_id').append('<option value="' + value.id + '">' + value
                                .name + '</option>');
                        });
                        $('#medicine_id').trigger('change');

                        $('#pack_id').empty().append(
                            '<option value="">Select Pack Details</option>');
                        $.each(data, function(key, value) {
                            $('#pack_id').append('<option value="' + value.pack + '">' + value
                                .pack + '</option>');
                        });
                        $('#pack_id').trigger('change');

                    },
                    error: function(xhr) {
                        alert('Error fetching medicine. Please try again.');
                    }
                });
            } else {
                $('#medicine_id').empty().append('<option value="">Select Medicine Name</option>').trigger(
                    'change');
            }
        });


        $('#medicine_id').on('change', function() {
            var medicineId = $('#medicine_id').val();
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
                function(value, element, regex) {
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

                    },
                    batch_number: {
                        required: true,
                        minlength: 3,
                        maxlength: 20,
                        // regex: /^[a-zA-Z0-9/-]$/,
                    },
                    rate: {
                        required: true,
                        number: true,
                        // regex: /^[0-9]+(\.[0-9]+)?$/,
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
                        maxlength: "Batch Number should not Exceed more than 20 characters",
                        // regex: "Batch number has invalid characters",
                    },
                    rate: {
                        required: "Please enter the rate.",
                        number: "Please enter a valid numeric value for rate.",
                        // regex: "Rate has invalid characters.",
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
