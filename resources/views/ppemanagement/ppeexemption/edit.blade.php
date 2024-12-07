@extends('admin.layouts.admin')
@section('title', 'PPE Exemption Edit')
@section('pageurl', admin_url('ppe_exemption/list'))
@section('content')
    @push('style')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    @endpush
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
                                    <x-button-back href="{{ admin_url('ppe_exemption/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form">
                                    <form method="POST" id="ppeExemptionForm"
                                        action="{{ admin_url('ppe_exemption/edit/submit') }}">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $encryptid }}">
                                        <input type="hidden" name="emp_id" id="emp_id"
                                            value="{{ $userData->employee_id }}">
                                        <input type="hidden" name="emp_name" id="emp_name" value="{{ $userData->name }}">
                                        <input type="hidden" name="department" id="department"
                                            value="{{ $userData->department_id }}">
                                        <input type="hidden" name="unit" id="unit"
                                            value="{{ $userData->unit_id }}">
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-4 mb-2">
                                                <label for="date" class="form-label require">From Date</label>
                                                    <div class="input-group date form-input">
                                                        <input type="text" class="form-control form-conrol-sm" name="from_date"
                                                        value="{{ $ppeexemption->from_date }}" id="from_date" autocomplete="off"
                                                        placeholder="Enter the From Date">
                                                        <div class="input-group-addon input-group-text">
                                                            <span class="fa fa-calendar"></span>
                                                        </div>
                                                    </div>
                                                @error('from_date')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                                <div class="text-danger" id="from_date_error"></div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <label for="date" class="form-label require">To Date</label>
                                                <div class="input-group date form-input">
                                                    <input type="text" class="form-control form-conrol-sm" name="to_date"
                                                    value="{{ $ppeexemption->to_date }}" id="to_date" autocomplete="off"
                                                    placeholder="Enter the To Date">
                                                    <div class="input-group-addon input-group-text">
                                                        <span class="fa fa-calendar"></span>
                                                    </div>
                                                </div>

                                                @error('to_date')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                                <div class="text-danger" id="to_date_error"></div>

                                            </div>

                                            <div class="col-md-12 mb-2">
                                                <label for="reason" class="form-label require">Reason</label>
                                                <textarea name="reason" id="reason" cols="3" rows="4" class="form-control form-control-sm"
                                                    placeholder="Enter the Reason">{{ $ppeexemption->reason }}</textarea>
                                                @error('reason')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                                <div class="text-danger" id="reason_error"></div>
                                            </div>
                                            <div class="col-md-12 mb-2">
                                                <input type="checkbox" id="checkbox" name="checkbox">
                                                <label for="checkbox" class="form-label">I agree to the terms and
                                                    conditions</label>
                                                <div class="text-danger" id="checkbox_error"></div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="submit-button float-end">
                                            <x-button-submit class="submit" id="submit"></x-button-submit>
                                            <x-button-reset class="submit"></x-button-reset>
                                            <x-button-cancel
                                                href="{{ admin_url('ppe_exemption/list') }}"></x-button-cancel>
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
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#resetform').on('click', function(e) {
                e.preventDefault();
                location.reload();
            });
            $('label[for="checkbox"]').on('click', function(e) {
                e.preventDefault();
                $('#checkbox').prop('checked', !$('#checkbox').prop('checked'));
            });
        });
        $(document).ready(function() {
            var fromDatepicker = flatpickr("#from_date", {
                dateFormat: "d-m-Y",
                minDate: new Date(),
                onChange: function(selectedDates) {
                    if (selectedDates.length > 0) {
                        var startDate = selectedDates[0];
                        toDatepicker.set('minDate', startDate);
                        toDatepicker.clear();
                    }
                }
            });

            var toDatepicker = flatpickr("#to_date", {
                dateFormat: "d-m-Y",
                minDate: new Date()
            });

            $('#ppeExemptionForm').validate({
                rules: {
                    from_date: {
                        required: true
                    },
                    to_date: {
                        required: true
                    },
                    reason: {
                        required: true,
                        minlength: 3,
                        maxlength: 255,
                        regex: /^[a-zA-Z0-9\s]+$/
                    },
                    checkbox: {
                        required: true
                    }
                },
                messages: {
                    from_date: {
                        required: "Please Select the From date."
                    },
                    to_date: {
                        required: "Please Select the To date."
                    },
                    reason: {
                        required: "Reason cannot be empty.",
                        minlength: "Reason must contain between 3 and 255 characters.",
                        maxlength: "Reason must contain between 3 and 255 characters.",
                        regex: "Reason must contain only letters and numbers."
                    },
                    checkbox: {
                        required: "You must agree to the terms and conditions."
                    }
                },
                errorElement: 'div',
                errorPlacement: function(error, element) {
                    var errorDiv = element.siblings('div.text-danger');
                    errorDiv.html(error);
                },
                highlight: function(element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                },
                submitHandler: function(form) {
                    $('#submit').prop('disabled', true);
                    form.submit();
                },
                invalidHandler: function(event, validator) {
                    var errors = validator.numberOfInvalids();
                    console.log(errors + " field(s) are invalid");
                    validator.errorList.forEach(function(error) {
                        console.log("Field: " + error.element.name + ", Error: " + error
                            .message);
                    });
                }
            });

            $.validator.addMethod("regex", function(value, element, regexp) {
                return this.optional(element) || regexp.test(value);
            }, "Please check your input.");
        });
    </script>
@endpush
