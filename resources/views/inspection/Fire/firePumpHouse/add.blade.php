@extends('admin.layouts.admin')
@section('title', 'Daily Fire Pump House Inspection Add')
@section('pageurl', admin_url('fire/daily-fire-pump-house-inspection/list'))


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
                                        href="{{ admin_url('fire/daily-fire-pump-house-inspection/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form">
                                    <form method="POST" id="firehouseinspectionadd"
                                        action="{{ admin_url('fire/daily-fire-pump-house-inspection/add/submit') }}"
                                        autocomplete="off" enctype="multipart/form-data">
                                        @csrf

                                        <div class="row">
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Inspection ID</label>
                                                    <input type="text" name="inspection_id" id = "inspection_id"
                                                        class="form-control" readonly
                                                        value="{{ getSequence('DailyfireHouse') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4 form-input">
                                                <label class="form-label">Doc. No</label>
                                                <input type="text" class="form-control" name="doc_no" id="doc_no"
                                                    readonly value="{{ $staticDocno->doc_no }}">
                                            </div>

                                            <div class="col-md-4 form-input">
                                                <label class="form-label">Issue Dt.</label>
                                                <input type="text" class="form-control" name="issue_date" id="issue_date"
                                                    readonly value="{{ Displaydateformat($staticDocno->issue_date) }}">
                                            </div>

                                            <div class="col-md-4 form-input mb-2">
                                                <label class="form-label">Rev. & Dt.</label>
                                                <input type="text" class="form-control" name="rev_dt" id="rev_dt"
                                                    readonly value="{{ $staticDocno->rev_dt }}">
                                            </div>
                                            <div class="col-md-4 form-input mb-2">
                                                <label class="form-label">Date of Inspection</label>
                                                <input type="text" class="form-control" name="date_of_inspection"
                                                    id="date_of_inspection">
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Unit</label>
                                                    <select name="unit_id" id="unit_id"
                                                        class=" form-control single-select" style="width: 100%">
                                                        <option value="">Select Unit</option>
                                                        @foreach ($unit as $unit)
                                                            <option value="{{ encryptId($unit->id) }}">
                                                                {{ $unit->unit_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Shift</label>
                                                    <select name="shift_id" id="shift_id"
                                                        class=" form-control single-select" style="width: 100%">
                                                        <option value="">Select Shift</option>
                                                        @foreach ($shift as $shift)
                                                            <option value="{{ encryptId($shift->id) }}">
                                                                {{ $shift->shift }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <table class="container p-5">
                                                <thead>
                                                    <tr>
                                                        <th
                                                            style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center;">
                                                            Sr. No
                                                        </th>

                                                        <th colspan="2"
                                                            style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center;">
                                                            Check Points
                                                        </th>
                                                        <th
                                                            style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center;">
                                                            Pump No
                                                        </th>

                                                        @foreach ($getoption as $option)
                                                            <th style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center;"
                                                                class="require">
                                                                {{ $option }}
                                                            </th>
                                                        @endforeach

                                                        <th
                                                            style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center;">
                                                            Remarks
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $i = 1;

                                                    @endphp
                                                    @foreach ($checklist_details as $key => $details)
                                                        @php
                                                            $rowCount = count($details);
                                                        @endphp
                                                        @foreach ($details as $index => $checklist)
                                                            <tr>
                                                                <td
                                                                    style="border: 1px solid black; padding: 8px; background-color: #f5f5f5; font-weight: bold;">
                                                                    {{ $i }}
                                                                </td>

                                                                <td colspan="2"
                                                                    style="border: 1px solid black; padding: 8px;">
                                                                    {{ $checklist->checklist_name }}
                                                                </td>
                                                                <td
                                                                    style="border: 1px solid black; padding: 8px; text-align: center;">
                                                                    @if ($checklist->pump_no == 1)
                                                                        <input type="text"
                                                                            name="pump_no[{{ $checklist->checklist_id ?? '' }}]"
                                                                            class="form-control required">
                                                                    @else
                                                                        N/A
                                                                    @endif
                                                                </td>
                                                                @foreach ($getoption as $option)
                                                                    <td style="border: 1px solid black; padding: 8px; text-align: center;"
                                                                        class="form-input">
                                                                        <input type="radio"
                                                                            name="checklist[{{ $checklist->sub_type_id }}][{{ $checklist->checklist_id }}]"
                                                                            value="{{ trim($option) }}"
                                                                            class="validate-radio-required">
                                                                    </td>
                                                                @endforeach
                                                                <td
                                                                    style="border: 1px solid black; padding: 8px; text-align: center;">
                                                                    <textarea name="remarks[{{ $checklist->checklist_id ?? '' }}]" cols="5" rows="3" class="form-control"></textarea>
                                                                </td>
                                                                @php
                                                                    $i++;
                                                                @endphp
                                                            </tr>
                                                        @endforeach
                                                    @endforeach
                                                </tbody>
                                            </table>

                                            <div class="col-md-4  form-input mt-2">
                                                @if (isset(Auth::user()->signature_upload))
                                                    <label class="form-label"
                                                        style="display: block; ">{{ __('inspection.signature') }}</label>
                                                    <img src="{{ admin_url(Auth::user()->signature_upload) }}"
                                                        alt="Signature Upload" style="width: 150px; margin-top:-10px">
                                                @else
                                                    <div class="form-input col-md-12 mb-2">
                                                        <label class="form-label require">Signature</label>
                                                        <input type="file" name="signature_image"
                                                            id="signature_upload" class="form-control" accept="image/*"
                                                            placeholder="Enter the image">
                                                        <small>Allowed file types: jpg, jpeg, png</small>
                                                        <div id="signature_upload" class="text-danger"></div>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="col-md-4 form-input mt-2">
                                                <label class="form-label require">Date</label>
                                                <input type="text" class="form-control" name="date"
                                                    id="date">
                                            </div>
                                            <div class="col-md-12 form-input mt-2">
                                                <label class="form-label require">Note</label>
                                                <textarea name="note" cols="5" rows="3" class="form-control"></textarea>
                                            </div>
                                        </div>

                                        <hr>
                                        <div class="submit-button" style="text-align: right;">
                                            <x-button-submit class="submit"></x-button-submit>
                                            <x-button-reset class="submit"></x-button-reset>
                                            <x-button-cancel
                                                href="{{ admin_url('fire/daily-fire-pump-house-inspection/list') }}"></x-button-cancel>
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
                flatpickr("#date_of_inspection", {
                    dateFormat: "d-m-Y",
                });
                flatpickr("#date", {
                    dateFormat: "d-m-Y",
                });
                $('.floor_executive').select2({
                    ajax: {
                        url: "{{ admin_url('fire/daily-fire-pump-house-inspection/employeeName') }}",
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
                        },
                        error: function(xhr, textStatus, errorThrown) {
                            console.log("Error in AJAX request:", textStatus, errorThrown);
                        }
                    },
                    minimumInputLength: 3,
                    dropdownCssClass: 'form-control',
                    selectionCssClass: 'form-control',

                });
            });
            $(function() {
                $('#firehouseinspectionadd').validate({
                    rules: {
                        date_of_inspection: {
                            required: true,
                        },
                        unit_id: {
                            required: true,
                        },
                        audit_date: {
                            required: true,
                        },
                        shift_id: {
                            required: true,
                        },
                        'pump_no[]': {
                            required: true,
                        },
                        'remarks[]': {
                            required: true,
                        },
                        signature_image: {
                            required: true,
                        },
                        date: {
                            required: true,
                        },
                        note: {
                            required: true,
                        },

                    },
                    messages: {
                        date_of_inspection: {
                            required: "{{ __('Date of Inspection is Required') }}",
                        },
                        unit_id: {
                            required: "{{ __('Unit is Required') }}",
                        },
                        audit_date: {
                            required: "{{ __('Date Of Audit is required') }}",
                        },
                        shift_id: {
                            required: "Shift is required",
                        },
                        'pump_no[]': {
                            required: "Pump No is required",
                        },
                        'remarks[]': {
                            required: "Remarks is required",
                        },
                        signature_image: {
                            required: "Signature is required",
                        },
                        date: {
                            required: "Date is required",
                        },
                        note: {
                            required: "Date is required",
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
                        console.log('test');
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
            });
        </script>
    @endpush
