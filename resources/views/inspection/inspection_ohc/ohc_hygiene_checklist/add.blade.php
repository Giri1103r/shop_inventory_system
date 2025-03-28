@extends('admin.layouts.admin')
@section('title', 'OHC HYGIENE INSPECTION CHECKLIST')
@section('pageurl', admin_url('ohc/ohc-hygiene-cleaning-checklist/list'))


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
                                        href="{{ admin_url('ohc/ohc-hygiene-cleaning-checklist/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form mx-3">
                                    <form method="POST" id="forklistassessmentAdd"
                                        action="{{ admin_url('ohc/ohc-hygiene-cleaning-checklist/add/submit') }}"
                                        autocomplete="off" enctype="multipart/form-data">
                                        @csrf

                                        <div class="row">
                                            <div class="row">
                                                <div class="card-header-inner">
                                                    <h4 class="text-white">
                                                        {{ __('OHC Hygiene and Cleaning Checklist') }}</h4>
                                                </div>
                                            </div>
                                            <div class="table-responsive">
                                                <table class="container p-5">
                                                    <thead>
                                                        <tr>
                                                            <th rowspan="2"
                                                                style="border: 1px solid black; padding: 12px; background-color: #ccc; text-align: center;">
                                                                Date
                                                            </th>
                                                            <th rowspan="2"
                                                                style="border: 1px solid black; padding: 12px; background-color: #ccc; text-align: center;">
                                                                Shift
                                                            </th>
                                                            <th rowspan="2"
                                                                style="border: 1px solid black; padding: 12px; background-color: #ccc; text-align: center;">
                                                                Description/Equipment
                                                            </th>
                                                            <th colspan="2"
                                                                style="border: 1px solid black; padding: 12px; background-color: #ccc; text-align: center;">
                                                                Cleaning and Sanitization
                                                            </th>
                                                            <th rowspan="2"
                                                                style="border: 1px solid black; padding: 12px; background-color: #ccc; text-align: center;">
                                                                Remarks</th>
                                                        </tr>
                                                        <tr>
                                                            <td
                                                                style="border: 1px solid black; text-align: center; padding: 12px; background-color: #ccc;">
                                                                YES</td>
                                                            <td
                                                                style="border: 1px solid black; text-align: center; padding: 12px; background-color: #ccc;">
                                                                NO</td>
                                                        </tr>

                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td
                                                                style="border: 1px solid black; text-align: center; padding: 12px;">
                                                                <div class="form-input">
                                                                    <input type="date" class="issue_date form-control"
                                                                        name="issue_date" />
                                                                </div>
                                                            </td>
                                                            <td style="border: 1px solid black; padding: 12px;">
                                                                <div class="form-group form-input">
                                                                    <select name="shift_id" id="shift_id"
                                                                        class="form-control single-select"
                                                                        style="width: 100%">
                                                                        <option value="">Select Shift</option>
                                                                        @foreach ($shifts as $shift)
                                                                            <option value="{{ encryptId($shift->id) }}">
                                                                                {{ $shift->shift }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </td>
                                                            <td style="border: 1px solid black; padding: 12px;">
                                                                {{ __('inspection.ohc_hygiene_cleaning_checklist') }}
                                                                <input type="hidden"
                                                                    value="{{ __('inspection.ohc_hygiene_cleaning_checklist') }}"
                                                                    name="inspection_question">
                                                            </td>
                                                            <td style="border: 1px solid black; text-align: center; padding: 12px;"
                                                                class="form-input">
                                                                <input type="radio" name="inspection"
                                                                    class="validate-radio-required" value="1">
                                                            </td>
                                                            <td style="border: 1px solid black; text-align: center; padding: 12px;"
                                                                class="form-input">
                                                                <input type="radio" name="inspection"
                                                                    class="validate-radio-required" value="0">
                                                            </td>
                                                            <td
                                                                style="border: 1px solid black; text-align: center; padding: 12px;">
                                                                <div class="col-md-12 mb-2 form-input" id="remarks">
                                                                    <label for="remarks" class="form-label">Remarks</label>
                                                                    <textarea id="remarks" class="form-control" rows="3" placeholder="Please Enter Remarks" name="remarks"></textarea>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                <hr>
                                                <div class="row mt-2">
                                                    <div class="col-md-4 form-group form-input mb-2">
                                                        <label class="form-label ">{{ __('inspection.name') }}</label>
                                                        <input type="text" name="name" id = "name"
                                                            class="form-control" value="{{ getUserName(Auth::id()) }}"
                                                            readonly>
                                                    </div>
                                                    <div class="col-md-4 form-group form-input mb-2">
                                                        <label class="form-label ">{{ __('inspection.date') }}</label>
                                                        <input type="text" name="date" id = "date"
                                                            class="form-control" value="{{ todayDate() }}" readonly>
                                                    </div>
                                                    <div class="col-md-4 form-group form-input mb-2">
                                                        @if (isset(Auth::user()->signature_upload))
                                                            <label class="form-label"
                                                                style="display: block; ">{{ __('inspection.signature') }}</label>
                                                            <img src="{{ admin_url(Auth::user()->signature_upload) }}"
                                                                alt="Signature Upload"
                                                                style="width: 150px; margin-top:-10px">
                                                        @else
                                                            <div class="form-input col-md-12 mb-2">
                                                                <label class="form-label require">Signature</label>
                                                                <input type="file" name="signature_image"
                                                                    id="signature_upload"
                                                                    class="form-control form-control-sm" accept="image/*"
                                                                    placeholder="Enter the image">
                                                                <small>Allowed file types: jpg, jpeg, png</small>
                                                                <div id="signature_upload" class="text-danger"></div>
                                                            </div>
                                                        @endif
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                        {{-- </div> --}}

                                        <hr>
                                        <div class="submit-button" style="text-align: right;">
                                            <x-button-submit class="submit"></x-button-submit>
                                            <x-button-reset class="submit"></x-button-reset>
                                            <x-button-cancel
                                                href="{{ admin_url('ohc/ohc-hygiene-cleaning-checklist/list') }}"></x-button-cancel>
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
                flatpickr(".issue_date", {
                    dateFormat: "d-m-Y",
                });

            });

            $(function() {
                $(function() {
                    $.validator.addMethod("noSpaces", function(value, element) {
                        return this.optional(element) || value.trim().length > 0;
                    }, "This field cannot contain only spaces");

                    $('#forklistassessmentAdd').validate({
                        rules: {

                            issue_date: {
                                required: true,
                            },

                            shift_id: {
                                required: true,
                            },
                            signature_image: {
                                required: true,
                            },
                            remarks: {
                                required: true,
                                noSpaces: true,
                            },
                        },
                        messages: {

                            issue_date: {
                                required: "{{ __('Date is required') }}",
                            },
                            shift_id: {
                                required: "Shift is required",
                            },
                            signature_image: {
                                required: "Signature is required",
                            },
                            remarks: {
                                required: "Remarks is required",
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

                        }
                    });
                });
            });
        </script>
    @endpush
