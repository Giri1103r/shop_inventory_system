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
                                                <input type="text" class="form-control form-conrol-sm" name="from_date"
                                                    value="{{ $ppeexemption->from_date }}" id="from_date"
                                                    placeholder="Enter the From Date">
                                                    @error('from_date')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                                <div class="text-danger" id="from_date_error"></div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <label for="date" class="form-label require">To Date</label>
                                                <input type="text" class="form-control form-conrol-sm" name="to_date"
                                                    value="{{ $ppeexemption->to_date }}" id="to_date"
                                                    placeholder="Enter the To Date">
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
            var fromDatepicker = flatpickr("#from_date", {
                dateFormat: "d-m-Y",
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
                minDate: "today"
            });

            $('#ppeExemptionForm').on('submit', function(e) {
                let valid = true;

                if (!validateFromDate()) valid = false;
                if (!validateToDate()) valid = false;
                if (!validateReason()) valid = false;
                if (!validateCheckbox()) valid = false;

                if (!valid) {
                    e.preventDefault();
                } else {
                    $('#submit').prop('disabled', true);
                }
            });

            function validateFromDate() {
                var FromDate = $('#from_date').val();
                if (FromDate === "") {
                    $('#from_date_error').text('Please Select the From date.');
                    return false;
                }
                $('#from_date_error').text('');
                return true;
            }

            function validateToDate() {
                var ToDate = $('#to_date').val();
                if (ToDate === "") {
                    $('#to_date_error').text('Please Select the to date.');
                    return false;
                }
                $('#to_date_error').text('');
                return true;
            }

            function validateReason() {
                var reason = $('#reason').val();
                if (reason === "") {
                    $('#reason_error').text('Reason cannot be empty.');
                    return false;
                }
                if (reason.length < 3 || reason.length > 255) {
                    $('#reason_error').text('Reason must contain between 3 and 255 characters.');
                    return false;
                }
                if (!/^[a-zA-Z0-9\s]+$/.test(reason)) {
                    $('#reason_error').text('Reason must contain only letters and numbers.');
                    return false;
                }
                $('#reason_error').text('');
                return true;
            }

            function validateCheckbox() {
                var checkbox = $('#checkbox').is(':checked');
                if (!checkbox) {
                    $('#checkbox_error').text('You must agree to the terms and conditions.');
                    return false;
                }
                $('#checkbox_error').text('');
                return true;
            }
        });
    </script>
@endpush
