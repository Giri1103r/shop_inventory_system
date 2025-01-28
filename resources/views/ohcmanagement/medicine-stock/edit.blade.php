@extends('admin.layouts.admin')
@section('title', 'Medicine Stock Inventory')
@section('pageurl', admin_url('ohc/medicine-stock-inventory/list'))
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
                                        href="{{ admin_url('ohc/medicine-stock-inventory/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="basic-form">
                                    <form method="POST" id="MedicineStockform" enctype="multipart/form-data"
                                        action="{{ admin_url('ohc/medicine-stock-inventory/edit/submit') }}">
                                        @csrf
                                        <input type="hidden" name="id" id="id"
                                            value="{{ encryptId($medicine_stock->id) }}">
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label for="unit_id" class="form-label require ">Medicine
                                                        Name</label>
                                                    <select name="unit_id" id="unit_id"
                                                        class="form-control form-control-sm single-select"
                                                        style="width: 100%">
                                                        <option value="">Select the Unit Name</option>
                                                        @foreach ($unit as $list)
                                                            <option value="{{ encryptId($list->id) }}"
                                                                @if ($medicine_stock->unit_id == $list->id) selected @endif>
                                                                {{ $list->unit_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label for="medicine_name" class="form-label require ">Medicine
                                                        Name</label>
                                                    <select name="medicine_id" id="medicine_id"
                                                        class="form-control form-control-sm single-select"
                                                        style="width: 100%">
                                                        <option value="">Select the Medicine Name</option>
                                                        @foreach ($medicine as $list)
                                                            <option value="{{ encryptId($list->id) }}"
                                                                @if ($medicine_stock->medicine_id == $list->id) selected @endif>
                                                                {{ $list->medicine }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label for="hsn_id" class="form-label require">Threshold Limit</label>
                                                    <input type="text" name="threshold_limit" id="threshold_limit"
                                                        class="form-control"
                                                        value="{{ $thresholdlimit->threshold_limit }}">
                                                    <input type="hidden" name="threshold_limit_id" value="{{$medicine_stock->threshold_limit}}" id="threshold_limit_id">
                                                </div>
                                            </div>


                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label for="quantity" class="form-label require ">Quantity</label>
                                                    <input type="text" name="quantity" id="quantity"
                                                        class="form-control"value={{ $medicine_stock->quantity }}>
                                                </div>
                                            </div>


                                        </div>
                                        <hr>
                                        <div class="submit-button float-end">
                                            <x-button-submit class="submit" id="submit"></x-button-submit>
                                            <x-button-reset class="submit"></x-button-reset>
                                            <x-button-cancel href="{{ admin_url('ppe_exemption/list') }}"></x-button-cancel>
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
        });
        $(document).on('change', '#medicine_id', function() {
            var medicineId = $(this).val();
            if (medicineId) {
                $.ajax({
                    url: "{{ admin_url('ohc/medicine-stock-inventory/threshold-limit') }}",
                    type: 'POST',
                    data: {
                        medicine_id: medicineId,
                    },
                    dataType: 'json',
                    success: function(data) {
                        console.log('Response data:', data);


                        if (data && data.id && data.text && data.encrypted_id) {
                            $('#threshold_limit').val(data.text);
                            $('#threshold_limit_id').val(data.id);
                        } else {
                            alert('Threshold Limit is incomplete or invalid.');
                        }
                    },
                    error: function(xhr) {
                        console.error(xhr);
                        alert('Error fetching Threshold limit. Please try again.');
                    },
                });
            } else {

                $('#threshold_limit').val('').prop('readonly', true);
                $('#threshold_limit_id').val('');
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

            $('#MedicineStockform').validate({
                rules: {
                    medicine_id: {
                        required: true,
                        remote: {
                            url: '{{ admin_url('ohc/medicine-stock-inventory/unique') }}',
                            type: 'post',
                            data: {
                                _token: "{{ csrf_token() }}",
                                medicine_id: function() {
                                    return $('#medicine_id').val();
                                },
                                id: function() {
                                    return $('#id').val();
                                },
                            },
                        },
                    },
                    unit_id: {
                        required: true,
                    },
                    threshold_limit: {
                        required: true,
                    },

                    quantity: {
                        required: true,
                        digits: true,

                    },

                },
                messages: {
                    medicine_id: {
                        required: "Please select the medicine name.",
                        remote:"Medicine name should Be unique",
                    },
                    unit_id: {
                        required: "Please select the Unit name .",
                    },

                    quantity: {
                        required: "Please enter the quantity.",
                        digits: "Please enter a valid number for quantity.",
                        min: "Quantity must be greater than 0.",
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
