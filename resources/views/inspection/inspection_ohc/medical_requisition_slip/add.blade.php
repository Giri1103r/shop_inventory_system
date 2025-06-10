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
                                                    <label class="form-label require">{{ __('common.unit') }}</label>
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
                                                    <label class="form-label require">{{ __('common.department') }}</label>
                                                    <select name="department_id" id="department_id"
                                                        class=" form-control single-select" style="width: 100%">
                                                        <option value="">Select Department </option>

                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label for="rate" class="form-label require ">
                                                        {{ __('common.date') }}</label>
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
                                                <h4 class="text-white">{{ __('ohc_management.medicine_details') }}</h4>

                                            </div>



                                        </div>



                                        <div class="mb-3 mt-3">
                                            <table class="table table-bordered table-striped">
                                                <thead class="table-secondary">
                                                    <tr>
                                                        <th style="text-align: center">{{ __('common.sno') }}</th>
                                                        <th style="text-align: center">
                                                            {{ __('ohc_management.medicine_name') }}</th>
                                                        <th style="text-align: center">
                                                            {{ __('ohc_management.freeze_quantity') }}</th>
                                                        <th style="text-align: center">
                                                            {{ __('ohc_management.quantity') }}</th>

                                                        <th style="text-align: center">{{ __('ohc_management.remarks') }}
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($medicine as $medicines)
                                                        <tr>
                                                            <td class="text-center">{{ $loop->iteration }}</td>
                                                            <td class="text-center">
                                                                {{ getMedicinename($medicines->medicine_id) }} <input
                                                                    type="hidden" name="medicine_id[{{ $medicines->id }}]"
                                                                    value="{{ encryptId($medicines->id) }}"></td>

                                                            <td class="text-center">{{ $medicines->freeze_quantity }}
                                                                <input type="hidden"
                                                                    name="freeze_quantity[{{ $medicines->id }}]"
                                                                    value="{{ $medicines->freeze_quantity }}">
                                                            </td>
                                                            <td>
                                                                <div class="form-input">
                                                                    <input class="form-control" type="number"
                                                                        min="1"
                                                                        name="quantity[{{ $medicines->id }}]" />
                                                                </div>
                                                            </td>

                                                            <td>
                                                                <div class="form-input">
                                                                    <textarea class="form-control" type="text" style="resize: none" name="remarks[{{ $medicines->id }}]"></textarea>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>


                                        </div>


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
                    // signature_image:{
                    //     filesize: 15728640,
                    // },
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
                    // signature_image:{
                    //     filesize: "File size must be less than 15MB."
                    // },
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
            $('#medicineRequisitionFloor').on('change input',
                'input[name^="available_quantity"], input[name^="expired_date"], select[name^="emp_id"], textarea[name^="remarks"]',
                function() {
                    $(this).valid();
                });

            $(document).ready(function() {
                $('input[name^="quantity"]').each(function() {
                    $(this).rules('add', {
                        required: true,
                        number: true,
                        min: 1,
                        messages: {
                            required: "Available Quantity is required",
                            number: "Please enter a valid number",
                            min: "Quantity must be at least 1"
                        }
                    });
                });



                $('select[name^="emp_id"]').each(function() {
                    $(this).rules('add', {
                        required: true,
                        messages: {
                            required: "Employee is required",
                        }
                    });
                });

                $('textarea[name^="remarks"]').each(function() {
                    $(this).rules('add', {

                        minlength: 3,
                        maxlength: 600,
                        messages: {

                            minlength: "Minimum 3 characters required",
                            maxlength: "Maximum character does not exceed 600"
                        }
                    });
                });
            });

        });
    </script>
@endpush
