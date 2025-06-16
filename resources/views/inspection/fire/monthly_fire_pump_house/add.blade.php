@extends('admin.layouts.admin')
@section('title', 'Montly Fire Pump House Inspection')
@section('pageurl', admin_url('fire/monthly-fire-pump-house-inspection/list'))


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
                                        href="{{ admin_url('fire/monthly-fire-pump-house-inspection/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form mx-3">
                                    <form method="POST" id="safetygalleryAdd"
                                        action="{{ admin_url('fire/monthly-fire-pump-house-inspection/add/submit') }}"
                                        autocomplete="off" enctype="multipart/form-data">
                                        @csrf

                                        <input type="hidden" name="document_reference_id"
                                            value="{{ encryptId($document_no->id) }}">
                                        <div class="row">
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">{{ __('inspection.doc_no') }}</label>
                                                    <input type="text" name="doc_no" id = "doc_no" class="form-control"
                                                        placeholder="Enter the Document Number"
                                                        value="{{ $document_no->doc_no }}" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label require">{{ __('inspection.issue_date') }}</label>

                                                    <div class="input-group date form-input custom-height">
                                                        <input type="text" name="issue_date" id = "issue_date"
                                                            class="form-control" placeholder="Issued Date"
                                                            value="{{ Displaydateformat($document_no->issue_date) }}"
                                                            readonly>
                                                        <div class="input-group-addon input-group-text">
                                                            <span class="fa fa-calendar"></span>
                                                        </div>
                                                    </div>
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
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label require">{{ __('inspection.inspection_date') }}</label>

                                                    <div class="input-group date form-input custom-height">
                                                        <input type="text" name="inspection_date" id = "inspection_date"
                                                            class="form-control">
                                                        <div class="input-group-addon input-group-text">
                                                            <span class="fa fa-calendar"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label require">{{ __('inspection.resource_code') }}</label>
                                                    <input type="text" name="resource_code" id = "resource_code"
                                                        class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">{{ __('inspection.shifts') }}</label>
                                                    <select name="shift" id="shift"
                                                        class=" form-control single-select" style="width: 100%">
                                                        <option value="">Select {{ __('inspection.shifts') }}
                                                        </option>
                                                        @foreach ($shifts as $shift)
                                                            <option value="{{ encryptId($shift->id) }}">
                                                                {{ $shift->shift }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">{{ __('inspection.unit') }}</label>
                                                    <select name="unit_id" id="unit_id"
                                                        class=" form-control single-select" style="width: 100%">
                                                        <option value="">Select Unit</option>
                                                        @foreach ($units as $unit)
                                                            <option value="{{ encryptId($unit->id) }}">
                                                                {{ $unit->unit_name }}</option>
                                                        @endforeach
                                                    </select>
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

                                                        <th colspan="3"
                                                            style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center;">
                                                            Check Points
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
                                                                {{-- @if ($index == 0)
                                                                    <td rowspan="{{ $rowCount }}"
                                                                        style="border: 1px solid black; padding: 8px; background-color: #f5f5f5; font-weight: bold;">
                                                                        {{ $checklist->subcategory_name }}
                                                                    </td>
                                                                @endif --}}
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
                                                                <td style="border: 1px solid black; padding: 8px; text-align: center;"
                                                                    class="form-input">
                                                                    <textarea class="form-control" name="remarks[{{ $checklist->sub_type_id }}][{{ $checklist->checklist_id }}]"
                                                                        id="" rows="2" style="resize:none;"></textarea>
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

                                        <hr>
                                        <div class="submit-button " style="text-align: right;">
                                            <x-button-submit class="submit"></x-button-submit>
                                            <x-button-reset class="submit"></x-button-reset>
                                            <x-button-cancel
                                                href="{{ admin_url('fire/monthly-fire-pump-house-inspection/list') }}"></x-button-cancel>
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

                flatpickr("#inspection_date", {
                    dateFormat: "d-m-Y",
                });
            });
            $(function() {
                $(function() {
                    $.validator.addMethod("noSpaces", function(value, element) {
                        return this.optional(element) || value.trim().length > 0;
                    }, "This field cannot contain only spaces");

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
                            shift: {
                                required: true,
                            },
                            unit_id: {
                                required: true,
                            },
                            resource_code: {
                                required: true,
                            },
                            "remarks[*][*]": {
                                required: true,
                                minlength: 3,
                                maxlength: 100,
                            },
                            // signature_image: {
                            //     required: true,
                            //     filesize: 15728640,
                            // },

                        },
                        messages: {
                            doc_no: {
                                required: "Document Number is Required",
                                minlength: "Minimum Characters should be 3",
                                maxlength: "Maximum Characters should not exceed 100",
                            },
                            issue_date: {
                                required: "Date Of Audit is required",
                            },
                            inspection_date: {
                                required: "Inspeciton Date is required",
                            },
                            shift: {
                                required: "Shift is required",
                            },
                            unit_id: {
                                required: "Unit is required",
                            },
                            resource_code: {
                                required: 'Recource Code is requried',
                            },
                            "remarks[*][*]": {
                                required: "Remarks is required",
                                minlength: "Minimum Characters should be 3",
                                maxlength: "Maximum Characters should not exceed 100",
                            },
                            // signature_image: {
                            //     required: 'Please upload your signature',
                            //     filesize: 'File size should not exceed 15MB',
                            // },

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

                    $('textarea[name^="remarks"]').each(function() {
                        $(this).rules("add", {
                            required: true,
                            minlength: 3,
                            maxlength: 255,
                            messages: {
                                required: "Remarks is required",
                                minlength: "Minimum Characters should be 3",
                                maxlength: "Maximum Characters should not exceed 255",
                            }
                        });
                    });
                });
            });
        </script>
    @endpush
