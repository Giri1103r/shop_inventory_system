@extends('admin.layouts.admin')
@section('title', 'Safety Gallery')
@section('pageurl', admin_url('safety/safety-gallery-inspection/list'))


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
                                <div class="align-back-btc d-flex justify-content-end align-items-center">
                                    <x-button-back
                                        href="{{ admin_url('safety/safety-gallery-inspection/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form mx-3">
                                    <form method="POST" id="safetygalleryAdd"
                                        action="{{ admin_url('safety/safety-gallery-inspection/add/submit') }}"
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


                                                    <div class="input-group date form-input  custom-height">
                                                        <input type="text" name="issue_date" id = "issue_date"
                                                            class="form-control" placeholder="Issued Date"
                                                            value="{{ displaydateformat($document_no->issue_date) }}"
                                                            readonly>
                                                        <div class="input-group-addon input-group-text">
                                                            <span class="fa fa-calendar"></span>
                                                        </div>
                                                    </div>
                                                    @error('issue_date')
                                                        <div class="error">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label require">{{ __('inspection.rev_date') }}</label>
                                                    <input type="text" name="rev_date" id = "rev_date"
                                                        class="form-control" value="{{ $document_no->rev_dt }}" readonly>
                                                </div>
                                            </div>
                                            <input type="hidden" name="document_reference_id"
                                                value="{{ encryptId($document_no->id) }}">
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label require">{{ __('inspection.inspection_date') }}</label>


                                                    <div class="input-group date form-input  custom-height">
                                                        <input type="text" name="inspection_date" id = "inspection_date"
                                                            class="form-control" value="{{ old('inspection_date') }}">
                                                        <div class="input-group-addon input-group-text">
                                                            <span class="fa fa-calendar"></span>
                                                        </div>
                                                    </div>
                                                    @error('inspection_date')
                                                        <div class="error">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label require">{{ __('inspection.resource_code') }}</label>
                                                    <input type="text" name="resource_code" id = "resource_code"
                                                        class="form-control" value="{{ old('resource_code') }}">
                                                    @error('resource_code')
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
                                                    <label class="form-label require">{{ __('inspection.unit') }}</label>
                                                    <select name="unit_id" id="unit_id"
                                                        class=" form-control single-select" style="width: 100%">
                                                        <option value="">Select Unit</option>
                                                        @foreach ($units as $unit)
                                                            <option value="{{ encryptId($unit->id) }}"
                                                                {{ old('unit_id') == encryptId($location->id) ? 'selected' : '' }}>
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
                                                        class="form-label require">{{ __('inspection.exact_location') }}</label>

                                                    <div class="input-group date form-input  custom-height">
                                                        <input type="text" name="excat_location"
                                                            id = "excat_location" class="form-control"
                                                            value="{{ old('excat_location') }}">

                                                    </div>
                                                    @error('inspection_date')
                                                        <div class="error">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            {{-- <div class=""> --}}
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

                                                                <td colspan="2"
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
                                                href="{{ admin_url('safety/safety-gallery-inspection/list') }}"></x-button-cancel>
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
        <script>
            $(document).ready(function() {
                // Reset form on button click
                $('#resetform').on('click', function(e) {
                    e.preventDefault();
                    location.reload();
                });


                flatpickr("#inspection_date", {
                    dateFormat: "d-m-Y",
                });

                // Custom validation method
                $.validator.addMethod("noSpaces", function(value, element) {
                    return this.optional(element) || value.trim().length > 0;
                }, "This field cannot contain only spaces");

                // Form validation
                $('#safetygalleryAdd').validate({
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
                        unit_id: {
                            required: true,
                        },
                        // signature_image: {
                        //     required: true,
                        //     filesize: 15728640,
                        // },
                        resource_code: {
                            required: true,
                            minlength: 3,
                            maxlength: 30,


                        }
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
                        inspection_date: {
                            required: "Inspection Date is required",
                        },
                        location_id: {
                            required: "Location is required",
                        },
                        unit_id: {
                            required: "Unit is required",
                        },
                        // signature_image: {
                        //     required: "Signature is required",
                        //     filesize: "File size must be less than 15MB."
                        // },
                        resource_code: {
                            required: 'Resource Code is required',
                            remote: 'Resource Code already exists',
                            minlength: "Minimum Characters should be 3",
                            maxlength: "Maximum Characters should not exceed 30",
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
                        console.log('Form is valid, submitting...');
                        form.submit();
                    },
                    invalidHandler: function(event, validator) {
                        var errors = validator.numberOfInvalids();
                        console.log(errors + " field(s) are invalid");
                        validator.errorList.forEach(function(error) {
                            console.log("Field: " + error.element.name + ", Error: " + error
                                .message);
                        });
                    },



                });
                $('input[name^="remarks"]').each(function() {
                    $(this).rules('add', {
                        required: true,
                        minlength: 3,
                        maxlength: 300,

                        messages: {
                            required: "Remarks is required",
                            minlength: "Minimum Characters should be 3",
                            maxlength: "Maximum Characters should not exceed 300",
                        }
                    });
                });
            });
        </script>
    @endpush
