@extends('admin.layouts.admin')
@section('title', 'Monthly Forklift Inspection Add')
@section('pageurl', admin_url('safety/forklift-inspection/monthly/list'))


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
                                        href="{{ admin_url('safety/forklift-inspection/monthly/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form mx-3">
                                    <form method="POST" id="forklistassessmentAdd"
                                        action="{{ admin_url('safety/forklift-inspection/monthly/add/submit') }}"
                                        autocomplete="off" enctype="multipart/form-data">
                                        @csrf

                                        <div class="row">
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">{{ __('inspection.doc_no') }}</label>
                                                    <input type="text" name="doc_no" id = "doc_no" class="form-control"
                                                        placeholder="Enter the Document Number"
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
                                                    <input type="text" name="issue_date" id="issue_date"
                                                        class="form-control" placeholder="Issued Date"
                                                        value="{{ displaydateformat($document_no->issue_date) }}" readonly>
                                                    @error('issue_date')
                                                        <div class="error">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label require">{{ __('inspection.rev_date') }}</label>
                                                    <input type="text" name="rev_date" id="rev_date"
                                                        class="form-control" readonly value="{{ $document_no->rev_dt }}"
                                                        readonly />
                                                </div>
                                            </div>
                                            <input type="hidden" name="document_reference_id"
                                                value="{{ encryptId($document_no->id) }}">


                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label require">{{ __('inspection.inspection_date') }}</label>
                                                    <input type="text" name="inspection_date" id = "inspection_date"
                                                        class="form-control" value="{{ old('inspection_date') }}">
                                                    @error('inspection_date')
                                                        <div class="error">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label require">{{ __('inspection.location') }}</label>
                                                    <select name="location_id" id="location_id"
                                                        class=" form-control single-select" style="width: 100%">
                                                        <option value="">Select {{ __('inspection.location') }}
                                                        </option>
                                                        @foreach ($locations as $location)
                                                            <option value="{{ encryptId($location->id) }}"
                                                                {{ old('location_id') == encryptId($location->id) ? 'selected' : '' }}>
                                                                {{ $location->location_name }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('location_id')
                                                        <div class="error">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Shift</label>
                                                    <select name="shift_id" id="shift_id"
                                                        class=" form-control single-select" style="width: 100%">
                                                        <option value="">Select Shift</option>
                                                        @foreach ($shift as $shift)
                                                            <option value="{{ encryptId($shift->id) }}"
                                                                {{ old('shift_id') == encryptId($shift->id) ? 'selected' : '' }}>
                                                                {{ $shift->shift }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('shift_id')
                                                        <div class="error">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label require">{{ __('inspection.next_due') }}</label>
                                                    <input type="text" name="next_due" id = "next_due"
                                                        class="form-control" value="{{ old('next_due') }}">
                                                    @error('next_due')
                                                        <div class="error">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">{{ __('inspection.unit') }}</label>
                                                    <select name="unit_id" id="unit_id"
                                                        class=" form-control single-select" style="width: 100%">
                                                        <option value="">Select Unit</option>
                                                        @foreach ($units as $unit)
                                                            <option value="{{ encryptId($unit->id) }}"
                                                                {{ old('unit_id') == encryptId($unit->id) ? 'selected' : '' }}>
                                                                {{ $unit->unit_name }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('unit_id')
                                                        <div class="error">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label require">{{ __('inspection.frequency') }}</label>
                                                    <select name="frequency_id" id="frequency_id"
                                                        class=" form-control single-select" style="width: 100%">
                                                        <option value="">Select Frequency</option>
                                                        @foreach ($frequency as $frequency)
                                                            <option value="{{ encryptId($frequency->id) }}"
                                                                {{ old('frequency_id') == encryptId($frequency->id) ? 'selected' : '' }}>
                                                                {{ $frequency->frequency_name }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('frequency_id')
                                                        <div class="error">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label require">{{ __('inspection.identification_no') }}</label>
                                                    <input type="text" name="identification_no"
                                                        id = "identification_no" class="form-control"
                                                        placeholder="Enter Identification No"
                                                        value="{{ old('identification_no') }}">
                                                    @error('identification_no')
                                                        <div class="error">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label require">{{ __('inspection.forklift_type') }}</label>
                                                    <select name="forklift_type" id="forklift_type"
                                                        class=" form-control single-select" style="width: 100%">
                                                        <option value="">Select Forklift Type</option>
                                                        @foreach ($forklifts as $forklifts)
                                                            <option value="{{ encryptId($forklifts->id) }}"
                                                                {{ old('forklift_type') == encryptId($forklifts->id) ? 'selected' : '' }}>
                                                                {{ $forklifts->forklift }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('forklift_type')
                                                        <div class="error">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label require">{{ __('inspection.capacity') }}</label>
                                                    <input type="text" name="capacity" id = "capacity"
                                                        class="form-control" placeholder="Enter Capacity"
                                                        value="{{ old('capacity') }}">
                                                </div>
                                                @error('capacity')
                                                    <div class="error">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-4 form-group form-input mb-2">
                                                @if (isset(Auth::user()->signature_upload))
                                                    <label class="form-label"
                                                        style="display: block; ">{{ __('inspection.signature') }}</label>
                                                    <img src="{{ admin_url(Auth::user()->signature_upload) }}"
                                                        alt="Signature Upload" style="width: 150px; margin-top:-10px">
                                                @else
                                                    <div class="form-input col-md-12 mb-2">
                                                        <label class="form-label require">Signature</label>
                                                        <input type="file" name="signature_image"
                                                            id="signature_upload" class="form-control form-control-sm"
                                                            accept="image/*" placeholder="Enter the image">
                                                        <small>Allowed file types: jpg, jpeg, png</small>
                                                        <div id="signature_upload" class="text-danger"></div>
                                                        @error('signature_upload')
                                                            <div class="error">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                @endif
                                            </div>
                                            {{-- <div class=""> --}}
                                            <table class="container p-5">
                                                <thead>
                                                    <tr>
                                                        <th
                                                            style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center;">
                                                            Sr. No
                                                        </th>

                                                        <th colspan="3"
                                                            style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center;">
                                                            Description
                                                        </th>

                                                        @foreach ($getoption as $option)
                                                            <th style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center;"
                                                                class="require">
                                                                {{ $option }}
                                                            </th>
                                                        @endforeach
                                                        <th style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center;"
                                                            class="require">Remarks</th>
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

                                                                <td colspan="3"
                                                                    style="border: 1px solid black; padding: 8px;">
                                                                    {{ $checklist->checklist_name }}
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
                                                                <td style="border: 1px solid black; text-align: center; padding:5px;"
                                                                    class="form-input">
                                                                    <input type="text" class="form-control"
                                                                        name="remarks[{{ $checklist->sub_type_id }}][{{ $checklist->checklist_id }}]">
                                                                </td>
                                                                @php
                                                                    $i++;
                                                                @endphp
                                                            </tr>
                                                        @endforeach
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        {{-- </div> --}}

                                        <hr>
                                        <div class="submit-button" style="text-align: right;">
                                            <x-button-submit class="submit"></x-button-submit>
                                            <x-button-reset class="submit"></x-button-reset>
                                            <x-button-cancel
                                                href="{{ admin_url('safety/forklift-inspection/monthly/list') }}"></x-button-cancel>
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
                flatpickr("#issue_date", {
                    dateFormat: "d-m-Y",
                });
                flatpickr("#inspection_date", {
                    dateFormat: "d-m-Y",
                });
                flatpickr("#next_due", {
                    dateFormat: "d-m-Y",
                    minDate: new Date(),
                });
                $('.floor_executive').select2({
                    ajax: {
                        url: "{{ admin_url('safety/forklift-inspection/monthly/employeeName') }}",
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
                    selectionCssClass: 'form-control'
                });
            });
            $(function() {
                $(function() {
                    $.validator.addMethod("noSpaces", function(value, element) {
                        return this.optional(element) || value.trim().length > 0;
                    }, "This field cannot contain only spaces");

                    $('#forklistassessmentAdd').validate({
                        rules: {
                            doc_no: {
                                required: true,
                                minlength: 3,
                                maxlength: 100,
                                noSpaces: true,
                            },
                            issue_date: {
                                required: true,
                            },
                            inspection_date: {
                                required: true,
                            },
                            location_id: {
                                required: true,
                            },
                            shift_id: {
                                required: true,
                            },
                            next_due: {
                                required: true,
                            },
                            unit_id: {
                                required: true,
                            },
                            signature_image: {
                                required: true,
                            },
                            frequency_id: {
                                required: true,
                            },
                            identification_no: {
                                required: true,
                                minlength: 3,
                                maxlength: 100,
                                noSpaces: true,
                            },
                            forklift_type: {
                                required: true,
                            },
                            capacity: {
                                required: true,
                                number: true,
                                noSpaces: true,
                            },

                        },
                        messages: {
                            doc_no: {
                                required: "{{ __('Document Number is Required') }}",
                                minlength: "Minimum Characters should be 3",
                                maxlength: "Maximum Characters should not exceed 100",
                            },
                            issue_date: {
                                required: "{{ __('Date Of Audit is required') }}",
                            },
                            rev_date: {
                                required: "Revision Date required",
                            },
                            inspection_date: {
                                required: "Inspeciton Date is required",
                            },
                            location_id: {
                                required: "Location is required",
                            },
                            shift_id: {
                                required: "Shift is required",
                            },
                            next_due: {
                                required: "Next due is required",
                            },
                            unit_id: {
                                required: "Unit is required",
                            },
                            frequency_id: {
                                required: "Frequency is required",
                            },
                            identification_no: {
                                required: "{{ __('Identification Number is Required') }}",
                                minlength: "Minimum Characters should be 3",
                                maxlength: "Maximum Characters should not exceed 100",
                            },
                            forklift_type: {
                                required: "Floor Executive on Duty is required",
                            },
                            capacity: {
                                required: "{{ __('Capacity is Required') }}",
                            },
                            signature_image: {
                                required: "Signature is required",
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
                                console.log("Field: " + error.element.name + ", Error: " +
                                    error
                                    .message);
                            });
                        }
                    });
                    $('input[name^="remarks"]').each(function() {
                        $(this).rules('add', {
                            required: 500,
                            messages: {
                                required: "Remarks is required"
                            }
                        });
                    });
                });
            });
        </script>
    @endpush
