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
                                        action="{{ admin_url('ohc/medicine-issuance/add/submit') }}">
                                        @csrf

                                        <hr>
                                        <div class="row">


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
                                                    <label for="rate" class="form-label require ">Issued
                                                        Date</label>
                                                    <input type="text" name="request_date" id="request_date"
                                                        class="form-control">
                                                </div>
                                            </div>

                                        </div>
                                        <div class="table-responsive d-flex justify-content-center mt-2">
                                            <div class="col-md-8">
                                                <table class="table table-bordered view_card text-center" id="requisition_medicine" style="margin: auto;">
                                                    <thead style="background-color: #343a40; color: white;">
                                                <thead >
                                                    <tr>
                                                        <th>Medicine</th>
                                                        <th>Available Quantity</th>
                                                        <th>Quantity</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            <select name="medicine_id[]" class="form-control " id="medicine_id"
                                                                style="width: 100%">
                                                                <option value="">Select the Medicine Name</option>
                                                                @foreach ($medicine as $list)
                                                                    <option value="{{ $list->id }}">
                                                                        {{ $list->medicine }}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input type="text" name="available_quantity[]"
                                                                class="form-control" readonly>
                                                        </td>
                                                        <td>
                                                            <input type="text" name="quantity[]" class="form-control">
                                                        </td>
                                                        <td>
                                                            <div class="d-flex justify-content-center align-items-center bg-primary mt-2 ml-2 text-white rounded add-row"
                                                                style="width: 30px; height: 30px; cursor: pointer;">
                                                                <i class="fa-solid fa-plus"></i>
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
        $('#medicine_id').select2();
        $(document).on('click', '.add-row', function() {
            const maxRows = 5; // Maximum allowed rows
            const rowCount = $('#requisition_medicine tbody tr').length;

            if (rowCount >= maxRows) {
                // Show SweetAlert warning
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning',
                    text: 'Your request has exceeded the limit of 5 rows.',
                    confirmButtonText: 'OK',
                });
                return;
            }

            const newRow = `
            <tr>
                <td>
                    <select name="medicine_id[]" class="form-control single-select" style="width: 100%">
                        <option value="">Select the Medicine Name</option>
                        @foreach ($medicine as $list)
                            <option value="{{ $list->id }}">{{ $list->medicine }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <input type="text" name="available_quantity[]" class="form-control" readonly>
                </td>
                <td>
                    <input type="text" name="quantity[]" class="form-control">
                </td>
                <td>
                    <div class="d-flex justify-content-center align-items-center bg-danger mt-2 ml-2 text-white rounded remove-row" style="width: 30px; height: 30px; cursor: pointer;">
                        <i class="fa-solid fa-minus"></i>
                    </div>
                </td>
            </tr>`;

           $('.single-select').select2();
            $('#requisition_medicine tbody').append(newRow);
        });

        // Remove a row when the minus icon is clicked
        $(document).on('click', '.remove-row', function() {
            $(this).closest('tr').remove();
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
